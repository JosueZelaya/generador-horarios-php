<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManejadorAsignacionesDocs
 *
 * @author abs
 */

include_once '../acceso_datos/Conexion.php';
include_once 'AsignacionDocente.php';

class ManejadorAsignacionesDocs {
    
    public static function obtenerTodasAsignacionesDocs(){
        $asignaciones = array();
        $respuesta = Conexion::consulta("SELECT * FROM docentes_agrupaciones");
        while ($fila = pg_fetch_array($respuesta)){
            $asignacion = new AsignacionDocente($fila[0],$fila[1],$fila[2]);
            $asignaciones[] = $asignacion;
        }
        return $asignaciones;
    }
    
    public static function obtenerAsignacionesDeAgrup($id_agrup,$todas_asignaciones){
        $asignaciones = array();
        foreach ($todas_asignaciones as $asignacion){
            if($asignacion->getId_agrupacion() == $id_agrup){
                $asignaciones[] = $asignacion;
            }
        }
        return $asignaciones;
    }
}
