<?php
/**
 * Description of ManejadorMaterias
 *
 * @author abs
 */

chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Dia.php';
include_once 'Hora.php';
include_once 'Aula.php';
include_once 'Grupo.php';
include_once 'Materia.php';
include_once 'ManejadorAgrupaciones.php';
include_once 'ManejadorCarreras.php';
include_once 'ManejadorGrupos.php';
include_once 'ManejadorAulas.php';

abstract class ManejadorMaterias {
    
    public static function getTodasMaterias($cicloPar,$año,$todas_agrups,$todas_carreras,$todas_aulas){
        $preferencias = self::getPreferenciasAulas();
        $materias = array();
        if(!$cicloPar){
            $respuesta = Conexion::consulta("select m.cod_materia,m.nombre_materia,m.uv,m.ciclo_carrera,m.id_carrera,m.plan_estudio,ma.id_agrupacion,m.horas_lab_semana,m.horas_discu_semana,m.lab_dis_alter,m.horas_clase from materias as m natural join materia_agrupacion as ma WHERE m.ciclo_carrera%2!=0 AND ma.año=$año AND ma.ciclo=1 ORDER BY m.cod_materia");
        } else {
            $respuesta = Conexion::consulta("select m.cod_materia,m.nombre_materia,m.uv,m.ciclo_carrera,m.id_carrera,m.plan_estudio,ma.id_agrupacion,m.horas_lab_semana,m.horas_discu_semana,m.lab_dis_alter,m.horas_clase from materias as m natural join materia_agrupacion as ma WHERE m.ciclo_carrera%2=0 AND ma.año=$año AND ma.ciclo=2 ORDER BY m.cod_materia");
        }
        while($fila = pg_fetch_array($respuesta)){
            $prefMateria = self::getPreferenciaAulaMateria($preferencias, $todas_aulas, $fila['cod_materia'], $fila['id_carrera'], $fila['plan_estudio']);
            $agrupacion = ManejadorAgrupaciones::getAgrupacion($fila['id_agrupacion'], $todas_agrups);
            $materia = new Materia($fila['cod_materia'],$fila['nombre_materia'],$fila['ciclo_carrera'],$fila['uv'],  ManejadorCarreras::getCarrera($fila['id_carrera'],$fila['plan_estudio'], $todas_carreras),$agrupacion,$fila['horas_clase'],$fila['horas_lab_semana'],$fila['horas_discu_semana'],$fila['lab_dis_alter'],$prefMateria['gt'],$prefMateria['gl'],true);
            $materias[] = $materia;
            $agrupacion->setMateria($materia);
        }
        return $materias;
    }
    
    public static function getPreferenciaAulaMateria($todas_prefs,$todas_aulas,$cod_materia,$id_carrera,$plan_estudio){
        $aulas_gt = array();
        $aulas_lab = array();
        foreach ($todas_prefs as $preferencia){
            if($preferencia['cod_carrera']==$id_carrera && $preferencia['plan_estudio']==$plan_estudio && $preferencia['cod_materia']==$cod_materia){
                if(preg_match("/^(1|3)$/", strval($preferencia['tipo_grupo']))){
                    $aulas_gt['aulas'][] = ManejadorAulas::getAula($todas_aulas, $preferencia['cod_aula']);
                    $aulas_gt['exclusiv'] = $preferencia['exclusiv_aula'];
                } else{
                    $aulas_lab['aulas'][] = ManejadorAulas::getAula($todas_aulas, $preferencia['cod_aula']);
                    $aulas_lab['exclusiv'] = true;
                }
            }
        }
        return array("gt"=>$aulas_gt,"gl"=>$aulas_lab);
    }

