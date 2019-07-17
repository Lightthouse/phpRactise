<?php

function sendXML($name,$server_file_name, $server_settings){

    $file_name = $name;
    $ftp_server = $server_settings['server'];
    $ftp_port = $server_settings['port'];
    $ftp_file = $server_file_name;
    $ftp_user_name = $server_settings['user_name'];
    $ftp_user_pass = $server_settings['user_pass'];

    $successful_file_transfer = false;

    $ftp = ftp_connect($ftp_server, $ftp_port, 15);
    ftp_login($ftp, $ftp_user_name, $ftp_user_pass);
    ftp_pasv($ftp, true); // Passive mode
    try{
        if(!ftp_put($ftp, $ftp_file, $file_name, FTP_BINARY))throw new Exception('Ошибка при передачи файла по FTP!');
        $successful_file_transfer = true;
    }
    catch(Exception $exp){
        change_request_message_answer('Ошибка при передачи файла по FTP!');
        file_put_contents(dirname(__FILE__) . "/../logs/errors.txt", date("Y-m-d h:i:s ", time()) .$exp->getMessage(), FILE_APPEND);
    }

    ftp_close($ftp);
    return $successful_file_transfer;


}
