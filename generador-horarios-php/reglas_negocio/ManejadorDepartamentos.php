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
    
    /*
     * QUITA LOS DEPARTAMENTOS QUE SON ESPECIALES PARA QUE NO SE MUESTREN AL USUARIO.
     */
    public static function quitarDepartamentosEspeciales($departamentos){
        $departamentosFiltrados=array();        
        $departamentosEspeciales=["PLAN ESPECIAL","DOCENTES DE RESPALDO","POSTGRADO"];
        foreach ($departamentos as $dpt) {
            $es_especial=FALSE;
            $nombre_depar = strtoupper($dpt->getNombre());
            foreach ($departamentosEspeciales as $dpt_especial) {
                if($nombre_depar==strtoupper($dpt_especial)){
                    $es_especial=TRUE;
                }
            }
            if(!$es_especial){
                $departamentosFiltrados[] = $dpt;
            }
        }
        return $departamentosFiltrados;
    }
    
    public static function modificarDepartamento($id,$campo,$valor){
        $consulta = "";
        if($campo=="nombre"){
            $consulta = "UPDATE departamentos SET nombre_depar='".$valor."' WHERE id_depar='".$id."'";
        }else if($campo=="activo"){
            if($valor=='t' || $valor=='f'){
                $consulta = "UPDATE departamentos SET activo='".$valor."' WHERE id_depar='".$id."'";
            }else{
                throw new Exception("El valor ingresado no es válido.");
            }            
        }else{
            throw new Exception("No se permite modificar ese campo.");
        }
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
            $depar->setActivo($fila['activo']);
            $depars[] = $depar;
        }
        return $depars;
    }
    
    public static function getDepartamentosConPaginacion($pagina,$numeroResultados,$activos){
        $depars = array();
        $pagina = ($pagina-1)*$numeroResultados;
        $consulta = "";
        if($activos=="activos"){
            $consulta = "SELECT * FROM departamentos WHERE activo='t' ORDER BY nombre_depar ASC LIMIT ".$numeroResultados." OFFSET ".$pagina;
        }else if($activos=="desactivados"){
            $consulta = "SELECT * FROM departamentos WHERE activo='f' ORDER BY nombre_depar ASC LIMIT ".$numeroResultados." OFFSET ".$pagina;    
        }else{
            $consulta = "SELECT * FROM departamentos ORDER BY nombre_depar ASC LIMIT ".$numeroResultados." OFFSET ".$pagina;
        }
        $respuesta = conexion::consulta($consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $depar = new Departamento($fila['id_depar'],$fila['nombre_depar']);
            $depar->setActivo($fila['activo']);
            $depars[] = $depar;
        }
        return $depars;
    }
    
    public static function buscarDepartamento($buscarComo,$activos){
        $departamentos = array();
        if($activos=="activos"){
            $consulta = "SELECT * FROM departamentos WHERE nombre_depar iLIKE '%$buscarComo%' AND activo='t' ORDER BY nombre_depar;";
        }else if($activos=="desactivados"){
            $consulta = "SELECT * FROM departamentos WHERE nombre_depar iLIKE '%$buscarComo%' AND activo='f' ORDER BY nombre_depar;";
        }else{
            $consulta = "SELECT * FROM departamentos WHERE nombre_depar iLIKE '%$buscarComo%' ORDER BY nombre_depar;";
        }        
        $respuesta = Conexion::consulta($consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $depar = new Departamento($fila['id_depar'],$fila['nombre_depar']);
            $depar->setActivo($fila['activo']);
            $departamentos[] = $depar;
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
