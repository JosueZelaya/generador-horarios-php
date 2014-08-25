<?php

chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorAgrupaciones.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

if($_POST){
    if($_POST['pk'] && $_POST['name'] && $_POST['value']){       
        
        $id = $_POST['pk'];
        $campo = $_POST['name'];
        $valor = $_POST['value'];               
        
        if($campo=="num_grupos_l" || $campo=="num_grupos_d" || $campo=="horas_lab_semana" || $campo=="horas_discu_semana" || $campo="lab_dis_alter"){
            try {
                ManejadorAgrupaciones::modificarAgrupacion($id, $campo, $valor,$año,$ciclo);
                ManejadorAgrupaciones::modificarAgrupacion($id, $campo, $valor,$año,$ciclo);
                if($campo=="num_grupos"){
                    $respuesta = array('status'=>'ok','msg'=>'¡Actualizado!');
                }else if($campo=="num_grupos_l"){
                    $respuesta = array('status'=>'ok','msg'=>'¡Actualizado!');
                }else if($campo=="num_grupos_d"){
                    $respuesta = array('status'=>'ok','msg'=>'¡Actualizado!');
                }else if($campo=="alumnos_nuevos"){
                    $respuesta = array('status'=>'actualizar_nuevos','msg'=> $id);
                }else if($campo =="otros_alumnos"){
                    $respuesta = array('status'=>'actualizar_otros','msg'=> $id);
                }else if($campo =="alumnos_grupo"){
                    $respuesta = array('status'=>'actualizar_alumnos_grupo','msg'=> $id);
                }else{
                    $respuesta = array('status'=>'ok','msg'=>'¡Actualizado!');
                }            
                echo json_encode($respuesta);
            }catch (Exception $exc){
                $respuesta = array('status'=>'error','msg'=>"¡".$exc->getMessage()."!");
                echo json_encode($respuesta);
            }
        }else{
            $respuesta = array('status'=>'error','msg'=>'¡No se permite modificar ese campo!');
            echo json_encode($respuesta);
        }     
        
    }
}

if($_GET){
    $id = $_GET['id'];
    $campo = $_GET['campo'];
    $valor = $_GET['valor'];        
    if($campo=="grupos_alternados"){
        $campo = "lab_dis_alter";
        if($valor=='false'){
            $valor='f';
        }else{
            $valor='t';
        }        
        ManejadorAgrupaciones::modificarAgrupacion($id, $campo, $valor,$año,$ciclo);                
    }else{
        $respuesta = array('status'=>'error','msg'=>'¡No se permite modificar ese campo!');
        echo json_encode($respuesta);
    }
    
}
