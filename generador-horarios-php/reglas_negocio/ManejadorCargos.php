<?php
/**
 * Description of ManejadorResponsabilidades
 *
 * @author abs
 */

include_once '../acceso_datos/Conexion.php';
include_once 'Cargo.php';

abstract class ManejadorCargos {
    
    public static function obtenerTodosCargos(){
        $sql_consulta = "SELECT * FROM cargo";
        $respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $cargo = new Cargo($fila[0],$fila[1]);
            $cargos[] = $cargo;
        }
        return $cargos;
    }
    
    public static function obtenerCargo($nombre_cargo, $todos_cargos){
        foreach ($todos_cargos as $cargo){
            if(strcmp($cargo->getNombre(), $nombre_cargo) == 0){
                return $cargo;
            }
        }
        return null;
    }
    
}
