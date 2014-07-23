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
            $cargo->setNombre($fila['nombre']);
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
     
    public static function getIdCargo($nombre){
        $consulta = "SELECT * FROM cargo WHERE nombre='$nombre'";
        $respuesta = Conexion::consulta2($consulta);
        return $respuesta['id'];
    }
    
}
