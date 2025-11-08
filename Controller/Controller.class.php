<?php

class Controller {
    
    /**
     * Helper method untuk memastikan session sudah started (OOP pattern)
     */
    protected function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
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