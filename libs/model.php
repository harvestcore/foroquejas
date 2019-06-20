<?php

    /**
     * Clase Model, sirve de base para crear modelos.
     */
    class Model {

        /**
         * Constructor por defecto. Crea un objeto Database
         */
        function __construct() {
            $this->db = new Database();
        }
    }

?>