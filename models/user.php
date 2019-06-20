<?php

    /**
     * Clase User. Un objeto User representa a un usuario en el sistema.
     */
    class User extends Model {

        public $id;
        public $email;
        public $firstname;
        public $surname;
        public $password;
        public $image;
        public $rol;
        public $address;
        public $telnumber;
        public $status;

        /**
         * Constructor por defecto
         */
        public function __construct() {
            parent::__construct();
        }

        /**
         * Inserta un usuario en la BD
         *
         * @param [array] $data Datos del usuario a agregar.
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function insert($data) {
            try {
                $img = null;
                if($data['image'] == null) {
                    $img = 'public/img/default-user.png';
                } else {
                    $img = $data['image'];
                }

                $pass = md5($data['password']);

                $query = $this->db->connect()->prepare('INSERT INTO users (email, firstname, surname, password, rol, image, address, telnumber)
                                                        VALUES (:email, :firstname, :surname, :password, :rol, :image, :address, :telnumber)');
                
                $query->execute([
                    'email'     => $data['email'],
                    'firstname' => $data['firstname'],
                    'surname'   => $data['surname'],
                    'password'  => $pass,
                    'rol'       => $data['rol'],
                    'image'     => $img,
                    'address'   => $data['address'],
                    'telnumber' => $data['telnumber']
                 ]);
                
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
        
        /**
         * Obtiene todos los usuarios
         *
         * @return array Contiene los usuarios
         */
        public function getall() {
            try {
                $items = [];
                $query = $this->db->connect()->query('SELECT * FROM users');

                while ($row = $query->fetch()) {
                    $user = new User();
                    $user->id           = $row['id'];
                    $user->email        = $row['email'];
                    $user->firstname    = $row['firstname'];
                    $user->surname      = $row['surname'];
                    $user->image        = $row['image'];
                    $user->rol          = $row['rol'];
                    $user->address      = $row['address'];
                    $user->telnumber    = $row['telnumber'];
                    $user->status       = $row['status'];

                    array_push($items, $user);
                }

                return $items;
            } catch (PDOException $e) {
                return [];
            }
        }

        /**
         * Obtiene los datos de un usuario
         *
         * @param [string] $email Email del usuario del que queremos obtener datos
         * @param [boolean] $pass True si queremos recuperar también su contraseña, false en caso contrario
         * @return array Con los datos del usuario
         */
        public function getuser($email, $pass) {
            try {
                $user = [];
                $query = $this->db->connect()->prepare('SELECT * FROM users WHERE email=:email');
                $query->execute(['email' => $email]);
                while ($row = $query->fetch()) {
                    $user = [
                        'id'           => $row['id'],
                        'email'        => $row['email'],
                        'firstname'    => $row['firstname'],
                        'surname'      => $row['surname'],
                        'password'     => "",
                        'image'        => $row['image'],
                        'rol'          => $row['rol'],
                        'address'      => $row['address'],
                        'telnumber'    => $row['telnumber'],
                        'status'       => $row['status']
                    ];

                    if ($pass)
                        $user['password'] = $row['password'];
                }

                return $user;
            } catch (PDOException $e) {
                return null;
            }
        }

        /**
         * Obtiene los datos de un usuario a partir de su identificador
         *
         * @param [int] $id Identificador del usuario
         * @return array Con los datos del usuario
         */
        public function getuserbyid($id) {
            try {
                $user = new User();
                $query = $this->db->connect()->prepare('SELECT * FROM users WHERE id=:id');
                $query->execute(['id' => $id]);
                while ($row = $query->fetch()) {
                    $user->email        = $row['email'];
                    $user->firstname    = $row['firstname'];
                    $user->surname      = $row['surname'];
                    $user->image        = $row['image'];
                    $user->rol          = $row['rol'];
                    $user->address      = $row['address'];
                    $user->telnumber    = $row['telnumber'];
                    $user->status       = $row['status'];
                }

                return $user;
            } catch (PDOException $e) {
                return null;
            }
        }

        /**
         * Valida los datos del usuario
         *
         * @param [array] $user Datos del usuario a validar
         * @return boolean True si el usuario es válido, false en caso contrario.
         */
        public function validateuser($user) {
            $us = new User();
            $us = $this->getuser($user['email'], true);
            if ($us != null) {
                if ($us['status'] == 'inactive') return false;
                
                if (md5($user['password']) == $us['password']) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        /**
         * Comprueba si el usuario es administrador
         *
         * @param [string] $email Email del usuario a comprobar
         * @return boolean True si es administrador, false en caso contrario.
         */
        public function isadmin($email) {
            $us = new User();
            $us = $this->getuser($email, false);
            if ($us != null) {
                if ($us->rol == 'admin') return true;
                else return false;
            } else {
                return false;
            }
        }

        /**
         * Actualiza la información completa de un usuario
         *
         * @param [array] $data Datos del usuario
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function update($data) {
            try {
                $pass = md5($data['password']);
                if ($data['image'] != null) {
                    $query = $this->db->connect()->prepare("UPDATE users
                                                            SET firstname=:firstname, surname=:surname, password=:password, image=:image, rol=:rol, address=:address, telnumber=:telnumber, status=:status
                                                            WHERE email=:email");
                    $query->execute([
                        'email'     => $data['email'],
                        'firstname' => $data['firstname'],
                        'surname'   => $data['surname'],
                        'password'  => $pass,
                        'image'     => $data['image'],
                        'rol'       => $data['rol'],
                        'address'   => $data['address'],
                        'telnumber' => $data['telnumber'],
                        'status'    => $data['status']
                    ]);
                } else {
                    $query = $this->db->connect()->prepare("UPDATE users
                                                            SET firstname=:firstname, surname=:surname, password=:password, rol=:rol, address=:address, telnumber=:telnumber, status=:status
                                                            WHERE email=:email");
                    $query->execute([
                        'email'     => $data['email'],
                        'firstname' => $data['firstname'],
                        'surname'   => $data['surname'],
                        'password'  => $pass,
                        'rol'       => $data['rol'],
                        'address'   => $data['address'],
                        'telnumber' => $data['telnumber'],
                        'status'    => $data['status']
                    ]);
                }

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Actualiza la información parcial de un usuario (todo menos rol y estado)
         *
         * @param [array] $data Datos del usuario
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function simpleupdate($data) {
            try {
                $pass = md5($data['password']);
                if ($data['image'] != null) {
                    echo "a";
                    $query = $this->db->connect()->prepare("UPDATE users
                                                            SET firstname=:firstname, surname=:surname, password=:password, image=:image, address=:address, telnumber=:telnumber
                                                            WHERE email=:email");
                    $query->execute([
                        'email'     => $data['email'],
                        'firstname' => $data['firstname'],
                        'surname'   => $data['surname'],
                        'password'  => $pass,
                        'image'     => $data['image'],
                        'address'   => $data['address'],
                        'telnumber' => $data['telnumber']
                    ]);
                } else {
                    echo "b";
                    $query = $this->db->connect()->prepare("UPDATE users
                                                            SET firstname=:firstname, surname=:surname, password=:password, address=:address, telnumber=:telnumber
                                                            WHERE email=:email");
                    $query->execute([
                        'email'     => $data['email'],
                        'firstname' => $data['firstname'],
                        'surname'   => $data['surname'],
                        'password'  => $pass,
                        'address'   => $data['address'],
                        'telnumber' => $data['telnumber']
                    ]);
                }

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Elimina a un usuario. Borra también todos sus datos asociados (likes, dislikes, comentarios y eventos).
         *
         * @param [string] $email Email del usuario a eliminar
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function delete($email) {
            try {
                require_once 'models/event.php';
                require_once 'models/comment.php';
                $e = new Event();
                $c = new Comment();

                // Borrar likes y dislikes
                $e->deletelikesdislikesbyemail($email);

                // Borrar comentarios
                $c->deleteallbyemail($email);

                // Borrar eventos
                $e->deleteallbyemail($email);

                // Borrar usuario
                $query = $this->db->connect()->prepare("DELETE FROM users WHERE email=:email");
                $query->execute(['email' => $email]);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Obtiene estadísticas de los usuarios
         * Número de usuarios totales
         * Numero de usuarios administradores
         * Número de usuarios activos
         *
         * @return array Con los datos estadísticos
         */
        public function getstats() {
            try {
                $query = $this->db->connect()->query("SELECT COUNT(*) FROM users");
                $noofusers = $query->fetch()[0];

                $query = $this->db->connect()->query("SELECT COUNT(*) FROM users WHERE rol='admin'");
                $noofadmins = $query->fetch()[0];

                $query = $this->db->connect()->query("SELECT COUNT(*) FROM users WHERE status='active'");
                $noofactives = $query->fetch()[0];


                return [
                    'noofusers' => $noofusers - $noofadmins,
                    'noofadmins' => $noofadmins,
                    'noofactives' => $noofactives,
                    'noofinactives' => $noofusers - $noofactives
                ];
            } catch (PDOException $e) {
                return [];
            }
        }

    }

?>