<?php
/**
 * Description of ManejadorResponsabilidades
 *
 * @author abs
 */

chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Cargo.php';

abstract class ManejadorCargos {
    
    public static function obtenerTodosCargos(){
        $sql_consulta = 'SELECT * FROM cargo';
        $respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $cargo = new Cargo($fila['id'],$fila['dia_exento']);
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
