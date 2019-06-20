<?php
    // Cargo librerias de Twig
    require_once 'vendor/autoload.php';

    /**
     * Clase View. Usada para interactuar con Twig y renderizar las plantillas.
     */
    class View {

        /**
         * Cosntructor por defecto
         */
        function __construct() {
            // Sistema de archivos de twig, donde se encuentran las vistas
            $loader = new \Twig\Loader\FilesystemLoader('views');
            // creo objeto Twig
            $this->twig = new \Twig\Environment($loader);
            // inicio session si no está iniciada
            if (session_status() == PHP_SESSION_NONE) session_start();
            // Agrego variables globales a Twig
            $this->twig->addGlobal('session', $_SESSION);
            $this->twig->addGlobal('URL', constant('URL'));

            // Agrego función específica para traducir el valor del estado al español
            $this->twig->addFunction(new \Twig_SimpleFunction('statustosp', function ($string) { return $this->statusToSP($string); }));
        }

        /**
         * Cambia el valor del estado a uno legible por el usuario
         *
         * @param [string] $string Estado a traducir
         * @return string Estado traducido
         */
        function statusToSP($string) {
            switch($string) {
                case "processed":    return "Tramitada";
                case "checking":     return "Pendiente";
                case "irresolvable": return "Irresoluble";
                case "resolved":     return "Resuelta";
                case "checked":      return "Comprobada";
            }
        }

        /**
         * Renderiza una plantilla.
         *
         * @param [string] $name Qué se quiere renderizar
         * @param [array] $argv Argumentos que se le pasan a la plantilla
         * @return void
         */
        function render($name, $argv) {
            echo $this->twig->render($name . '.twig', $argv);
        }
    }

?>