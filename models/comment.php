<?php

    /**
     * Modelo Comment. Un objeto Comment representa un comentario asociado a un usuario y a un evento.
     */
    class Comment extends Model {

        public $id;
        public $email;
        public $content;
        public $datetime;
        public $event;

        /**
         * Constructor por defecto
         */
        public function __construct() {
            parent::__construct();
        }

        /**
         * Inserta un comentario en la BD
         *
         * @param [array] $data Datos del comentario a agregar.
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function insert($data) {
            try {
                $query = $this->db->connect()->prepare('INSERT INTO comments (email, content, event) VALUES (:email, :content, :event)');
                $query->execute([
                    'email'   => $data['email'],
                    'content' => $data['content'],
                    'event'   => $data['event'],
                 ]);
                
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
        
        /**
         * Obtiene todos los comentarios de una queja
         *
         * @param [id] $event Identificador del evento
         * @return array Contiene los comentarios de la queja. Array vacío en caso de error.
         */
        public function getall($event) {
            try {
                $items = [];
                $query = $this->db->connect()->prepare('SELECT * FROM comments WHERE event=:event ORDER BY datetime DESC');
                $query->execute(['event' => $event]);
                while ($row = $query->fetch()) {
                    $com = [];
                    $com['id']        = $row['id'];
                    $com['email']     = $row['email'];
                    $com['content']   = $row['content'];
                    $com['datetime']  = $row['datetime'];
                    $com['event']     = $row['event'];

                    array_push($items, $com);
                }

                return $items;
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Devuelve los últimos comentarios agregados en la web a cualquier queja
         *
         * @param [int] $qtty Cantidad de comentarios a recuperar
         * @return array Con cada uno de los comentarios asociados a la queja junto con el usuario que los publica
         */
        public function getlastcomments($qtty) {
            try {
                require_once "user.php";
                $u = new User();
                $comments = [];
                $query = $this->db->connect()->prepare('SELECT * FROM comments ORDER BY datetime DESC LIMIT :qtty');
                $query->execute(['qtty' => $qtty]);
                while ($row = $query->fetch()) {
                    array_push($comments, ['data' => $row, 'user' => $u->getuser($row['email'], false)]);
                }

                return $comments;
            } catch (PDOException $e) {
                return null;
            }
        }

        /**
         * Obtiene el número de comentarios que tiene una queja
         *
         * @param [int] $event Identificador de la queja
         * @return int Número de comentarios que tiene la queja, null en caso de error
         */
        public function countcommentsbyevent($event) {
            try {
                $query = $this->db->connect()->prepare('SELECT COUNT(*) FROM comments WHERE event=:event');
                $query->execute(['event' => $event]);
                $noofcomments = $query->fetch()[0];

                return $noofcomments;
            } catch (PDOException $e) {
                return null;
            }
        }

        /**
         * Obtiene un comentario por su identificador
         *
         * @param [int] $id Identificador del comentario
         * @return array Con el comentario, vacío en caso de error
         */
        public function getcomment($id) {
            try {
                $com = [];
                $query = $this->db->connect()->prepare('SELECT * FROM comments WHERE id=:id');
                $query->execute(['id' => $id]);
                while ($row = $query->fetch()) {
                    array_push($com, $row);
                }

                return $com;
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Obtiene todos los comentarios que ha publicado un usuario
         *
         * @param [string] $email Email del usuario a consultar
         * @return array Con los comentarios del usuario, null en caso de error.
         */
        public function getcommentsbyuser($email) {
            try {
                $query = $this->db->connect()->prepare('SELECT * FROM comments WHERE email=:email ORDER BY datetime DESC');
                $query->execute(['email' => $email]);
                
                $items = [];
                while ($row = $query->fetch()) {
                    array_push($items, $row);
                }

                return $items;
            } catch (PDOException $e) {
                return null;
            }
        }

        /**
         * Elimina un comentario
         *
         * @param [int] $id Identificador del comentario
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function delete($id) {
            try {
                $query = $this->db->connect()->prepare("DELETE FROM comments WHERE id=:id");
                $query->execute(['id' => $id]);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Elimina todos los comentarios de un evento
         *
         * @param [int] $event Identificador del evento
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function deletebyevent($event) {
            try {
                $query = $this->db->connect()->prepare("DELETE FROM comments WHERE event=:event");
                $query->execute(['event' => $event]);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Elimina todos los comentarios de un usuario
         *
         * @param [string] $email Email del usuario
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function deleteallbyemail($email) {
            try {
                $query = $this->db->connect()->prepare("DELETE FROM comments WHERE email=:email");
                $query->execute(['email' => $email]);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Cuenta los comentarios de un evento
         *
         * @param [int] $event Identificador del evento
         * @return int Número de comentarios, -1 en caso de error.
         */
        public function countcomments($event) {
            try {
                $query = $this->db->connect()->prepare("SELECT COUNT(*) FROM comments WHERE event=:event");
                $query->execute(['event' => $event]);
                $noofcomments = $query->fetch()[0];

                return $noofcomments;
            } catch (PDOException $e) {
                return -1;
            }
        }

        /**
         * Obtiene el top de usuarios que más comentarios tienen en el sistema
         *
         * @param [int] $qtty Cantidad de usuarios a devolver
         * @return array Con los usuarios y el número de comentarios que tienen
         */
        public function gettop($qtty) {
            try {
                require_once "user.php";
                $u = new User();
                $top = [];
                $query = $this->db->connect()->prepare("SELECT email, COUNT(email) AS comms FROM comments GROUP BY email ORDER BY comms DESC LIMIT :qtty");
                $query->execute(['qtty' => $qtty]);

                while ($row = $query->fetch()) {
                    array_push($top, ['data' => $row, 'user' => $u->getuser($row['email'], false)]);
                }

                return $top;
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Obtiene el número de comentarios que hay en el sistema, independientemente del evento
         *
         * @return int Número de comentarios, -1 en caso de error
         */
        public function getcountevents() {
            try {
                $query = $this->db->connect()->query("SELECT COUNT(*) FROM comments");
                $noofcomments = $query->fetch()[0];

                return $noofcomments;
            } catch (PDOException $e) {
                return -1;
            }
        }
    }

?>