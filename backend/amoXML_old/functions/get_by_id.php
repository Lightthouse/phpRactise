<?php
// Поиск элемента в коллекции по идентификатору
function get_by_id($source, $id)
{
    foreach ($source as $key => $value)
    {
        if ((string) $value['id'] === (string) $id)
        {
            return $key;
        }
    }
    return -1;
}
// ?>