<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManejadorDias
 *
 * @author arch
 */

include_once '../acceso_datos/conexion.php';
include_once './Dia.php';
include_once './Procesador.php';
include_once './Hora.php';

abstract class ManejadorDias {
    //put your code here
    
    /**
     * Todos los días de la base de datos
     * @return \Dia = array que contiene todos los días en la base de datos.
     */
    public static function getDias(){
        $dias = array();
        $sql_consulta = "SELECT * FROM dias";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){            
            $dia = new Dia();
            $dia->setNombre($fila['nombre_dia']);
            $dias[] = $dia;            
        }
        return $dias;
        
    }
    
    /**
     * Elige un día al azar dentro del array
     * @param type $dias = array de dias
     * @return \Dia = el dia elegido
     */
    public static function elegirDia($dias){
        $desde = 0;
        $hasta = count($dias)-2;    //Le restamos dos para que no tome en cuenta el Sábado
        $dia = Procesador::getNumeroAleatorio($desde, $hasta);
        return $dias[$dia];
    }

    /**
     * Devuelve un día diferente a los ya usados
     * 
     * @param type $dias = los dias posibles
     * @param type $diasUsados = los dias usados
     * @return \Dia,null El dia elegido, si ya no hay dias disponibles devuelve null
     */
    public static function elegirDiaDiferente($dias,$diasUsados){
        //Si ya se usaron todos los días entonces no seguimos buscando y devolvemos null
        //Le restamos 1 para que no tome en cuenta el día Sábado
        if(count($dias)-1==count($diasUsados)){
            return null;
        }
        $elegido=null;
        do {
            $elegido = ManejadorDias::elegirDia($dias);
        } while (!ManejadorDias::sonDiferentes($elegido, $diasUsados));
        return $elegido;
    }
    
    /**
     * Para saber si un dia es diferente a los demas dias del array
     * 
     * @param type $elegido = el dia que se ha elegido para probar
     * @param type $dias = los dias contra los que se comparara
     * @return boolean = true si el día es diferente a los del array, false en caso contrario
     */
    public static function sonDiferentes($elegido,$dias){
        for ($index = 0; $index < count($dias); $index++) {
            if(ManejadorDias::esIgual($elegido, $dias[$index])){
                return false;
            }
        }
        return true;
    }
    
    /**
     * Campara si dos días son iguales
     * 
     * @param type $dia1 = dia a comparar
     * @param type $dia2 = dia a comparar
     * @return boolean = true si son iguales, false en caso contrario
     */
    public static function esIgual($dia1,$dia2){
        if(strcmp($dia1->getNombre(), $dia2->getNombre())==0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Devuelve las horas pertenecientes a determinado dia
     * 
     * @param type $nombre_dia = el nombre del dia en que se quieren obtener las horas
     * @return \Hora = array con las horas
     */
    public static function obtenerHorasDia($nombre_dia){
        $horas = array();
        $sql_consulta = "SELECT * FROM horas WHERE id_hora IN (SELECT id_hora FROM dia_horas WHERE nombre_dia='".$nombre_dia."') ORDER BY id_hora";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){            
            $hora = new Hora($fila['id_hora']);
            $hora->setInicio($fila['inicio']);
            $hora->setFin($fila['fin']);
            $horas[] = $hora;
        }
        return $horas;
    }
    
}
