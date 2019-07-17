<?php
// Установка часового пояса на период сессии
ini_set('date.timezone', 'Europe/Moscow');
date_default_timezone_set('Europe/Moscow');

// Обработчик ошибок
(!is_dir(dirname(__FILE__) . '/logs')) ? mkdir(dirname(__FILE__) . '/logs') : '';
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $errorMessege = "";
    switch ($errno) {
            case E_USER_ERROR || E_RECOVERABLE_ERROR:
                $errorMessege .= "Error: " . $errstr . "\n";
                $errorMessege .= $errfile . ':' . $errline . "\n";
                break;
            case E_USER_WARNING || E_WARNING:
                $errorMessege .= "Warning: " . $errstr . "\n";
                $errorMessege .= $errfile . ':' . $errline . "\n";
                break;
            case E_USER_NOTICE || E_NOTICE:
                $errorMessege .= "Notice: " . $errstr . "\n";
                $errorMessege .= $errfile . ':' . $errline . "\n";
                break;
            default:
                $errorMessege .= "Unknown: " . $errstr . "\n";
                $errorMessege .= $errfile . ':' . $errline . "\n";
                break;
        }
    file_put_contents(dirname(__FILE__) . "/logs/errors.txt", date("Y-m-d h:i:s ", time()) . $errorMessege, FILE_APPEND);
});

// Установка лимита времени и памяти для выполнения скрипта
set_time_limit(240);
ini_set('memory_limit', '512M');

// Определение путей файлов
$basePath = __DIR__;
$logsPath = "{$basePath}/logs";
$functionsPath = "{$basePath}/functions";
$settingsPath = "{$basePath}/settings";
$xmlPath = "{$basePath}/xml";

// Подключение классов и функций
require_once("{$basePath}/vendor/autoload.php");
require_once ("{$functionsPath}/include_functions.php");
includeFunctions($functionsPath);

//Подключение параметров сервера
$server_settings = file_get_contents(__DIR__ .'/'."ftp_settings.json");
$server_settings = json_decode($server_settings, true);

// Переменные для хранения объектов дат
$start_date_obj = '';
$end_date_obj = '';

// Обработка веб-формы/CLI
$start_date_obj = $_POST['start_date'] ?? date('Y-m-d');
$end_date_obj = $_POST['end_date'] ?? date('Y-m-d');

$start_date_obj = new DateTime( $start_date_obj .= " 00:00:00");
$end_date_obj = new DateTime( $end_date_obj .= " 23:59:59");

// Получение callbackURI
$callBackURI = $_POST['callBackURI'] ?? false;
if($callBackURI == false)echo 'передайте callBackURI';

// Начальная временная точка
$start_date = $start_date_obj->format('D d M Y H:i:s eO');

// Конечная временная точка
$end_date = $end_date_obj->format('D d M Y H:i:s eO'); // format('U') для вывода UNIX-timestamp

// Проверка наличия папки с настройками
if (!is_dir("{$settingsPath}")) {
    file_put_contents("{$logsPath}/cron.txt", date("Y-m-d h:i:s") . "\tДиректория с настройками не обнаружена\n", FILE_APPEND);
    exit(0);
}

// Получение имён файлов с настройками
$settingsFiles = scandir("{$settingsPath}");

// Количество файлов в папке
$filesCount = count($settingsFiles);

// Если папка пуста, то выход из программы
if ($filesCount < 3) {
    file_put_contents("{$logsPath}/cron.txt", date("Y-m-d h:i:s") . "\tДиректория с настройками пуста\n", FILE_APPEND);
    exit(0);
}

