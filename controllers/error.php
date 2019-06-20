<?php
    /**
     * Clase manejadora de errores. Se activa cuando se intenta acceder a una URL que no se puede manejar
     */
    class Errors extends Controller {

        /**
         * Constructor por defecto, renderiza la plantilla con el mensaje
         */
        function __construct() {
            parent::__construct();
            $this->view->render('error/index', ['message' => "Error al cargar el recurso."]);
        }
    }
    
?>