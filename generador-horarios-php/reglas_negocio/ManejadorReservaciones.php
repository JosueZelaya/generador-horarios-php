<?php
/**
 * Description of ManejadorReservaciones
 *
 * @author alexander
 */

chdir(dirname(__FILE__));
require_once '../acceso_datos/Conexion.php';
chdir(dirname(__FILE__));
require_once 'Reservacion.php';

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
    
    public static function nuevaReserva($reservaciones,$año,$ciclo){        
        $dia = $reservaciones[0]->getId_dia();
        $desde = $reservaciones[0]->getId_hora();
        $hasta = $reservaciones[count($reservaciones)-1]->getId_hora();
        $aula = $reservaciones[0]->getCod_aula();
        if(self::estaLibreParaReservar($dia, $desde, $hasta, $aula, $año, $ciclo)){
            $consulta = "INSERT INTO reservaciones (id_dia,id_hora,cod_aula,año,ciclo) VALUES ";
            $cont=1;
            foreach ($reservaciones as $reservacion) {            
                if($cont==count($reservaciones)){
                    $consulta = $consulta."(".$reservacion->getId_dia().",".$reservacion->getId_hora().",'".$reservacion->getCod_aula()."',$año,$ciclo)";
                }else{
                    $consulta = $consulta."(".$reservacion->getId_dia().",".$reservacion->getId_hora().",'".$reservacion->getCod_aula()."',$año,$ciclo),";
                }
                $cont++;
            }
            Conexion::consulta($consulta);
        }else{
            throw new Exception("No se puede realizar la reservación. Asegúrese de que no querer reservar sobre espacio reservado");
        }
    }
    
    public static function liberarHoras($dia,$aula,$hora_inicial,$hora_final,$año,$ciclo){
        $consulta = "DELETE FROM reservaciones WHERE id_dia='$dia' AND cod_aula='$aula' AND id_hora>='$hora_inicial' AND id_hora<='$hora_final' AND año='$año' AND ciclo='$ciclo';";
        Conexion::consulta($consulta);
    }
    
    public static function estaLibreParaReservar($dia,$desde,$hasta,$aula,$año,$ciclo){
        $consulta = "SELECT count(*) FROM reservaciones WHERE id_hora>='$desde' AND id_hora<='$hasta' AND id_dia='$dia' AND cod_aula='$aula' AND año='$año' AND ciclo='$ciclo';";
        $respuesta = Conexion::consulta2($consulta);
        if($respuesta['count']!=0){
            return FALSE;
        }else{
            return TRUE;
        }
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
    
    public static function limpiarReservaciones($aulas){
        foreach ($aulas as $aula) {
            $dias = $aula->getDias();
            foreach ($dias as $dia) {
                $horas = $dia->getHoras();
                foreach ($horas as $hora) {
                    if(!$hora->estaDisponible() && $hora->getGrupo()->getAgrup()==NULL){
                        $hora->setDisponible(TRUE);
                    }
                }
            }
        }        
    }
    
}
