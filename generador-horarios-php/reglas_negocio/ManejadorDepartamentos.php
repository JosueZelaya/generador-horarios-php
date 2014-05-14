<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManejadorDepartamentos
 *
 * @author abs
 */

include_once '../acceso_datos/Conexion.php';
include_once 'Departamento.php';

abstract class ManejadorDepartamentos {
    
    public static function getDepartamentos(){
        $depars = array();
        $respuesta = Conexion::consulta("SELECT * FROM departamentos;");
        while ($fila = pg_fetch_array($respuesta)){
            $depar = new Departamento($fila[0], $fila[1]);
            $depars[] = $depar;
        }
        return $depars;
    }
    
    public static function getNombreDepartamentos(){
        $nombre_departamentos = array();
        $respuesta = Conexion::consulta("SELECT nombre_depar FROM departamentos ORDER BY nombre_depar");
        while ($fila = pg_fetch_array($respuesta)){
            $nombre_departamentos[] = $fila[0];
        }
        return $nombre_departamentos;
    }
    
    public static function getNombreDepartamento($idDepartamento,$depars){
        $nombreDepar="";
        foreach ($depars as $depar){
            if($depar->getId() == $idDepartamento){
                $nombreDepar = $depar->getNombre();
                break;
            }
        }
        return $nombreDepar;
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
    
    public static function getIdDepartamentoAgrupacion($idagrupacion,$agrupaciones){
        $id = 0;
        foreach ($agrupaciones as $agrupacion){
            if(strcmp($agrupacion->getId(), $idagrupacion)==0){
                $id = $agrupacion->getDepartamento();
                break;
            }
        }
        return $id;
    }
    
    public static function getIdDepartamento($nombreDepartamento){
        $idDepartamento=0;
        $respuesta = Conexion::consulta("SELECT id_depar FROM departamentos WHERE nombre_depar='$nombreDepartamento'");
        while ($fila = pg_fetch_array($respuesta)){
            $idDepartamento = $fila[0];
        }
        return $idDepartamento;
    }
    
    
    
}
