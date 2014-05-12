<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManejadorReservaciones
 *
 * @author alexander
 */
abstract class ManejadorReservaciones {
    
    public static function getTodasReservaciones(){
        $reservas=array();
        $sql_consulta = "select nombre_dia,(select inicio from horas as h where h.id_hora = res.id_hora),(select fin from horas as h where h.id_hora = res.id_hora), cod_aula from reservaciones as res;";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){            
            $reserva = new Reservacion($fila['nombre_dia'], $fila['inicio'], $fila['fin'], $fila['cod_aula']);
            $reservas[] = $reserva;
        }
        return $reservas;
    }
    
    public static function nuevaReserva($dia,$id_hora,$cod_aula){
        $sql_consulta = "INSERT INTO reservaciones values('".$dia."',".$id_hora.",'".$cod_aula."')";
	Conexion::consulta($sql_consulta);
    }

    public static function eliminarReservacion($nombre_dia,$hora_inicio,$cod_aula,$aulas){
        $sql_consulta = "DELETE FROM reservaciones WHERE nombre_dia='".$nombre_dia."' AND id_hora=(select id_hora from horas where inicio='".$hora_inicio."') AND cod_aula='".$cod_aula."'";
	Conexion::consulta($sql_consulta);
        for ($index = 0; $index < count($aulas); $index++) {
            $aula = $aulas[$index];
            $hecho = FALSE;
            if(strcmp($aula->getNombre(),$cod_aula)==0){
                $dia = $aula->getDia($nombre_dia);
                $horas = $dia->getHoras();
                for ($i = 0; $i < count($horas); $i++) {
                    $hora = $horas[$i];
                    if(strcmp($hora->getInicio(),$hora_inicio)==0){
                        $hora->setDisponible(TRUE);
                        $hecho = TRUE;
                        break;
                    }
                }
            }
            if($hecho){
                break;
            }
        }
    }    
    
    public static function asignarRerservaciones($facultad){
        $aulas = $facultad->getAulas();
        $sql_consulta = "SELECT * FROM reservaciones;";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){            
            $hecha = FALSE;
            $aulas = $facultad->getAulas();
            for ($i = 0; $i < count($aulas); $i++) {
                $aula = $aulas[$i];
                if(strcmp($aula->getNombre(), $fila['cod_aula'])){
                    $dia = $aula->getDia($fila['nombre_dia']);
                    if($dia != NULL){
                        $horas = $dia->getHoras();
                        for ($x = 0; $x < count($horas); $x++) {
                            $hora = $horas[$x];
                            if(strcmp($hora->getIdHora(),$fila['id_hora'])){
                                $hora->setDisponible(FALSE);
                                $hecha = TRUE;
                                break;
                            }
                        }
                    }
                }
                if($hecha){
                    break;
                }
            }
        }
    }
    
}
