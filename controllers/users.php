<?php

    /**
     * Controlador Users, controla todo lo relacionado con las estadísticas.
     */
    class Users extends Controller {

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
            $this->view->render('users/' . $whattorender, $argv);
        }

        /**
         * Maneja el index de las estadísticas
         *
         * @return void
         */
        function index() {
            $stats = $this->model->getstats();
            $this->render('stats', ['stats' => $stats]);
        }

        /**
         * Obtiene datos estadísticos y los devuelve en un json
         *
         * @return json Con el estado de la operación y los datos estadísticos
         */
        function getstats() {
            header_remove();
            header('Access-Control-Allow-Origin: *');
            header('Content-type: application/json');
            
            $data = $this->model->getstats();
            if (!empty($data)) {
                echo json_encode(array('status' => true, 'data' => $data), JSON_FORCE_OBJECT);
            } else {
                echo json_encode(array('status' => false), JSON_FORCE_OBJECT);
            }
        }
    }

?>