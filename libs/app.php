<?php

    require_once 'controllers/error.php';

    /**
     * Clase App. Maneja toda la aplicación web
     */
    class App {

        /**
         * Constructor por defecto
         */
        function __construct() {
            
            // Obtengo la url
            $url = isset($_GET['url']) ? $_GET['url'] : null;
            $url = rtrim($url, '/');
            $url = explode('/', $url);

            // Si la url no tiene parámetros
            if (empty($url[0])) {
                $controllerPath = 'controllers/main.php';
                // Cargo controlador Main
                require_once $controllerPath;
                $controller = new Main();
                // Cargo el modelo Event al controlador Main
                $controller->loadModel('Event');
                // Ejecuto su main
                $controller->index();
                
                return false;
            }
        
            // URL con parámetros, obtengo el path del controlador a cargar
            $controllerPath = 'controllers/' . $url[0] . '.php';

            // Si existe ese controlador
            if (file_exists($controllerPath)) {
                // Cargo controlador
                require_once $controllerPath;
                // y lo creo
                $controller = new $url[0];

                // según el controlador cargo un modelo u otro
                switch($url[0]) {
                    case 'main':    $controller->loadModel('Event');    break;
                    case 'events':  $controller->loadModel('Event');    break;
                    case 'login':   $controller->loadModel('User');     break;
                    case 'admin':   $controller->loadModel('User');     break;
                    case 'users':   $controller->loadModel('User');     break;
                    case 'profile': $controller->loadModel('User');     break;
                    default:        $controller->loadModel('$url[0]');  break;
                }

                // Nº de argumentos de la url, a partir de la posicion 1 inclusive (controlador/método/args...)
                $nargs = sizeof($url);

                // Si hay más de dos argumentos
                if ($nargs > 2) {
                    $args = [];
                    // agrego args al array
                    for ($i = 2; $i < $nargs; $i++) {
                        array_push($args, $url[$i]);
                    }

                    // ejecuto función del controlador y le paso los argumentos
                    try {
                        $controller->{$url[1]}($args);
                    } catch (Exception $e) {
                        $controller = new Errors();
                    }
                } else {
                    // si la url solo tiene controlador + metodo, lo ejecuto
                    if (isset($url[1])) {
                        try {
                            $controller->{$url[1]}();
                        } catch (Exception $e) {
                            $controller = new Errors();
                        }
                    } else {
                        // en caso contrario ejecuto el index
                        $controller->index();
                    }
                }

            // si el controlador no existe, llamo al controlador de errores
            } else {
                $controller = new Errors();
            }
        }
    }
?>