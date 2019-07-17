<?php
// Получение контактов по ID
function getContactsByIds($session, $contactsIds = [])
{   
    // Если нет ID контактов...
    if (count($contactsIds) === 0) {
        // Выход из функции
        return [];
    }
    
    // Массив контактов
    $contacts = [];
    
    // Разделение массива с ID на части
    $chunks = array_chunk($contactsIds, 500);
    
    // Получение записей по частям
    foreach ($chunks as $chunk) {
        // Параметры запроса
        $params = http_build_query([
            'id' => $chunk
        ]);
        
        // Адрес запроса
        $url = sprintf("contacts?%s", $params);
        
        // Выполнение запроса
        $response = $session->get($url);
        
        // Если запрос не прошёл успешно...
        if ($response->getStatusCode() !== 200) {
            // Выход из функции
            $code = $response->getStatusCode();
            print("Код: {$code}\n");
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
            $contacts = array_merge($contacts, $response['_embedded']['items']);
        }
    }
    
    return $contacts;
}
// ?>