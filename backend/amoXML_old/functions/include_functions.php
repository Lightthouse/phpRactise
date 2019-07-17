<?php
// Подключение файлов с функциями
function includeFunctions($funcPath)
{
    // Получение имён файлов с настройками
    $files = scandir("{$funcPath}");
    
    // Количество файлов в папке
    $filesCount = count($files);
    
    // Если папка пуста, то выход из функции
    if ($filesCount < 3) {
        return false;
    }
    
    // Перебор файлов функций для их подключения
    for ($i = 2; $i < $filesCount; $i++) {
        // Имя файла функции
        $fileName = $files[$i];
        
        // Расширение файла
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        
        // Если расширение файла не php, то переход к следующему
        if ($ext !== "php") {
            continue;
        }
        
        // Получение имени файла без расширения
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);
        
        // Получение частей имени файла
        $parts = explode('_', $fileName);
        
        // Преобразование частей имени файла в вид camelCase
        for ($part = 1; $part < count($parts); $part++) {
            $parts[$part] = ucwords($parts[$part]);
        }
        
        // Формирование имени функции
        $funcName = implode('', $parts);
        
        // Если функция ещё не подключена...
        if (!function_exists($funcName)) {
            // Подключение файла функции
            require_once("{$funcPath}/{$fileName}.php");
        }
    }
    return true;
}
// ?>