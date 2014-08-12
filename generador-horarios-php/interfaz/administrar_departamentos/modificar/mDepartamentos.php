<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDepartamentos.php';
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();

if($_POST){
    if($_POST['pk'] && $_POST['name'] && $_POST['value']){
        $id = $_POST['pk'];
        $campo = $_POST['name'];
        $valor = $_POST['value'];
        try{
            ManejadorDepartamentos::modificarDepartamento($id, $campo, $valor);
            $respuesta = array('status'=>'ok','msg'=>'¡Materia Modificada!');
            print json_encode($respuesta);
        }catch(Exception $ex){
            $respuesta = array('status'=>'error','msg'=>$ex->getMessage());
            print json_encode($respuesta);            
        }       

    }    
    
}