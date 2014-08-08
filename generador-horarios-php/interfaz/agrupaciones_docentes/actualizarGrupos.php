<?php

chdir(dirname(__FILE__));
require_once '../../reglas_negocio/Grupo.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorGrupos.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

if(isset($_GET)){
    if(isset($_GET['grupos'])){        
        $arrayGrupos = utf8_encode($_GET['grupos']);                
        $arrayGrupos = json_decode($arrayGrupos,true);                        
        if($arrayGrupos!=NULL){
            if(count($arrayGrupos[0])<=0){
                echo json_encode("No se recibió ningún grupo!");
            }else{
                $arrayGrupos = $arrayGrupos[0];
                $grupos = array();                
                try{
                    for ($i = 1; $i <= count($arrayGrupos); $i++) {    
                        if($arrayGrupos[$i]["docentes"]=="" || $arrayGrupos[$i]["docentes"]==NULL){                            
                            throw new Exception("Debe agregar docentes a todos los grupos!");
                        }else{
//                            $docentes = explode(",", $arrayGrupos[$i]['docentes']);         
                            $usuarios = $arrayGrupos[$i]['docentes'];
                            $grupo = new Grupo();
                            $grupo->setAgrup($arrayGrupos[$i]["agrupacion"]);
                            $grupo->setDocentes($usuarios);
                            $grupo->setId_grupo($arrayGrupos[$i]["id"]);
                            $grupo->setTipo($arrayGrupos[$i]["tipo"]);
                            $grupos[]=$grupo;                    

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
