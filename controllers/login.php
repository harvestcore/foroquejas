<?php

    /**
     * Controlador Login, controla todo lo relacionado con el login del sitio web.
     */
    class Login extends Controller {

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
            $this->view->render('login/' . $whattorender, $argv);
        }
        
        /**
         * Maneja el index del login
         *
         * @return void
         */
        function index() {
            $this->render('index', []);
        }

        /**
         * Valida un usuario. Si los datos son correctos se inicia la sesión. Agrega cookie de timeout.
         *
         * @return void
         */
        function validate() {
            $message = "";
            if (isset($_POST['login-email']) && isset($_POST['login-password'])) {
                
                $u = [
                    'email'    => $_POST['login-email'],
                    'password' => $_POST['login-password']
                ];

                if ($this->model->validateuser($u) && $u['email'] != "anonymous@anonymous") {
                    $user = $this->model->getuser($u['email'], false);

                    // Cookie timeout user
                    ini_set('session.gc_maxlifetime', 1800);
                    session_set_cookie_params(1800);
                    if (session_status() == PHP_SESSION_NONE) session_start();
                    $_SESSION['discard_after'] = time() + 1800;
                    
                    $_SESSION['user'] = [
                        'email'     => $user['email'],
                        'firstname' => $user['firstname'],
                        'surname'   => $user['surname'],
                        'image'     => $user['image'],
                        'rol'       => $user['rol'],
                        'address'   => $user['address'],
                        'telnumber' => $user['telnumber'],
                        'state'     => $user['state']
                    ];
                    
                    $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' .  $_SESSION['user']['email'] . '] -> Acceso al sistema');
                    
                    $this->header('');
                    die();
                } else {
                    $message = "Error al validar el usuario. Usuario/contraseña incorrecta o usuario inactivo.";
                }
            }

            $this->render('index', ['message' => $message]);
        }

        /**
         * Desloguea al usuario destruyendo la sesión
         *
         * @return void
         */
        function logout() {
            session_start();

            $this->logger->log('[' . $_SESSION['user']['rol'] . '] [' .  $_SESSION['user']['email'] . '] -> Salida del sistema');

            session_unset();
            session_destroy();
            $this->header('');
            die();
        }
    }

?>