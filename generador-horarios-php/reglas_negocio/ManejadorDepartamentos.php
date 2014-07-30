<?php

/**
 * Description of ManejadorDepartamentos
 *
 * @author abs
 */
chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Departamento.php';

abstract class ManejadorDepartamentos {
    
    public static function agregarDepartamento($departamento){
        if(self::existe($departamento) && self::estaActivo($departamento)){
            throw new Exception("Ya existe ese departamento.");
        }else if(self::existe($departamento) && !self::estaActivo($departamento)){
            $consulta = "UPDATE departamentos SET activo='t' WHERE nombre_depar='".$departamento->getNombre()."';";
            conexion::consulta2($consulta);
        }else{
            $consulta = "INSERT INTO departamentos(nombre_depar,activo) VALUES ('".$departamento->getNombre()."','t')";
            conexion::consulta2($consulta);
        }
    }
    
    public static function eliminarDepartamento($departamento){
        $consulta = "UPDATE departamentos SET activo='f' WHERE id_depar='".$departamento->getId()."'";
        conexion::consulta2($consulta);
    }
    
    public static function existe($departamento){
        $consulta = "SELECT COUNT(*) FROM departamentos WHERE nombre_depar='".$departamento->getNombre()."'";
        $respuesta = conexion::consulta2($consulta);
        if($respuesta['count']>0){
            return true;
        }else{
            return false;
        }
    }
    
    public static function estaActivo($departamento){
        $consulta = "SELECT activo FROM departamentos WHERE nombre_depar='".$departamento->getNombre()."'";
        $respuesta = conexion::consulta2($consulta);
        if($respuesta['activo']=="t"){
            return TRUE;
        }else{
            return FALSE;
        }        
    }
    
    public static function getDepartamentos(){
        $depars = array();
        $respuesta = Conexion::consulta("SELECT * FROM departamentos ORDER BY nombre_depar;");
        while ($fila = pg_fetch_array($respuesta)){
            $depar = new Departamento($fila[0], $fila[1]);
            $depars[] = $depar;
        }
        return $depars;
    }
    
    public static function getDepartamentosConPaginacion($pagina,$numeroResultados){
        $depars = array();
        $pagina = ($pagina-1)*$numeroResultados;
        $sql_consulta = "SELECT * FROM departamentos WHERE activo='t' ORDER BY nombre_depar ASC LIMIT ".$numeroResultados." OFFSET ".$pagina;
        $respuesta = conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $depars[] = new Departamento($fila['id_depar'],$fila['nombre_depar']);
        }
        return $depars;
    }
    
    public static function buscarDepartamento($buscarComo){
        $departamentos = array();
        $consulta = "SELECT * FROM departamentos WHERE nombre_depar iLIKE '%$buscarComo%' AND activo='t' ORDER BY nombre_depar;";
        $respuesta = Conexion::consulta($consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $departamentos[] = new Departamento($fila['id_depar'],$fila['nombre_depar']);
        }
        return $departamentos;
    }
    
    public static function getIdDepar($nombre, $depars){
        $id = 0;
        foreach ($depars as $depar){
            if(strcmp($depar->getNombre(), $nombre)==0){
                $id = $depar->getId();
                break;
            }
        }
        return $id;
    }
    
    public static function obtenerDepartamento($id_depar,$depars){
        foreach ($depars as $depar){
            if($depar->getId() == $id_depar){
                return $depar;
            }
        }
        return null;
    }
}
