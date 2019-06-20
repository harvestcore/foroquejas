<?php
    // Cargo modelo básico
    require_once "libs/model.php";

    /**
     * Modelo AdminDB, no representa un modelo de datos como tal. Se usa para realizar todas las operaciones
     * relacionadas con la gestión de la base de datos.
     */
    class AdminDB extends Model {

        /**
         * Constructor por defecto
         */
        public function __construct() {
            parent::__construct();
        }

        /**
         * Vuelca todos los datos de la BD en un string
         *
         * @return string Contiene todas los comandos SQL para restaurar la BD. Null en caso de que se produzca algún error.
         */
        public function backup() {
            try {
                // Solo vuelvo estas tablas
                $tablas = array('users', 'events', 'comments', 'u_interact_e', 'log');

                $output = '';
                
                foreach ($tablas as $tab) {
                    $result = $this->db->connect()->query('SELECT * FROM ' . $tab);
                    $num = $result->columnCount();
                    
                    $output .= 'DROP TABLE ' . $tab . ';';

                    $out2 = $this->db->connect()->query('SHOW CREATE TABLE ' . $tab);
                    $row2 = $out2->fetch();
                    $output .= "\n\n" . $row2[1] . ";\n\n";

                    while ($row = $result->fetch()) {
                        $output .= 'INSERT INTO ' . $tab . ' VALUES(';
                        
                        for ($j = 0; $j < $num; $j++) {
                            if (isset($row[$j])) {
                                $row[$j] = addslashes($row[$j]);
                                $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
                                $output .= '"'.$row[$j].'"';
                            } else {
                                $output .= '""';
                            }
                            
                            if ($j < ($num-1)) $output .= ',';
                        }
                        $output .= ");\n";
                    }
                    $output .= "\n\n\n";
                }

                return $output;
            } catch (PDOException $e) {
                return null;
            }
        }
        
        /**
         * Recupera los datos del archivo pasado como parámetro
         * Conexión a la BD hecha con mysqli porque PDO no ejecutaba correctamente las órdenes FOREIGN_KEY_CHECKS
         * $x para depuración de errores
         *
         * @param [file] $f Archivo con los comandos SQL
         * @return void
         */
        public function upload($f) {
            $db = mysqli_connect(constant('HOST'), constant('USER'), constant('PASSWORD'), constant('DB'));
            
            $tablas = array('users', 'events', 'comments', 'u_interact_e', 'log');
            $x = 0;
            $sql = file_get_contents($f);
            $queries = explode(';', $sql);
            try {
                // $this->db->connect()->query('SET FOREIGN_KEY_CHECKS=0');
                mysqli_query($db,'SET FOREIGN_KEY_CHECKS=0');

                foreach ($tablas as $tab)
                    $this->db->connect()->query('DELETE FROM ' . $tab);
                
                foreach ($queries as $q)
                    mysqli_query($db,$q);

                // foreach ($queries as $q) {
                //     $this->db->connect()->query($queries[$i]);
                //     $x += 1;
                // }

                // $this->db->connect()->query('SET FOREIGN_KEY_CHECKS=1');
                mysqli_query($db,'SET FOREIGN_KEY_CHECKS=1');

                mysqli_close($db);

                return true;
            } catch (PDOException $e) {
                echo "ERROR - " . $x . " - " . $queries[$x];
                return false;
            }
        }

        /**
         * Vacia todas las tablas y agrega los usuarios mínimos (admin y anonymous)
         *
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function drop() {
            try {
                $tablas = array('users', 'events', 'comments', 'u_interact_e', 'log');

                $this->db->connect()->query('SET FOREIGN_KEY_CHECKS=0');

                foreach ($tablas as $tab)
                    $this->db->connect()->query('DELETE FROM ' . $tab);

                $this->db->connect()->query('SET FOREIGN_KEY_CHECKS=1');

                $this->addDefaultUsers();

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        /**
         * Agrega los usuarios por defecto. Admin y anónimo
         *
         * @return boolean True si la operación se produce sin errores, false en caso contrario.
         */
        public function addDefaultUsers() {
            try {
                // Usuarios por defecto
                // admin      ->  admin@admin
                // anonymous  ->  anonymous@anonymous
                require_once 'models/user.php';
                $u = new User();
                $u->insert([
                    'email'     => "admin@admin",
                    'firstname' => "admin",
                    'surname'   => "",
                    'password'  => "admin",
                    'rol'       => "admin",
                    'image'     => null,
                    'address'   => "admin",
                    'telnumber' => 123456789
                ]);

                $u->insert([
                    'email'     => "anonymous@anonymous",
                    'firstname' => "anonymous",
                    'surname'   => "",
                    'password'  => "anonymous",
                    'rol'       => "colaborator",
                    'image'     => null,
                    'address'   => "anonymous",
                    'telnumber' => 123456789
                ]);

                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
    }

?>