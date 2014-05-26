<?php
/**
 * Description of ManejadorAsignacionesDocs
 *
 * @author abs
 */

include_once '../acceso_datos/Conexion.php';
include_once 'AsignacionDocente.php';
include_once 'ManejadorDocentes.php';
include_once 'Docente.php';

class ManejadorAsignacionesDocs {
    
    public static function obtenerTodasAsignacionesDocs($todos_docentes){
        $asignaciones = array();
        $respuesta = Conexion::consulta("SELECT * FROM docentes_agrupaciones");
        while ($fila = pg_fetch_array($respuesta)){
            $asignacion = new AsignacionDocente(ManejadorDocentes::obtenerDocente($fila[0], $todos_docentes),$fila[1],$fila[2]);
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
