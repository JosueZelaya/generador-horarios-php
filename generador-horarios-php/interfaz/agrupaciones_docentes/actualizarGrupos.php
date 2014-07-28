<?php

chdir(dirname(__FILE__));
include 'config.php';
require_once '../../reglas_negocio/Grupo.php';
require_once '../../reglas_negocio/ManejadorGrupos.php';

if(isset($_GET)){
    if(isset($_GET['grupos'])){        
        $arrayMaterias = utf8_encode($_GET['grupos']);                
        $arrayMaterias = json_decode($arrayMaterias,true);                        
        if($arrayMaterias!=NULL){
            if(count($arrayMaterias[0])<=0){
                echo json_encode("No se recibió ningún grupo!");
            }else{
                $arrayMaterias = $arrayMaterias[0];
                $grupos = array();                
                try{
                    for ($i = 1; $i <= count($arrayMaterias); $i++) {    
                        if($arrayMaterias[$i]["docentes"]=="" || $arrayMaterias[$i]["docentes"]==NULL){                            
                            throw new Exception("Debe agregar docentes a todos los grupos!");
                        }else{
//                            $docentes = explode(",", $arrayGrupos[$i]['docentes']);         
                            $usuarios = $arrayMaterias[$i]['docentes'];
                            $materia = new Grupo();
                            $materia->setAgrup($arrayMaterias[$i]["agrupacion"]);
                            $materia->setDocentes($usuarios);
                            $materia->setId_grupo($arrayMaterias[$i]["id"]);
                            $materia->setTipo($arrayMaterias[$i]["tipo"]);
                            $grupos[]=$materia;                    
                        }                    
                    }                
                    ManejadorGrupos::actualizarGrupos($grupos, $año, $ciclo);                    
                    echo json_encode("exito");
                }catch(Exception $e){
                    echo json_encode($e->getMessage());
                }
            }
        }
    }
}