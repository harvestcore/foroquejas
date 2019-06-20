<?php
    /**
     * Controlador Events, controla todo lo relacionado con las quejas.
     */
    class Events extends Controller {

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
            $this->view->render('events/' . $whattorender, $argv);
        }

        /**
         * Maneja el index de los eventos, obtiene comentarios y quejas recientes. Así como top comentarios y top quejicas
         *
         * @return void
         */
        function index() {
            require_once "models/comment.php";
            $c = new Comment();
            
            $comments = $c->getlastcomments(15);
            $topcomments = $c->gettop(10);
            $events = $this->model->getlastevents(15);
            $topevents = $this->model->gettop(10);

            $this->render('index', ['events' => $events, 'comments' => $comments, 'topcomments' => $topcomments, 'topevents' => $topevents]);
        }

        /**
         * Maneja la vista que contiene el formulario para agregar una queja
         *
         * @return void
         */
        function add() {
            if ($this->checksession())
                $this->render('add', []);
            else
                $this->header('login');
        }

        /**
         * Comprueba que los parámetros son correctos
         *
         * @param [int] $p Tipo de comprobación.
         * @return void
         */
        private function paramsok($p) {
            switch($p) {
                // Título, lugar y descripción
                case 1: return isset($_POST['event-title']) && $_POST['event-title'] != "" && isset($_POST['event-place']) && $_POST['event-place'] != "" && isset($_POST['event-description']) && $_POST['event-description'] != ""; 
                
                // palabras clave
                case 2: break;

                // Comentarios
                case 3: return isset($_POST['comment-content']) && $_POST['comment-content'] != "" && isset($_POST['comment-event']) && $_POST['comment-event'] != "" && isset($_POST['comment-email']) && $_POST['comment-email'] != "";

                // búsqueda
                case 4: return isset($_POST['search-content']) && $_POST['search-content'] != "";
            
                // update de evento
                case 5: return isset($_POST['edit-title']) && $_POST['edit-title'] != "" && isset($_POST['edit-description']) && $_POST['edit-description'] != "" && isset($_POST['edit-place']) && $_POST['edit-place'] != "" && isset($_POST['edit-id']) && $_POST['edit-id'] != "";
            }
        }

        /**
         * Agrega una queja al sistema
         *
         * @return void
         */
        function addevent() {
            if (session_status() == PHP_SESSION_NONE) session_start();

            if ($this->paramsok(1)) {
                $data = [
                    'title'     => $_POST['event-title'],
                    'description' => $_POST['event-description'],
                    'place'   => $_POST['event-place'],
                    'keywords'  => "",
                    'images'     => "-",
                    'email'       => ""
                ];

                if (isset($_SESSION['user']))
                    $data['email'] = $_SESSION['user']['email'];
                else
                    $data['email'] = 'anonymous@anonymous';

                $this->model->insert($data);

                $evtid = $this->model->geteventid($_POST['event-title']);

                $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' . $_SESSION['user']['email'] . '] -> Queja creada #' . $evtid['id']);
                
                // subida de mágenes
                $paths = [];
                if (isset($_FILES['event-images'])) {
                    $nooffiles = sizeof($_FILES['event-images']['name']);
                    
                    if ($nooffiles != 0) {
                        for($i = 0; $i < $nooffiles; $i++) {
                            $tmppath = $_FILES['event-images']['tmp_name'][$i];
                            $info = pathinfo($_FILES['event-images']['name'][$i]);
                            
                            if ($tmppath != "") {
                                $newpath = 'public/uploads/' . $evtid['id'] . '_' . $i . '.' . strtolower($info['extension']);
                                
                                if (move_uploaded_file($tmppath, $newpath)) {
                                    array_push($paths, $newpath);
                                }
                            }
                        }
                        $this->model->updateimages(['images' => serialize($paths), 'id' => $evtid['id']]);
                    }
                }
            }
            $this->header('events/get/' . $evtid['id']);
        }
        
        /**
         * Maneja la plantilla que contiene la información de una queja
         *
         * @param [int] $argv Identificador de la queja.
         * @return void
         */
        function get($argv = null) {
            if ($argv != null) {
                $event = $this->model->getevent($argv[0]);
                require_once "models/comment.php";
                require_once "models/user.php";
                $us = new User();
                $cm = new Comment();
                $comments = $cm->getall($argv[0]);
                $commentswithuser = [];

                if (sizeof($comments) > 0) {
                    foreach ($comments as $c) {
                        $user = $us->getuser($c['email'], false);
                        
                        $xd = [
                            'id'        => $c['id'],
                            'content'   => $c['content'],
                            'email'     => $c['email'],
                            'datetime'  => $c['datetime'],
                            'user'      => $user
                        ];
                        
                        array_push($commentswithuser, $xd);
                    }
                }
                
                $this->render('event', ['event' => $event, 'comments' => $commentswithuser]);
            }
        }

        /**
         * Maneja la vista con el listado completo de quejas
         *
         * @return void
         */
        function list() {
            require_once "models/comment.php";
            $c = new Comment();

            $topcomments = $c->gettop(10);
            $topevents = $this->model->gettop(10);
            $events = $this->model->getall();

            $this->render('list', ['events' => $events, 'topcomments' => $topcomments, 'topevents' => $topevents]);
        }

        /**
         * Maneja la plantilla que contiene la información de una queja para borrarla
         *
         * @param [int] $argv Identificador de la queja.
         * @return void
         */
        function delete($argv = null) {
            if ($argv != null) {
                $evt = $this->model->getevent($argv[0]);
                if ($_SESSION['user']['email'] == $evt['email']) {
                    $this->render('delete', ['event' => $evt]);
                }
            }
        }

        /**
         * Elimina una queja del sistema
         *
         * @return void
         */
        function deleteevent() {
            if (isset($_POST['event-delete-id'])) {
                // Borrar todos los comentarios de ese evento
                require_once "models/comment.php";
                $c = new Comment();
                $coms = $c->countcommentsbyevent($_POST['event-delete-id']);
                if ($coms != null && $coms > 0)
                    $c->deletebyevent($_POST['event-delete-id']);

                // Borrar todos los likes y dislikes
                $this->model->deletelikesdislikes($_POST['event-delete-id']);
                
                // Borrar evento
                if ($this->model->delete($_POST['event-delete-id'])) {
                    $this->index();
                    $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' . $_SESSION['user']['email'] . '] -> Queja eliminada #' . $_POST['event-delete-id']);
                }
            }
        }

        /**
         * Maneja la plantilla que contiene la información de una queja para editarla
         *
         * @param [int] $argv Identificador de la queja.
         * @return void
         */
        function edit($argv = null) {
            if ($argv != null) {
                $evt = $this->model->getevent($argv[0]);
                if ($_SESSION['user']['email'] == $evt['email']) {
                    $this->render('edit', ['event' => $evt]);
                }
            }
        }

        /**
         * Actualiza la información de una queja
         *
         * @return void
         */
        function editevent() {
            if (session_status() == PHP_SESSION_NONE) session_start();
            
            if ($this->paramsok(5)) {
                $evt = $this->model->getevent($_POST['edit-id']);
                $data = [
                    'id'          => $_POST['edit-id'],
                    'title'       => $_POST['edit-title'],
                    'description' => $_POST['edit-description'],
                    'place'       => $_POST['edit-place'],
                    'keywords'    => $evt['keywords'],
                    'images'      => $evt['images']
                ];

                if (isset($_POST['edit-keywords']))
                    $data['keywords'] = $_POST['edit-keywords'];

                $status = $this->model->update($data);
                $evt = $this->model->getevent($_POST['edit-id']);

                if ($status) {
                    $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' . $_SESSION['user']['email'] . '] -> Queja editada #' . $_POST['edit-id']);
                    $this->render('edit', ['message' => 'Queja editada correctamente.', 'event' => $evt]);
                }
                
                // Subida de imágenes
                // $paths = [];
                // var_dump($_FILES);
                // if (isset($_FILES['event-images'])) {
                //     $nooffiles = sizeof($_FILES['event-images']['name']);
                    
                //     if ($nooffiles != 0) {
                //         for($i = 0; $i < $nooffiles; $i++) {
                //             $tmppath = $_FILES['event-images']['tmp_name'][$i];
                //             $info = pathinfo($_FILES['event-images']['name'][$i]);
                            
                //             if ($tmppath != "") {
                //                 $newpath = 'public/uploads/' . $id['id'] . '_' . $i . '.' . $info['extension'];
                                
                //                 if (copy($tmppath, $newpath)) {
                //                     array_push($paths, $newpath);
                //                 }
                //             }
                //         }
                //         echo serialize($paths);
                //         $this->model->updateimages(['images' => serialize($paths), 'id' => $id]);
                //     }
                // }
            }
        }

        /**
         * Da like a una queja
         *
         * @param [int] $argv Identificador de la queja.
         * @return json Con el status de la operación
         */
        function like($argv = null) {
            if ($argv != null) {
                if (session_status() == PHP_SESSION_NONE) session_start();
                header_remove();
                $status = false;

                // Si el usuario está logueado
                if (isset($_SESSION['user'])) {
                    // obtengo sus likes (deserializados)
                    $likes = $this->model->getuserlikes($_SESSION['user']['email']);
                        // si no ha dado like se lo doy
                        if (!in_array($argv[0], $likes))
                            $status = $this->model->likeevent($argv[0]);
    
                    if ($status) $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' . $_SESSION['user']['email'] . '] -> Like evento #' . $argv[0]);
                
                // Si el user no está logueado
                } else {
                    // si existe una cookie con likes
                    if (isset($_COOKIE['liked'])) {
                        // deserializo los datos
                        $data = unserialize($_COOKIE['liked'], ["allowed_classes" => false]);
                        // si ya he dado like no lo doy, en caso contrario sí
                        if (in_array($argv[0], $data)) {
                            $status = false;
                        } else {
                            array_push($data, $argv[0]);
                            $status = $this->model->likeevent($argv[0]);
                            setcookie('liked', serialize($data), time() + 86400);
                        }
                    // si no existe la cookie la creo y le meto los datos
                    } else {
                        $status = $this->model->likeevent($argv[0]);
                        setcookie('liked', serialize(array($argv[0])), time() + 86400);
                    }
    
                    if ($status) $this->logger->log('[colaborator] [Anonymous] -> Like evento #' . $argv[0]);
                }
    
                // Devuelvo json con status (true/false)
                header('Access-Control-Allow-Origin: *');
                header('Content-type: application/json');
                echo json_encode(array('status' => $status), JSON_FORCE_OBJECT);
            }
        }

        /**
         * Da dislike a una queja. Funciona de la misma manera que la función de like
         *
         * @param [int] $argv Identificador de la queja.
         * @return json Con el status de la operación
         */
        function dislike($argv = null) {
            if ($argv != null) {
                if (session_status() == PHP_SESSION_NONE) session_start();
                header_remove();
                $status = false;
                if (isset($_SESSION['user'])) {
                    $dislikes = $this->model->getuserdislikes($_SESSION['user']['email']);
                    if (!in_array($argv[0], $dislikes))
                        $status = $this->model->dislikeevent($argv[0]);

                    if($status) $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' . $_SESSION['user']['email'] . '] -> Disliked event #' . $argv[0]);
                } else {
                    if (isset($_COOKIE['disliked'])) {
                        $data = unserialize($_COOKIE['disliked'], ["allowed_classes" => false]);
                        if (in_array($argv[0], $data)) {
                            $status = false;
                        } else {
                            array_push($data, $argv[0]);
                            $status = $this->model->dislikeevent($argv[0]);
                            setcookie('disliked', serialize($data), time() + 86400);
                            $this->logger->log('[colaborator] [Anonymous] -> Dislike evento #' . $argv[0]);
                        }
                    } else {
                        $status = $this->model->likeevent($argv[0]);
                        setcookie('disliked', serialize(array($argv[0])), time() + 86400);
                        $this->logger->log('[colaborator] [Anonymous] -> Dislike evento #' . $argv[0]);
                    }
                }

                header('Access-Control-Allow-Origin: *');
                header('Content-type: application/json');
                echo json_encode(array('status' => $status), JSON_FORCE_OBJECT);
            }
        }

        /**
         * Devuelve los likes de un usuario
         *
         * @param [int] $argv Identificador del usuario
         * @return json Con el número de likes
         */
        function getlikes($argv = null) {
            if ($argv != null) {
                $likes = $this->model->getlikes($argv[0]);
                header_remove();
                header('Access-Control-Allow-Origin: *');
                header('Content-type: application/json');
                echo json_encode(array('likes' => $likes), JSON_FORCE_OBJECT);
            }
        }

        /**
         * Devuelve los dislikes de un usuario
         *
         * @param [int] $argv Identificador del usuario
         * @return json Con el número de dislikes
         */
        function getdislikes($argv = null) {
            if ($argv != null) {
                $dislikes = $this->model->getdislikes($argv[0]);
                header_remove();
                header('Access-Control-Allow-Origin: *');
                header('Content-type: application/json');
                echo json_encode(array('dislikes' => $dislikes), JSON_FORCE_OBJECT);
            }
        }

        /**
         * Agrega un comentario a una queja
         *
         * @return void
         */
        function addcomment() {
            if ($this->paramsok(3)) {
                require_once "models/comment.php";
                $cm = new Comment();
                $data = [
                    'email'     => $_POST['comment-email'],
                    'content'   => $_POST['comment-content'],
                    'event'     => $_POST['comment-event']
                ];
                
                $inserted = $cm->insert($data);

                if ($inserted) {
                    require_once "models/user.php";
                    $us = new User();
                    $user = $us->getuser($data['email'], false);

                    $this->logger->log('[' . $user['rol'] . '] [' . $user['email'] . '] -> Agrega comentario en evento #' . $_POST['comment-event']);
                    $this->header('events/get/' . $_POST['comment-event']);
                }
            }
        }

        /**
         * Búsqueda avanzada de quejas
         *
         * @return void
         */
        function search() {
            // Asigno valores previos
            $content = $_POST['search-content'] ?? null;
            $place = $_POST['search-place'] ?? null;
            $searchby = $_POST['search-by'] ?? null;
            $checking = $_POST['search-status-checking'] ?? null;
            $checked = $_POST['search-status-checked'] ?? null;
            $processed = $_POST['search-status-processed'] ?? null;
            $irresolvable = $_POST['search-status-irresolvable'] ?? null;
            $resolved = $_POST['search-status-resolved'] ?? null;

            $status = [];

            // Compruevo valores y los agrego al array para hacer la búsqueda
            if ($checking != null) array_push($status, $checking);
            if ($checked != null) array_push($status, $checked);
            if ($processed != null) array_push($status, $processed);
            if ($irresolvable != null) array_push($status, $irresolvable);
            if ($resolved != null) array_push($status, $resolved);

            $events = $this->model->search([
                'content'       => $content,
                'place'         => $place,
                'searchby'      => $searchby,
                'status'        => $status
            ]);

            if (sizeof($events) > 0 && isset($_POST['search-form']))
                $this->render('search', ['events' => $events]);
            else if (sizeof($events) == 0 && isset($_POST['search-form']))
                $this->render('search', ['message' => 'No se han obtenido resultados']);
            else
                $this->render('search', []);
        }

        /**
         * Elimina un comentario de una queja
         *
         * @param [int] $argv Identificador del comentario
         * @return json Con el estado de la operación
         */
        function deletecomment($argv = null) {
            if ($argv != null) {
                if (session_status() == PHP_SESSION_NONE) session_start();
                $status = false;
                require_once "models/comment.php";
                $cm = new Comment();

                if (isset($_SESSION['user']) && $_SESSION['user']['rol'] == 'admin')
                    $status = $cm->delete($argv[0]);

                if ($status) $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' .  $_SESSION['user']['email'] . '] -> Deleted comment #' . $argv[0]);
                    
                header_remove();
                header('Access-Control-Allow-Origin: *');
                header('Content-type: application/json');
                echo json_encode(array('status' => $status), JSON_FORCE_OBJECT);
            }
        }

        /**
         * Devuelve el estado de una queja
         *
         * @param [int] $argv Identificador de la queja
         * @return json Con el estado de la operacion y el valor del estado
         */
        function getstatus($argv = null) {
            if ($argv != null) {
                $value = null;
                if ($argv != null) {
                    $value = $this->model->getstatus($argv[0]);
                    //$value = $this->view->statusToSP($value);
                }

                $status = ($value != null) ? true : false;
                
                header_remove();
                header('Access-Control-Allow-Origin: *');
                header('Content-type: application/json');
                echo json_encode(array('status' => $status, 'value' => $value), JSON_FORCE_OBJECT);
            }
        }

        /**
         * Cambia de estado una queja
         *
         * @param [array] $argv Identificador de la queja + estado al que se quiere cambiar
         * @return json Con el estado de la operación
         */
        function changeeventstatus($argv = null) {
            $status = false;
            if ($argv != null) {
                if (in_array($argv[0], array('checking', 'checked', 'resolved', 'irresolvable', 'processed'))) {
                    $status = $this->model->changestatus(['status' => $argv[0], 'id' => $argv[1]]);
                }
            }

            if ($status) $this->logger->log('[' .$_SESSION['user']['rol'] . "] [" . $_SESSION['user']['email'] . "] -> Cambio status a (" . $argv[0] . ") en evento #" . $argv[1]);

            header_remove();
            header('Access-Control-Allow-Origin: *');
            header('Content-type: application/json');
            echo json_encode(array('status' => $status), JSON_FORCE_OBJECT);
        }

        /**
         * Devuelve estadísticos de las quejas
         *
         * @return json Con el estado de la operación y los datos obtenidos
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