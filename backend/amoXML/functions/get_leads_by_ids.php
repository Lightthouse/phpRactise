<?php
// Получение сделок по ID
function getLeadsByIds($session, $leadsIds = [])
{   
    // Если нет ID сделок...
    if (count($leadsIds) === 0) {
        // Выход из функции
        return [];
    }
    
    // Массив сделок
    $leads = [];
    
    // Разделение массива с ID на части
    $chunks = array_chunk($leadsIds, 500);
    
    // Получение записей по частям
    foreach ($chunks as $chunk) {
        // Параметры запроса
        $params = http_build_query([
            'id' => $chunk
        ]);
        
        // Адрес запроса
        $url = sprintf("leads?%s", $params);
        
        // Выполнение запроса
        $response = $session->get($url);
        
        // Если запрос не прошёл успешно...
        if ($response->getStatusCode() !== 200) {
            // Выход из функции
            $code = $response->getStatusCode();
            change_request_message_answer("Код: {$code}\n");
            if ($code !== 204) {
                exit(0);
            }
            return [];
        }
        
        // Преобразование ответа в массив
        $response = json_decode((string) $response->getBody(), true);
        
        // Если записи есть...
        if (isset($response['_embedded']['items'])) {
            // Добавление записей в массив
            $leads = array_merge($leads, $response['_embedded']['items']);
        }
    }
    
    return $leads;
}
?>
