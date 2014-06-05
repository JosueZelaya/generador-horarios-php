<?php

/**
 * Description of ManejadorAgrupaciones
 *
 * @author arch
 */

include_once '../acceso_datos/Conexion.php';
require_once 'Agrupacion.php';
include_once 'ManejadorAsignacionesDocs.php';

abstract class ManejadorAgrupaciones {
 
    /**
     * Este método devuelve todas las agrupaciones existentes en la base de datos que corresponden al año y al ciclo al cual se generara un horario.
     * @return \Agrupacion = array de tipo agrupacion
     */
    public static function getAgrupaciones($todos_docentes,$año,$ciclo){
        $agrupaciones = array();
        $sql_consulta = 'SELECT * FROM agrupacion WHERE id_agrupacion IN (SELECT id_agrupacion FROM agrupacion_historial WHERE "año"='.$año.' and ciclo='.$ciclo.')';
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $num_alumnos = $fila['alumnos_nuevos'] + $fila['otros_alumnos'];
            $agrupacion = new Agrupacion($fila['id_agrupacion'],$fila['num_grupos'],$num_alumnos);
            $agrupacion->setAsignaciones(ManejadorAsignacionesDocs::obtenerAsignacionesDeAgrupNueva($todos_docentes, $año, $ciclo, $agrupacion->getId()));
            $agrupaciones[] = $agrupacion;
        }
        return $agrupaciones;
    }

    /**
     * 
     * Devuelve la agrupación que cumpla con el criterio a evaluar.
     * 
     * @param type $id_agrup = id de la agrupacion buscada
     * @param type $agrupaciones = Todas las agrupaciones en las cuales buscar.
     * @return null,agrupacion = Devuelve la agrupacion que coincida con el criterio, si ninguna coincide devuelve null
     */
    public static function getAgrupacion($id_agrup,$agrupaciones){
        for ($index = 0; $index < count($agrupaciones); $index++) {
            if($agrupaciones[$index]->getId()==$id_agrup){
                return $agrupaciones[$index];
            }
        }      
        return null;
    }
    
    /**
     * Sirve para conocer cuáles son las agrupaciones que una carrera posee
     * 
     * @param type $carrera = la carrera de la que se desea conocer su agrupaciones
     * @return type = el array que contiene a las agrupaciones de la carrera.
     */
    public static function obtenerAgrupacionesDeCarrera($carrera){
        $ids=array();
        $sql_consulta = "select ma.id_agrupacion from materia_agrupacion as ma join materias as m on ma.cod_materia = m.cod_materia join carreras as c on c.id_carrera = m.id_carrera where c.nombre_carrera='".$carrera."'";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){                        
            $ids[] = $fila['id_agrupacion'];
        }
        return $ids;
    }

}
