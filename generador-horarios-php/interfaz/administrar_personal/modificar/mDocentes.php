<?php

chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDepartamentos.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorCargos.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Docente.php';
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();
$id_departamento = $_SESSION['id_departamento'];

if($_POST){
    if($_POST['pk'] && $_POST['name'] && $_POST['value']){

        $id = $_POST['pk'];
        $campo = $_POST['name'];
        $valor = $_POST['value'];
        
        $docenteActual = new Docente("","","","","");
        $docenteActual = ManejadorPersonal::getDocente($id);
        
        $docenteNuevo = new Docente("","","","","");
        $docenteNuevo->setIdDocente($docenteActual->getIdDocente());
        $docenteNuevo->setNombres($docenteActual->getNombres());
        $docenteNuevo->setApellidos($docenteActual->getApellidos());
        $docenteNuevo->setContratacion($docenteActual->getContratacion());
        $nombre_departamento = $docenteActual->getDepar();
        $departamento = ManejadorDepartamentos::getIdDepar($nombre_departamento,ManejadorDepartamentos::getDepartamentos());        
        $docenteNuevo->setDepar($departamento);
        $nombre_cargo = $docenteActual->getCargo();
        $docenteNuevo->setCargo(ManejadorCargos::getIdCargo($nombre_cargo));
        
        switch ($campo) {
            case "nombres":
                $docenteNuevo->setNombres($valor);
                break;
            case "apellidos":
                $docenteNuevo->setApellidos($valor);
                break;
            case "contratacion":
                $docenteNuevo->setContratacion($valor);
                break;
            case "departamento":
                $docenteNuevo->setDepar($valor);
                break;
            case "cargo":
                if($valor=="ninguno"){
                    $docenteNuevo->setCargo("");
                }else{
                    $docenteNuevo->setCargo($valor);
                }                
                break;            
            default:
                break;
        }
        
        try{
            if($campo=="departamento"){
                if($id_departamento=="todos" || ($id_departamento!="todos" && $id_departamento==$valor)){
                    ManejadorPersonal::modificarDocente($docenteActual,$docenteNuevo);
                    $respuesta = array('status'=>'ok','msg'=>'¡Docente Modificado!');
                    print json_encode($respuesta);
                }else{
                    $respuesta = array('status'=>'error','msg'=>'¡No está autorizado para cambiar de departamento a un docente!');
                    print json_encode($respuesta);
                }
            }else{
                ManejadorPersonal::modificarDocente($docenteActual,$docenteNuevo);
                $respuesta = array('status'=>'ok','msg'=>'¡Docente Modificado!');
                print json_encode($respuesta);
            }            
        }catch(Exception $ex){
            $respuesta = array('status'=>'error','msg'=>$ex->getMessage());
            print json_encode($respuesta);            
        }       

    }
}
