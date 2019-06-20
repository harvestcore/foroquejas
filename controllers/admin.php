<?php

    /**
     * Controlador Admin, controla todo lo relacionado con lo que puede hacer o no el administrador.
     */
    class Admin extends Controller {

        /**
         * Constructor por defecto, llama al constructor de la superclase Controller 
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
            $this->view->render('admin/' . $whattorender, $argv);
        }

        /**
         * Comprueba si el usuario que quiere acceder a las funciones de administración es administrador
         *
         * @return boolean
         */
        function isadmin() {
            return $this->checksession() && $_SESSION['user']['rol'] == 'admin';
        }

        /**
         * Maneja el index de la vista admin
         *
         * @return void
         */
        function index() {
            if ($this->isadmin()) {
                $this->render('index', []);
            } else {
                $this->header('main');
            }
        }

        /**
         * Comprueba que los parámetros son correctos
         *
         * @param [int] $p Tipo de comprobación.
         * @return void
         */
        private function paramsok($p) {
            switch($p) {
                // Nombre, apellidos, email, pass, passconfirm, direccion, telefono
                case 1: return isset($_POST['user-firstname']) && $_POST['user-firstname'] != "" && isset($_POST['user-rol']) && isset($_POST['user-surname']) && $_POST['user-surname'] != "" && isset($_POST['user-email']) && $_POST['user-email'] != "" && isset($_POST['user-password']) && $_POST['user-password'] != "" && isset($_POST['user-password-confirm']) && $_POST['user-password-confirm'] != "" && isset($_POST['user-address']) && $_POST['user-address'] != "" && isset($_POST['user-telnumber']) && $_POST['user-telnumber'] != ""; 
                
                // password
                case 2: return $_POST['user-password'] == $_POST['user-password-confirm'];
                
                // update
                case 3: return isset($_POST['profile-firstname']) && $_POST['profile-firstname'] != "" && isset($_POST['profile-surname']) && $_POST['profile-surname'] != "" && isset($_POST['profile-telnumber']) && $_POST['profile-telnumber'] != "" && isset($_POST['profile-address']) && $_POST['profile-address'] != "" && isset($_POST['profile-rol']) && $_POST['profile-rol'] != "" && isset($_POST['profile-status']) && $_POST['profile-status'] != ""; 
            }
        }

        /**
         * Actualiza la información de un usuario
         *
         * @return void
         */
        function updateuser() {
            // Si el user actual es admin
            if ($this->isadmin()) {
                if ($this->paramsok(3)) {
                    if (session_status() == PHP_SESSION_NONE) session_start();
                    
                    // Datos a actualizar
                    $data = [
                        'email'     => $_POST['profile-email'],
                        'firstname' => $_POST['profile-firstname'],
                        'surname'   => $_POST['profile-surname'],
                        'password'  => $_POST['profile-password'],
                        'rol'       => $_POST['profile-rol'],
                        'status'    => $_POST['profile-status'],
                        'image'     => null,
                        'address'   => $_POST['profile-address'],
                        'telnumber' => $_POST['profile-telnumber']
                    ];

                    // Si se sube una nueva imagen
                    if (isset($_FILES['profile-image'])) {
                        if ($_FILES['profile-image']['size'] != 0) {
                            // Path e información de la imagen
                            $tmppath = $_FILES['profile-image']['tmp_name'];
                            $info = pathinfo($_FILES['profile-image']['name']);
                            $newpath = 'public/uploads/profilepics/' . $data['email'] . '.' . strtolower($info['extension']);
                            
                            // Subida de la imagen
                            if (move_uploaded_file($tmppath, $newpath))
                                $data['image'] = $newpath;
                        }
                    }

                    // Si se actualizan los datos se redirige al listado de usuarios y se loguea
                    if ($this->model->update($data)) {
                        $this->header('admin/userlist');
                        $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' .  $_SESSION['user']['email'] . '] -> Usuario modificado (' . $data['email'] . ')');
                    }
                } else {
                    $this->header('admin/userlist');
                }
            } else {
                $this->header('main');
            }
        }

        /**
         * Maneja la vista que contiene el formulario para agregar un usuario
         *
         * @return void
         */
        function useradd() {
            if ($this->isadmin()) {
                $this->render('user/add', []);
            } else {
                $this->header('main');
            }
        }

        /**
         * Agrega un usuario al sistema
         *
         * @return void
         */
        function adduser() {
            // Si el user actual es admin
            if ($this->isadmin()) {
                $msg = "";
                if ($this->paramsok(1)) {
                    if ($this->paramsok(2)) {
                        // Datos a insertar
                        $user = [
                            'email'     => $_POST['user-email'],
                            'firstname' => $_POST['user-firstname'],
                            'surname'   => $_POST['user-surname'],
                            'password'  => $_POST['user-password'],
                            'rol'       => $_POST['user-rol'],
                            'image'     => null,
                            'address'   => $_POST['user-address'],
                            'telnumber' => $_POST['user-telnumber']
                        ];
    
                        // Si se ha subido una imagen
                        if (isset($_FILES['user-image']))
                            if ($_FILES['user-image']['size'] != 0) {
                                $tmppath = $_FILES['user-image']['tmp_name'];
                                $info = pathinfo($_FILES['user-image']['name']);
                                $newpath = 'public/uploads/profilepics/' . $user['email'] . '.' . strtolower($info['extension']);
                                
                                if (move_uploaded_file($tmppath, $newpath))
                                    $user['image'] = $newpath;
                            }
    
                        // Insertado correcto
                        if ($this->model->insert($user)) {
                            $msg = "Usuario agregado correctamente.";
                            $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' .  $_SESSION['user']['email'] . '] -> Usuario creado (' . $user['email'] . ')');
                        } else {
                            $msg = "Error al insertar el usuario. Inténtelo de nuevo.";
                        }
                        
                        // Renderizado de la plantilla + mensaje
                        $this->render('user/add', ['message' => $msg, 'data' => $user]);
                    }
                }
            } else {
                $this->header('main');
            }
        }

        /**
         * Maneja la vista que contiene un listado con todos los usuarios
         *
         * @return void
         */
        function userlist() {
            if ($this->isadmin()) {
                $uq = $this->model->getall();
                $users = [];
                foreach ($uq as $u) {
                    array_push($users, [
                        'id'        => $u->id,
                        'firstname' => $u->firstname,
                        'surname'   => $u->surname,
                        'email'     => $u->email,
                        'image'     => $u->image,
                        'rol'       => $u->rol,
                        'status'    => $u->status
                    ]);
                }
                $this->render('user/list', ['users' => $users]);
            } else {
                $this->header('main');
            }
        }

        /**
         * Maneja la plantilla que contiene la información de un usuario
         *
         * @param [int] $argv Identificador del usuario.
         * @return void
         */
        function user($argv = null) {
            if ($this->isadmin()) {
                $user = $this->model->getuserbyid($argv[0]);
                $data = [
                    'firstname' => $user->firstname,
                    'surname'   => $user->surname,
                    'email'     => $user->email,
                    'address'   => $user->address,
                    'telnumber' => $user->telnumber,
                    'rol'       => $user->rol,
                    'status'    => $user->status
                ];

                $this->render('user/edit', ['data' => $data]);
            } else {
                $this->header('main');
            }
        }

        /**
         * Maneja la plantilla que contiene la información de un usuario para borrarlo
         *
         * @param [int] $argv Identificador del usuario.
         * @return void
         */
        function delete($argv = null) {
            if ($this->isadmin()) {
                $user = $this->model->getuserbyid($argv[0]);
                $data = [
                    'firstname' => $user->firstname,
                    'surname'   => $user->surname,
                    'email'     => $user->email,
                    'address'   => $user->address,
                    'telnumber' => $user->telnumber,
                    'rol'       => $user->rol,
                    'status'    => $user->status
                ];

                $this->render('user/delete', ['data' => $data]);
            } else {
                $this->header('main');
            }
        }

        /**
         * Elimina un usuario del sistema
         *
         * @return void
         */
        function deleteuser() {
            if ($this->isadmin()) {
                if (isset($_POST['user-email'])) {
                    echo $_POST['user-email'];
                    if ($this->model->delete($_POST['user-email'])) {
                        echo "2";
                        $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' .  $_SESSION['user']['email'] . '] -> Usuario eliminado (' . $_POST['user-email'] . ')');
                        $this->header('admin');
                    } else {
                        $user = $this->model->getuser($_POST['user-email'], false);
                        $this->delete([$user['id']]);
                    }
                } else {
                    $this->header('admin/userlist');
                }
            } else {
                $this->header('main');
            }
        }

        /**
         * Maneja la vista con los datos del log
         *
         * @return void
         */
        function loglist() {
            if ($this->isadmin()) {
                $log = $this->logger->getlog();
                $this->render('log/index', ['log' => $log]);
            } else {
                $this->header('main');
            }
        }

        /**
         * Obtiene todos los datos de la BD y los devuelve en un archivo .sql
         *
         * @return void
         */
        function backupdb() {
            if ($this->isadmin()) {
                require_once 'models/admin.php';
                $a = new AdminDB();
                $output = $a->backup();
                
                header_remove();
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="foroquejas_backup.sql"');
                echo($output);
            } else {
                $this->header('main');
            }
        }

        /**
         * Maneja la vista de confirmación de borrado de datos de la BD
         *
         * @return void
         */
        function drop() {
            if ($this->isadmin()) {
                $this->render('delete', []);
            } else {
                $this->header('main');
            }
        }

        /**
         * Borra todos los datos de la BD
         *
         * @return void
         */
        function dropdb() {
            if ($this->isadmin()) {
                require_once 'models/admin.php';
                $a = new AdminDB();
                if ($a->drop()) {
                    $this->header('login/logout');
                }
            } else {
                $this->header('main');
            }
        }

        /**
         * Carga datos en la BD a partir de un archivo
         *
         * @return void
         */
        function uploaddb() {
            if ($this->isadmin()) {
                if (isset($_FILES['db-file']) && $_FILES['db-file']['size'] > 0) {
                    require_once 'models/admin.php';
                    $a = new AdminDB();
                    if ($a->upload($_FILES['db-file']['tmp_name'])) {
                        $this->header('login/logout');
                    }
                } else {
                    $this->header('admin');
                }
            } else {
                $this->header('main');
            }
        }
    }

?>