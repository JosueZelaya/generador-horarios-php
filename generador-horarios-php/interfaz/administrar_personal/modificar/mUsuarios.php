<?php

chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();

if($_POST){
    if($_POST['pk'] && $_POST['name'] && $_POST['value']){

        $id = $_POST['pk'];
        $campo = $_POST['name'];
        $valor = $_POST['value'];
        
        $usuarioActual = new Usuario();
        $usuarioActual = ManejadorPersonal::getUsuarioPorId($id);
        
        $usuarioNuevo = new Usuario();
        $usuarioNuevo->setId($usuarioActual->getId());
        $usuarioNuevo->setLogin($usuarioActual->getLogin());
        $usuarioNuevo->setDocente($usuarioActual->getDocente());        
        
        switch ($campo) {
            case "login":
                $usuarioNuevo->setLogin($valor);
                break;
            case "docente":
                $docente = new Docente("","","");
                $docente->setIdDocente($valor);
                $usuarioNuevo->setDocente($docente);
                break;                       
            default:
                break;
        }
        
        try{
            ManejadorPersonal::modificarUsuario($usuarioActual, $usuarioNuevo);
            $respuesta = array('status'=>'ok','msg'=>'¡Usuario Modificado!');
            print json_encode($respuesta);
        }catch(Exception $ex){
            $respuesta = array('status'=>'error','msg'=>$ex->getMessage());
            print json_encode($respuesta);            
        }       

    }
}
