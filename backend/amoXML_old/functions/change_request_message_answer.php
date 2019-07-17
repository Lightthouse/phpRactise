<?php

    function change_request_message_answer(string $error_message, bool $success = false){
        $message_direction = (__DIR__ .'/'. '../logs/request_answer_message.json');
        $message_file = file_get_contents($message_direction);
        $message = json_decode($message_file, true);
        $message['success'] = $success;
        $message['message'] = $error_message;
        $message_file = json_encode($message);
        file_put_contents($message_direction, $message_file);
    }
