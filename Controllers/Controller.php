<?php

namespace Controllers;

abstract class Controller
{
    protected function input($key, $filter = false)
    {
        if (is_array($key)) {
            $input = [];
            foreach ($key as $index => $value) {
                if (is_numeric($index)) {
                    $input[$value] = $this->input($value);
                } else {
                    $input[$index] = $this->input($index, $value);
                }
            }
            return $input;
        } else {
            $value = $_GET[$key] ?? $_POST[$key] ?? false;
            if ($value !== false) {
                switch ($filter) {
                    case 'string':
                        $value = filter_var($value, FILTER_SANITIZE_STRING);
                        break;
                    case 'float':
                        $value = (double)filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
                        break;
                    case 'integer':
                        $value = (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                        break;
                }
            }

            return $value;
        }
    }

    protected function json($object, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        $object['status_code'] = $status;
        echo json_encode($object, JSON_UNESCAPED_SLASHES);
    }

    protected function html($file, $variables = [])
    {

        foreach ($variables as $key => $value) {
            $$key = $value;
        }

        if ($file != "login") {
            include __DIR__ . '/../views/sections/header.php';
            include __DIR__ . '/../views/sections/navbar.php';
        }
        include __DIR__ . '/../views/' . $file . '.php';
    }

}