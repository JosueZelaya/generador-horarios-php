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
                $docentes = $grupo->getDocentes();
                $arrayGrupos[$cont]["docentes"] = array();
                $arrayGrupos[$cont]["id_docentes"] = array();
                foreach ($docentes as $docente) {
                    $arrayGrupos[$cont]["docentes"][] = $docente->getNombre_completo();
                    $arrayGrupos[$cont]["id_docentes"][] = $docente->getIdDocente();
                }
            }else{
                $arrayGrupos[$cont]["docentes"]="";
                $arrayGrupos[$cont]["id_docentes"][]="";
            } 
            $cont++;
        }
        echo json_encode($arrayGrupos);
//        foreach ($grupos as $grupo) {
//            if($grupo->getTipo()=='1'){
//                $respuesta = $respuesta."<div id='g".$grupo->getId_grupo()."' numGrupo='".$grupo->getId_grupo()."' grupo='".$cont."' tipo='teorico' agrupacion='".$grupo->getAgrup()."' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: rgb(217, 237, 247);color: rgb(0, 136, 204);' class='grupo grupoTeorico row'>Grupo Teórico ".$grupo->getId_grupo()."<br/>";
//                if($grupo->getDocentes()!=""){                    
//                    $docentes = $grupo->getDocentes();
//                    error_log("cantidad docentes ".count($docentes),0);
//                    foreach ($docentes as $docente) {                                            
//                        $respuesta = $respuesta."<div class='btn-info col-lg-11 g".$grupo->getId_grupo()."d".$grupo->getId_grupo()."'>".$docente->getNombre_completo()."</div>";                                            
//                    }
//                }  
//                $respuesta = $respuesta."</div><br/>";
//            }else if($grupo->getTipo()=='2'){
//                $respuesta = $respuesta."<div id='gl".$grupo->getId_grupo()."' numGrupo='".$grupo->getId_grupo()."' grupo='".$cont."' tipo='laboratorio' agrupacion='".$grupo->getAgrup()."' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: #90EE90; color: rgb(0, 136, 0);' class='grupo grupoLaboratorio row'>Grupo Laboratorio ".$grupo->getId_grupo()."<br/>";                        
//                if($grupo->getDocentes()!=""){                    
//                    $docentes = $grupo->getDocentes();
//                    error_log("cantidad docentes ".count($docentes),0);
//                    foreach ($docentes as $docente) {                                            
//                        $respuesta = $respuesta."<div class='btn-info col-lg-11 g".$grupo->getId_grupo()."d".$grupo->getId_grupo()."'>".$docente->getNombre_completo()."</div>";                                            
//                    }
//                }
//                $respuesta = $respuesta."</div><br/>";
//            }else{
//                $respuesta = $respuesta."<div id='gd".$grupo->getId_grupo()."' numGrupo='".$grupo->getId_grupo()."' grupo='".$cont."' tipo='discusion' agrupacion='".$grupo->getAgrup()."' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: #B0C4DE; color: rgb(0, 0, 204);' class='grupo grupoDiscusion row'>Grupo Discusion ".$grupo->getId_grupo()."<br/>";
//                if($grupo->getDocentes()!=""){                    
//                    $docentes = $grupo->getDocentes();
//                    error_log("cantidad docentes ".count($docentes),0);
//                    foreach ($docentes as $docente) {                                            
//                        $respuesta = $respuesta."<div class='btn-info col-lg-11 g".$grupo->getId_grupo()."d".$grupo->getId_grupo()."'>".$docente->getNombre_completo()."</div>";                                            
//                    }
//                }
//                $respuesta = $respuesta."</div><br/>";
//            }            
//            $cont++;
//        }     
    }       
}

//echo $respuesta;