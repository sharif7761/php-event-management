<?php

namespace controller;
class BaseController
{
    protected function view($name, $data = [])
    {
        extract($data);
        require_once __DIR__ . "/../views/layouts/main.php";
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }
}