    public static function getPreferenciasAulas(){
        $consulta = Conexion::consulta("select * from info_materia_aula natural join lista_materia_aulas");
        $respuesta = pg_fetch_all($consulta);
        return $respuesta;
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
    
    /**
     * 
     * @param Aula[] $aulas
     * @param String $cod_materia
     * @param Integer $id_depar
     * @return array
     */
    public static function getHorarioMateria($aulas,$cod_materia,$id_depar){
        $horario = array();
        foreach ($aulas as $aula){
            $dias = $aula->getDias();                  
            foreach ($dias as $dia){
                $horas = $dia->getHoras();
                $grupoAnterior=null;
                foreach ($horas as $hora){
                    $grupo = $hora->getGrupo();
                    if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                        $materias = $grupo->getAgrup()->getMaterias();
                        foreach ($materias as $materia){
                            if($materia->getCarrera()->getDepartamento()->getId() == $id_depar && strcmp($materia->getCodigo(),$cod_materia)==0){                                                        
                                if($grupoAnterior!=null && $grupoAnterior===$grupo){
                                    $indiceUltimaHora = count($horario[$grupo->getTipo()])-1;
                                    $horario[$grupo->getTipo()][$indiceUltimaHora]['horaFin'] = $hora->getFin();                                    
                                }else{
                                    $arrayGrupo = [
                                    "aula" => $aula->getNombre(),
                                    "dia" => $dia->getNombre(),
                                    "horaInicio" => $hora->getInicio(),                                
                                    "horaFin" => $hora->getFin(),
                                    "grupo" => $grupo->getId_grupo(),
                                    "tipo" => $grupo->getTipo(),
                                    "more" => false,
                                    "cloned" => false];
                                    $propietarios = ManejadorGrupos::obtenerCodigoPropietario($grupo->getAgrup()->getMaterias());
                                    if(count(array_unique($propietarios))>1){
                                        $arrayGrupo['cloned']=true;
                                    } elseif(count($propietarios)>1){
                                        $arrayGrupo['more']=true;
                                    }
                                    $horario[$grupo->getTipo()][] = $arrayGrupo;
                                    $grupoAnterior = $hora->getGrupo();
                                }
                                break;
                            }
                        }
                    }
                }
            }            
        }
        return $horario;
    }
    
    public static function buscarMateriaParaAgrupar($materia,$ciclo,$departamento){
        if($ciclo=="impar"){
            if($departamento=="todos"){
                $consulta = "SELECT DISTINCT nombre_materia FROM materias WHERE nombre_materia iLIKE '$materia%' AND ciclo_carrera IN (1,3,5,7,9,11,13,15,17,19,21,23,25) ORDER BY nombre_materia LIMIT 15;";
            }else{
                $consulta = "SELECT DISTINCT m.nombre_materia FROM materias AS m JOIN carreras AS c ON m.id_carrera=c.id_carrera WHERE m.nombre_materia iLIKE '%$materia%' AND c.id_depar='$departamento' AND m.ciclo_carrera IN (1,3,5,7,9,11,13,15,17,19,21,23,25) ORDER BY m.nombre_materia LIMIT 15;";
            }            
        }else{            
            if($departamento=="todos"){
                $consulta = "SELECT DISTINCT nombre_materia FROM materias WHERE nombre_materia iLIKE '$materia%' AND ciclo_carrera IN (2,4,6,8,10,12,14,16,18,20,22,24,26) ORDER BY nombre_materia LIMIT 15;";
            }else{
                $consulta = "SELECT DISTINCT m.nombre_materia FROM materias AS m JOIN carreras AS c ON m.id_carrera=c.id_carrera WHERE m.nombre_materia iLIKE '%$materia%' AND c.id_depar='$departamento' AND m.ciclo_carrera IN (2,4,6,8,10,12,14,16,18,20,22,24,26) ORDER BY m.nombre_materia LIMIT 15;";
            }            
        }        
        $respuesta = conexion::consulta($consulta);
        $materias = array();
        while ($row = pg_fetch_array($respuesta)){
            $materias[] = array("value"=>$row['nombre_materia']);
        }
        return $materias;
    }
       
