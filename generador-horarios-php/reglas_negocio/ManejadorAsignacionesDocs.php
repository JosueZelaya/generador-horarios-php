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
    
    public static function obtenerAsignacionesDeAgrupNueva($todos_docentes,$año,$ciclo,$id_agrup){
        $asignaciones = array();
        $respuesta = Conexion::consulta('SELECT id_docente,num_grupos FROM docentes_agrupaciones WHERE "año"='.$año.' and ciclo='.$ciclo.' and id_agrupacion='.$id_agrup);
        while ($fila = pg_fetch_array($respuesta)){
            $asignacion = new AsignacionDocente(ManejadorDocentes::obtenerDocente($fila[0], $todos_docentes),$fila[1]);
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
