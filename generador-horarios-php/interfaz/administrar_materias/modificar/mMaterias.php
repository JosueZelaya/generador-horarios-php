<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorMaterias.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

ManejadorSesion::sec_session_start();

if($_POST){
    if($_POST['pk'] && $_POST['name'] && $_POST['value']){

        $id = $_POST['pk'];
        $campo = $_POST['name'];
        $valor = $_POST['value'];
        $pk = split(",",$id);
        $codigo = $pk[0];
        $plan = $pk[1];
        $id_carrera = $pk[2];
        try{
            ManejadorMaterias::modificarMateria($codigo, $plan, $id_carrera, $campo, $valor, $año,$ciclo);
            $respuesta = array('status'=>'ok','msg'=>'¡Materia Modificada!');
            print json_encode($respuesta);
        }catch(Exception $ex){
            $respuesta = array('status'=>'error','msg'=>$ex->getMessage());
            print json_encode($respuesta);            
        }       

    }
}