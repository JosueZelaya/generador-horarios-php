<?php
/**
 * Procesamiento diseñado para manejar 15 horas clase en un dia
 *
 * @author arch
 */
chdir(dirname(__FILE__));
include_once 'Grupo.php';
include_once 'Materia.php';
include_once 'Agrupacion.php';
include_once 'ManejadorAulas.php';
include_once 'Hora.php';
include_once 'Dia.php';
include_once 'Aula.php';
include_once 'Docente.php';
include_once 'ManejadorDias.php';
include_once 'ManejadorHoras.php';
include_once 'ManejadorGrupos.php';
include_once 'ManejadorAgrupaciones.php';

class Procesador {
    /**
     * @var Materia $materia = La materia a la que pertenecen los grupos que se asignan
     * @var Aula[] $todasAulas = Todas las aulas con las que cuenta la facultad
     * @var Integer $holguraAula = La holgura que cada aula debe tener al albergar alumnos
     * @var Aula[] $aulasPosibles = Todas las aulas en las que se podria asignar la materia
     * @var Integer $alumnosEnGrupo = cantidad de alumnos que tiene un grupo y se quieren asignar en un aula
     * @var boolean $aulasExclusivas = true si se ha obtenido una lista de aulas unicas para evaluar la asignacion de un grupo
     */
    private $materia;
    private $todasAulas;
    private $holguraAula;
    private $aulasPosibles;
    private $alumnosEnGrupo;
    private $aulasExclusivas;
    /**
     *
     * @var Integer[] $horasAsignables = ids de horas de trabajo de el(los) docente(s)
     */
    private $horasAsignables;
    /**
     * @var Agrupacion $agrupacion = Agrupacion a la que pertenece el grupo a asignar un horario
     */
    private $agrupacion;
    /**
     * @var Grupo $grupo    Grupo a asignar
     */
    private $grupo;
    /**
     * @var Boolean $prioridad = true, si es un grupo asignado a un docente (EV)CT o (EV)MT
     * @var Integer $desde
     * @var Integer $hasta
     * @var Integer $limite Fin de turno primeros años e inicio de turno ultimos años
     */
    private $prioridad;
    private $desde;
    private $hasta;
    private $limite;

    public function __construct($aulas) {
        $this->limite=10;
        $this->todasAulas = $aulas;
    }
    
    /** Asigna propiedades de la clase relacionadas al grupo que se quiere asignar horario, hace la llamada para asignar horas al grupo
     *  en caso de no poder asignarle horas, se reiniciara el procesamiento con un ajuste de propiedades de la clase
     * @param Grupo $grupo = grupo que se quiere asignar horario
     * @param boolean $prioridad = true, si es un grupo asignado a un docente CT o MT
     */
    public function procesarGrupo($grupo,$prioridad){
        if($grupo->isProcesado()){
            throw new Exception("Grupo ya fue procesado");
        }
        $this->holguraAula=10;
        $this->agrupacion = $grupo->getAgrup();
        $this->alumnosEnGrupo = $this->agrupacion->getNum_alumnos();
        $this->materia = $this->agrupacion->getMaterias()[0];             //La materia a la que corresponde el grupo a procesar
        $this->grupo = $grupo;
        self::asignarAulas();
        $this->prioridad = $prioridad;
        if($this->prioridad){
            $this->horasAsignables = $this->traducirIdHorasEnIndices(ManejadorDocentes::intersectarHorarios($this->grupo->getDocentes(),false), $this->todasAulas[0]->getDias()[0]);
        }
        self::establecerTurno(false);                 //Se establece el turno en franja matutina o vespertina segun ciclo de la materia
        if($this->localizarBloque()){
            return;
        }
        $this->reiniciarProceso();
    }
    
