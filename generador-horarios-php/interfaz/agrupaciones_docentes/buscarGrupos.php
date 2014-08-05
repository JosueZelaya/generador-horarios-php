<?php

chdir(dirname(__FILE__));
include 'config.php';
require_once '../../reglas_negocio/ManejadorGrupos.php';

//$respuesta="";
if (isset($_GET)){
    if(isset($_GET['agrupacion'])){
        $agrupacion = $_GET['agrupacion'];  
        $grupos = ManejadorGrupos::getGruposDeAgrupacion($año, $ciclo, $agrupacion);        
        $cont=0;
        $arrayGrupos=array();           
        foreach ($grupos as $grupo){                     
            $arrayGrupos[$cont]["id"] = $grupo->getId_grupo();
            $arrayGrupos[$cont]["tipo"] = $grupo->getTipo();
            $arrayGrupos[$cont]["agrupacion"] = $grupo->getAgrup();            
            if($grupo->getDocentes()!=""){                    
                $usuarios = $grupo->getDocentes();
                $arrayGrupos[$cont]["docentes"] = array();
                $arrayGrupos[$cont]["id_docentes"] = array();
                foreach ($usuarios as $usuario) {
                    $arrayGrupos[$cont]["docentes"][] = $usuario->getNombre_completo();
                    $arrayGrupos[$cont]["id_docentes"][] = $usuario->getIdDocente();
                }
            }else{
                $arrayGrupos[$cont]["docentes"]="";
                $arrayGrupos[$cont]["id_docentes"][]="";
            } 
            $cont++;
        }
        echo json_encode($arrayGrupos);
    }       
}
