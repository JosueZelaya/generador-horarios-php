<?php

/**
 * Description of ManejadorDepartamentos
 *
 * @author abs
 */
chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Departamento.php';

abstract class ManejadorDepartamentos {
    
    public static function getDepartamentos(){
        $depars = array();
        $respuesta = Conexion::consulta("SELECT * FROM departamentos WHERE id_depar<12 ORDER BY nombre_depar;");
        while ($fila = pg_fetch_array($respuesta)){
            $depar = new Departamento($fila[0], $fila[1]);
            $depars[] = $depar;
        }
        return $depars;
    }
    
    public static function getIdDepar($nombre, $depars){
        $id = 0;
        foreach ($depars as $depar){
            if(strcmp($depar->getNombre(), $nombre)==0){
                $id = $depar->getId();
                break;
            }
        }
        return $id;
    }
    
    public static function obtenerDepartamento($id_depar,$depars){
        foreach ($depars as $depar){
            if($depar->getId() == $id_depar){
                return $depar;
            }
        }
        return null;
    }
}
