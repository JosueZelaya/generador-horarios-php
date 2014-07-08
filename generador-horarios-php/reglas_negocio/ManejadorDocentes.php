<?php

chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Docente.php';
include_once 'ManejadorCargos.php';

abstract class ManejadorDocentes{
    
    public static function obtenerTodosDocentes($todos_cargos){
        $sql_consulta = "SELECT d.id_docente,d.contratacion,d.id_depar,dc.id_cargo FROM docentes as d left join docente_cargo as dc on d.id_docente = dc.id_docente";
        $respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $docente = new Docente($fila['id_docente'],$fila['contratacion']);
            if(isset($fila['id_cargo'])){
                $docente->setCargo(ManejadorCargos::obtenerCargo($fila['id_cargo'], $todos_cargos));
            }
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
    
    public static function clasificarDocentes($todos_docentes){
        foreach ($todos_docentes as $docente){
            if(preg_match('/(MT|CT)$/', $docente->getContratacion()) == 1){
                $docentes[0][] = $docente;
            } else{
                $docentes[1][] = $docente;
            }
        }
        return $docentes;
    }
    
    public static function buscarDocentes($buscarComo,$idDepartamento){
        $datos = array();
        $materias = array();
        if($idDepartamento=="todos"){
//            $consulta = "SELECT * FROM docentes WHERE (nombres iLIKE '%$buscarComo%' OR apellidos iLIKE '%$buscarComo%') LIMIT 15";
            $consulta = "SELECT * FROM docentes WHERE (nombres || ' ' || apellidos) iLIKE '%$buscarComo%' LIMIT 15;";
        }else{
//            $consulta = "SELECT * FROM docentes WHERE (nombres iLIKE '%$buscarComo%' OR apellidos iLIKE '%$buscarComo%') AND id_depar='$idDepartamento' LIMIT 15";
            $consulta = "SELECT * FROM docentes WHERE (nombres || ' ' || apellidos) iLIKE '%$buscarComo%' AND id_depar='$idDepartamento' LIMIT 15;";
        }        
        $respuesta = conexion::consulta($consulta);
        while ($row = pg_fetch_array($respuesta)){
            $datos[] = array("value"=>$row['nombres']." ".$row['apellidos'],
                            "id"=>$row['id_docente']);
        }        
        return $datos;
    }
    
}
