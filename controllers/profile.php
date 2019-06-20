<?php

    /**
     * Controlador Profile, controla todo lo relacionado con el perfil de cada usuario
     */
    class Profile extends Controller {

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
            $this->view->render('profile/' . $whattorender, $argv);
        }

        /**
         * Maneja el index del perfil, obtiene los likes/dislikes, comentarios y quejas del usuario. Los datos del usuario se obtienen de la sesión
         *
         * @return void
         */
        function index() {
            require_once "models/event.php";
            require_once "models/comment.php";
            $ev = new Event();
            $co = new Comment();

            $likes = count($ev->getuserlikes($_SESSION['user']['email']));
            $dislikes = count($ev->getuserdislikes($_SESSION['user']['email']));
            $events = count($ev->geteventsbyuser($_SESSION['user']['email']));
            $comments = count($co->getcommentsbyuser($_SESSION['user']['email']));

            $this->render('index', ['likes' => $likes, 'dislikes' => $dislikes, 'events' => $events, 'comments' => $comments]);
        }

        /**
         * Comprueba que los parámetros son correctos
         *
         * @param [int] $p Tipo de comprobación.
         * @return void
         */
        private function paramsok($p) {
            switch($p) {
                case 1: return isset($_POST['profile-firstname']) && $_POST['profile-firstname'] != "" && isset($_POST['profile-surname']) && $_POST['profile-surname'] != "" && isset($_POST['profile-telnumber']) && $_POST['profile-telnumber'] != "" && isset($_POST['profile-address']) && $_POST['profile-address'] != "" && isset($_POST['profile-password']) && $_POST['profile-password'] != "" && isset($_POST['profile-password-confirm']) && $_POST['profile-password-confirm'] != ""; 
                case 2: return $_POST['profile-password'] == $_POST['profile-password-confirm'];
            }
        }

        /**
         * Actualiza los datos del usuario
         *
         * @return void
         */
        function updateuser() {
            if ($this->paramsok(1) && $this->paramsok(2)) {
                if (session_status() == PHP_SESSION_NONE) session_start();
                
                $data = [
                    'email'     => $_POST['profile-email'],
                    'firstname' => $_POST['profile-firstname'],
                    'surname'   => $_POST['profile-surname'],
                    'password'  => $_POST['profile-password'],
                    'image'     => null,
                    'address'   => $_POST['profile-address'],
                    'telnumber' => $_POST['profile-telnumber']
                ];

                // Imagen
                if (isset($_FILES['profile-image'])) {
                    if ($_FILES['profile-image']['size'] != 0) {
                        $tmppath = $_FILES['profile-image']['tmp_name'];
                        $info = pathinfo($_FILES['profile-image']['name']);
                        $newpath = 'public/uploads/profilepics/' . $data['email'] . '.' . strtolower($info['extension']);
                        
                        if (move_uploaded_file($tmppath, $newpath))
                            $data['image'] = $newpath;
                    }
                }

                if ($this->model->simpleupdate($data)) {
                    $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' .  $_SESSION['user']['email'] . '] -> Usuario modificado (' . $_POST['profile-email'] . ')');
                    $this->header('login/logout');
                }
            } else {
                $this->header('profile');
            }
        }

        /**
         * Maneja la plantilla que muestra los likes y dislikes del usuario. Número y eventos donde los ha dado.
         *
         * @return void
         */
        function mylikesdislikes() {
            require_once "models/event.php";
            $ev = new Event();

            $idlikes =$ev->getuserlikes($_SESSION['user']['email']);
            $iddislikes = $ev->getuserdislikes($_SESSION['user']['email']);

            $evlikes = [];
            $evdislikes = [];

            foreach ($idlikes as $e) array_push($evlikes, $ev->getevent($e));
            foreach ($iddislikes as $e) array_push($evdislikes, $ev->getevent($e));

            $likes = [
                'count' => count($idlikes),
                'content' => $evlikes
            ];

            $dislikes = [
                'count' => count($iddislikes),
                'content' => $evdislikes
            ];

            $this->render('likesdislikes', ['likes' => $likes, 'dislikes' => $dislikes]);
        }

        /**
         * Maneja la plantilla que muestra los comentarios del usuario. Número y eventos donde ha comentado.
         *
         * @return void
         */
        function mycomments () {
            require_once "models/comment.php";
            $cm = new Comment();
            $comments = $cm->getcommentsbyuser($_SESSION['user']['email']);
            $this->render('comments', ['comments' => $comments]);
        }

        /**
         * Maneja la plantilla que muestra las quejas del usuario.
         *
         * @return void
         */
        function myevents () {
            require_once "models/event.php";
            $ev = new Event();
            $events = $ev->geteventsbyuser($_SESSION['user']['email']);
            $this->render('events', ['events' => $events]);
        }
    }

?>