    public static function getMateriasParaAgrupar($materia,$ciclo,$departamento){
        if($ciclo=="impar"){
            if($departamento=="todos"){
                $consulta = "SELECT DISTINCT m.cod_materia,m.id_carrera,m.plan_estudio,m.nombre_materia,m.tipo_materia,m.ciclo_carrera,m.uv,c.id_carrera,c.id_depar,c.nombre_carrera,d.nombre_depar FROM materias as m JOIN carreras as c on m.id_carrera=c.id_carrera AND m.plan_estudio=c.plan_estudio JOIN departamentos as d ON c.id_depar=d.id_depar WHERE m.nombre_materia='".$materia."' AND m.ciclo_carrera IN (1,3,5,7,9,11,13,15,17,19,21,23,25) ORDER BY m.cod_materia,m.id_carrera;";
            }else{
                $consulta = "SELECT DISTINCT m.cod_materia,m.id_carrera,m.plan_estudio,m.nombre_materia,m.tipo_materia,m.ciclo_carrera,m.uv,c.id_carrera,c.id_depar,c.nombre_carrera,d.nombre_depar FROM materias as m JOIN carreras as c on m.id_carrera=c.id_carrera AND m.plan_estudio=c.plan_estudio JOIN departamentos as d ON c.id_depar=d.id_depar WHERE m.nombre_materia='".$materia."' AND c.id_depar='$departamento' AND m.ciclo_carrera IN (1,3,5,7,9,11,13,15,17,19,21,23,25) ORDER BY m.cod_materia,m.id_carrera;";
            }
        }else{
            if($departamento=="todos"){
                $consulta = "SELECT DISTINCT m.cod_materia,m.id_carrera,m.plan_estudio,m.nombre_materia,m.tipo_materia,m.ciclo_carrera,m.uv,c.id_carrera,c.id_depar,c.nombre_carrera,d.nombre_depar FROM materias as m JOIN carreras as c on m.id_carrera=c.id_carrera AND m.plan_estudio=c.plan_estudio JOIN departamentos as d ON c.id_depar=d.id_depar WHERE m.nombre_materia='".$materia."' AND m.ciclo_carrera IN (2,4,6,8,10,12,14,16,18,20,22,24,26) ORDER BY m.cod_materia,m.id_carrera;";
            }else{
                $consulta = "SELECT DISTINCT m.cod_materia,m.id_carrera,m.plan_estudio,m.nombre_materia,m.tipo_materia,m.ciclo_carrera,m.uv,c.id_carrera,c.id_depar,c.nombre_carrera,d.nombre_depar FROM materias as m JOIN carreras as c on m.id_carrera=c.id_carrera AND m.plan_estudio=c.plan_estudio JOIN departamentos as d ON c.id_depar=d.id_depar WHERE m.nombre_materia='".$materia."' AND c.id_depar='$departamento' AND m.ciclo_carrera IN (2,4,6,8,10,12,14,16,18,20,22,24,26) ORDER BY m.cod_materia,m.id_carrera;";
            }    
        }  
        $respuesta = conexion::consulta($consulta);
        while ($row = pg_fetch_array($respuesta)){
            $materia = new MateriaAgrupacion();     
            $materia->setCodigo($row['cod_materia']);
            $materia->setNombre($row['nombre_materia']);
            $materia->setPlan_estudio($row['plan_estudio']);
            $materia->setCarrera(new Carrera($row['id_carrera'],"",$row['nombre_carrera'],""));
            $materia->setDepartamento(new Departamento($row['id_depar'], $row['nombre_depar']));            
            $materias[] = $materia;
        }                   			
        return $materias;
    }
    
    //Devuelve el número de materias que existen en el departamento para un ciclo par o impar
    public static function getCuantasMateriasExisten($idDepartamento,$ciclo){
        $materias = ManejadorMaterias::getMaterias($idDepartamento);
        $materiasFiltradas = ManejadorMaterias::filtrarPorCiclo($materias, $ciclo);
        return count($materiasFiltradas);
    }
    
}
