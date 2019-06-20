<?php

    // Cargo el modelo del log
    require_once "models/log.php";

    /**
     * Clase Controller, sirve de base para crear el resto de controladores.
     */
    class Controller {

        /**
         * Constructor por defecto
         */
        function __construct() {
            // Creo vista y logger
            $this->view = new View();
            $this->logger = new Log();
        }

        /**
         * Carga un modelo de datos en el controlador
         *
         * @param [string] $model Modelo a cargar
         * @return void
         */
        function loadModel($model) {
            $path = 'models/' . strtolower($model) . '.php';

            if (file_exists($path)) {
                require $path;
                $this->model = new $model();
            }
        }

        /**
         * Logea un mensaje
         *
         * @param [string] $msg Mensaje a almacenar
         * @return void
         */
        protected function log($msg) {
            $this->logger->insert($msg);
        }

        /**
         * Obtiene todo el contenido del log
         *
         * @return void
         */
        protected function getlog() {
            return $this->logger->getall();
        }

        /**
         * Comprueba si el usurio está logueado
         *
         * @return bool True si el usuario está logeado, false en caso contrario.
         */
        protected function checksession() {
            if (session_status() == PHP_SESSION_NONE) session_start();
            return isset($_SESSION['user']);
        }

        /**
         * Realiza una redireccion
         *
         * @param [string] $goto A donde se quiere redireccionar
         * @return void
         */
        protected function header($goto) {
            header("Location: " . constant('URL') . $goto);
        }

        /**
         * Comprueba si ha caducado la sesión y en caso afirmativo desloguea al usuario
         *
         * @return void
         */
        protected function checkiftimeout() {
            if (session_status() == PHP_SESSION_NONE) session_start();
            if (isset($_SESSION['discard_after']) && time() > ($_SESSION['discard_after']))
                $this->header('login/logout');
        }
    }

?>