    private function asignarAulas(){
        if(preg_match("/^(TEORICO|DISCUSION)$/", $this->grupo->getTipo())){
            $aulasPref = $this->agrupacion->getAulas_gtd();
        } elseif($this->grupo->getTipo()=='LABORATORIO'){
            $aulasPref = $this->agrupacion->getAulas_gl();
        }
        if(count($aulasPref)==0){
            $this->aulasPosibles = ManejadorAulas::obtenerAulasPorCapacidad($this->todasAulas, $this->alumnosEnGrupo+$this->holguraAula);
            $this->aulasExclusivas = false;
        } elseif(!$aulasPref['exclusiv']){
            $this->aulasPosibles = array_merge($aulasPref['aulas'],ManejadorAulas::obtenerAulasPorCapacidad($this->todasAulas, $this->alumnosEnGrupo+$this->holguraAula));
            $this->aulasExclusivas = false;
        } elseif($aulasPref['exclusiv']){
            $this->aulasPosibles = $aulasPref['aulas'];
            $this->aulasExclusivas = true;
        }
    }

    //Devuelve un número aleatorio entre los límites desde y hasta, los límites también se incluyen
    /**
     * 
     * @param Integer $desde limite inferior
     * @param Integer $hasta limite superior
     * @return Integer
     */
    private static function getNumeroAleatorio($desde,$hasta){
        return mt_rand($desde, $hasta);
    }
    
    //Calcula el número de horas continuas que se necesitan para impartir las clases
    private function calcularHorasContinuasRequeridas(){
        $horasRequeridas = ManejadorAgrupaciones::obtenerHorasTipoGrupo($this->grupo, $this->agrupacion);
        if(is_numeric($horasRequeridas) && (($this->grupo->getTipo()=='TEORICO' && $horasRequeridas>=14) || (preg_match("/^(LABORATORIO|DISCUSION)$/", $this->grupo->getTipo()) && $horasRequeridas>7))){
            $newHorasReq = self::calcularBloquesHorasSinDesbordeDias($horasRequeridas, count($this->todasAulas[0]->getDias()));
            if($newHorasReq!=0){
                $this->agrupacion->setBloquesRequeridos($newHorasReq);
                $horasRequeridas = ManejadorAgrupaciones::obtenerHorasTipoGrupo($this->grupo, $this->agrupacion);
            }
        }
        if(is_array($horasRequeridas)){
            $horas = intval ($horasRequeridas[0]);
            return $horas;
        } elseif(preg_match("/^(LABORATORIO|DISCUSION)$/", $this->grupo->getTipo())){
            return $horasRequeridas;
        }
        $horasAsignadas = $this->grupo->getHorasAsignadas(); //Horas que ya han sido asignadas a la materia esta semana
        if($horasRequeridas==3 || $horasRequeridas==1){
            return $horasRequeridas;
        }else if($horasRequeridas-$horasAsignadas==3){
            return 3;
        }else{
            return 2;
        }
    }
    
    private function calcularBloquesHorasSinDesbordeDias($numHoras,$numDias){
        if($this->grupo->getTipo()=='TEORICO'){
            $limiteHoras = 5;
        } else{
            $limiteHoras = 7;
        }
        for($i=2;$i<=$numDias;$i++){
            $numHorasBloque = floor($numHoras/$i);
            $numHorasRestantes = $numHoras - ($numHorasBloque*$i);
            if($numHorasBloque <= $limiteHoras){
                if($numHorasRestantes <= $numHorasBloque && $numHorasRestantes!=1){
                    for($x=0;$x<$i;$x++){
                        $horas[]=$numHorasBloque;
                    }
                    if($numHorasRestantes!=0){
                        $horas[]=$numHorasRestantes;
                    }
                    return $horas;
                }
            }
        }
        return 0;
    }

     /*
     * Establece en qué turno debe impartirse la materia
     * Si $change = false: Para las materias de los primeros años (menores al septimo semestre):
     * -Su turno abarca las horas de la mañana y parte de la tarde
     * Para las materias de los últimos años (mayores al sexto semestre):
     * -Su turno abarca los horarios de la tarde y noche
     * $change = true: Materias menores al ciclo 7 se ubican en turno tarde/noche
     * y materias mayores a ciclo 6 se ubican en turno de la mañana
     */
    private function establecerTurno($change){
        if ( ($this->materia->getCiclo()<=6 && $change) || ($this->materia->getCiclo()>6 && !$change) ){
            goto turnoVesp;
        } elseif ( ($this->materia->getCiclo()>6 && $change) || ($this->materia->getCiclo()<=6 && !$change) ){
            goto turnoMatu;
        }
        turnoVesp:{
            $this->desde = $this->limite;
            $this->hasta = 15;
            return;
        }
        turnoMatu:{
            $this->desde = 0;
            $this->hasta = $this->limite;
        }
    }

