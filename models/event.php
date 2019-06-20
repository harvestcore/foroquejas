<?php

    /**
     * Modelo Event. Un objeto Event representa un evento o queja asociada a un usuario.
     * Modelo usado para manejar también los likes y dislikes de los eventos.
     */
    class Event extends Model {

        public $id;
        public $title;
        public $description;
        public $place;
        public $keywords;
        public $images;
        public $datetime;
        public $email;
        public $likes;
        public $dislikes;
        public $status;

        /**
         * Constructor por defecto
         */
        public function __construct() {
            parent::__construct();
        }

        /**
         * Inserta un evento en la BD
         *
         * @param [array] $data Datos del evento a agregar.
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function insert($data) {
            try {
                $query = $this->db->connect()->prepare('INSERT INTO events (title, description, place, keywords, images, email)
                                                        VALUES (:title, :description, :place, :keywords, :images, :email)');

                $query->execute([
                    'title'       => $data['title'],
                    'description' => $data['description'],
                    'place'       => $data['place'],
                    'keywords'    => $data['keywords'],
                    'images'      => $data['images'],
                    'email'       => $data['email']
                 ]);
                
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
        
        /**
         * Obtiene todos los eventos
         *
         * @return array Contiene los eventos
         */
        public function getall() {
            try {
                $items = [];
                $query = $this->db->connect()->query('SELECT * FROM events ORDER BY datetime DESC');
                require_once "comment.php";
                require_once "user.php";
                $mu = new User();
                $cu = new Comment();
                while ($row = $query->fetch()) {
                    $noofcoments = $cu->countcomments($row['id']);
                    $user = $mu->getuser($row['email'], false);
                    $item = $row;
                    $item['noofcomments'] = $noofcoments;
                    $item['user'] = $user;

                    array_push($items, $item);
                }

                return $items;
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Actualiza las imágenes de un evento
         *
         * @param [array] $data Con el identificador del evento y los datos de las imágenes
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function updateimages($data) {
            try {
                $query = $this->db->connect()->prepare("UPDATE events SET images=:images WHERE id=:id");
                $query->execute(['images' => $data['images'], 'id' => $data['id']]);
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
        
        /**
        * Obtiene los datos de un evento
        *
        * @param [int] $id Identificador del evento
        * @return array Con los datos del evento
        */
        public function getevent($id) {
            try {
                $item = [];
                require_once "user.php";
                $mu = new User();
                $query = $this->db->connect()->prepare('SELECT * FROM events WHERE id=:id');
                $query->execute(['id' => $id]);
                while ($row = $query->fetch()) {                    
                    $item = $row;
                    $item['user'] = $mu->getuser($row['email'], false);
                    $item['images'] = unserialize($row['images']);
                }

                return $item;
            } catch (PDOException $e) {
                return null;
            }
        }

        /**
         * Obtiene los eventos de un usuario
         *
         * @param [string] $email Email del usuario a buscar
         * @return array Con los datos de los eventos
         */
        public function geteventsbyuser($email) {
            try {
                $query = $this->db->connect()->prepare('SELECT * FROM events WHERE email=:email ORDER BY datetime DESC');
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
         * Actualiza la información de un evento
         *
         * @param [array] $data Datos del evento
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function update($data) {
            try {
                if ($data['images'] != "") {
                    $query = $this->db->connect()->prepare("UPDATE events
                                                            SET title=:title, description=:description, place=:place, keywords=:keywords, images=:images, datetime=CURRENT_TIMESTAMP
                                                            WHERE id=:id");
                    $query->execute([
                        'id'          => $data['id'],
                        'title'       => $data['title'],
                        'description' => $data['description'],
                        'place'       => $data['place'],
                        'keywords'    => $data['keywords'],
                        'images'      => $data['images']
                    ]);
                } else {
                    $query = $this->db->connect()->prepare("UPDATE events
                                                            SET title=:title, description=:description, place=:place, keywords=:keywords, datetime=CURRENT_TIMESTAMP
                                                            WHERE id=:id");
                    $query->execute([
                        'id'          => $data['id'],
                        'title'       => $data['title'],
                        'description' => $data['description'],
                        'place'       => $data['place'],
                        'keywords'    => $data['keywords']
                    ]);
                }

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Elimina un evento.
         *
         * @param [string] $id Identificador del evento.
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function delete($id) {
            try {
                $query = $this->db->connect()->prepare("DELETE FROM events WHERE id=:id");
                $query->execute(['id' => $id]);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Elimina todos los eventos de un usuario
         *
         * @param [string] $email Email del usuario
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function deleteallbyemail($email) {
            try {
                $query = $this->db->connect()->prepare("DELETE FROM events WHERE email=:email");
                $query->execute(['email' => $email]);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Elimina todos los likes y dislikes de un usuario.
         *
         * @param [string] $email Email del usuario
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function deletelikesdislikesbyemail($email) {
            try {
                $query = $this->db->connect()->prepare("DELETE FROM u_interact_e WHERE email=:email");
                $query->execute(['email' => $email]);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Elimina todos los likes y dislikes de un evento.
         *
         * @param [string] $event Identificador del evento
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function deletelikesdislikes($event) {
            try {
                $query = $this->db->connect()->prepare("DELETE FROM u_interact_e WHERE event=:event");
                $query->execute(['event' => $event]);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Obtiene el identificador de un evento a partir de su título
         *
         * @param [string] $title Título del evento
         * @return array Con los datos obtenidos
         */
        public function geteventid($title) {
            try {
                $item = [];
                $query = $this->db->connect()->prepare("SELECT id FROM events WHERE title=:title");
                $query->execute(['title' => $title]);
                while ($row = $query->fetch()) {                    
                    $item = [
                        'id' => $row['id']
                    ];
                }

                return $item;
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Obtiene eventos aleatorios
         *
         * @param [int] $qtty Cantidad de eventos a devolver
         * @return array Con los eventos obtenidos
         */
        public function getrandomevents($qtty) {
            try {
                $items = [];
                $query = $this->db->connect()->prepare("SELECT * FROM events ORDER BY RAND() LIMIT :qtty");
                $query->execute(['qtty' => $qtty]);
                while ($row = $query->fetch()) {                    
                    array_push($items, $row);
                }

                return $items;
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Devuelve los últimos eventos agregados en la web
         *
         * @param [int] $qtty Cantidad de eventos a recuperar
         * @return array Con cada uno de los eventos obtenidos
         */
        public function getlastevents($qtty) {
            try {
                $items = [];
                $query = $this->db->connect()->prepare("SELECT * FROM events ORDER BY datetime DESC LIMIT :qtty");
                $query->execute(['qtty' => $qtty]);
                while ($row = $query->fetch()) {                    
                    array_push($items, $row);
                }

                return $items;
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Devuelve los likes que tiene un evento
         *
         * @param [int] $id Identificador del evento
         * @return int Número de likes
         */
        public function getlikes($id) {
            $evt = $this->getevent($id);
            return $evt['likes'];
        }

        /**
         * Devuelve los dislikes que tiene un evento
         *
         * @param [int] $id Identificador del evento
         * @return int Número de dislikes
         */
        public function getdislikes($id) {
            $evt = $this->getevent($id);
            return $evt['dislikes'];
        }

        /**
         * Devuelve los likes que ha dado un usuario
         *
         * @param [string] $email Email del usuario
         * @return array Con los datos obtenidos
         */
        public function getuserlikes($email) {
            try {
                $query = $this->db->connect()->prepare("SELECT * from u_interact_e WHERE email=:email AND interaction='like'");
                $query->execute(['email' => $email]);
                $items = [];
                while ($row = $query->fetch()) {                    
                    array_push($items, $row['event']);
                }

                return $items;
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Devuelve los dislikes que ha dado un usuario
         *
         * @param [string] $email Email del usuario
         * @return array Con los datos obtenidos
         */
        public function getuserdislikes($email) {
            try {
                $query = $this->db->connect()->prepare("SELECT * from u_interact_e WHERE email=:email AND interaction='dislike'");
                $query->execute(['email' => $email]);
                $items = [];
                while ($row = $query->fetch()) {                    
                    array_push($items, $row['event']);
                }

                return $items;
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Da like a un evento
         *
         * @param [int] $id Identificador del evento
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function likeevent($id) {
            try {
                $evt = $this->getevent($id);
                $query = $this->db->connect()->prepare("UPDATE events SET likes=:likes WHERE id=:id");
                $likes = $evt['likes'] + 1;
                $query->execute(['likes' => $likes, 'id' => $id]);

                $params = [
                    'email' => "",
                    'event' => $id,
                    'interaction' => "like"];

                if (session_status() == PHP_SESSION_NONE) session_start();
                if (isset($_SESSION['user'])) {
                    $params['email'] = $_SESSION['user']['email'];
                } else {
                    $params['email'] = "anonymous@anonymous";
                }

                $query = $this->db->connect()->prepare("INSERT INTO u_interact_e (email, event, interaction) VALUES (:email, :event, :interaction)");
                $query->execute($params);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Da dislike a un evento
         *
         * @param [int] $id Identificador del evento
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function dislikeevent($id) {
            try {    
                $evt = $this->getevent($id);
                $query = $this->db->connect()->prepare("UPDATE events SET dislikes=:dislikes WHERE id=:id");
                $dislikes = $evt['dislikes'] + 1;
                $query->execute(['dislikes' => $dislikes, 'id' => $id]);

                $params = [
                    'email' => "",
                    'event' => $id,
                    'interaction' => "dislike"];

                if (session_status() == PHP_SESSION_NONE) session_start();
                if (isset($_SESSION['user'])) {
                    $params['email'] = $_SESSION['user']['email'];
                } else {
                    $params['email'] = "anonymous@anonymous";
                }

                $query = $this->db->connect()->prepare("INSERT INTO u_interact_e (email, event, interaction) VALUES (:email, :event, :interaction)");
                $query->execute($params);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Búsqueda avanzada de eventos en el sistema
         *
         * @param [array] $data Array con los criterios de búsqueda
         * @return array Con los eventos encontrados que cumplen los criteros
         */
        public function search($data) {
            try {
                // Query inicial
                $squery = "SELECT * FROM events WHERE 1=1";
                // Parámetros para preparar la query
                $params = [];

                // Si se busca por título
                if ($data['content'] != null) {
                    // Wildcard de búsqueda
                    $title = '%' . $data['content'] . '%';
                    // Agrego filtro a la query
                    $squery .= " AND title LIKE :title";
                    // Agrego parámetro de búsqueda
                    $params['title'] = $title;
                }

                // Si se busca por lugar
                if ($data['place'] != null) {
                    $place = '%' . $data['place'] . '%';
                    $squery .= " AND place LIKE :place";
                    $params['place'] = $place;
                }

                // Si busco por algun tipo de estado
                if (sizeof($data['status']) > 0) {
                    $squery .= " AND";

                    // Recorro vector de status
                    for ($i = 0; $i < sizeof($data['status']); $i++) {
                        $name = 'status' . $data['status'][$i];
                        // Agrego status a la query
                        $squery .= " status=:" . $name;
                        // Agrego parámetro
                        $params[$name] = $data['status'][$i];
                        // Agrego operador OR mientras no sea la última búsqueda
                        if (($i + 1) != sizeof($data['status'])) {
                            $squery .= " OR";
                        }
                    }
                }

                // Filtrado final, orden de los datos
                if ($data['searchby'] != null) {
                    if ($data['searchby'] == 'byrecent') {
                        $squery .= " ORDER BY datetime DESC";
                    } else if ($data['searchby'] == 'bylikes') {
                        $squery .= " ORDER BY likes DESC";
                    } else if ($data['searchby'] == 'bynetlikes') {
                        $squery .= " ORDER BY (likes - dislikes) DESC";
                    }
                }
                
                $query = $this->db->connect()->prepare($squery);
                $query->execute($params);

                $items = [];
                while ($row = $query->fetch()) {                    
                    array_push($items, $row);
                }

                return $items;
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Cambia el estado de un evento
         *
         * @param [array] $data Con el identificador del evento y el estado a cambiar.
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function changestatus($data) {
            try {
                $query = $this->db->connect()->prepare("UPDATE events SET status=:status WHERE id=:id");
                $query->execute(['status' => $data['status'], 'id' => $data['id']]);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Obtiene el estado de un evento
         *
         * @param [int] $id Identificador del evento
         * @return string Estado del evento
         */
        public function getstatus($id) {
            try {
                $query = $this->db->connect()->prepare("SELECT status FROM events WHERE id=:id");
                $query->execute(['id' => $id]);

                return $query->fetch()[0];
            } catch (PDOException $e) {
                return null;
            }
        }

        /**
         * Obtiene estadísticas de los eventos
         * Número de eventos
         * Número de eventos procesados
         * Número de eventos resueltos
         * Número de eventos irresolubles
         * Número de eventos comprobándose
         * Número de eventos comprobados
         * Número de likes totales
         * Número de dislikes totales
         *
         * @return array Con los datos estadísticos
         */
        public function getstats() {
            try {
                $query = $this->db->connect()->query("SELECT COUNT(*) FROM events");
                $noofevents = $query->fetch()[0];

                $query = $this->db->connect()->query("SELECT COUNT(*) FROM events WHERE status='processed'");
                $noofprocessed = $query->fetch()[0];
                $query = $this->db->connect()->query("SELECT COUNT(*) FROM events WHERE status='resolved'");
                $noofresolved = $query->fetch()[0];
                $query = $this->db->connect()->query("SELECT COUNT(*) FROM events WHERE status='irresolvable'");
                $noofirresolvable = $query->fetch()[0];
                $query = $this->db->connect()->query("SELECT COUNT(*) FROM events WHERE status='checked'");
                $noofchecked = $query->fetch()[0];
                $query = $this->db->connect()->query("SELECT COUNT(*) FROM events WHERE status='checking'");
                $noofchecking = $query->fetch()[0];

                $query = $this->db->connect()->query("SELECT COUNT(*) FROM u_interact_e WHERE interaction='like'");
                $nooflikes = $query->fetch()[0];
                $query = $this->db->connect()->query("SELECT COUNT(*) FROM u_interact_e WHERE interaction='dislike'");
                $noofdislikes = $query->fetch()[0];

                require_once "comment.php";
                $c = new Comment();
                $noofcomments = $c->getcountevents();

                return [
                    'noofevents' => $noofevents,
                    'noofcomments' => $noofcomments,

                    'noofprocessed' => $noofprocessed,
                    'noofresolved' => $noofresolved,
                    'noofirresolvable' => $noofirresolvable,
                    'noofchecked' => $noofchecked,
                    'noofchecking' => $noofchecking,

                    'nooflikes' => $nooflikes,
                    'noofdislikes' => $noofdislikes,
                ];
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Obtiene el top de usuarios que más eventos han agregado en el sistema
         *
         * @param [int] $qtty Cantidad de usuarios a devolver
         * @return array Con los usuarios y el número de eventos que tienen
         */
        public function gettop($qtty) {
            try {
                require_once "user.php";
                $u = new User();
                $top = [];
                $query = $this->db->connect()->prepare("SELECT email, COUNT(email) AS posts FROM events GROUP BY email ORDER BY posts DESC LIMIT :qtty");
                $query->execute(['qtty' => $qtty]);

                while ($row = $query->fetch()) {
                    array_push($top, ['data' => $row, 'user' => $u->getuser($row['email'], false)]);
                }

                return $top;
            } catch (PDOException $e) {
                return [];
            }
        }
    }

?>