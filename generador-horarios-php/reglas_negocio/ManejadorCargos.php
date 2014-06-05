<?php
/**
 * Description of ManejadorResponsabilidades
 *
 * @author abs
 */

include_once '../acceso_datos/Conexion.php';
include_once 'Cargo.php';

abstract class ManejadorCargos {
    
    public static function obtenerTodosCargos($año,$ciclo){
        $sql_consulta = 'SELECT * FROM cargo_historial WHERE "año"='.$año.' and ciclo='.$ciclo;
        $respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $cargo = new Cargo($fila['id_cargo'],$fila['id_dia']);
            $cargos[] = $cargo;
        }
        return $cargos;
    }
    
    public static function obtenerCargo($id_cargo, $todos_cargos){
        foreach ($todos_cargos as $cargo){
            if($cargo->getId_cargo() == $id_cargo){
                return $cargo;
            }
        }
        return null;
    }
    
}
