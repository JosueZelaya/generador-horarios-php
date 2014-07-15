<?php

chdir(dirname(__FILE__));
require_once 'config.php';
require_once '../../reglas_negocio/ManejadorAgrupaciones.php';

if($_POST){
    if($_POST['pk'] && $_POST['name'] && $_POST['value']){       
        
        $id = $_POST['pk'];
        $campo = $_POST['name'];
        $valor = $_POST['value'];               
        if($campo=="num_grupos" || $campo=="alumnos_nuevos" || $campo =="otros_alumnos" || $campo == "alumnos_grupo" || $campo == "num_grupos_l" || $campo == "num_grupos_d"){               
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
            }            
            echo json_encode($respuesta);
        }else{
            $respuesta = array('status'=>'error','msg'=>'¡No se permite modificar ese campo!');
            echo json_encode($respuesta);
        }     
        
    }
}
