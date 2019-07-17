<?php
// Получение списка контактов
function getContacts($session, $cids = [], $offset = 0)
{
    // При отсутствии ID контактов выход из рекурсии
    if (count($cids) === 0)
    {
        return [];
    }
    
    // Строковый параметр для запроса
    $ids = implode('&id[]=', $cids);
    
    // Формирование адреса запроса
    $url = sprintf("contacts?limit_offset=%s&limit_rows=500&id[]=%s", $offset, $ids);
    
    // Увеличение оффсета для выборки следующей партии записей
    $offset += 500;
    
    // Выполнение запроса
    $response = $session->request('GET', $url);
    
    // Если запрос не прошёл успешно...
    if ($response->getStatusCode() !== 200)
    {
        // Выход из рекурсии
        $code = $response->getStatusCode();
        change_request_message_answer("Код: {$code}\n");
        if ($code !== 204) {
            exit(0);
        }
        return [];
    }
    
    // Преобразование ответа в массив
    $response = (string) $response->getBody();
    $response = json_decode($response, true);
    
    // Если записей в ответе нет...
    if (!isset($response['_embedded']['items']))
    {
        // Выход из рекурсии
        return [];
    }
    
    // Если записи закончились (получена последняя партия)...
    if (count($response['_embedded']['items']) < 500)
    {
        // Выход из рекурсии
        return $response['_embedded']['items'];
    }
    
    // Вход в рекурсию (объединение результатов запросов в один массив)
    return array_merge($response['_embedded']['items'], getContacts($session, $cids, $offset));
}
?>
