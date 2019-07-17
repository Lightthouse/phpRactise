<?php
// Получение количества страниц событий аккаунта amoCRM
function getEventsPagesTotal($session, $startTimeObj, $endTimeObj, $eventType = 14)
{
    // Заголовки запроса
    $headers = array(
        'X-Requested-With' => 'XMLHttpRequest'
    );

    // Параметры запроса
    $params = http_build_query([
        'useFilter' => 'y',
        'filter' => [
            'event_type' => [$eventType]
        ],
        'filter_date_from' => $startTimeObj->format('d.m.Y'),
        'filter_date_to' => $endTimeObj->format('d.m.Y')
    ]);
    
    $url = sprintf("/ajax/events/count");
    
    // Выполнение запроса
    $response = $session->get($url . "?{$params}", ['headers' => $headers]);

    // Если запрос не прошёл успешно...
    if ($response->getStatusCode() !== 200)
    {
        $code = $response->getStatusCode();
        print("Код: {$code}\n");
        return -1;
    }

    // Преобразование ответа в массив
    $response = json_decode((string) $response->getBody(), true);
    
    // Получение количества страниц
    return (int) $response['pagination']['total'] ?? -1;
}