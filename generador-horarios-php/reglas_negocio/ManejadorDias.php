<?php

/**
 * Description of ManejadorDias
 *
 * @author arch
 */
chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Dia.php';
include_once 'Hora.php';

abstract class ManejadorDias {
    
    /**
     * Todos los días de la base de datos
     * @return \Dia = array que contiene todos los días en la base de datos.
     */
    public static function getDias($año,$ciclo){
        $dias = array();
        $sql_consulta = 'SELECT DISTINCT historial.id_dia, dias.nombre_dia FROM dia_hora_historial AS historial JOIN dias ON historial.id_dia = dias.id WHERE "año"='.$año.' and ciclo='.$ciclo.' ORDER BY id_dia ASC';
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){            
            $dia = new Dia($fila['id_dia'],$fila['nombre_dia']);
            $dias[] = $dia;            
        }
        return $dias;
    }
    
    /**
     * Para saber si un dia es diferente a los demas dias del array
     * 
     * @param Dia $elegido = el dia que se ha elegido para probar
     * @param Dia[] $dias = los dias contra los que se comparara
     * @return boolean = true si el día es diferente a los del array, false en caso contrario
     */
    public static function sonDiferentes($elegido,$dias){
        for ($index = 0; $index < count($dias); $index++) {
            if($elegido->getNombre() == $dias[$index]->getNombre()){
                return false;
            }
        }
        return true;
    }
    
    public static function getHorasDia($dia,$año,$ciclo){
        $horas = array();
        $respuesta = Conexion::consulta('select * from horas where id_hora in (select id_hora from dia_hora_historial where id_dia='.$dia.' and "año"='.$año.' and ciclo='.$ciclo.') order by id_hora asc');
        while($fila = pg_fetch_array($respuesta)){
            $hora = new Hora();
            $hora->setIdHora($fila[0]);
            $hora->setInicio($fila[1]);
            $hora->setFin($fila[2]);
            $horas[] = $hora;
        }
        return $horas;
    }
}