    private function ajustarTurnoPrioridad($desde,$numHoras){
        if(($desde+$numHoras) > 15){
            return false;
        } else{
            $this->desde=$desde;
            $this->hasta=  $this->desde+$numHoras;
            return true;
        }
    }

    /** Asignar el grupo en procesamiento al bloque de horas encontrado
     * 
     * @param Hora[] $horasDisponibles array de horas que cumplen los criterios de procesamiento para asignar un grupo a ellas
     */
    private function asignar($horasDisponibles){
        foreach ($horasDisponibles as $hora){
            $hora->setGrupo($this->grupo);
            $hora->setDisponible(FALSE);
            $this->grupo->setHorasAsignadas($this->grupo->getHorasAsignadas()+1);
        }
        $this->agrupacion->limpiarBloqueAsignado();
        if(is_numeric(ManejadorAgrupaciones::obtenerHorasTipoGrupo($this->grupo, $this->agrupacion)) && ($this->agrupacion->getTotalHorasRequeridas() == $this->grupo->getHorasAsignadas() || preg_match("/^(LABORATORIO|DISCUSION)$/", $this->grupo->getTipo()))){
            $this->grupo->setIncompleto(false);
            $this->grupo->setProcesado(true);
            $this->agrupacion->setBloquesRequeridos(null);
        } elseif(ManejadorAgrupaciones::obtenerHorasTipoGrupo($this->grupo, $this->agrupacion) == null){
            $this->grupo->setIncompleto(false);
            $this->grupo->setProcesado(true);
            $this->agrupacion->setBloquesRequeridos(null);
        }
    }
    
    /** Ubicar bloque de horas en dia escogido para el grupo en procesamiento considerando choques de materia de mismo nivel en primera instancia
     * y en segunda instancia sin consideras los choques de materia (solo cuando existe mas de 1 grupo de la materia)
     * 
     */
    private function localizarBloque(){
/*>>>>>>>>>>>>>>*/        error_log ("A tratar con ".$this->materia->getNombre()." ".$this->grupo->getTipo().": ".$this->grupo->getId_grupo(),0);
        if(self::asignarDias(false)){ //se trata de asignar el grupo en el aula elegida comprobando si existen choques
            return true;
        }else if($this->agrupacion->getNumGrupos($this->grupo->getTipo()) > 1 && self::asignarDias(true)){ //se asignan las horas que no se pudieron asignar debido a que hubieron choques de horario, esta vez ya no se consideran choques
            return true;
        }
        return false;
    }
    
    private function reiniciarProceso(){
        if(!$this->prioridad){
            $this->establecerTurno(true);
            if($this->localizarBloque()){
                return;
            }
        } else{
            $this->prioridad = false;
            $this->establecerTurno(false);
            if($this->localizarBloque()){
                return;
            }
            $this->establecerTurno(true);
            if($this->localizarBloque()){
                return;
            }
            $this->prioridad = true;
        }
        if($this->asignarEnUltimoDia()){
            if($this->grupo->isIncompleto() && $this->grupo->getTipo()=="TEORICO"){
                goto regresion;
            } elseif($this->grupo->isIncompleto() && $this->grupo->getTipo()!="TEORICO"){
                $this->forzarFinal();
            } else{
                return;
            }
        }
        regresion:
            if(!$this->aulasExclusivas){
                $this->regresionHolgura();
            }
    }
    
    private function regresionHolgura(){
        $this->establecerTurno(false);
        while($this->holguraAula != 0 && $this->grupo->isIncompleto()){
            $this->holguraAula -= 5;
            $this->asignarAulas();
            if($this->localizarBloque()){
                return;
            }else{
                self::reiniciarProceso();
            }
        }
        if($this->grupo->isIncompleto()){
            $this->regresionAulas();
        }
    }
    
