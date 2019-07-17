<?php
// Создание сессии для работы с API amoCRM
function amoSession($settings)
{
    $amouser = $settings['amouser'];
    $amohash = $settings['amohash'];
    $subdomain = $settings['subdomain'];
    
    // Хранилище coockies
    $jar = new \GuzzleHttp\Cookie\CookieJar;

    // Экземпляр клиента Guzzle
    $HTTPClient = new \GuzzleHttp\Client(['base_uri' => sprintf('https://%s.amocrm.ru/api/v2/', $subdomain), 'cookies' => $jar, 'exceptions' => false]);
    
    // Данные для аутентификации
    $auth_data = array(
        "USER_LOGIN" => $amouser,
        "USER_HASH"  => $amohash
    );
    
    // Адрес запроса
    $url = sprintf('https://%s.amocrm.ru/private/api/auth.php?type=json', $subdomain);
    
    // Аутентификация в API amoCRM с сохранением cookies
    $HTTPClient->request('POST', $url, ['form_params' => $auth_data]);
    return $HTTPClient;
} 
// ?>