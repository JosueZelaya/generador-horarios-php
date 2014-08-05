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
    public static function chocaMateria($nombre_dia, $desde, $hasta, $aulas, $agrupacion){
        foreach($aulas as $aula){
            $dia = $aula->getDia($nombre_dia);
            for($h=$desde; $h<$hasta; $h++){
                $hora = $dia->getHoras()[$h];
                if(!$hora->estaDisponible() && $hora->getGrupo()->getId_grupo() != 0){
                    $grupo = $hora->getGrupo();
                    if($agrupacion === $grupo->getAgrup() || ManejadorMaterias::materiasMismoNivel($agrupacion->getMaterias(), $grupo->getAgrup()->getMaterias())){
/*>>>>>>>>>>>>>>*/      error_log("Agrupacion ".$agrupacion->getId()." en conflicto con agrupacion ".$grupo->getAgrup()->getId()." en hora ".$hora->getIdHora()." del dia ".$dia->getNombre()." en aula ".$aula->getNombre(),0);
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function chocaGrupoDocente($docentes, $desde, $hasta, $aulas, $nombre_dia){
        foreach ($aulas as $aula) {
            $dia = $aula->getDia($nombre_dia);
            for($h=$desde; $h<$hasta; $h++){
                $hora = $dia->getHoras()[$h];
                if(!$hora->estaDisponible() && $hora->getGrupo()->getId_grupo() != 0){
                    if(ManejadorDocentes::docenteTrabajaHora($docentes, $hora)){
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    /** Aprobar la asignacion de un grupo en un bloque de horas especifico solo si a esas horas esta asignada una agrupacion que posee mas de 1 grupo del
     *  mismo tipo que el grupo que esta asignado en esas horas y si el grupo asignado es del mismo nivel que el grupo a asignar
     * @param String $nombre_dia = nombre del dia en el que se quiere realizar la asignacion
     * @param int $desde = limite superior del bloque de horas para la asignacion
     * @param int $hasta = limite inferior del bloque de horas para la asignacion
     * @param Aula[] $aulas = todas las aulas de la facultad
     * @param Agrupacion $agrupacion agrupacion a la que pertenece el grupo que se quiere asignar
     * @return boolean
     */
    public static function aprobarChoque($nombre_dia,$desde,$hasta,$aulas,$agrupacion){
        foreach ($aulas as $aula) {
            $dia = $aula->getDia($nombre_dia);
            for($h=$desde; $h<$hasta; $h++){
                $hora = $dia->getHoras()[$h];
                if(!$hora->estaDisponible() && $hora->getGrupo()->getId_grupo() != 0){
                    $grupoHora = $hora->getGrupo();
                    $agrupHora = $grupoHora->getAgrup();
                    if($agrupHora->getNumGrupos($grupoHora->getTipo())==1 && ManejadorMaterias::materiasMismoNivel($agrupHora->getMaterias(), $agrupacion->getMaterias())){
/*>>>>>>>>>>>>>>*/      error_log ("Grupo: ".$grupoHora->getId_grupo()." de la Agrupacion ".$agrupHora->getId()." en hora: $h del dia $nombre_dia es unico, choque no aprobado",0);
                        return false;
                    }
                }
            }
        }
        return true;
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
    
    /** Devuelve las primeras horas disponibles consecutivas que encuentre que cumplas las condiciones de choque (evitar choque de materia de mismo nivel y evitar choque de horario de docente)
     * 
     * @param Docente $docentes
     * @param Hora $horas = horas del dia en que va a tratar de asignar
     * @param Integer $cantidadHoras = cuantas horas a asignar
     * @param Integer $desde = desde cual hora tratar de asignar
     * @param Integer $hasta = hasta cual hora tratar de asginar
     * @param String $nombre_dia = nombre del dia en el que se quiere asignar, se usa para comprobar choques
     * @param Agrupacion $agrupacion = objeto agrupacion de la cual se esta tratando de asignar un grupo
     * @param Aula[] $aulas = todas las aulas de campus, se usan para verificar choques
     * @param boolean $ultimoRecurso Es una busqueda de ultimo recurso o no
     * @return Hora[] las horas disponibles sin choque en las que se puede asignar el grupoHora; null si no hay ninguna
     */
    public static function buscarHorasDisponibles($docentes,$horas,$cantidadHoras,$desde,$hasta,$nombre_dia,$agrupacion,$aulas,$ultimoRecurso){
        for($i=$desde;$i<$hasta;$i++){
            if(self::hayBloquesDisponibles($i, $horas, $hasta, $cantidadHoras)){
                if(self::comprobacionesDeHorasDisponibles(array($docentes,$agrupacion), false, $nombre_dia, $aulas, $i, $i+$cantidadHoras)){
                    for ($j = $i; $j < $i+$cantidadHoras; $j++) {
                        $horasDisponibles[] = $horas[$j];
                    }
/*>>>>>>>>>>>>>>*/  error_log("ahi va un bloque para asignar de ".  count($horasDisponibles),0);
                    return $horasDisponibles;
                } else{
                    if(!$ultimoRecurso){
                        return "Choque";
                    }
                }
            }
        }
        return null;
    }
    
    /** Se localizan bloques de horas continuas que cumplan con las condiciones de choque (evitar choque de grupo consigo mismo y evitar choque de horario de docente)
     * 
     * @param Agrupacion $agrupacion agrupacion a la que pertenece el grupo que se quiere asignar
     * @param Docente[] $docentes array de docentes que impartiran el grupo que se quiere asignar
     * @param Hora[] $horas = horas del dia en que va a tratar de asignar
     * @param Integer $cantidadHoras = cuantas horas a asignar
     * @param Integer $desde = desde cual hora tratar de asignar
     * @param Integer $hasta = hasta cual hora tratar de asginar
     * @param String $nombre_dia
     * @param Aula[] $aulas Todas las aulas de la facultad
     * @return horas disponibles en las que se puede asignar el grupoHora aunque hayan choques
     */
    public static function buscarHorasDisponiblesParaChoque($agrupacion,$docentes,$horas,$cantidadHoras,$desde,$hasta,$nombre_dia,$aulas){
        for($i=$desde;$i<$hasta;$i++){
            if(self::hayBloquesDisponibles($i, $horas, $hasta, $cantidadHoras)){
                if(self::comprobacionesDeHorasDisponibles(array($docentes,$agrupacion), true, $nombre_dia, $aulas, $i, $i+$cantidadHoras)){
                    for ($j = $i; $j < $i+$cantidadHoras; $j++) {
                        $horasDisponibles[] = $horas[$j];
                    }
/*>>>>>>>>>>>>>>*/  error_log("ahi va un bloque con choque para asignar de ".  count($horasDisponibles),0);
                    return $horasDisponibles;
                }
            }
        }
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
    public static function buscarHoras($docentes,$cantidadHoras,$desde,$hasta,$nombre_dia,$agrupacion,$aulasConCapa,$aulas,$ultimoRecurso){
        $horasDisponibles = null;
        for($x=0; $x<count($aulasConCapa); $x++){
/*>>>>>>>>>>>>>>*/error_log ("A probar en aula ".$aulasConCapa[$x]->getNombre(),0);
            $dia = $aulasConCapa[$x]->getDia($nombre_dia);
            $resul = self::buscarHorasDisponibles($docentes,$dia->getHoras(),$cantidadHoras,$desde,$hasta,$nombre_dia,$agrupacion,$aulas,$ultimoRecurso);
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
    public static function buscarHorasConChoque($agrupacion,$docentes,$cantidadHoras,$desde,$hasta,$nombre_dia,$aulas){
        $horasDisponibles = null;
        for($x=0; $x<count($aulas); $x++){
/*>>>>>>>>>>>>>>*/error_log ("A probar con choque en aula ".$aulas[$x]->getNombre(),0);
            $dia = $aulas[$x]->getDia($nombre_dia);
            $horasDisponibles = self::buscarHorasDisponiblesParaChoque($agrupacion,$docentes,$dia->getHoras(),$cantidadHoras,$desde,$hasta,$nombre_dia,$aulas);
            if($horasDisponibles != null){
                break;
            }
        }
        return $horasDisponibles;
    }
    
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
                    if(!$horas[$x]->estaDisponible() && $horas[$x]->getGrupo()->getId_grupo() != 0 && self::mismoDepartamentoAgrupacionMateria($horas[$x]->getGrupo()->getAgrup(), $materia)){
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
    
    public static function bloqueCompleto($desde,$hasta,$horas,$grupos){
        if($desde != 1 && $hasta != 15){
            $grupoAnterior = $horas[$desde-2]->getGrupo();
            $grupoPosterior = $horas[$hasta]->getGrupo();
            $grupoEnDesde = $horas[$desde-1]->getGrupo();
            $grupoEnHasta = $horas[$hasta-1]->getGrupo();
        } elseif ($desde == 1) {
            $grupoAnterior = null;
            $grupoPosterior = $horas[$hasta]->getGrupo();
            $grupoEnDesde = $horas[$desde-1]->getGrupo();
            $grupoEnHasta = $horas[$hasta-1]->getGrupo();
        } elseif ($hasta == 15) {
            $grupoAnterior = $horas[$desde-2]->getGrupo();
            $grupoPosterior = null;
            $grupoEnDesde = $horas[$desde-1]->getGrupo();
            $grupoEnHasta = $horas[$hasta-1]->getGrupo();
        }
        if(count($grupos) == 1 && $grupoAnterior == $grupoEnDesde && $grupoEnHasta == $grupoPosterior){
            echo 'incorrecto';
            return false;
        } elseif (count($grupos) > 1){
            if(is_a($grupoAnterior, "Grupo") && $grupoAnterior == $grupoEnDesde && $grupoAnterior->getId_grupo() != 0){
                echo 'incorrecto1';
                return false;
            } elseif(is_a($grupoPosterior, "Grupo") && $grupoPosterior == $grupoEnHasta && $grupoPosterior->getId_grupo() != 0){
                echo 'incorrecto2';
                return false;
            } elseif(!ManejadorGrupos::gruposIgualesEnBloque($grupos)){
                echo 'incorrecto3';
                return false;
            }
        }
        return true;
    }
    
    public static function grupoHuerfano($horas,$desde,$grupo,$origen){
        if($grupo->getAgrup() == null){
            return false;
        }
        if($horas[$desde-1]->getGrupo() == $grupo){
            echo "Grupos iguales en horas origen y destino del intercambio";
            exit(0);
        }
        $contador = 0;
        $i = ($desde-1);
        if($i == 14){
            goto evalUp;
        }
        evalDown:{
            while($i < ($desde+1) && $i < 14){
                if($horas[$i+1]->getGrupo()->getAgrup() != $grupo->getAgrup() || ($horas[$i+1]->getGrupo()->getAgrup() == $grupo->getAgrup() && $horas[$i+1]->getGrupo()->getId_grupo() != $grupo->getId_grupo()) || ($horas[$i+1]->getGrupo() === $grupo && ($i+1) == ($origen-1))){
                    $contador++;
                }
                $i += 1;
            }
            if($contador > 1 && $desde == 1){
                return true;
            } elseif($desde == 1 && $contador <= 1){
                return false;
            } elseif($desde != 1 && $contador <= 1){
                $huerfano1 = false;
            } else {
                $huerfano1 = true;
            }
            $contador = 0;
            $i = ($desde-1);
        }
        evalUp:{
            while($i > 0 && $i > ($desde-3)){
                if($horas[$i-1]->getGrupo()->getAgrup() != $grupo->getAgrup() || ($horas[$i-1]->getGrupo()->getAgrup() == $grupo->getAgrup() && $horas[$i-1]->getGrupo()->getId_grupo() != $grupo->getId_grupo()) || ($horas[$i-1]->getGrupo() === $grupo && ($i-1) == ($origen-1))){
                    $contador++;
                }
                $i -= 1;
            }
            if($contador > 1 && $desde == 15){
                return true;
            } elseif ($contador <= 1 && $desde == 15){ 
                return false;
            } elseif ($contador <= 1 && $desde != 15){
                $huerfano2 = false;
            } else{
                $huerfano2 = true;
            }
        }
        endEval:{
            if($huerfano1 == true && $huerfano2 == true){
                return true;
            } else{
                return false;
            }
        }
    }
    
    public static function intercambiar($aula1,$dia1,$desde1,$aula2,$dia2,$desde2,$grupos,$aulas){
        for ($i = 0; $i < count($grupos[0]); $i++){
            ManejadorAulas::getAula($aulas, $aula2)->getDia($dia2)->getHoras()[$desde2-1]->setGrupo($grupos[0][$i]);
            if(ManejadorGrupos::getGrupoEnHora($aulas, $aula2, $dia2, $desde2)->getId_grupo() == 0){
                ManejadorAulas::getAula($aulas, $aula2)->getDia($dia2)->getHoras()[$desde2-1]->setDisponible(true);
            } else{
                ManejadorAulas::getAula($aulas, $aula2)->getDia($dia2)->getHoras()[$desde2-1]->setDisponible(false);
            }
            $desde2++;
        }
        for($i=0; $i < count($grupos[1]); $i++){
            ManejadorAulas::getAula($aulas, $aula1)->getDia($dia1)->getHoras()[$desde1-1]->setGrupo($grupos[1][$i]);
            if(ManejadorGrupos::getGrupoEnHora($aulas, $aula1, $dia1, $desde1)->getId_grupo() == 0){
                ManejadorAulas::getAula($aulas, $aula1)->getDia($dia1)->getHoras()[$desde1-1]->setDisponible(true);
            } else {
                ManejadorAulas::getAula($aulas, $aula1)->getDia($dia1)->getHoras()[$desde1-1]->setDisponible(false);
            }
            $desde1++;
        }
    }

    public static function mismoDepartamentoAgrupacionMateria($agrupacion,$materia){
        $materiasAgrup = $agrupacion->getMaterias();
        foreach ($materiasAgrup as $materiaAgrup){
            if($materiaAgrup->getCarrera()->getDepartamento() == $materia->getCarrera()->getDepartamento()){
                return true;
            }
        }
        return false;
    }
    
    public static function getIdHoraSegunInicio($inicioHora,$horas){
        foreach ($horas as $hora){
            if(strcmp($hora->getInicio(),$inicioHora)==0){
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
    
//    public static function getHorarioEnHora_depar($idDia,$idHora,$idDepar,$aulas){
//        $horario = array();
//        foreach ($aulas as $aula){
//            $dias = $aula->getDias();                  
//            foreach ($dias as $dia){
//                $horas = $dia->getHoras();
//                $grupoAnterior=null;
//                foreach ($horas as $hora){
//                    $grupo = $hora->getGrupo();
//                    if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
//                        $materias = $grupo->getAgrup()->getMaterias();
//                        foreach ($materias as $materia){
//                            if($dia->getId()==$idDia && $hora->getIdHora()==$idHora && $materia->getCarrera()->getDepartamento()->getId() == $idDepar){                                                        
//                                if($grupoAnterior!=null && $grupoAnterior===$grupo){
//                                    $indiceUltimaHora = count($horario[$grupo->getTipo()])-1;
//                                    $horario[$grupo->getTipo()][$indiceUltimaHora]['horaFin'] = $hora->getFin();                                    
//                                }else{
//                                    $arrayGrupo = [
//                                    "aula" => $aula->getNombre(),
//                                    "dia" => $dia->getNombre(),
//                                    "horaInicio" => $hora->getInicio(),                                
//                                    "horaFin" => $hora->getFin(),
//                                    "grupo" => $grupo->getId_grupo(),
//                                    "nombre" => $materia->getNombre(),   
//                                    "tipo" => $grupo->getTipo(),
//                                    "more" => false,
//                                    "cloned" => false];
//                                    $propietarios = ManejadorGrupos::obtenerCodigoPropietario($grupo->getAgrup()->getMaterias());
//                                    if(count(array_unique($propietarios))>1){
//                                        $arrayGrupo['cloned']=true;
//                                    } elseif(count($propietarios)>1){
//                                        $arrayGrupo['more']=true;
//                                    }
//                                    $horario[$grupo->getTipo()][] = $arrayGrupo;
//                                    $grupoAnterior = $hora->getGrupo();
//                                }
//                                break;
//                            }
//                        }
//                    }
//                }
//            }            
//        }
//        return $horario;
//    }
    
    
    private static function getInfoHoraVacia($index,$dias,$hora){
        return ["texto" => "",                
                "idHora" => $hora->getIdHora(),
                "dia" => $dias[$index]->getNombre()];        
    }
    
    private static function getInfoHoraReservada($index,$dias,$hora){
        return ["texto" => "reservada",                
                "idHora" => $hora->getIdHora(),
                "dia" => $dias[$index]->getNombre()];
    }
    
}
