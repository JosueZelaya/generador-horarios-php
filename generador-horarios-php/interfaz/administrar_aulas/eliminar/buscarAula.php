<?php
chdir(dirname(__FILE__));
require_once "../../../reglas_negocio/ManejadorAulas.php";
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';

if (isset($_GET)){
    ManejadorSesion::sec_session_start();
    if(isset($_GET['term'])){        
        $buscarComo = $_GET['term'];   
        $aulas = ManejadorAulas::buscarAulas($buscarComo);
        $array_aulas=array();
        foreach ($aulas as $aula) {
            $exclusiva="";
            if($aula->isExclusiva()){
                $exclusiva="Sí";
            }else{
                $exclusiva="No";
            }
            $array_aulas[] = array("value"=>$aula->getNombre(),"capacidad"=>$aula->getCapacidad(),"exclusiva"=>$exclusiva);
        }        
        echo json_encode($array_aulas);
    }       
}