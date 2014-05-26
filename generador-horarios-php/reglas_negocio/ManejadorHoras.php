<?php
/**
 * Description of ManejadorHoras
 *
 * @author abs
 */

include_once '../acceso_datos/Conexion.php';
include_once 'Hora.php';
include_once 'Dia.php';
include_once 'Aula.php';
include_once 'Grupo.php';
include_once 'Procesador.php';
include_once 'Materia.php';
include_once 'ManejadorAgrupaciones.php';

class ManejadorHoras {
    
    public static function getTodasHoras(){
        $horas = array();
        $respuesta = Conexion::consulta("SELECT * FROM horas ORDER BY id_hora");
        while($fila = pg_fetch_array($respuesta)){
            $hora = new Hora();
            $hora->setId($fila[0]);
            $hora->setInicio($fila[1]);
            $hora->setFin($fila[2]);
            $horas[] = $hora;
        }
        return $horas;
    }
    
    public static function getHorasDia($dia){
        $horas = array();
        $respuesta = Conexion::consulta("select * from horas where id_hora in (select id_hora from dia_horas where nombre_dia='$dia') order by id_hora asc");
        while($fila = pg_fetch_array($respuesta)){
            $hora = new Hora();
            $hora->setIdHora($fila[0]);
            $hora->setInicio($fila[1]);
            $hora->setFin($fila[2]);
            $horas[] = $hora;
        }
        return $horas;
    }
    
    public static function elegirHora($horas,$desde,$hasta){
        $hora = Procesador::getNumeroAleatorio($desde, $hasta);
        return $horas[$hora];
    }
    
