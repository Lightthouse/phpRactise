<?php
// Получение реализованных сделок за период
function getLeads($session, $startDate, $endDate, $offset = 0)
{
    // Адрес запроса
    $url = sprintf("leads?status=142&limit_offset=%s&limit_rows=500&filter[date_modify][from]=%s&filter[date_modify][to]=%s", $offset, $startDate, $endDate);
    
    // Увеличение оффсета для выборки следующей партии записей
    $offset += 500;
    
    // Выполнение запроса
    $response = $session->request('GET', $url);

    // Если запрос не прошёл успешно...
    if ($response->getStatusCode() !== 200)
    {
        // Выход из рекурсии
        $code = $response->getStatusCode();
        print("Код: {$code}\n");
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
    return array_merge($response['_embedded']['items'], getLeads($session, $startDate, $endDate, $offset));
}
?>