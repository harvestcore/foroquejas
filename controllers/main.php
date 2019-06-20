<?php

    /**
     * Controlador Main, controla la página principal del sitio web.
     */
    class Main extends Controller {

        /**
         * Constructor por defecto
         */
        function __construct() {
            parent::__construct();
        }

        /**
         * Renderiza una plantilla. Comprueba también si se ha cumplido el timeout (para desloguear al usuario)
         *
         * @param [string] $whattorender Qué se quiere renderizar
         * @param [array] $argv Argumentos que se le pasan a la plantilla
         * @return void
         */
        function render($whattorender, $argv) {
            $this->checkiftimeout();
            $this->view->render('main/' . $whattorender, $argv);
        }

        /**
         * Maneja el index principal
         *
         * @return void
         */
        function index() {
            $this->render('index', []);
        }
    }

?>