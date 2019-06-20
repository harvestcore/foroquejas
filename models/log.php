<?php

    // Cargo el modelo base
    require_once "libs/model.php";

    /**
     * Modelo Log. Un objeto Log representa un mensaje log del sistema.
     */
    class Log extends Model {

        public $datetime;
        public $message;

        /**
         * Constructor por defecto
         */
        public function __construct() {
            parent::__construct();
        }

        /**
         * Inserta un mensaje de log en la BD
         *
         * @param [string] $message Mensaje a almacenar
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function log($message) {
            try {
                $query = $this->db->connect()->prepare('INSERT INTO log (message) VALUES (:message)');
                $query->execute(['message' => $message]);
                
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
        
        /**
         * Obtiene todo el contenido del log
         *
         * @return array Con todos los mensajes log
         */
        public function getlog() {
            try {
                $items = [];
                $query = $this->db->connect()->query('SELECT * FROM log ORDER BY datetime DESC');

                while ($row = $query->fetch()) {
                    $log = [
                        'datetime' => $row['datetime'],
                        'message' => $row['message']
                    ];
                    array_push($items, $log);
                }

                return $items;
            } catch (PDOException $e) {
                return [];
            }
        }
    }

?>