    private function regresionAulas(){
        if($this->aulasExclusivas){
            $this->forzarFinal();
        }
/*>>>>>>>>>>>>>>*/        error_log("voy a comenzar regresion aulas",0);
//        $this->aulasPosibles = ManejadorAulas::obtenerAulasCapacidadCercana($this->todasAulas, end($this->aulasPosibles));
        $this->aulasPosibles = ManejadorAulas::obtenerAulasCapacidadCercana($this->todasAulas, $this->aulasPosibles[0]);
        if($this->aulasPosibles == null){
            $this->forzarFinal();
        } else{
            $this->aulasExclusivas=true;
            if($this->localizarBloque()){
                return;
            }else{
                self::reiniciarProceso();
            }
        }
        if($this->grupo->isIncompleto()){
            $this->forzarFinal();
        }
    }
    
    private function asignarEnUltimoDia(){
        $dia = $this->aulasPosibles[0]->getDia("Sabado");
        if(ManejadorDocentes::docenteInhabilitado($this->grupo->getDocentes(), $dia)){
            return false;
        }
        $horas = $dia->getHoras();
        $this->desde = $horas[0]->getIdHora()-1;
        $this->hasta = $horas[count($horas)-1]->getIdHora();
        $numHorasContinuas = self::calcularHorasContinuasRequeridas();
        $horasDisponibles = ManejadorHoras::buscarHoras($this->grupo->getDocentes(),  $numHorasContinuas,  $this->desde,  $this->hasta,"Sabado",  $this->agrupacion,  $this->aulasPosibles,  $this->todasAulas, true, false);
        if($horasDisponibles != null){
            self::asignar($horasDisponibles);
            return true;
        }
        return false;
    }
    
     /** Asignar dias al grupo
     * @param boolean $choques indica si se evaluaran choques o no
     * @return true si se puede hacer la asignacion de todas las horas que requiere el grupo
     */
    private function asignarDias($choques){
        $dias = $this->aulasPosibles[0]->getDias();
        $diasUsados=array();
        //Se repite el proceso hasta que todas las horas del grupo hayan sido asignadas en uno o mas dias escogidos segun numero de horas requeridas
        while ($this->grupo->isIncompleto()) {
            //Se debe elegir un día diferente para cada asignacion de un grupo
            $diaElegido = $this->elegirDiaDiferente($dias, $diasUsados); //Elegimos un día entre todos que sea diferente de los días que ya hemos elegido
            if($diaElegido == NULL){
                return FALSE;
            }
/*>>>>>>>>>>>>>>*/  error_log ("Se probara en dia ".$diaElegido->getNombre(),0);
            if(ManejadorDocentes::docenteInhabilitado($this->grupo->getDocentes(), $diaElegido)){
                goto fin;
            }elseif($this->prioridad && $this->horasAsignables!=null){
                $this->asignarHorasPrioridad($diaElegido->getNombre(),$choques);
            }else{
                $this->asignarHoras($diaElegido->getNombre(),$choques);
            }
            fin: $diasUsados[] = $diaElegido; //Guardamos el día para no elegirlo de nuevo para este grupo
        }
        return TRUE;
    }
    
     /** Asignar Horas considerando choques
     * 
     * @param nombreDia = nombre del dia en el que se quiere hacer la asignacion; se utiliza para compbrobar choques
     */
    private function asignarHoras($nombreDia,$choques){
        if(ManejadorHoras::grupoPresente($nombreDia, $this->grupo, $this->todasAulas)){
            return;
        }
        $horasDisponibles = NULL;
        $numHorasContinuas = $this->calcularHorasContinuasRequeridas(); //Calculamos el numero de horas continuas para la clase
        $horasNivel = ManejadorHoras::getUltimasHoraDeNivel($this->agrupacion, $this->aulasPosibles, $nombreDia);
        foreach ($horasNivel as $materia) {
            foreach ($materia as $hora){
                if($hora+$numHorasContinuas < $this->hasta){
                    $horasDisponibles = ManejadorHoras::buscarHoras($this->grupo->getDocentes(), $numHorasContinuas, $hora+1, $hora+1+$numHorasContinuas, $nombreDia, $this->agrupacion, $this->aulasPosibles, $this->todasAulas, false, $choques);
                    if($horasDisponibles != NULL){
                        goto nextEval;
                    }
                }
            }
        }
        nextEval:
        if($horasDisponibles == NULL){
            $horasDisponibles = ManejadorHoras::buscarHoras($this->grupo->getDocentes(), $numHorasContinuas, $this->desde, $this->hasta, $nombreDia, $this->agrupacion, $this->aulasPosibles, $this->todasAulas, false, $choques);
        }
        if($horasDisponibles != NULL){
            $this->asignar($horasDisponibles);
        }
    }
    