// Перебор файлов настроек для выполнение логики по каждому из них
for ($i = 2; $i < $filesCount; $i++) {
    // Имя файла настроек
    $fileName = $settingsFiles[$i];
    
    // Расширение файла
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    
    // Если расширение файла не php, то переход к следующему
    if ($ext !== "json") {
        continue;
    }
    
    // Получение настроек из файла
    $settings = file_get_contents("{$settingsPath}/{$fileName}");
    $settings = json_decode($settings, true);
    
    // Если поступил запрос из формы...
    if (isset($_POST['subdomain']) && $_POST['subdomain'] != $settings['subdomain']) {
        continue;
    }
    
    // Получение сессии для доступа к amoCRM
    $session = amoSession($settings);
    
    // Получение примечаний о смене статуса сделки
    $notes = getNotes($session, $start_date_obj);
    
    // Получение примечаний от Dropbox
    $dropboxNotes = getDboxNotes($session);
    
    // Выборка примечаний от Dropbox, добавленных за период
    $periodicalDropboxNotes = array_filter($dropboxNotes, function($note) use($start_date_obj, $end_date_obj) {
        return ($note['created_at'] >= $start_date_obj->format('U') && $note['created_at'] <= $end_date_obj->format('U'));
    });
    
    // Выборка примечаний перехода на определённый статус за период
    $notes = array_filter($notes, function($note) use($settings, $start_date_obj, $end_date_obj) {
        return ($note['created_at'] >= $start_date_obj->format('U') && $note['created_at'] <= $end_date_obj->format('U') && in_array($note['params']['STATUS_NEW'], $settings['statuses']));
    });
    
    // Объединение массивов
    $notes = array_merge($notes, $periodicalDropboxNotes);
    
    // Получение ID сделок из примечаний
    $leadsIds = array_column($notes, 'element_id');
    $leadsIds = array_unique($leadsIds);
    
    // Выборка примечаний от Dropbox для конкретных сделок
    $dropboxNotes = array_filter($dropboxNotes, function($note) use($leadsIds) {
        return in_array($note['element_id'], $leadsIds);
    });
    
    // Получение коллекции сделок по ID
    $leads = getLeadsByIds($session, $leadsIds);
    
    // Добавление к сделкам информации о файле в Dropbox
    foreach($leads as $index => $lead) {
        // Выборка примечаний сделки
        $leadNotes = array_filter($dropboxNotes, function($note) use($lead){
            return $note['element_id'] == $lead['id'];
        });
        
        // Массив файлов, прикреплённых к сделке
        $leads[$index]['files'] = [];
        
        // Добавление информации к сделке
        foreach($leadNotes as $note) {
            $leads[$index]['files'][] = [
                'name' => $note['params']['FILE_NAME'],
                'link' => $note['params']['LINK']
            ];
        }
    }
    
    // Массивы для коллекций
    $users = [];
    $contacts = [];
    $pipelines = [];
    
    // Если есть хотя бы одна сделка...
    if (count($leads) > 0) {
        // Получение коллекции пользователей аккаунта amoCRM
        $users = getUsers($session);
    
        // ID контактов сделок
        $cids = [];
        
        // Получение ID контактов из сделок
        foreach ($leads as $lead) {
           if (isset($lead['main_contact']['id'])) {
               $cids[] = $lead['main_contact']['id'];
           }
        }
        
        // Исключение дублей
        $cids = array_unique($cids);
        
        // Массив контактов
        $contacts = [];
        
        // Разделение массива контактов на части
        $chunks = array_chunk($cids, 100);
        
        // Получение коллекции контактов
        foreach ($chunks as $chunk) {
            $contacts = array_merge($contacts, getContacts($session, $chunk));
        }
    
        // Получение коллекции воронок аккаунта amoCRM
        $pipelines = getPipelines($session);
    }
    
    // Версия формата XML
    $version = '1.0';
    
    // Кодировка XML-документа
    $encoding = 'Windows-1251';
    
    // Вид отступа в документе
    $indentation = '  ';
    
    // Создание экземпляра XMLWriter
    $xmlw = new XMLWriter();
    
    // Создание XML-документа
    $xmlw->openMemory();
    
    // Включение отступов
    $xmlw->setIndent(true);
    $xmlw->setIndentString($indentation);
    
    // Начало документа
    $xmlw->startDocument($version, $encoding);
        $xmlw->startElement('root');
            $xmlw->startElement('header');
                $xmlw->startElement('subdomain');
                $xmlw->text($settings['subdomain']);
                $xmlw->endElement();
                
                $xmlw->startElement('date_begin');
                $xmlw->text($start_date_obj->format('D d M Y H:i:s'));
                $xmlw->endElement();
                
                $xmlw->startElement('date_end');
                $xmlw->text($end_date_obj->format('D d M Y H:i:s'));
                $xmlw->endElement();
                
                $xmlw->startElement('leads_count');
                $xmlw->text(sizeof($leads));
                $xmlw->endElement();
            $xmlw->endElement();
            
            $xmlw->startElement('leads');
            foreach ($leads as $lead) {
                $xmlw->startElement('lead');
                    $xmlw->startElement('sale');
                    $xmlw->text($lead['sale']);
                    $xmlw->endElement();
                    
                    $xmlw->startElement('amo_id');
                    $xmlw->text($lead['id']);
                    $xmlw->endElement();
                    
                    $xmlw->startElement('lead_name');
                    $xmlw->text($lead['name']);
                    $xmlw->endElement();
                    
                    $pipeline_id = $lead['pipeline']['id'];
                    $lead_status_id = $lead['status_id'];
                    @$lead_status = $pipelines[$pipeline_id]['statuses'][$lead_status_id]['name'];
                    $xmlw->startElement('status');
                    $xmlw->text(($lead_status) ? $lead_status : '');
                    $xmlw->endElement();
                    
                    $xmlw->startElement('status_id');
                    $xmlw->text($lead['status_id']);
                    $xmlw->endElement();
                    
                    @$responsible_user_name = $users[$lead['responsible_user_id']]['name'];
                    $xmlw->startElement('responsible_user');
                    $xmlw->text(($responsible_user_name) ? $responsible_user_name : '');
                    $xmlw->endElement();
                    
                    $xmlw->startElement('responsible_user_id');
                    $xmlw->text($lead['responsible_user_id']);
                    $xmlw->endElement();
                    
                    $xmlw->startElement('created_at');
                    $xmlw->text($lead['created_at']);
                    $xmlw->endElement();
                    
                    $xmlw->startElement('custom_fields');
                        foreach ($lead['custom_fields'] as $field) {
                            $xmlw->startElement('field');
                                $xmlw->startElement('key');
                                $xmlw->text($field['name']);
                                $xmlw->endElement();
                            
                            // Выборка значений кастомного поля
                            $values = [];
                            foreach ($field['values'] as $value) {
                                $values[] = $value['value'];
                            }
                            
                                $xmlw->startElement('value');
                                $xmlw->text(implode(', ', $values));
                                $xmlw->endElement();
                            $xmlw->endElement();
                        }
                    $xmlw->endElement();
                    
                    $xmlw->startElement('contact');
                        $contact_id = isset($lead['main_contact']['id']) ? $lead['main_contact']['id'] : 0;
                        $contact_index = get_by_id($contacts, $contact_id);
                        $contact_name = isset($contacts[$contact_index]['name']) ? $contacts[$contact_index]['name'] : '';
                        
                        $xmlw->startElement('id');
                        $xmlw->text(($contact_id) ? $contact_id : '');
                        $xmlw->endElement();
                    
                        $xmlw->startElement('name');
                        $xmlw->text(($contact_name) ? $contact_name : '');
                        $xmlw->endElement();
                        
                        $xmlw->startElement('custom_fields');
                            // Заполнение при наличии контакта у сделки
                            if ($contact_index !== -1) {
                                foreach ($contacts[$contact_index]['custom_fields'] as $field) {
                                    $xmlw->startElement('field');
                                        $xmlw->startElement('key');
                                        $xmlw->text($field['name']);
                                        $xmlw->endElement();
                                
                                    // Выборка значений кастомного поля
                                    $values = [];
                                    foreach ($field['values'] as $value) {
                                        $values[] = $value['value'];
                                    }
                                
                                        $xmlw->startElement('value');
                                        $xmlw->text(implode(', ', $values));
                                        $xmlw->endElement();
                                    $xmlw->endElement();
                                }
                            }
                        $xmlw->endElement();
                    $xmlw->endElement();
                    
                    $xmlw->startElement('files');
                    foreach ($lead['files'] as $file) {
                        $xmlw->startElement('file');
                            $xmlw->startElement('name');
                            $xmlw->text($file['name']);
                            $xmlw->endElement();
                            
                            $xmlw->startElement('link');
                            $xmlw->text($file['link']);
                            $xmlw->endElement();
                        $xmlw->endElement();
                    }
                    $xmlw->endElement();
                $xmlw->endElement();
            }
            $xmlw->endElement();
        $xmlw->endElement();
    $xmlw->endDocument();
    
    // Формирование имени XML-файла
    $filename = sprintf("OUT_%s_%s.xml", $settings['subdomain'], date('Ymd_His'));
    
    (!is_dir($xmlPath)) ? mkdir($xmlPath) : '';
    
    // Вывод XML-документа в файл
    if (file_put_contents("{$xmlPath}/{$filename}", $xmlw->outputMemory()) > 0) {
        file_put_contents("{$logsPath}/cron.txt", date("Y-m-d h:i:s") . "\tСоздан файл {$filename}\n", FILE_APPEND);

    //Отправка XML по FTP
        sendXML("{$xmlPath}/{$filename}",$filename,$server_settings);

    } else {
        file_put_contents("{$logsPath}/cron.txt", date("Y-m-d h:i:s") . "\tПроизошла ошибка при создании файла {$filename}\n", FILE_APPEND);
        change_request_message_answer('ошибка при формировании XML файла');
    }

    //Отправка статуса выполнения функции на callbackURI
    success_status_request($callBackURI);

}
?>
