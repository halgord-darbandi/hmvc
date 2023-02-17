<?php

function view($view, $data = [])
{
    extract($data);
    $view = str_replace('.', '/', $view);
    $path = $_ENV['BASE_PATH'] . "views/$view.php";
    require $path;
}