<?php

include_once '../acceso_datos/Conexion.php';
include_once 'Docente.php';
include_once 'ManejadorCargos.php';

abstract class ManejadorDocentes{
    
    public static function obtenerTodosDocentes($todos_cargos){
        $sql_consulta = "SELECT * FROM docentes";
        $respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            if($fila[3] != NULL){
                $cargo = ManejadorCargos::obtenerCargo($fila[3], $todos_cargos);
            } else{
                $cargo = null;
            }
            $docente = new Docente($fila[0],$cargo);
            $docentes[] = $docente;
        }
        return $docentes;
    }
    
    public static function obtenerDocente($idDocente,$todos_docentes){
        foreach ($todos_docentes as $docente) {
            if($docente->getIdDocente() == $idDocente){
                return $docente;
            }
        }
        return null;
    }
    
}