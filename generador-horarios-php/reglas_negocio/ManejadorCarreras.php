<?php

chdir(dirname(__FILE__));
require_once '../acceso_datos/Conexion.php';
include_once 'ManejadorDepartamentos.php';
include_once 'Carrera.php';

abstract class ManejadorCarreras {
    
    public static function getTodasCarreras($todos_depars){
        $sql_consulta = "SELECT * FROM carreras WHERE id_depar<12 ORDER BY nombre_carrera";
        $respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $departamento = ManejadorDepartamentos::obtenerDepartamento($fila['id_depar'], $todos_depars);
            $carrera = new Carrera($fila['id_carrera'],$fila['plan_estudio'],$fila['nombre_carrera'],$departamento);
            $carreras[] = $carrera;
            $departamento->addCarrera($carrera);
        }
        return $carreras;
    }
    
    public static function getCarrerasDeDepartamento($id_departamento){
        $sql_consulta = "SELECT * FROM carreras NATURAL JOIN departamentos WHERE id_depar='$id_departamento' ORDER BY nombre_carrera";
        $respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $carrera = new Carrera($fila['id_carrera'],$fila['plan_estudio'],$fila['nombre_carrera'],new Departamento($fila['id_depar'],$fila['nombre_depar']));
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
        $sql_consulta="";
        if(strtoupper($idDepartamento)=="TODOS" || $idDepartamento==""){
            $sql_consulta = "SELECT nombre_carrera FROM carreras ORDER BY nombre_carrera ASC";
        }else{
            $sql_consulta = "SELECT nombre_carrera FROM carreras WHERE id_depar='".$idDepartamento."' ORDER BY nombre_carrera ASC";
        }        
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
