<?php

chdir(dirname(__FILE__));
require_once '../acceso_datos/Conexion.php';
require_once 'Usuario.php';
require_once 'Docente.php';

abstract class ManejadorPersonal{
	
    public static function getUsuario($login){
            if (ereg("[^A-Za-z0-9._]+",$login)) {	//EVITAR QUE EN EL LOGIN APAREZCAN CARACTERES ESPECIALES
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
    
    public static function getDocentes(){
        $docentes = array();
        $sql_consulta = "SELECT * FROM docentes ORDER BY nombres ASC";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $docente = new Docente("","","");
            $docente->setIdDocente($fila['id_docente']);
            $docente->setNombres($fila['nombres']);
            $docente->setApellidos($fila['apellidos']);
            $docente->setNombre_completo($fila['nombres']." ".$fila['apellidos']);
            $docente->setCargo($fila['cargo']);
            $docente->setContratacion($fila['contratacion']);
            $docente->setDepar($fila['id_depar']);
            $docentes[] = $docente;
        }
        return $docentes;
    }
    
    public static function agregarDocente($docente){
		if(ManejadorPersonal::existe($docente)){
                    throw new Exception("El docente ya ha sido agregado anteriormente");
                }else{
                    if($docente->getNombres()!=="" && $docente->getApellidos()!==""){
                        $consulta = "INSERT INTO docentes (nombres,apellidos, contratacion,id_depar,cargo) VALUES ('".
                                $docente->getNombres()."','".
                                $docente->getApellidos()."','".                                
                                $docente->getContratacion()."','".
                                $docente->getDepar()."',";
                                if($docente->getCargo()==""){
                                    $consulta = $consulta."NULL)";
                                }else{
                                    $consulta = $consulta.$docente->getCargo().")";
                                }                                                      
                        conexion::consulta($consulta);
                    }else{
                        throw new Exception("Debe ingresar nombres y apellidos");
                    }
                }
	}
        
        public static function agregarUsuario($usuario){
		if(ManejadorPersonal::existeUsuario($usuario)){
                    throw new Exception("Ya existe un usuario con ese login");
                }else{
                    $consulta = "";
                    if($usuario->getLogin()!=="" && $usuario->getPassword()!==""){
                        $consulta = "INSERT INTO usuarios (login,password,id_docente) VALUES ('".
                                $usuario->getLogin()."','".
                                $usuario->getPassword()."','".                                
                                $usuario->getDocente()."')";                                
                        conexion::consulta($consulta);
                    }else{
                        throw new Exception("Debe ingresar un login y un password");
                    }
                }
	}
    
        public static function existe($docente){
		$consulta = "SELECT * FROM docentes WHERE nombres='".$docente->getNombres()."' AND apellidos='".$docente->getApellidos()."'";
                $respuesta = conexion::consulta($consulta);
                if(pg_fetch_array($respuesta)){
                    return true;
                }else{
                    return false;
                }
	}
    
        public static function existeUsuario($usuario){
		$consulta = "SELECT * FROM usuarios WHERE login='".$usuario->getLogin()."'";
                $respuesta = conexion::consulta($consulta);
                if(pg_fetch_array($respuesta)){
                    return true;
                }else{
                    return false;
                }
	}
    
}

