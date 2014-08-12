<?php

/**
 * Description of ManejadorHoras
 *
 * @author abs
 */
chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Hora.php';
include_once 'Dia.php';
include_once 'Aula.php';
include_once 'Docente.php';
include_once 'Grupo.php';
include_once 'Carrera.php';
include_once 'Departamento.php';
include_once 'Materia.php';
include_once 'ManejadorGrupos.php';
include_once 'ManejadorAulas.php';
include_once 'ManejadorDocentes.php';
include_once 'ManejadorMaterias.php';

class ManejadorHoras {
    
    /** Determinar si el grupo de una materia en una hora determinada choca con otra materia del mismo nivel (mismo ciclo, misma carrera)
     * 
     * @param String $nombre_dia nombre del dia en que se quiere realizar la asignacion
     * @param Integer $desde hora desde la cual se quiere realizar la asignacion
     * @param Integer $hasta hora hasta la cual se quiere realizar la asignacion
     * @param Aula[] $aulas Todas las aulas de la facultad
     * @param Agrupacion $agrupacion agrupacion a la que pertenece el grupo que se quiere asignar
     * @return boolean True si hay choque, False si no hay choque
     */
    private static function chocaMateria($nombre_dia, $desde, $hasta, $aulas, $agrupacion){
        foreach($aulas as $aula){
            $dia = $aula->getDia($nombre_dia);
            for($h=$desde; $h<$hasta; $h++){
                $hora = $dia->getHoras()[$h];
                if(!$hora->estaDisponible() && $hora->getGrupo()->getId_grupo() != 0){
                    $grupo = $hora->getGrupo();
                    if($agrupacion === $grupo->getAgrup() || ManejadorMaterias::materiasMismoNivel($agrupacion->getMaterias(), $grupo->getAgrup()->getMaterias())){
                        error_log("Agrupacion ".$agrupacion->getId()." en conflicto con agrupacion ".$grupo->getAgrup()->getId()." en hora ".$hora->getIdHora()." del dia ".$dia->getNombre()." en aula ".$aula->getNombre(),0);
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private static function chocaGrupoDocente($docentes, $desde, $hasta, $aulas, $nombre_dia){
        foreach ($aulas as $aula) {
            $dia = $aula->getDia($nombre_dia);
            for($h=$desde; $h<$hasta; $h++){
                $hora = $dia->getHoras()[$h];
                if(!$hora->estaDisponible() && $hora->getGrupo()->getId_grupo() != 0){
                    $retorno = ManejadorDocentes::docenteTrabajaHora($docentes, $hora);
                    if(count($retorno)!=0){
                        foreach ($retorno as $docente) {
                            $ids[] = $docente->getIdDocente();
                        }
                        error_log("Docente ".implode(',', $ids)." ya trabaja en hora ".$hora->getIdHora()." del dia ".$dia->getNombre(),0);
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    private static function chocaDocenteIntercambio($docentesGrupos, $desde, $hasta, $aulas, $nombre_dia){
        $msgs = array();
        foreach ($aulas as $aula) {
            $dia = $aula->getDia($nombre_dia);
            for($i=$desde;$i<$hasta;$i++){
                list($key,$docentes) = each($docentesGrupos);
                $hora = $dia->getHoras()[$i];
                if(!$hora->estaDisponible() && $hora->getGrupo()->getId_grupo() != 0){
                    $retorno = ManejadorDocentes::docenteTrabajaHora($docentes, $hora);
                    if(count($retorno)!=0){
                        foreach ($retorno as $docente) {
                            $nombres[] = "$docente";
                        }
                        $msgs[] = "Docente ".implode(',', $nombres)." ya trabaja en hora ".$hora->getInicio()." del dia ".$dia->getNombre()." en aula ".$aula->getNombre()." en grupo ".$hora->getGrupo()->getTipo()." ".$hora->getGrupo()->getId_grupo()." de la agrupacion que contiene a ".$hora->getGrupo()->getAgrup()->getMaterias()[0]->getNombre();
                        $nombres = null;
                    }
                }
            }
            reset($docentesGrupos);
        }
        return $msgs;
    }
    
    /** Aprobar la asignacion de un grupo en un bloque de horas especifico solo si a esas horas esta asignada una agrupacion que posee mas de 1 grupo del
     *  mismo tipo que el grupo que se esta tratando de asignar y si el grupo asignado es del mismo nivel que el grupo a asignar
     * @param String $nombre_dia = nombre del dia en el que se quiere realizar la asignacion
     * @param int $desde = limite superior del bloque de horas para la asignacion
     * @param int $hasta = limite inferior del bloque de horas para la asignacion
     * @param Aula[] $aulas = todas las aulas de la facultad
     * @param Agrupacion $agrupacion agrupacion a la que pertenece el grupo que se quiere asignar
     * @return boolean
     */
    private static function aprobarChoque($nombre_dia,$desde,$hasta,$aulas,$agrupacion){
        foreach ($aulas as $aula) {
            $dia = $aula->getDia($nombre_dia);
            for($h=$desde; $h<$hasta; $h++){
                $hora = $dia->getHoras()[$h];
                if(!$hora->estaDisponible() && $hora->getGrupo()->getId_grupo() != 0){
                    $grupoHora = $hora->getGrupo();
                    $agrupHora = $grupoHora->getAgrup();
                    if($agrupHora->getNumGrupos($grupoHora->getTipo())==1 && ManejadorMaterias::materiasMismoNivel($agrupHora->getMaterias(), $agrupacion->getMaterias())){
                        error_log ("Grupo: ".$grupoHora->getId_grupo()." de la Agrupacion ".$agrupHora->getId()." en hora: $h del dia $nombre_dia es unico, choque no aprobado",0);
                        return false;
                    }
                }
            }
        }
        return true;
    }
    
    public static function choqueIntercambios($nombre_dia,$desde,$hasta,$aulas,$agrupaciones,$docentesGrupos){
        $msgs = array();
        foreach ($agrupaciones as $agrupacion){
            foreach ($aulas as $aula) {
                $dia = $aula->getDia($nombre_dia);
                for($h=$desde; $h<$hasta; $h++){
                    $hora = $dia->getHoras()[$h];
                    if(!$hora->estaDisponible() && $hora->getGrupo()->getId_grupo() != 0){
                        $grupoHora = $hora->getGrupo();
                        $agrupHora = $grupoHora->getAgrup();
                        if($agrupHora->getNumGrupos($grupoHora->getTipo())==1 && ManejadorMaterias::materiasMismoNivel($agrupHora->getMaterias(), $agrupacion->getMaterias())){
                            $msgs[] = "Choque entre agrupacion que contiene a ".$agrupHora->getMaterias()[0]->getNombre()." y agrupacion que contiene a ".$agrupacion->getMaterias()[0]->getNombre()." a las ".$hora->getInicio()." en aula ".$aula->getNombre().". Choque no es factible porque existe un grupo único.";
                        } elseif($agrupHora->getNumGrupos($grupoHora->getTipo())!=1 && ManejadorMaterias::materiasMismoNivel($agrupHora->getMaterias(), $agrupacion->getMaterias())){
                            $msgs[] = "Choque entre agrupacion que contiene a ".$agrupHora->getMaterias()[0]->getNombre()." y agrupacion que contiene a ".$agrupacion->getMaterias()[0]->getNombre()." a las ".$hora->getInicio()." en aula ".$aula->getNombre().". Choque es factible porque no existe un grupo único.";
                        }
                    }
                }
            }
        }
        return array_merge($msgs,self::chocaDocenteIntercambio($docentesGrupos, $desde, $hasta, $aulas, $nombre_dia));
    }

    private static function comprobacionesDeHorasDisponibles($objetos,$choque,$nombreDia,$aulas,$desde,$hasta){
        $chocaDocente = false;
        if(!ManejadorDocentes::existeDocRespaldo($objetos[0])){
            $chocaDocente = self::chocaGrupoDocente($objetos[0], $desde, $hasta, $aulas, $nombreDia);
        }
        if(!$choque){
            $chocaGrupo = self::chocaMateria($nombreDia, $desde, $hasta, $aulas, $objetos[1]);
        } else{
            $aprobarChoque = self::aprobarChoque($nombreDia, $desde, $hasta, $aulas, $objetos[1]);
        }
        if((isset($chocaGrupo) && !$chocaGrupo && !$chocaDocente) || (isset($aprobarChoque) && $aprobarChoque && !$chocaDocente)){
            return true;
        } else{
            return false;
        }
    }
    
    private static function hayBloquesDisponibles($index,$horas,$hasta,$cantidadHoras){
        $hayBloquesDisponibles=false;
        if($horas[$index]->estaDisponible() && $horas[$index]->getIdHora()<=($hasta+1)-$cantidadHoras){
            $hayBloquesDisponibles = true;
            for($j=$index+1;$j<$index+$cantidadHoras;$j++){
                $hora = $horas[$j];
                if($hora->getIdHora()==8){
                    $hayBloquesDisponibles=false;
                    break;
                }elseif(!$hora->estaDisponible()){
                    $hayBloquesDisponibles=false;
                    break;
                }
            }
        }
        return $hayBloquesDisponibles;
    }
    
    /** Devuelve las primeras horas disponibles consecutivas que encuentre que cumplas las condiciones de choque
     * 
     * @param Docente $docentes = Docentes que impartiran el grupo/grupos a asignar
     * @param Hora $horas = horas del dia en que va a tratar de asignar
     * @param Integer $cantidadHoras = cuantas horas a asignar
     * @param Integer $desde = desde cual hora tratar de asignar
     * @param Integer $hasta = hasta cual hora tratar de asginar
     * @param String $nombre_dia = nombre del dia en el que se quiere asignar, se usa para comprobar choques
     * @param Agrupacion $agrupacion = objeto agrupacion de la cual se esta tratando de asignar un grupo
     * @param Aula[] $aulas = todas las aulas de campus, se usan para verificar choques
     * @param boolean $ultimoRecurso Es una busqueda de ultimo recurso o no
     * @param boolean $choques Se evaluaran choques de materias o no
     * @param boolean $retener Se apilaran horas disponibles o se devolveran las primeras encontradas
     * @return Hora[] las horas disponibles sin choque en las que se puede asignar el grupoHora; null si no hay ninguna
     */
    private static function buscarHorasDisponibles($docentes,$horas,$cantidadHoras,$desde,$hasta,$nombre_dia,$agrupacion,$aulas,$ultimoRecurso,$choques,$retener){
        for($i=$desde;$i<$hasta;$i++){
            if(self::hayBloquesDisponibles($i, $horas, $hasta, $cantidadHoras)){
                if(self::comprobacionesDeHorasDisponibles(array($docentes,$agrupacion), $choques, $nombre_dia, $aulas, $i, $i+$cantidadHoras)){
                    for ($j = $i; $j < $i+$cantidadHoras; $j++) {
                        $horasDisponibles[] = $horas[$j];
                    }
                    if($retener){$rango['inicio'] = $horasDisponibles[0]->getInicio(); $rango['fin'] = end($horasDisponibles)->getFin(); $horasRetorno[] = $rango; unset($horasDisponibles); continue;}
/*>>>>>>>>>>>>>>*/  error_log("ahi va un bloque para asignar con choques=".var_export($choques,true)." de ".  count($horasDisponibles),0);
                    return $horasDisponibles;
                } elseif (!$ultimoRecurso && !$choques && !$retener){
                    return "Choque";
                }
            }
        }
        if($retener){ if(isset($horasRetorno)){return $horasRetorno;} else{return array();} }
        return null;
    }
    
    /** Metodo para buscar horas disponibles en un dia elegido ya sea en todas las horas del dia o en un margen de horas considerando los choques de materia
     * 
     * @param Docente[] $docentes = para verificar si el(los) docente(s) no tiene asignado un grupo a la misma hora
     * @param Integer $cantidadHoras = numero de horas que se quieren asignar
     * @param Integer $desde = desde cual hora se quiere hacer la asignacion
     * @param Integer $hasta = hasta cual hora tratar de hacer la asignacion
     * @param String $nombre_dia = nombre del dia en que se quiere hacer la asignacion
     * @param Agrupacion $agrupacion = objeto agrupacion de la cual se quiere asignar un grupo
     * @param Aula[] $aulasConCapa = array de aulas que tienen capacidad para asignar al grupo de la materia
     * @param Aula[] $aulas = array de todas las aulas que tiene el campus, se usa para verificar si hay choques
     * @param boolean $ultimoRecurso true si es para ultimo recurso la busqueda (buscar en todo el dia)
     * @return Hora[] horas disponibles en las que se puede asignar el grupoHora
     */
    public static function buscarHoras($docentes,$cantidadHoras,$desde,$hasta,$nombre_dia,$agrupacion,$aulasConCapa,$aulas,$ultimoRecurso,$choques){
        $horasDisponibles = null;
        for($x=0; $x<count($aulasConCapa); $x++){
/*>>>>>>>>>>>>>>*/error_log ("A probar en aula ".$aulasConCapa[$x]->getNombre(),0);
            $dia = $aulasConCapa[$x]->getDia($nombre_dia);
            $resul = self::buscarHorasDisponibles($docentes,$dia->getHoras(),$cantidadHoras,$desde,$hasta,$nombre_dia,$agrupacion,$aulas,$ultimoRecurso,$choques,false);
            if($resul != null && is_array($resul)){
                $horasDisponibles = $resul;
                break;
            }else if(!$ultimoRecurso && $resul != null && $resul == "Choque"){
                break;
            }
        }
        return $horasDisponibles;
    }
    
    /** Buscar horas disponibles en un dia elegido sin considerar los choques de materia
     * 
     * @param Agrupacion $agrupacion agrupacion a la que pertenece el grupo que se quiere asignar
     * @param Docente[] $docentes array de docentes que impartiran el grupo que se quiere asignar
     * @param int $cantidadHoras = numero de horas que se quieren asignar
     * @param int $desde = desde cual hora se quiere hacer la asignacion
     * @param int $hasta = hasta cual hora tratar de hacer la asignacion
     * @param String $nombre_dia = nombre del dia en que se quiere hacer la asignacion
     * @param Aula[] $aulas array de aulas que tienen capacidad para asignar al grupo
     * @return Hora[] = array de horas para asignar al grupo
     */
//    public static function buscarHorasConChoque($agrupacion,$docentes,$cantidadHoras,$desde,$hasta,$nombre_dia,$aulas){
//        $horasDisponibles = null;
//        for($x=0; $x<count($aulas); $x++){
///*>>>>>>>>>>>>>>*/error_log ("A probar con choque en aula ".$aulas[$x]->getNombre(),0);
//            $dia = $aulas[$x]->getDia($nombre_dia);
//            $horasDisponibles = self::buscarHorasDisponibles($docentes,$dia->getHoras(),$cantidadHoras,$desde,$hasta,$nombre_dia,$agrupacion,$aulas,false,true,false);
//            if($horasDisponibles != null){
//                break;
//            }
//        }
//        return $horasDisponibles;
//    }
    
    /**
     * Para ver si ya se asignó el grupo en un día
     * @param String nombre_dia nombre del dia en que se quiere realizar la busqueda
     * @param Grupo grupo objeto grupo que se busca en el dia
     * @param Aula[] aulas todas las aulas de la facultad en las que se quiere realizar la busqueda
     * @return boolean
     */
    public static function grupoPresente($nombre_dia, $grupo, $aulas){
        foreach ($aulas as $aula) {
            $dia = $aula->getDia($nombre_dia);
            $horas = $dia->getHoras();
            foreach($horas as $hora){
                if(!$hora->estaDisponible() && $hora->getGrupo()->getId_grupo() != 0){
                    if($hora->getGrupo() === $grupo){
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    /** Meotodo para relizar busquedas de una materia que pertenece al mismo nivel en el dia elegido
     *
     * @param grupo = grupo que se quiere asignar en dia elegido
     * @param agrupacion
     * @param aulas
     * @param nombreDia
     * @return ultima hora en la que hay una materia del mismo nivel
     */
    public static function getUltimasHoraDeNivel($agrupacion,$aulas,$nombreDia){
        $horasNivel = array();
        $materiasAgrupacion = $agrupacion->getMaterias();
        foreach ($materiasAgrupacion as $materia) {
            foreach ($aulas as $aula) {
                $hora = -1;
                $horas = $aula->getDia($nombreDia)->getHoras();
                for($x=0; $x<count($horas); $x++){
                    if(!$horas[$x]->estaDisponible() && $horas[$x]->getGrupo()->getId_grupo() != 0 && ManejadorMaterias::mismoDepartamentoAgrupacionMateria($horas[$x]->getGrupo()->getAgrup(), $materia)){
                        $grupoHora = $horas[$x]->getGrupo();
                        $materias = $grupoHora->getAgrup()->getMaterias();
                        foreach ($materias as $materiaHora) {
                            if($materiaHora->getCarrera() === $materia->getCarrera() && $materiaHora->getCiclo() == $materia->getCiclo()){
                                $hora = $x;
                                break;
                            }
                        }
                    }
                }
                if($hora != -1){
                    $horasNivel[$materia->getCodigo()][] = $hora;
                }
            }
        }
        return $horasNivel;
    }
    
    public static function getIdHoraSegunInicio($inicioHora,$horas){
        foreach ($horas as $hora){
            if(strcmp($hora->getInicio(),$inicioHora)==0){
                return $hora->getIdHora();
            }
        }
        return null;
    }
    
    public static function getIdHoraSegunFin($finHora,$horas){
        foreach ($horas as $hora){
            if(strcmp($hora->getFin(),$finHora)==0){
                return $hora->getIdHora();
            }
        }
        return null;
    }
            
    public static function getHorarioTodasHoras($id_depar,$facultad,$tabla){
        $array = array();
        for ($i = 0; $i < count($facultad->getAulas()); $i++) {
                $dias = $facultad->getAulas()[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];                        
                        $grupo = $hora->getGrupo();
                        if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                            if(ManejadorGrupos::mismoDepartamento($grupo->getAgrup(), $id_depar) || $id_depar=="todos"){
                                $depar = $grupo->getAgrup()->getMaterias()[0]->getCarrera()->getDepartamento();
                                $array = [
                                "texto" => "ver",                                
                                "idHora" => $hora->getIdHora(),
                                "depar" => $depar->getId(),
                                "nombre_depar" => $depar->getNombre(),    
                                "dia" => $dias[$x]->getNombre(),
                                "idDia" => $dias[$x]->getId()];
                            }else{
                                $array = self::getInfoHoraVacia($x, $dias, $hora);
                            }
                        }else{
                            $array = self::getInfoHoraVacia($x, $dias, $hora);
                        }                        
                        if($tabla[$x+1][$y+1]==""){                           
                           $tabla[$x+1][$y+1] = $array;
                        }else{
                            if($tabla[$x+1][$y+1]['texto']!="ver"){                                
                                $tabla[$x+1][$y+1] = $array;
                            }
                        }
                    }
                }
        }
        return $tabla;
    }
    
    public static function getHorarioHora_depar($idDia,$idHora,$idDepar,$facultad){
        $tabla = array();
        for ($i = 0; $i < count($facultad->getAulas()); $i++) {
                $aula = $facultad->getAulas()[$i];
                $dias = $aula->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $dia = $dias[$x];
                    $horas = $dia->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];                        
                        $grupo = $hora->getGrupo();
                        if(!$hora->estaDisponible() && $grupo->getAgrup() != null && $dia->getId()==$idDia && $hora->getIdHora()==$idHora){
                            if(ManejadorGrupos::mismoDepartamento($grupo->getAgrup(), $idDepar) || $idDepar=="todos"){
                                $departamento = $grupo->getAgrup()->getMaterias()[0]->getCarrera()->getDepartamento();
                                $nombre_depar = $departamento->getNombre();
                                $nombres = ManejadorGrupos::obtenerNombrePropietario($grupo->getAgrup()->getMaterias());
                                $nombre = $nombres[0];
                                $array = [
                                    "aula" => $aula->getNombre(),
                                    "dia" => $dia->getNombre(),
                                    "nombre" => $nombre,
                                    "nombre_depar" => $nombre_depar,
                                    "horaInicio" => $hora->getInicio(),                                
                                    "horaFin" => $hora->getFin(),
                                    "grupo" => $grupo->getId_grupo(),
                                    "tipo" => $grupo->getTipo(),
                                    "more" => false,
                                    "cloned" => false];
                                $tabla[] = $array;
                            }
                        }                       
                    }
                }
        }
        return $tabla;
    }
    
    private static function getInfoHoraVacia($index,$dias,$hora){
        return ["texto" => "",                
                "idHora" => $hora->getIdHora(),
                "dia" => $dias[$index]->getNombre()];        
    }
    
    /** Encontrar todas las horas que aun estan disponibles para una asignacion despues de generar un horario
     * 
     * @param Aula[] $aulas = todas las aulas de la facultad
     * @param Agrupacion $agrupacion = agrupacion a la que pertenece el/los grupo(s) que se quiere intercambiar
     * @param Grupo $grupo = Grupo que se quiere intercambiar
     * @param int $cantidadHoras = numero de horas que se quieren intercambiar
     */
    public static function buscarHorasParaIntercambio($aulas,$agrupacion,$grupo,$cantidadHoras){
        $retorno = array();
        $aulasCapacidad = ManejadorAulas::obtenerAulasPorCapacidad($aulas, $agrupacion->getNum_alumnos());
        foreach ($aulasCapacidad as $aula){
            $dias = $aula->getDias();
            foreach ($dias as $dia){
                $horas = self::buscarHorasDisponibles($grupo->getDocentes(), $dia->getHoras(), $cantidadHoras, 0, $dia->getPosEnDiaHora(end($dia->getHoras())->getIdHora())+1, $dia->getNombre(), $agrupacion, $aulas, true, false, true);
                if(count($horas)!=0){
                    $bloquesDia['aula'] = $aula->getNombre();
                    $bloquesDia['dia'] = $dia->getNombre();
                    $bloquesDia['horas'] = $horas;
                    $retorno[] = $bloquesDia;
                }
            }
        }
        return $retorno;
    }
}
