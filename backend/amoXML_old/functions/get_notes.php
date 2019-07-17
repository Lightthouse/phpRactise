<?php
// Получение коллекции примечаний аккаунта amoCRM
function getNotes($session, $startTime, $offset = 0)
{
    // Заголовок времени старта выборки
    $ifModifiedSince = $startTime->format("D, d M Y H:i:s");

    // Заголовки запроса
    $headers = array(
        'If-Modified-Since' => $ifModifiedSince
    );

    // Коллекция примечаний
    $notes = [];

    // Параметры запроса
    $params = http_build_query([
        'note_type' => '3',
        'type' => 'lead',
        'limit_offset' => $offset,
        'limit_rows' => '500'
    ]);

    $url = sprintf("notes?%s", $params);

    // Увеличение оффсета для выборки следующей партии записей
    $offset += 500;

    // Выполнение запроса
    $response = $session->get($url, ['headers' => $headers]);

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
    $response = json_decode((string) $response->getBody(), true);

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
    return array_merge($response['_embedded']['items'], getNotes($session, $startTime, $offset));
}
