<?php

class Controller {
    function model($modelName) {
        require_once "./Model/{$modelName}.class.php";
        return new $modelName;
    }

    function view($view, $data = []) {
        foreach($data as $key => $value)
            $$key = $value;
        include("view/$view");
    }
}