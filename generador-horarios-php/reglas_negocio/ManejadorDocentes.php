<?php

include_once '../acceso_datos/Conexion.php';
include_once 'Docente.php';
include_once 'ManejadorCargos.php';

abstract class ManejadorDocentes{
    
    public static function obtenerTodosDocentes($todos_cargos){
        $sql_consulta = "SELECT id_docente FROM docentes";
        $respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $cargo = ManejadorCargos::obtenerCargo($fila['id_docente'], $todos_cargos);
            $docente = new Docente($fila['id_docente'],$cargo);
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
