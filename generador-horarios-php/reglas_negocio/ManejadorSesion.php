<?php
chdir(dirname(__FILE__));
require_once 'ManejadorPersonal.php';
chdir(dirname(__FILE__));
require_once 'Departamento.php';
chdir(dirname(__FILE__));
require_once 'Usuario.php';
chdir(dirname(__FILE__));

abstract class ManejadorSesion{
	
	public static function iniciarSesion($login,$password){
                try{
                    $usuario = ManejadorPersonal::getUsuario($login);
                    $usuario->comprobarPassword(hash('sha512',$password));
                    //Obtenemos el navegador y sistema operativo del usuario
                    $user_navegador = $_SERVER['HTTP_USER_AGENT'];                    
                    // XSS protection as we might print this value
                    $user_nombre = preg_replace("/[^a-zA-Z0-9_\- ]+/","",$usuario->getNombres());
                    $usuario->setNombres($user_nombre);
                    // XSS protection as we might print this value
                    $user_apellidos = preg_replace("/[^a-zA-Z0-9_\- ]+/","",$usuario->getApellidos());
                    $usuario->setApellidos($user_apellidos);
                    // XSS protection as we might print this value
                    $user_departamento = preg_replace("/[^a-zA-Z0-9_\-]+/","",$usuario->getDepartamento()->getId());
                    $nombre_depar = preg_replace("/[^a-zA-Z0-9_\- ]+/","",$usuario->getDepartamento()->getNombre());
                    $usuario->setDepartamento($user_departamento);
                    
                    $_SESSION['usuario_login']=$login;
                    $_SESSION['usuario_nombres'] = $user_nombre;
                    $_SESSION['usuario_apellidos'] = $user_apellidos;
                    $_SESSION['usuario_navegador'] = $user_navegador;                    
                    $_SESSION['random_string'] = self::getRandomString(16);
                    $_SESSION['usuario_login_string'] = hash('sha512',$user_navegador.$usuario->getPassword().$_SESSION['random_string']);                    
                    $_SESSION['id_departamento'] = $user_departamento;
                    $_SESSION['nombre_departamento'] = $nombre_depar;
                    return $usuario;
                    
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());	
                }		
	}
        
        public static function sec_session_start() {
            $session_name = 'generador_horarios';   // Set a custom session name
            $secure = FALSE; //Ponerla a true si estoy usando https
            // This stops JavaScript being able to access the session id.
            $httponly = true;
            // Forces sessions to only use cookies.
            if (ini_set('session.use_only_cookies', 1) === FALSE) {
                header("Location: Error.php?err=Could not initiate a safe session (ini_set)");
                exit();
            }
            // Gets current cookies params.
            $cookieParams = session_get_cookie_params();
            session_set_cookie_params($cookieParams["lifetime"],
                $cookieParams["path"], 
                $cookieParams["domain"], 
                $secure,
                $httponly);
            // Sets the session name to the one set above.
            session_name($session_name);
            session_start();            // Start the PHP session 
            session_regenerate_id();    // regenerated the session, delete the old one. 
        }
        
        //Verifica la autenticidad de la sesion
        public static function comprobar_sesion() {
            // Verifica si todas las variables de sesión están asignadas
            if (isset($_SESSION['usuario_login'],$_SESSION['usuario_nombres'],$_SESSION['usuario_apellidos'],$_SESSION['usuario_navegador'],$_SESSION['usuario_login_string'],$_SESSION['random_string'])) {              
                //Obtenemos el usuario al que debería pertencer la sesión
                $usuario = ManejadorPersonal::getUsuario($_SESSION['usuario_login']);
                //Comprobamos que la cadena de autenticación coincida con la de la sesión
                $cadena_autenticacion = hash('sha512',$_SESSION['usuario_navegador'].$usuario->getPassword().$_SESSION['random_string']);
                if($cadena_autenticacion==$_SESSION['usuario_login_string']){
                    //Está autenticado
                    return true;
                }else{
                    //No está autenticado
                    return false;
                }
                
            } else {
                // No está autenticado 
                return false;
            }
            
        }
	
	public static function cerrarSesion(){
                ManejadorSesion::sec_session_start();

                // Unset all session values 
                $_SESSION = array();

                // get session parameters 
                $params = session_get_cookie_params();

                // Delete the actual cookie. 
                setcookie(session_name(),
                        '', time() - 42000, 
                        $params["path"], 
                        $params["domain"], 
                        $params["secure"], 
                        $params["httponly"]);

                // Destroy session 
                session_destroy();
                header('Location: ../interfaz/autenticacion/login.php');
	}
        
        public static function getRandomString($l, $c = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNÑOPQRSTUVWXYZ') {
            for ($s = '', $cl = strlen($c)-1, $i = 0; $i < $l; $s .= $c[mt_rand(0, $cl)], ++$i);
                return $s;
        }
	
}