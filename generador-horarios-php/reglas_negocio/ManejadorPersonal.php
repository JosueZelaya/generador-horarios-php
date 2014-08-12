<?php

chdir(dirname(__FILE__));
require_once '../acceso_datos/Conexion.php';
require_once 'Usuario.php';
chdir(dirname(__FILE__));
require_once 'Docente.php';
chdir(dirname(__FILE__));
require_once 'Departamento.php';

abstract class ManejadorPersonal{
	
    public static function getUsuario($login){
        if (ereg("[^A-Za-z0-9._ ]+",$login)) {	//EVITAR QUE EN EL LOGIN APAREZCAN CARACTERES ESPECIALES
                throw new Exception("¡Login Inválido!");	
        } 
        else{
            $sql_consulta = "SELECT * FROM usuarios NATURAL JOIN docentes NATURAL JOIN departamentos WHERE login='".$login."' AND habilitado='t';";                        
            $respuesta = conexion::consulta2($sql_consulta);
            if($respuesta['login']==""){
                $sql_consulta = "SELECT * FROM usuarios WHERE login='".$login."' AND habilitado='t'";                        
                $respuesta = conexion::consulta2($sql_consulta);
                $usuario = new Usuario();
                $usuario->setlogin($respuesta['login']);                            
                //Si el password no está encriptado en la base de datos se encripta acá
                //Borrar la siguiente línea si el password ya está encriptado en la BD
                $password = hash('sha512',$respuesta['password']);                        
                $usuario->setPassword($password);
                $usuario->setHabilitado($respuesta['habilitado']);
                $usuario->setDepartamento(new Departamento("todos","todos"));
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
                $usuario->setDepartamento(new Departamento($respuesta['id_depar'],$respuesta['nombre_depar']));                
                $docente = self::getDocente($respuesta['id_docente']);
                $usuario->setDocente($docente);
            }
            return $usuario;
        }		
    }
    
    public static function getUsuarioPorId($id){            
        $sql_consulta = "SELECT * FROM usuarios WHERE id_usuario='".$id."' AND habilitado='t'";                        
        $respuesta = conexion::consulta2($sql_consulta);
        $usuario = new Usuario();
        $usuario->setlogin($respuesta['login']);                            
        //Si el password no está encriptado en la base de datos se encripta acá
        //Borrar la siguiente línea si el password ya está encriptado en la BD
        $password = hash('sha512',$respuesta['password']);                        
        $usuario->setPassword($password);
        $usuario->setHabilitado($respuesta['habilitado']);
        $usuario->setId($id);
        $docente = self::getDocente($respuesta['id_docente']);
        $usuario->setDocente($docente);
        return $usuario;            
    }
    
    public static function getDocente($id){        
        $sql_consulta = "SELECT d.id_docente,d.nombres,d.apellidos,d.contratacion,d.cargo,d.activo,dd.nombre_depar FROM docentes AS d NATURAL JOIN departamentos AS dd WHERE d.id_docente='$id' AND d.activo='t' ORDER BY d.nombres ASC;";
	$fila = Conexion::consulta2($sql_consulta);
        $docente = new Docente("","","","","");
        $docente->setIdDocente($fila['id_docente']);
        $docente->setNombres($fila['nombres']);
        $docente->setApellidos($fila['apellidos']);
        $docente->setContratacion($fila['contratacion']);
        $docente->setDepar($fila['nombre_depar']);
        $cargo = $fila['cargo'];
        if($cargo!=""){
            $consulta = "SELECT * FROM cargo WHERE id='".$cargo."'";
            $fila = Conexion::consulta2($consulta);
            $docente->setCargo($fila['nombre']);
        }else{
            $docente->setCargo($cargo);
        }
        return $docente;
    }
    
    public static function getIdDocente($nombre_completo){
        
    }
    
