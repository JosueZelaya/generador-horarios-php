<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManejadorAgrupaciones
 *
 * @author arch
 */

include_once '../acceso_datos/conexion.php';

abstract class ManejadorAgrupaciones {
    //put your code here
    
    /**
     * Este método devuelve todas las agrupaciones existentes en la base de datos.
     * @return \Agrupacion = array de tipo agrupacion
     */
    public static function getAgrupaciones(){
        $agrupaciones = array();
        $sql_consulta = "SELECT * FROM agrupacion";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){            
            $agrupacion = new Agrupacion($fila['id_agrupacion'],$fila['id_depar'],$fila['num_grupos'],$fila['num_alumnos']);            
            $agrupaciones[] = $agrupacion;
        }
        return $agrupaciones;
    }

    /**
     * 
     * Devuelve la agrupación que cumpla con el criterio a evaluar.
     * 
     * @param type $criterio = Es el criterio mediante el cual se filtraran las agrupaciones.
     * @param type $agrupaciones = Todas las agrupaciones en las cuales buscar.
     * @return null,agrupacion = Devuelve la agrupacion que coincida con el criterio, si ninguna coincide devuelve null
     */
    public static function getAgrupacion($criterio,$agrupaciones){      
        if(is_a($criterio,"Materia")){
            for ($index = 0; $index < count($agrupaciones); $index++) {
                if($agrupaciones[$index]->getId()==$criterio->getIdAgrupacion()){
                    return $agrupaciones[$index];
                }
            }    
        }else{
            for ($index = 0; $index < count($agrupaciones); $index++) {
                if($agrupaciones[$index]->getId()==$criterio){
                    return $agrupaciones[$index];
                }
            }    
        }        
        return null;
    }
    
    /**
     * Devuelve el nombre del propietario de la materia
     * ¿QUÉ PASA CUANDO ESTA AGRUPACION TIENE VARIOS PROPIETARIOS?
     * 
     * @param type $id_agrup = el identificador de la agrupación
     * @param type $materias = las materias en las que buscaremos al propietario
     * @return string = El nombre de la materia que es propietaria de la agrupacion
     */
    public static function obtenerNombrePropietario($id_agrup,$materias){
        $propietario="";
        for ($index = 0; $index < count($materias); $index++) {
            if($materias[$index]->getAgrupacion()==$id_agrup){
                return $materias[$index]->getNombre();
            }
        }
        return $propietario;
    }
    
    /**
     * Sirve para conocer a qué departamento pertenece una agrupación
     * 
     * @param type $id_agrup = el id de la agrupación del que se necesita saber su departamento.
     * @param type $agrupaciones = las agrupaciones en las cuales se buscará la que coincida con ese id de agrupación.
     * @return int = el id del departamento
     */
    public static function obtenerIdDepartamento($id_agrup,$agrupaciones){
        $id_departamento=0;
        for ($index = 0; $index < count($agrupaciones); $index++) {
            if($agrupaciones[$index]->getId()==$id_agrup){
                return $agrupaciones->getDepartamento();                
            }
        }
        return $id_departamento;
    }
    
    /**
     * Sirve para conocer cuáles son las agrupaciones que una carrera posee
     * 
     * @param type $carrera = la carrera de la que se desea conocer su agrupaciones
     * @return type = el array que contiene a las agrupaciones de la carrera.
     */
    public static function obtenerAgrupacionesDeCarrera($carrera){
        $ids=array();
        $sql_consulta = "SELECT cm.id_agrupacion FROM carreras_materias as cm JOIN carreras as c ON cm.carreras_id_carrera = c.id_carrera WHERE c.nombre_carrera = '".$carrera."'";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){                        
            $ids[] = $fila['id_agrupacion'];
        }
        return $ids;
    }
    
    /**
     * Sirve para conocer el id de la agrupación de una materia determinada que pertenesca al departamento determinado.
     * 
     * @param type $cod_mat = el código de la materia
     * @param type $id_depar = el identificador del departamento
     * @param type $materias = el aray de materias.
     * @return int = el id de la agrupación.
     */
    public static function obtenerIdAgrupacion($cod_mat,$id_depar,$materias){
        $id_agrup=0;
        for ($index = 0; $index < count($materias); $index++) {
            if($materias[$index]->getCodigo()==$cod_mat && $materias[$index]->getDepartamento()==$id_depar){
                return $materias[$index]->getIdAgrupacion();
            }
        }
        return $id_agrup;
    }

}
