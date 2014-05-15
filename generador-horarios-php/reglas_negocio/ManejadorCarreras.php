<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManejadorCarreras
 *
 * @author arch
 */

require_once '../acceso_datos/Conexion.php';

abstract class ManejadorCarreras {
        
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
        return $respuesta['id_carrera'];
    }    
    
}
