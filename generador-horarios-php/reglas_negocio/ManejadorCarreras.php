<?php

require_once '../acceso_datos/Conexion.php';
include_once 'ManejadorDepartamentos.php';
include_once 'Carrera.php';

abstract class ManejadorCarreras {
    
    public static function getTodasCarreras($todos_depars){
        $sql_consulta = "SELECT * FROM carreras";
        $respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $carrera = new Carrera($fila['id_carrera'],$fila['plan_estudio'],$fila['nombre_carrera'],ManejadorDepartamentos::obtenerDepartamento($fila['id_depar'], $todos_depars));
            $carreras[] = $carrera;
        }
        return $carreras;
    }
    
    public static function getCarrera($codigo,$plan,$carreras){
        foreach ($carreras as $carrera){
            if($carrera->getCodigo() == $codigo && $carrera->getPlanEstudio() == $plan){
                return $carrera;
            }
        }
        return null;
    }
    
    public static function getNombreTodasCarreras(){
        $carreras = array();
        $sql_consulta = "SELECT nombre_carrera FROM carreras ORDER BY nombre_carrera ASC";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $carreras[] = $fila['nombre_carrera'];
        }
        return $carreras;
    }
    
    public static function getNombreTodasCarrerasPorDepartamento($idDepartamento){
        $carreras = array();
        $sql_consulta = "SELECT nombre_carrera FROM carreras WHERE id_depar='".$idDepartamento."' ORDER BY nombre_carrera ASC";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){                        
            $carreras[] = $fila['nombre_carrera'];
        }
        return $carreras;
    }

    public static function getCodigoCarrera($nombreCarrera){
        $sql_consulta = "SELECT id_carrera FROM carreras WHERE nombre_carrera='".$nombreCarrera."'";
        $respuesta = conexion::consulta($sql_consulta);
        $array = pg_fetch_array($respuesta);
        return $array['id_carrera'];
    }
}