    public static function getDocentes(){
        $docentes = array();
        $sql_consulta = "SELECT d.id_docente,d.nombres,d.apellidos,d.contratacion,d.cargo,d.activo,dd.nombre_depar FROM docentes AS d NATURAL JOIN departamentos AS dd WHERE d.activo='t' ORDER BY d.nombres ASC;";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $docente = new Docente("","","","","");
            $docente->setIdDocente($fila['id_docente']);
            $docente->setNombres($fila['nombres']);
            $docente->setApellidos($fila['apellidos']);
            $docente->setContratacion($fila['contratacion']);
            $docente->setDepar($fila['nombre_depar']);
            $cargo = $fila['cargo'];
            if($cargo!=""){
                $consulta = "SELECT * FROM cargo WHERE id='".$cargo."'";
                $fila = Conexion::consulta2($consulta);
                $docente->setCargo($fila['nombre']);
            }else{
                $docente->setCargo($cargo);
            }
            $docentes[] = $docente;
        }
        return $docentes;
    }
    
    public static function agregarDocente($docente){
        if(ManejadorPersonal::existe($docente)){
            if(self::docenteActivo($docente)){
                throw new Exception("El docente ya ha sido agregado anteriormente");
            }else{
                if($docente->getCargo()==""){
                    $consulta = "UPDATE docentes SET activo='t',contratacion='".$docente->getContratacion()."',id_depar='".$docente->getDepar()."',cargo=NULL WHERE nombres='".$docente->getNombres()."' AND apellidos='".$docente->getApellidos()."'";
                }else{
                    $consulta = "UPDATE docentes SET activo='t',contratacion='".$docente->getContratacion()."',id_depar='".$docente->getDepar()."',cargo='".$docente->getCargo()."' WHERE nombres='".$docente->getNombres()."' AND apellidos='".$docente->getApellidos()."'";
                }                     
             conexion::consulta($consulta);
            }                    
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
            if(self::usuarioHabilitado($usuario)){
                throw new Exception("Ya existe un usuario con ese login");
            }else{
                $consulta = "SELECT * FROM docentes WHERE id_docente='".$usuario->getDocente()."'";
                $respuesta = conexion::consulta2($consulta);
                if($respuesta['activo']=='t'){
                    $consulta = "UPDATE usuarios SET habilitado='t',id_docente='".$usuario->getDocente()."',password='".$usuario->getPassword()."' WHERE login='".$usuario->getLogin()."'";
                    conexion::consulta($consulta);
                }else{
                    throw new Exception("Docente incorrecto!");
                }                        
            }                    
        }else{
            $consulta = "";
            if($usuario->getLogin()!=="" && $usuario->getPassword()!==""){
                $consulta = "SELECT * FROM docentes WHERE id_docente='".$usuario->getDocente()."'";
                $respuesta = conexion::consulta2($consulta);
                if($respuesta['activo']=='t'){
                    $consulta = "INSERT INTO usuarios (login,password,id_docente) VALUES ('".
                        $usuario->getLogin()."','".
                        $usuario->getPassword()."','".                                
                        $usuario->getDocente()."')";                                
                    conexion::consulta($consulta);
                }else{
                    throw new Exception("Docente incorrecto!");
                }                        
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

    public static function docenteActivo($docente){
        $consulta = "SELECT * FROM docentes WHERE nombres='".$docente->getNombres()."' AND apellidos='".$docente->getApellidos()."'";
        $respuesta = conexion::consulta($consulta);
        $activo = false;
        while ($fila = pg_fetch_array($respuesta)){
            if($fila['activo'] == 't'){
                $activo = true;
            }
        }
        return $activo;
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

    public static function usuarioHabilitado($usuario){
        $consulta = "SELECT * FROM usuarios WHERE login='".$usuario->getLogin()."'";
        $respuesta = conexion::consulta($consulta);
        $habilitado = false;
        while ($fila = pg_fetch_array($respuesta)){
            if($fila['habilitado'] == 't'){
                $habilitado = true;
            }
        }
        return $habilitado;
    }

    public static function getCuantosDocentesExisten($id_departamento="todos"){
        $consulta="";
        if($id_departamento=="todos"){
            $consulta = "SELECT COUNT(*) FROM docentes NATURAL JOIN departamentos WHERE activo='t'";
        }else{
            $consulta = "SELECT COUNT(*) FROM docentes NATURAL JOIN departamentos WHERE id_depar='$id_departamento' AND activo='t'";
        }        
        $respuesta = conexion::consulta2($consulta);
        return $respuesta['count'];
    }

    public static function getCuantosUsuariosExisten(){
        $consulta = "SELECT COUNT(*) FROM usuarios WHERE habilitado='t'";
        $respuesta = conexion::consulta2($consulta);
        return $respuesta['count'];
    }

    public static function getTodosDocentesConPaginacion($pagina,$numeroResultados,$id_departamento="todos"){
        $docentes = array();
        $pagina = ($pagina-1)*$numeroResultados;
        $sql_consulta="";
        if($id_departamento=="todos"){
            $sql_consulta = "SELECT d.id_docente,d.nombres,d.apellidos,d.contratacion,d.cargo,d.activo,dd.id_depar,dd.nombre_depar FROM docentes AS d NATURAL JOIN departamentos AS dd WHERE d.activo='t' ORDER BY d.nombres ASC LIMIT ".$numeroResultados." OFFSET ".$pagina;
        }else{
            $sql_consulta = "SELECT d.id_docente,d.nombres,d.apellidos,d.contratacion,d.cargo,d.activo,dd.id_depar,dd.nombre_depar FROM docentes AS d NATURAL JOIN departamentos AS dd WHERE dd.id_depar='$id_departamento' AND d.activo='t' ORDER BY d.nombres ASC LIMIT ".$numeroResultados." OFFSET ".$pagina;
        }        
        $respuesta = conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $docente = new Docente("","","","","");
            $docente->setIdDocente($fila['id_docente']);
            $docente->setNombres($fila['nombres']);
            $docente->setApellidos($fila['apellidos']);
            $docente->setContratacion($fila['contratacion']);
            $docente->setDepar($fila['nombre_depar']);
            $cargo = $fila['cargo'];
            if($cargo!=""){
                $consulta = "SELECT * FROM cargo WHERE id='".$cargo."'";
                $fila = Conexion::consulta2($consulta);
                $docente->setCargo($fila['nombre']);
            }else{
                $docente->setCargo($cargo);
            }
            $docentes[] = $docente;
        }                   			
        return $docentes;
    }

    public static function getTodosUsuariosConPaginacion($pagina,$numeroResultados){
        $usuarios = array();
        $pagina = ($pagina-1)*$numeroResultados;
        $sql_consulta = "SELECT * FROM usuarios NATURAL JOIN docentes WHERE habilitado='t' ORDER BY login ASC LIMIT ".$numeroResultados." OFFSET ".$pagina;
        $respuesta = conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $usuario = new Usuario();
            $usuario->setId($fila['id_usuario']);
            $usuario->setLogin($fila['login']);
//                $docente = new Docente("","","");
//                $docente->setNombres($fila['nombres']);
//                $docente->setApellidos($fila['apellidos']);
//                $docente->setNombre_completo($fila['nombres']." ".$fila['apellidos']);
//                $docente->setIdDocente($fila['id_docente']);  
            $docente = self::getDocente($fila['id_docente']);
            $usuario->setDocente($docente);
            $usuarios[] = $usuario;
        }                   			
        return $usuarios;
    }

    public static function buscarDocente($buscarComo,$id_departamento="todos"){
        $docentes = array();
        $sql_consulta="";
        if($id_departamento=="todos"){
            $sql_consulta = "SELECT d.id_docente,d.nombres,d.apellidos,d.contratacion,d.cargo,d.activo,dd.id_depar,dd.nombre_depar FROM docentes AS d NATURAL JOIN departamentos AS dd WHERE (nombres || ' ' || apellidos) iLIKE '%$buscarComo%' AND d.activo='t' ORDER BY d.nombres ASC LIMIT 25;";
        }else{
            $sql_consulta = "SELECT d.id_docente,d.nombres,d.apellidos,d.contratacion,d.cargo,d.activo,dd.id_depar,dd.nombre_depar FROM docentes AS d NATURAL JOIN departamentos AS dd WHERE (nombres || ' ' || apellidos) iLIKE '%$buscarComo%' AND dd.id_depar='$id_departamento' AND d.activo='t' ORDER BY d.nombres ASC LIMIT 25;";
        }        
        $respuesta = conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $docente = new Docente("","","","","");
            $docente->setIdDocente($fila['id_docente']);
            $docente->setNombres($fila['nombres']);
            $docente->setApellidos($fila['apellidos']);
            $docente->setContratacion($fila['contratacion']);
            $docente->setDepar($fila['nombre_depar']);
            $cargo = $fila['cargo'];
            if($cargo!=""){
                $consulta = "SELECT * FROM cargo WHERE id='".$cargo."'";
                $fila = Conexion::consulta2($consulta);
                $docente->setCargo($fila['nombre']);
            }else{
                $docente->setCargo($cargo);
            }
            $docentes[] = $docente;
        }                   			
        return $docentes;
    }

    public static function buscarUsuario($buscarComo){
        $usuarios = array();
//            if (ereg("[^A-Za-z0-9._ ]+",$buscarComo)) {	//EVITAR QUE APAREZCAN CARACTERES ESPECIALES
//			throw new Exception("¡Carácteres Inválidos!");	
//            } else{
            $sql_consulta = "SELECT * FROM usuarios NATURAL JOIN docentes WHERE login iLIKE '%$buscarComo%' AND habilitado='t' ORDER BY login ASC LIMIT 25;";                    
            $respuesta = conexion::consulta($sql_consulta);
            while ($fila = pg_fetch_array($respuesta)){
                $usuario = new Usuario();
                $usuario->setId($fila['id_usuario']);
                $usuario->setLogin($fila['login']);
                $docente = self::getDocente($fila['id_docente']);
                $usuario->setDocente($docente);
//                    $usuario->setDocente($fila['nombres']." ".$fila['apellidos']);
                $usuarios[] = $usuario;
            }                   			
            return $usuarios;
//            }
    }

    public static function desactivarDocente($docente){
        if(ManejadorPersonal::existe($docente)){
            $docente->desactivar();
        }else{
            throw new Exception("Ese docente no existe en la BD");
        }
    }

    public static function desactivarUsuario($usuario){
        if(ManejadorPersonal::existeUsuario($usuario)){
            $usuario->desactivar();
        }else{
            throw new Exception("Ese usuario no existe en la BD");
        }
    }

    public static function modificarDocente($actual,$nueva){            
        if(self::existe($actual)){                
            $nueva->guardar();
        }else{
            throw new Exception("No existe el docente que se quiere modificar");
        }            		
    }

    public static function modificarUsuario($actual,$nueva){            
        if(ManejadorPersonal::existeUsuario($actual)){                
                $nueva->guardar();  //Se guarda el usuario               
        }else{
            throw new Exception("No existe el usuario que se quiere modificar");
        }            		
    }
        
    public static function esPropietario($deparSesion,$grupos){
        if($deparSesion != "todos"){
            foreach ($grupos as $grupo) {
                if($grupo->getId_grupo() == 0){
                    continue;
                }
                $materiasGrupo = $grupo->getAgrup()->getMaterias();
                foreach ($materiasGrupo as $materia){
                    if($materia->getCarrera()->getDepartamento()->getId() != $deparSesion){
                        return false;
                    }
                }
            }
        }
        return true;
    }
}
