<?php
/**
 * Description of ManejadorMaterias
 *
 * @author abs
 */


include_once '../acceso_datos/Conexion.php';
include_once 'Dia.php';
include_once 'Hora.php';
include_once 'Aula.php';
include_once 'Grupo.php';
include_once 'Materia.php';
include_once 'ManejadorAgrupaciones.php';
include_once 'ManejadorCarreras.php';
include_once 'ManejadorGrupos.php';

abstract class ManejadorMaterias {
    
    public static function getTodasMaterias($cicloPar,$año,$todas_agrups,$todas_carreras){
        $materias = array();
        if(!$cicloPar){
            $respuesta = Conexion::consulta("select m.cod_materia,m.nombre_materia,m.uv,m.ciclo_carrera,m.id_carrera,m.plan_estudio,ma.id_agrupacion,(select id_depar from carreras where id_carrera = m.id_carrera and plan_estudio = m.plan_estudio) from materias as m join materia_agrupacion as ma on m.cod_materia = ma.cod_materia and m.plan_estudio = ma.plan_estudio and m.id_carrera = ma.id_carrera WHERE m.ciclo_carrera IN (1,3,5,7,9) AND ma.año=$año AND ma.ciclo=1 ORDER BY m.cod_materia");
        } else {
            $respuesta = Conexion::consulta("select m.cod_materia,m.nombre_materia,m.uv,m.ciclo_carrera,m.id_carrera,m.plan_estudio,ma.id_agrupacion,(select id_depar from carreras where id_carrera = m.id_carrera and plan_estudio = m.plan_estudio) from materias as m join materia_agrupacion as ma on m.cod_materia = ma.cod_materia and m.plan_estudio = ma.plan_estudio and m.id_carrera = ma.id_carrera WHERE m.ciclo_carrera IN (2,4,6,8,10) AND ma.año=$año AND ma.ciclo=1 ORDER BY m.cod_materia");
        }
        while($fila = pg_fetch_array($respuesta)){
            $agrupacion = ManejadorAgrupaciones::getAgrupacion($fila['id_agrupacion'], $todas_agrups);
            $materia = new Materia($fila['cod_materia'],$fila['nombre_materia'],$fila['ciclo_carrera'],$fila['uv'],  ManejadorCarreras::getCarrera($fila['id_carrera'],$fila['plan_estudio'], $todas_carreras),$agrupacion,true);
            if(preg_match('/^Practica Docente/', $materia->getNombre()) == 1){
                $materia->setHorasRequeridas(5);
            }
            $materias[] = $materia;
            $agrupacion->setMateria($materia);
        }
        return $materias;
    }
    
    public static function getMateriasDeCarrera($materias, $carrera){
        $materiasCarrera = array();
        foreach($materias as $materia){
            if(strcmp($materia->getCarrera()->getNombre(), $carrera)==0){
                $materiasCarrera[] = $materia;
            }
        }
        return $materiasCarrera;
    }
    
    public static function obtenerMateriasDeDepartamento($materias, $idDepar){
        $materiasDepar = array();
        foreach($materias as $materia){
            if(strcmp($materia->getCarrera()->getDepartamento()->getId(), $idDepar)==0){
                $materiasDepar[] = $materia;
            }
        }        
        $materiasSinRepetir = ManejadorMaterias::quitarMateriasRepetidas($materiasDepar);
        return $materiasSinRepetir;
    }
    
    public static function quitarMateriasRepetidas($materias){
        $resultado=array();
        for ($index = 0; $index < count($materias); $index++) {
            if($index>0){
                if(strcmp($materias[$index-1]->getNombre(),$materias[$index]->getNombre())==0){
                    
                }else{
                    $resultado[] = $materias[$index];
                }
            }else{
                $resultado[] = $materias[$index];
            }
        }
        return $resultado;
    }
    
    public static function getHorarioMateria($aulas,$cod_materia,$id_depar){
        $horario = array();
        $arrayGrupo = array();                
        foreach ($aulas as $aula){
            $dias = $aula->getDias();                  
            foreach ($dias as $dia){
                $diaAnterior="";
                $horas = $dia->getHoras();
                $grupoAnterior="";
                foreach ($horas as $hora){
                    $grupo = $hora->getGrupo();
                    if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                        $materias = $grupo->getAgrup()->getMaterias();
                        foreach ($materias as $materia){
                            if($materia->getCarrera()->getDepartamento()->getId() == $id_depar && strcmp($materia->getCodigo(),$cod_materia)==0){                                                        
                                if(strcmp($diaAnterior, $dia->getNombre())==0 && strcmp($grupoAnterior, $grupo->getId_grupo())==0){                                
                                    $indiceUltimaHora = count($horario)-1;
                                    $horario[$indiceUltimaHora]['horaFin'] = $hora->getFin();                                    
                                }else{
                                    $arrayGrupo = [
                                    "aula" => $aula->getNombre(),
                                    "dia" => $dia->getNombre(),
                                    "horaInicio" => $hora->getInicio(),                                
                                    "horaFin" => $hora->getFin(),
                                    "grupo" => $grupo->getId_grupo(),
                                    "more" => false,
                                    "cloned" => false];
                                    if(count(ManejadorGrupos::obtenerCodigoPropietario($grupo->getAgrup()->getMaterias()))>1){
                                        $arrayGrupo['more']=true;
                                    }
                                    if(count(array_unique(ManejadorGrupos::getNombreDepartamento($grupo->getAgrup()->getMaterias())))>1){
                                        $arrayGrupo['cloned']=true;
                                    }
                                    $horario[] = $arrayGrupo;                                        
                                    $diaAnterior = $dia->getNombre();                                    
                                    $grupoAnterior = $grupo->getId_grupo();
                                }                                                                                                                                     
                            }
                        }
                    }
                }            
            }
        }
        return $horario;
    }
}
