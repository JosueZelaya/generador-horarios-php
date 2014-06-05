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
    
    public static function getTodasReservaciones($año,$ciclo){
        $reservas=array();
        $sql_consulta = 'select id_dia, id_hora, cod_aula from reservaciones WHERE "año"='.$año.' and ciclo='.$ciclo;
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){            
            $reserva = new Reservacion($fila['id_dia'], $fila['id_hora'], $fila['cod_aula']);
            $reservas[] = $reserva;
        }
        return $reservas;
    }
    
    public static function nuevaReserva($id_dia,$id_hora,$cod_aula,$año,$ciclo){
        $sql_consulta = "INSERT INTO reservaciones as Res (Res.id_dia,Res.id_hora,Res.cod_aula,Res.año,Res.ciclo) values(".$id_dia.",".$id_hora.",'".$cod_aula."',$año,$ciclo)";
	Conexion::consulta($sql_consulta);
    }
    
    public static function eliminarReservacion($nombre_dia,$hora_inicio,$cod_aula,$aulas,$año,$ciclo){
        $sql_consulta = "DELETE FROM reservaciones as Res WHERE Res.nombre_dia='".$nombre_dia."' AND Res.id_hora=(select id_hora from horas where inicio='".$hora_inicio."') AND Res.cod_aula='".$cod_aula."' AND Res.año=$año AND Res.ciclo=$ciclo";
	Conexion::consulta($sql_consulta);
        foreach ($aulas as $aula){
            $hecho = FALSE;
            if(strcmp($aula->getNombre(),$cod_aula)==0){
                $dia = $aula->getDia($nombre_dia);
                $horas = $dia->getHoras();
                foreach ($horas as $hora){
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
    
    public static function asignarRerservaciones($reservaciones,$aulas){
        foreach ($reservaciones as $reservacion){            
            $hecha = FALSE;
            foreach ($aulas as $aula){
                if(strcmp($aula->getNombre(), $reservacion->getCod_aula())==0){
                    $dia = $aula->getDias()[$reservacion->getId_dia()-1];
                    $horas = $dia->getHoras();
                    foreach ($horas as $hora){
                        if(strcmp($hora->getIdHora(),$reservacion->getId_hora()) == 0){
                            $hora->setDisponible(FALSE);
                            $hecha = TRUE;
                            break;
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