    private function asignarHorasPrioridad($nombreDia,$choques){
        if(ManejadorHoras::grupoPresente($nombreDia, $this->grupo, $this->todasAulas)){
            return;
        } 
        $numHorasContinuas = self::calcularHorasContinuasRequeridas(); //Calculamos el numero de horas continuas para la clase
        $horas = $this->horarioSegunBloque($numHorasContinuas);
        foreach ($horas as $hora) {
            if($this->ajustarTurnoPrioridad($hora,$numHorasContinuas)){
                $horasDisponibles = ManejadorHoras::buscarHoras($this->grupo->getDocentes(), $numHorasContinuas, $this->desde, $this->hasta, $nombreDia, $this->agrupacion, $this->aulasPosibles, $this->todasAulas, false, $choques);
            } else{
                break;
            }
            if ($horasDisponibles != null){
                self::asignar($horasDisponibles);
                break;
            }
        }
    }
    
    /** Obtener la posicion correspondiente de cada hora laboral de un docente en el array de horas laborales de la facultad
     * 
     * @param Integer[] $idHoras = array que contiene los id de las horas laborales de un docentes (se asume son continuas)
     * @param Dia $dia = un dia cualquiera para hacer uso de sus horas y mapear los ids de las horas
     * @return int[]
     */
    private static function traducirIdHorasEnIndices($idHoras,$dia){
        $indices = null;
        foreach ($idHoras as $idHora){
            $indices[] = $dia->getPosEnDiaHora($idHora);
        }
        return $indices;
    }
    
    /**
     * Elige un día al azar dentro del array
     * @param type $dias = array de dias
     * @return \Dia = el dia elegido
     */
    private static function elegirDia($dias){
        $desde = 0;
        $hasta = count($dias)-2;    //Le restamos dos para que no tome en cuenta el Sábado
        $dia = self::getNumeroAleatorio($desde, $hasta);
        return $dias[$dia];
    }

    /**
     * Devuelve un día diferente a los ya usados
     * 
     * @param Dia[] $dias = los dias posibles
     * @param Dia[] $diasUsados = los dias usados
     * @return Dia o null El dia elegido, si ya no hay dias disponibles devuelve null
     */
    private static function elegirDiaDiferente($dias,$diasUsados){
        //Si ya se usaron todos los días entonces no seguimos buscando y devolvemos null
        //Le restamos 1 para que no tome en cuenta el día Sábado
        if(count($dias)-1==count($diasUsados)){
            return null;
        }
        $elegido=null;
        do {
            $elegido = self::elegirDia($dias);
        } while (!ManejadorDias::sonDiferentes($elegido, $diasUsados));
        return $elegido;
    }
    
    private function forzarFinal(){
        $this->grupo->setProcesado(true);
        $this->agrupacion->setBloquesRequeridos(null);
        throw new Exception("¡Sin cupo para el grupo ".$this->grupo->getId_grupo()." ".$this->grupo->getTipo()." Materia: ".$this->grupo->getAgrup()->getMaterias()[0]->getCodigo()." Agrupacion: ".$this->grupo->getAgrup()->getId()."!");
    }
    
    /** Asumiendo que el horario laboral consta de horas ininterrumpidas
     * 
     * @param Integer $numHorasContinuas = numero de horas que se deben asignar en un dia
     * @return Integer[]
     */
    private function horarioSegunBloque($numHorasContinuas){
        $cuenta = $numHorasContinuas;
        $long = count($this->horasAsignables);
        $horas = array();
        for ($i=0;$i<$long;$i++){
            if($cuenta == $numHorasContinuas && $i+($numHorasContinuas-1)<$long){
                $horas[]=  $this->horasAsignables[$i];
            }
            $cuenta--;
            if($cuenta == 0){
                $cuenta = $numHorasContinuas;
            }
        }
        if(count($horas)==0){
            $horas[] = $this->horasAsignables[0];
        }
        return $horas;
    }
}