    public static function chocaMateria($nombre_dia, $id_hora, $aulas, $materia, $num_horas, $todas_mats){
        foreach($aulas as $aula){
            $dia = $aula->getDia($nombre_dia);
            for($h=$id_hora; $h<$id_hora+$num_horas; $h++){
                $hora = $dia->getHoras()[$h-1];
                if(!$hora->estaDisponible()){
                    $grupo = $hora->getGrupo();
                    $materias = ManejadorMaterias::getMateriaDeGrupo($grupo->getId_Agrup(), $todas_mats);
                    foreach ($materias as $materiaDeAgrup) {
                        if(strcmp($materiaDeAgrup->getCodigoCarrera(),$materia->getCodigoCarrera())==0 && $materiaDeAgrup->getCiclo() == $materia->getCiclo()){
                            $m = $materia->getCodigo();
                            $p = ManejadorAgrupaciones::obtenerNombrePropietario($grupo->getId_Agrup(),$todas_mats);
                            $g = $grupo->getId_grupo();
                            error_log ("Esta materia $m choca con $p GT $g en hora: $h del dia $nombre_dia",0);
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public static function chocaGrupoDocente($idDocente, $desde, $hasta, $aulas, $nombre_dia){
        foreach ($aulas as $aula) {
            $dia = $aula->getDia($nombre_dia);
            for($h=$desde; $h<$hasta; $h++){
                $hora = $dia->getHoras()[$h-1];
                if(!$hora->estaDisponible()){
                    $grupoHora = $hora->getGrupo();
                    if($idDocente==$grupoHora->getId_docente()){
                        $g = $grupoHora->getId_grupo();
                        $ho = $hora->getIdHora();
                        error_log ("El docente: $idDocente atiende ya el grupo: $g a la hora: $ho",0);
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    public static function chocaGrupo($nombre_dia,$desde,$hasta,$aulas,$grupo){
        foreach ($aulas as $aula) {
            $dia = $aula->getDia($nombre_dia);
            for($h=$desde; $h<$hasta; $h++){
                $hora = $dia->getHoras()[$h-1];
                if(!$hora->estaDisponible()){
                    $grupoHora = $hora->getGrupo();
                    if($grupoHora->getId_Agrup() == $grupo->getId_Agrup() && $grupoHora->getId_grupo() == $grupo->getId_grupo()){
                        $g = $grupo->getId_grupo();
                        $a = $grupo->getId_Agrup();
                        error_log ("Este grupo: $g de la Agrupacion $a choca en hora: $h del dia $nombre_dia",0);
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    /**Devuelve las primeras horas disponibles consecutivas que encuentre
     * 
     * @param idDocente
     * @param horas = horas del dia en que va a tratar de asignar
     * @param cantidadHoras = cuantas horas a asignar
     * @param desde = desde cual hora tratar de asignar
     * @param hasta = hasta cual hora tratar de asginar
     * @param nombre_dia = nombre del dia en el que se quiere asignar, se usa para comprobar choques
     * @param materia = objeto materia que se esta tratando de asignar
     * @param aulas = todas las aulas de campus, se usan para verificar choques
     * @param todas_mats = array de todas las materias que posee campus
     * @param ultimoRecurso
     * @return las horas disponibles sin choque en las que se puede asignar el grupoHora; null si no hay ninguna
     */
    public static function buscarHorasDisponibles($idDocente,$horas,$cantidadHoras,$desde,$hasta,$nombre_dia,$materia,$aulas,$todas_mats,$ultimoRecurso){
        $horasDisponibles = array();
        $resultado = null;
        for($i=$desde;$i<$hasta;$i++){
            $hayBloquesDisponibles=false;
            if($horas[$i]->estaDisponible() && $horas[$i]->getIdHora()<=($hasta+1)-$cantidadHoras){
                $hayBloquesDisponibles = true;
                for($j=$i+1;$j<$i+$cantidadHoras;$j++){
                    $hora = $horas[$j];
                    if($hora->getIdHora()==8){
                        $hayBloquesDisponibles=false;
                        break;
                    }
                    if(!$hora->estaDisponible()){
                        $hayBloquesDisponibles=false;
                        break;
                    }
                }
            }
            
            if($hayBloquesDisponibles){
                $chocaMateria = self::chocaMateria($nombre_dia, $horas[$i]->getIdHora(), $aulas, $materia, $cantidadHoras, $todas_mats);
                $chocaDocente = self::chocaGrupoDocente($idDocente, $horas[$i]->getIdHora(), $horas[$i]->getIdHora()+$cantidadHoras, $aulas, $nombre_dia);
                if(!$chocaMateria && !$chocaDocente){
                    for ($j = $i; $j < $i+$cantidadHoras; $j++) {
                        $horasDisponibles[] = $horas[$j];
                    }
                    error_log("ahi va un bloque para asignar",0);
                    $resultado = $horasDisponibles;
                    return $resultado;
                } else{
                    $resultado = "Choque";
                    if(!$ultimoRecurso){
                        return $resultado;
                    }
                }
            }
        }
        return $resultado;
    }
    
    /**
     * 
     * @param horas = horas del dia en que va a tratar de asignar
     * @param cantidadHoras = cuantas horas a asignar
     * @param desde = desde cual hora tratar de asignar
     * @param hasta = hasta cual hora tratar de asginar
     * @param nombre_dia
     * @param aulasConCapa
     * @param grupo
     * @return horas disponibles en las que se puede asignar el grupoHora aunque hayan choques
     */
    public static function buscarHorasDisponiblesParaChoque($horas,$cantidadHoras,$desde,$hasta,$nombre_dia,$aulasConCapa,$grupo){
        $horasDisponibles = array();
        for($i=$desde;$i<$hasta;$i++){
            $hayBloquesDisponibles=false;
            if($horas[$i]->estaDisponible() && $horas[$i]->getIdHora()<=($hasta+1)-$cantidadHoras){
                $hayBloquesDisponibles = true;
                for($j=$i+1;$j<$i+$cantidadHoras;$j++){
                    $hora = $horas[$j];
                    if($hora->getIdHora()==8){
                        $hayBloquesDisponibles=false;
                        break;
                    }
                    if(!$hora->estaDisponible()){
                        $hayBloquesDisponibles=false;
                        break;
                    }
                }
            }
            
            if($hayBloquesDisponibles){
                $grupoChocaConElMismo = self::chocaGrupo($nombre_dia, $horas[$i]->getIdHora(), $horas[$i]->getIdHora()+$cantidadHoras, $aulasConCapa, $grupo);
                $chocaDocente = self::chocaGrupoDocente($grupo->getId_docente(), $horas[$i]->getIdHora(), $horas[$i]->getIdHora()+$cantidadHoras, $aulasConCapa, $nombre_dia);
                if(!$grupoChocaConElMismo && !$chocaDocente){
                    for ($j = $i; $j < $i+$cantidadHoras; $j++) {
                        $horasDisponibles[] = $horas[$j];
                    }
                    return $horasDisponibles;
                }
            }
        }
        return null;
    }
    
    /** Metodo para buscar horas en un dia elegido debajo de una materia del mismo nivel
     * 
     * @param idDocente = para verificar si el docente no tiene asignado un grupo a la misma hora
     * @param cantidadHoras = numero de horas que se quieren asignar
     * @param desde = desde cual hora se quiere hacer la asignacion
     * @param hasta = hasta cual hora tratar de hacer la asignacion
     * @param nombre_dia = nombre del dia en que se quiere hacer la asignacion
     * @param materia = objeto materia de la cual se quiere asignar un grupoHora
     * @param aulasConCapa = array de aulas que tienen capacidad para asignar al grupoHora de la materia
     * @param aulas = array de todas las aulas que tiene el campus, se usa para verificar si hay choques
     * @param todas_mats = array de todas las materias del campus, se usa para comprobar choques
     * @param grupo
     * @return horas disponibles en las que se puede asignar el grupoHora
     */
    public static function buscarHoras($idDocente,$cantidadHoras,$desde,$hasta,$nombre_dia,$materia,$aulasConCapa,$aulas,$todas_mats){
        $horasDisponibles = null;
        for($x=0; $x<count($aulasConCapa); $x++){
            $a = $aulasConCapa[$x]->getNombre();
            error_log ("A probar en aula $a",0);
            $dia = $aulasConCapa[$x]->getDia($nombre_dia);
            $resul = self::buscarHorasDisponibles($idDocente,$dia->getHoras(),$cantidadHoras,$desde,$hasta,$nombre_dia,$materia,$aulas,$todas_mats,false);
            if($resul != null && $resul == "Choque"){
                break;
            } else if($resul != null && count($resul)!=0){
                $horasDisponibles = $resul;
                break;
            }
        }
        return $horasDisponibles;
    }
    
    public static function buscarHorasUltimoRecurso($idDocente,$cantidadHoras,$desde,$hasta,$nombre_dia,$materia,$aulasConCapa,$aulas,$todas_mats){
        $horasDisponibles = null;
        for($x=0; $x<count($aulasConCapa); $x++){
            $a = $aulasConCapa[$x]->getNombre();
            error_log ("A probar en aula $a Desde: $desde Hasta: $hasta",0);
            $dia = $aulasConCapa[$x]->getDia($nombre_dia);
            $resul = self::buscarHorasDisponibles($idDocente,$dia->getHoras(),$cantidadHoras,$desde,$hasta,$nombre_dia,$materia,$aulas,$todas_mats,true);
            if($resul != null && $resul != "Choque"){
                $horasDisponibles = $resul;
                break;
            }
        }
        return $horasDisponibles;
    }
    
    public static function buscarHorasConChoque($cantidadHoras,$desde,$hasta,$nombre_dia,$aulasConCapa,$grupo){
        $horasDisponibles = null;
        for($x=0; $x<count($aulasConCapa); $x++){
            $dia = $aulasConCapa[$x]->getDia($nombre_dia);
            if(!self::grupoPresente($desde, $hasta, $nombre_dia, $grupo, $aulasConCapa)){
                $horasDisponibles = self::buscarHorasDisponiblesParaChoque($dia->getHoras(),$cantidadHoras,$desde,$hasta,$nombre_dia,$aulasConCapa,$grupo);
            }
            else{
                break;
            }
            if($horasDisponibles != null){
                break;
            }
        }
        return $horasDisponibles;
    }
    
    /**
     * Para ver si ya se asignó el grupo en un día
     * @param desde
     * @param hasta
     * @param nombre_dia
     * @param grupo
     * @param aulas
     * @return 
     */
    public static function grupoPresente($desde, $hasta, $nombre_dia, $grupo, $aulas){
        foreach ($aulas as $aula) {
            $dia = $aula->getDia($nombre_dia);
            for($i=$desde; $i<$hasta; $i++){
                $hora = $dia->getHoras()[$i];
                if(!$hora->estaDisponible()){
                    $grupoHora = $hora->getGrupo();
                    if($grupoHora->getId_Agrup() == $grupo->getId_Agrup() && $grupoHora->getId_grupo() == $grupo->getId_grupo()){
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
     * @param materia = materia a la que pertenece el grupo que se quiere asignar
     * @param todas_mats = array con todas las materias de la facultad
     * @param todas_agrups = todas las agrupaciones en sistema
     * @param aulas
     * @param nombreDia
     * @return ultima hora en la que hay una materia del mismo nivel
     */
    public static function getUltimasHoraDeNivel($grupo,$materia,$todas_mats,$todas_agrups,$aulas,$nombreDia){
        $horasNivel = array();
        foreach ($aulas as $aula) {
            $hora = -1;
            $horas = $aula->getDia($nombreDia)->getHoras();
            for($x=0; $x<count($horas); $x++){
                if(!$horas[$x]->estaDisponible() && ManejadorAgrupaciones::obtenerIdDepartamento($horas[$x]->getGrupo()->getId_Agrup(),$todas_agrups) == $materia->getDepartamento()){
                    $grupoHora = $horas[$x]->getGrupo();
                    $materias = ManejadorMaterias::getMateriaDeGrupo($grupoHora->getId_Agrup(), $todas_mats);
                    foreach ($materias as $materiaHora) {
                        if(strcmp($materiaHora->getCodigoCarrera(),$materia->getCodigoCarrera())==0 && $materiaHora->getCiclo() == $materia->getCiclo()){
                            if(strcmp($materiaHora->getCodigo(),$materia->getCodigo())==0 && $grupoHora->getId_grupo() != $grupo->getId_grupo()){
                                $hora = $x;
                                break;
                            }
                            else if(strcmp($materiaHora->getCodigo(),$materia->getCodigo())!=0){
                                $hora = $x;
                                break;
                            }
                        }
                    }
                }
            }
            if($hora != -1){
                $horasNivel[] = $hora;
            }
        }
        return $horasNivel;
    }
    
    /** Metodo para generar nuevas horas clase
     * 
     * @param initManana = hora de inicio del dia clase
     * @param initTarde = hora de final dia clase
     * @return horas generadas en los limites recibidos
     */
    public static function generarHoras($initManana,$initTarde){
        $id=1;
        $horaInicial=$initManana;
        $horaFinal=new DateTime();
        $duracionHora = 60 * 50;
        $horas = array();
        try{
            if($horaInicial->getTimestamp()+($duracionHora*7) > $initTarde->getTimestamp()){
                throw new Exception("Horas se sobrelapan");
            }
            while($id <= 15){
                $horaFinal->setTimestamp($horaInicial->getTimestamp()+($duracionHora*7));
                $hora = new Hora();
                $hora->setId($id);
                $elementosHora = getdate($horaInicial->getTimestamp());
                $hora->setInicio($elementosHora[hours]+":"+$elementosHora[minutes]+":00");
                $elementosHora = getdate($horaFinal->getTimestamp());
                $hora->setFin($elementosHora[hours]+":"+$elementosHora[minutes]+":00");
                $horas[] = $hora;
                $horaInicial->setTimestamp($horaFinal->getTimestamp());
                $id++;
                if($id == 8){
                    $horaInicial->setTimestamp($initTarde->getTimestamp());
                    $horaFinal->setTimestamp($horaInicial->setTimestamp());
                }
            }
        } catch (Exception $ex) {
            echo "Error en generarHoras()";
        }
        return $horas;
    }
    
    /** Metodo para actulizar las horas creadas con generarHoras() en la base de datos
     * 
     * @param horas = horas generadas con generarHoras()
     */
    public static function actualizarHoras($horas){
        foreach ($horas as $hora) {
            $resultado = Conexion::consulta("UPDATE horas_test SET inicio='$hora->getIdHora()',final='$h->getInicio()' WHERE id_hora='$h->getFin()'");
        }
    }
}
