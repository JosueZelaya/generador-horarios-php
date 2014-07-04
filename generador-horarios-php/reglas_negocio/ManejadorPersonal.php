<?php

chdir(dirname(__FILE__));
require_once '../acceso_datos/Conexion.php';
require_once 'Usuario.php';

abstract class ManejadorPersonal{
	
    public static function getUsuario($login){
            if (ereg("[^A-Za-z0-9.]+",$login)) {	//EVITAR QUE EN EL LOGIN APAREZCAN CARACTERES ESPECIALES
                    throw new Exception("¡Login Inválido!");	
            } 
            else{
                    $sql_consulta = "SELECT * FROM usuarios NATURAL JOIN docentes WHERE login='".$login."';";                        
                    $respuesta = conexion::consulta2($sql_consulta);
                    if($respuesta['login']==""){
                        $sql_consulta = "SELECT * FROM usuarios WHERE login='".$login."'";                        
                        $respuesta = conexion::consulta2($sql_consulta);
                        $usuario = new Usuario();
                        $usuario->setlogin($respuesta['login']);                            
                        //Si el password no está encriptado en la base de datos se encripta acá
                        //Borrar la siguiente línea si el password ya está encriptado en la BD
                        $password = hash('sha512',$respuesta['password']);                        
                        $usuario->setPassword($password);
                        $usuario->setHabilitado($respuesta['habilitado']);
                        $usuario->setDepartamento("todos");
                    }else{
                        $usuario = new Usuario();
                        $usuario->setlogin($respuesta['login']);                            
                        //Si el password no está encriptado en la base de datos se encripta acá
                        //Borrar la siguiente línea si el password ya está encriptado en la BD
                        $password = hash('sha512',$respuesta['password']);                        
                        $usuario->setPassword($password);
                        $usuario->setHabilitado($respuesta['habilitado']);
                        $usuario->setNombres($respuesta['nombres']);
                        $usuario->setApellidos($respuesta['apellidos']);                        
                        $usuario->setDepartamento($respuesta['id_depar']);                        
                    }
                    return $usuario;
            }		
    }
}

?>