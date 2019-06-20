<?php

    /**
     * Clase Database. Un objeto contiene los datos de conexión a la base de datos
     */
    class Database {
        private $host;
        private $db;
        private $user;
        private $password;
        private $charset;

        /**
         * Constructor por defecto. Toma los valores de constantes.
         */
        public function __construct() {
            $this->host = constant('HOST');
            $this->db = constant('DB');
            $this->user = constant('USER');
            $this->password = constant('PASSWORD');
            $this->charset = constant('CHARSET');
        }

        /**
         * Conexión con la base de datos.
         *
         * @return PDO Objeto PDO para hacer operaciones en la base de datos
         */
        function connect() {
            try {
                $connection = "mysql:host=" . $this->host . ";dbname=" . $this->db . ";charset=" . $this->charset;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES =>  false,
                ];

                return new PDO($connection, $this->user, $this->password, $options);
            } catch (PDOException $e) {
                print_r('Error connection' . $e->getMessage());
            }
        }
    }

?>