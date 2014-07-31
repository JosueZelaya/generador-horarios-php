<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Docente.php';        
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();

if (ManejadorSesion::comprobar_sesion() == true){
    if($_POST){       
        $login = $_POST['login'];
        $password = $_POST['password'];
        $docente = $_POST['docente'];
        $usuario = new Usuario();
        $usuario->setLogin($login);
        $usuario->setPassword($password);
        $usuario->setDocente($docente);
        try{
            ManejadorPersonal::agregarUsuario($usuario);
            $respuesta = "¡Usuario Agregado!";
            echo json_encode($respuesta);
        }catch(Exception $e){
            echo json_encode($e->getMessage());
        }

    }    
}else{
    echo json_encode("No está autorizado para realizar esta acción");
}
