<?php

/**
 * Description of ManejadorAulas
 *
 * @author arch
 */
chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Dia.php';
include_once 'Hora.php';
include_once 'Aula.php';
include_once 'Grupo.php';
include_once 'ManejadorAulas.php';
include_once 'Procesador.php';
include_once 'ManejadorGrupos.php';

abstract class ManejadorAulas {
   
    /**
     * Devuelve todas las aulas de la facultad por capacidad ascendente
     * 
     * @return Aula[] $aulas
     */
    public static function getTodasAulas(){
        $aulas = array();
        $sql_consulta = "SELECT * FROM aulas ORDER BY capacidad ASC";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $aula = new Aula();
            $aula->setNombre($fila['cod_aula']);
            $aula->setCapacidad($fila['capacidad']);
            $aula->setDisponible(TRUE);
            $aula->setExclusiva($fila['exclusiva']);
            $aulas[] = $aula;
        }
        return $aulas;
    }
    
    /**
     * 
     * @param Aula[] $todas_aulas = Todas las aulas de la facultad
     * @param String $nombre_aula = Nombre del aula que se busca
     * @return null = Si no se encuentra el aula buscada
     */
    public static function getAula($todas_aulas, $nombre_aula){
        foreach ($todas_aulas as $aula){
            if(strcmp($aula->getNombre(),$nombre_aula)==0){
                return $aula;
            }
        }
        return null;
    }
    
    /** Determinar si un grupo de horas es permisible para una determinada aula en un determinado dia
     * 
     * @param Hora[] $horas Horas de un determinado dia en una determinada aula
     * @param Integer $desde Limite inferior de bloque de horas a aprobar
     * @param Integer $hasta Limite superior de bloque de horas a aprobar
     */
    public static function aprobarHorarioEnAula($horas,$desde,$hasta){
        if ($desde > count($horas)-1 || $desde < $horas[0]->getIdHora() || $hasta > count($horas)){
            return false;
        }
        return true;
    }
    
    /**
     * Devuelve las aulas capaces de albergar a la cantidad de alumnos especificada
     * 
     * @param Aula $aulas = las aulas entra las cuales elegir
     * @param Integer $num_alumnos = cantidad de alumnos especificada
     * @return Aula[] $aulasSeleccionadas = las aulas seleccionadas que cumplen criterio de capacidad
     */
    public static function obtenerAulasPorCapacidad($aulas,$num_alumnos){
        $aulasSeleccionadas = array();
        foreach ($aulas as $aula){
            $capacidad = $aula->getCapacidad();
            if($capacidad >= $num_alumnos && !$aula->isExclusiva()){
                $aulasSeleccionadas[] = $aula;
            }
        }
        return $aulasSeleccionadas;
    }
    
    public static function getRangoHoras($horas,$grupo){
        $grupoAnterior="";
        $rangoHoras=["inicio"=>"","fin"=>""];        
        foreach ($horas as $hora) {
            $grupoActual = $hora->getGrupo();
            if($grupoActual===$grupo){
                if($grupoAnterior===$grupo){                                    
//                    $rangoHoras['fin'] = $hora->getFin();
                      $rangoHoras['fin'] = $hora->getIdHora();   
                }else{                    
//                    $rangoHoras['inicio'] = $hora->getInicio();
//                    $rangoHoras['fin'] = $hora->getFin();
                      $rangoHoras['inicio'] = $hora->getIdHora(); 
                    $grupoAnterior = $grupo;
                }
            }            
        }
        return $rangoHoras;
    }

    /**
     * Devuelve el horario de la semana para un aula específica
     * 
     * @param Aula[] $aulas = Las aulas
     * @param String $aula = El nombre del aula
     * @param array $tabla Matriz que contendra el horario
     * @return array = una tabla que representa al horario de la semana
     */
    public static function getHorarioEnAula($aulas,$aula,$tabla){
        for ($i = 0; $i < count($aulas); $i++) {
            if(strcmp($aulas[$i]->getNombre(),$aula)==0){
                $dias = $aulas[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];
                        $grupo = $hora->getGrupo();
                        if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                            $cod_materia = ManejadorGrupos::obtenerCodigoPropietario($grupo->getAgrup()->getMaterias());
                            $nombres = ManejadorGrupos::obtenerNombrePropietario($grupo->getAgrup()->getMaterias());
                            $nombre = $nombres[0];
                            $departamento = ManejadorGrupos::getNombreDepartamento($grupo->getAgrup()->getMaterias());
                            if(count(array_unique($cod_materia))>1){
                                $nombre .= ' (Clonada)';
                            }
                            if($grupo->getTipo()=='TEORICO'){
                                $texto = $cod_materia[0]."<br/> GT: ".$grupo->getId_grupo();
                            } elseif($grupo->getTipo()=='DISCUSION'){
                                $texto = $cod_materia[0]."<br/> GD: ".$grupo->getId_grupo();
                            } elseif($grupo->getTipo()=='LABORATORIO'){
                                $texto = $cod_materia[0]."<br/> GL: ".$grupo->getId_grupo();
                            }
                            $rango = ManejadorAulas::getRangoHoras($horas, $grupo);
                            $array = [
                                "texto" => $texto,
                                "nombre" => $nombre,
                                "codigo" => implode("-", $cod_materia),
                                "grupo" => $grupo->getId_grupo(),
                                "departamento" => $departamento[0],
                                "inicioBloque" => $rango['inicio'],
                                "finBloque" => $rango['fin'],
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                            if(count($cod_materia)>1){
                                $array['more']=true;
                            }
                        }else if(!$hora->estaDisponible() && $grupo->getId_grupo() == 0){
                            $array = [
                                "texto" => "reservada",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                        }else{
                            $array = [
                                "texto" => "",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                        }
                        $tabla[$x+1][$y+1] = $array;
                    }
                }
                break;
            }
        }
        return $tabla;
    }
    
    /**
     * Devuelve el horario de la semana para un aula y filtrado por departamento
     * 
     * @param type $aulas = las aulas del campus
     * @param type $aula = el nombre del aula
     * @param type $id_depar = el identificador del departamento
     * @param type $agrups = las agrupaciones de la facultad
     * @param type $materias = las materias
     * @return type = una tabla que representa al horario de la semana
     */
    public static function getHorarioEnAula_Depar($aula,$id_depar,$tabla,$facultad){
        for ($i = 0; $i < count($facultad->getAulas()); $i++) {
            if(strcmp($facultad->getAulas()[$i]->getNombre(), $aula)==0){
                $dias = $facultad->getAulas()[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];                        
                        $grupo = $hora->getGrupo();
                        if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                            if(ManejadorGrupos::mismoDepartamento($grupo->getAgrup(), $id_depar)){
                                $cod_materia = ManejadorGrupos::obtenerCodigoPropietario($grupo->getAgrup()->getMaterias());
                                $nombres = ManejadorGrupos::obtenerNombrePropietario($grupo->getAgrup()->getMaterias());
                                $nombre = $nombres[0];
                                $departamento = ManejadorGrupos::getNombreDepartamento($grupo->getAgrup()->getMaterias());
                                if(count(array_unique($cod_materia))>1){
                                    $nombre .= ' (Clonada)';
                                }
                                if($grupo->getTipo()=='TEORICO'){
                                $texto = $cod_materia[0]."<br/> GT: ".$grupo->getId_grupo();
                                } elseif($grupo->getTipo()=='DISCUSION'){
                                    $texto = $cod_materia[0]."<br/> GD: ".$grupo->getId_grupo();
                                } elseif($grupo->getTipo()=='LABORATORIO'){
                                    $texto = $cod_materia[0]."<br/> GL: ".$grupo->getId_grupo();
                                }
                                $rango = ManejadorAulas::getRangoHoras($horas, $grupo);
                                $array = [
                                "texto" => $texto,
                                "nombre" => $nombre,
                                "codigo" => implode("-", $cod_materia),
                                "grupo" => $grupo->getId_grupo(),
                                "departamento" => $departamento[0],
                                "inicioBloque" => $rango['inicio'],
                                "finBloque" => $rango['fin'],
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                                if(count($cod_materia)>1){
                                    $array['more']=true;
                                }
                            }else{
                                 $array = [
                                "texto" => "",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                            }
                        }else if(!$hora->estaDisponible() && $grupo->getId_grupo() == 0){
                            $array = [
                                "texto" => "reservada",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                        }else{
                            $array = [
                                "texto" => "",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                        }
                        $tabla[$x+1][$y+1] = $array;
                    }
                }
                break;
            }
        }
        return $tabla;
    }
    
    /**
     * Devuelve el horario para un aula filtrado por carrera
     * 
     * @param type $aulas = las aulas de la facultad
     * @param type $aula = nombre del aula solicitada
     * @param type $ids_agrups = identificadores de las agrupaciones
     * @return string = una tabla que representa al horario de la semana
     */
    public static function getHorarioEnAula_Carrera($aulas,$aula,$ids_agrups,$tabla){
        for ($i = 0; $i < count($aulas); $i++) {
            if(strcmp($aulas[$i]->getNombre(),$aula)==0){
                $dias = $aulas[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];
                        $grupo = $hora->getGrupo();
                        if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                            if(in_array($grupo->getAgrup()->getId(), $ids_agrups)){
                                $cod_materia = ManejadorGrupos::obtenerCodigoPropietario($grupo->getAgrup()->getMaterias());
                                $nombres = ManejadorGrupos::obtenerNombrePropietario($grupo->getAgrup()->getMaterias());
                                $nombre = $nombres[0];
                                $departamento = ManejadorGrupos::getNombreDepartamento($grupo->getAgrup()->getMaterias());
                                if(count(array_unique($cod_materia))>1){
                                    $nombre .= ' (Clonada)';
                                }
                                if($grupo->getTipo()=='TEORICO'){
                                $texto = $cod_materia[0]."<br/> GT: ".$grupo->getId_grupo();
                                } elseif($grupo->getTipo()=='DISCUSION'){
                                    $texto = $cod_materia[0]."<br/> GD: ".$grupo->getId_grupo();
                                } elseif($grupo->getTipo()=='LABORATORIO'){
                                    $texto = $cod_materia[0]."<br/> GL: ".$grupo->getId_grupo();
                                }
                                $rango = ManejadorAulas::getRangoHoras($horas, $grupo);
                                $array = [
                                    "texto" => $texto,
                                    "nombre" => $nombre,
                                    "codigo" => implode("-", $cod_materia),
                                    "grupo" => $grupo->getId_grupo(),
                                    "departamento" => $departamento[0],
                                    "inicioBloque" => $rango['inicio'],
                                    "finBloque" => $rango['fin'],
                                    "idHora" => $hora->getIdHora(),
                                    "dia" => $dias[$x]->getNombre(),
                                    "more" => false];
                                if(count($cod_materia)>1){
                                    $array['more']=true;
                                }
                            }else{                                
                                $array = [
                                "texto" => "",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                            }                            
                        }else if(!$hora->estaDisponible() && $grupo->getId_grupo() == 0){
                            $array = [
                                "texto" => "reservada",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                        }else{
                            $array = [
                                "texto" => "",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                        }
                        $tabla[$x+1][$y+1] = $array;
                    }
                }
                break;
            }            
        }
        return $tabla;
    }
    
    /**
     * Devuelve las aulas en las cuales el departamento especificado tiene clases
     * 
     * @param type $idDepartamento = departamento
     * @return string $aulas = array de aulas (solo el nombre del aula)
     */
    public static function getAulasDepartamento($id_depar,$facultad){
        $aulasSelec=array();
        $aulas = $facultad->getAulas();
        foreach ($aulas as $aula){
            $dias = $aula->getDias();
            for ($x = 0; $x < count($dias); $x++) {
                $horas = $dias[$x]->getHoras();                    
                for ($y = 0; $y < count($horas); $y++) {                        
                    $hora = $horas[$y];                        
                    $grupo = $hora->getGrupo();
                    if(!$hora->estaDisponible() && $grupo->getAgrup() != null){                            
                        if(ManejadorGrupos::mismoDepartamento($grupo->getAgrup(), $id_depar)){
                            $aulasSelec[] = $aula->getNombre();                                
                            goto next;
                        }   
                    }                      
                }
            }
            next:    
        }
        return $aulasSelec;
    }
    
    public static function getAulasCarrera($ids_agrups,$facultad){
        $aulasSeleccionadas=array();
        $aulas = $facultad->getAulas();
        $numAulas = count($aulas);                
        foreach ($aulas as $aula){
            $dias = $aula->getDias();
            for ($x = 0; $x < count($dias); $x++) {
                $horas = $dias[$x]->getHoras();
                for ($y = 0; $y < count($horas); $y++) {
                    $hora = $horas[$y];
                    $grupo = $hora->getGrupo();
                    if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                        if(in_array($grupo->getAgrup()->getId(), $ids_agrups)){                                                                                               
                            $aulasSeleccionadas[] = $aula->getNombre();
                            goto next;
                        }
                    }
                }                    
            }
            next:                
        }
        return $aulasSeleccionadas;
    }
}
