<?php
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorAulas.php';

if (isset($_GET)){    
    if(isset($_GET['term'])){
        $buscarComo = $_GET['term'];                              
        $aulas = ManejadorAulas::buscarAulas($buscarComo);
        $arrayAulas = array();
        foreach ($aulas as $aula) {
            $arrayAulas[] = array("value"=>$aula->getNombre());
        }
        echo json_encode($arrayAulas);
    }
}

