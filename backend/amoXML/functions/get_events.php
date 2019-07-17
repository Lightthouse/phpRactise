<?php
// Подключение функции получения количества страниц событий
require_once(sprintf("%s/get_events_pages_total.php", __DIR__));

// Получение коллекции событий аккаунта amoCRM
function getEvents($session, $startTimeObj, $endTimeObj, $eventType = 14)
{
    // Получение количества страниц событий
    $pagesTotal = getEventsPagesTotal($session, $startTimeObj, $endTimeObj, $eventType);
    
    // Проверка количества страниц
    if ($pagesTotal < 1) {
        if ($pagesTotal !== 0) {
            exit(0);
        }
        return [];
    }
    
    // Заголовки запроса
    $headers = array(
        'X-Requested-With' => 'XMLHttpRequest'
    );

    // Коллекция событий
    $events = [];
    
    // Получение событий постранично
    for ($pageNumber = 1; $pageNumber <= $pagesTotal; $pageNumber++) {
        // Параметры запроса
        $params = http_build_query([
            'useFilter' => 'y',
            'filter' => [
                'event_type' => [$eventType]
            ],
            'filter_date_from' => $startTimeObj->format('d.m.Y'),
            'filter_date_to' => $endTimeObj->format('d.m.Y'),
            'PAGEN_1' => $pageNumber,
            'json' => 1
        ]);
        
        // Адрес запроса
        $url = sprintf("/ajax/events/list?%s", $params);
        
        // Выполнение запроса
        $response = $session->get($url, ['headers' => $headers]);
    
        // Если запрос не прошёл успешно...
        if ($response->getStatusCode() !== 200)
        {
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
        
        // Если записей в ответе нет...
        if (!isset($response['response']['items']))
        {
            // Выход из функции
            return [];
        }
        
        $events = array_merge($events, $response['response']['items']);
        
        // Задержка между запросами
        if (($pageNumber % 4) === 0) {
            sleep(1);
        }
    }
    return $events;
}