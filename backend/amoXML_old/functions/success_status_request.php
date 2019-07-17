<?php

function success_status_request($callBackURI)
{
    $client = new GuzzleHttp\Client();
    $requestResult = file_get_contents(__DIR__ .'/'. '../logs/request_answer_message.json');

    try {
        $response = $client->request('POST', $callBackURI, ['json' => json_decode($requestResult)]);
    } catch (Exception $exp) {
        file_put_contents(dirname(__FILE__) . "/../logs/errors.txt", date("Y-m-d h:i:s ", time()) .$exp->getMessage(), FILE_APPEND);
    }
    change_request_message_answer('',true);
}

