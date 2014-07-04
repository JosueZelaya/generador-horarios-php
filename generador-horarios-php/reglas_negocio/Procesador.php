<?php
/**
 * Description of Procesador
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

class Procesador {
    
    /**
     * @var Materia $materia = La materia a la que pertenecen los grupos que se asignan
     * @var Aula[] $todasAulas = Todas las aulas con las que cuenta la facultad
     * @var Integer $holguraAula = La holgura que cada aula debe tener al albergar alumnos
     * @var Aula[] $aulasPosibles = Todas las aulas en las que se podria asignar la materia
     */
    private $materia;
    private $todasAulas;
    private $holguraAula;
    private $aulasPosibles;
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
     * @var Integer $limite Fin de turno matutino e inicio de turno vespertino
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
        $this->holguraAula=10;
        $this->agrupacion = $grupo->getAgrup();
        $this->materia = $this->agrupacion->getMaterias()[0];             //La materia a la que corresponde el grupo a procesar
        $this->grupo = $grupo;
        self::asignarAulas();
        $this->prioridad = $prioridad;
        self::establecerTurno(false);                 //Se establece el turno en franja matutina o vespertina segun ciclo de la materia
        if(self::localizarBloque()){
            return;
        }
        $this->reiniciarProceso();
    }
    
    public function asignarAulas(){
        if(preg_match("/^(TEORICO|DISCUSION)$/", $this->grupo->getTipo())){
            $aulasPref = $this->materia->getAulas_gtd();
        } elseif($this->grupo->getTipo()=='LABORATORIO'){
            $aulasPref = $this->materia->getAulas_gl();
        }
        if(count($aulasPref)==0){
            $this->aulasPosibles = ManejadorAulas::obtenerAulasPorCapacidad($this->todasAulas, $this->agrupacion->getNum_alumnos()+$this->holguraAula);
        } elseif(!$aulasPref['exclusiv']){
            $this->aulasPosibles = array_merge($aulasPref['aulas'],ManejadorAulas::obtenerAulasPorCapacidad($this->todasAulas, $this->agrupacion->getNum_alumnos()+$this->holguraAula));
        } elseif($aulasPref['exclusiv']){
            $this->aulasPosibles = $aulasPref['aulas'];
        }
    }

    //Devuelve un número aleatorio entre los límites desde y hasta, los límites también se incluyen
    /**
     * 
     * @param Integer $desde
     * @param Integer $hasta
     * @return Integer
     */
    public static function getNumeroAleatorio($desde,$hasta){
        return mt_rand($desde, $hasta);
    }
    
    //Calcula el número de horas continuas que se necesitan para impartir las clases
    //La materia contiene varios grupos de trabajo
    //A cada grupo de trabajo (teorico o de discusion) se le debe asignar la misma cantidad de horas que requiere la materia a la semana
    public function calcularHorasContinuasRequeridas(){
        if($this->grupo->getTipo()=='LABORATORIO'){
            return $this->materia->getHorasLab();
        } elseif($this->grupo->getTipo()=='DISCUSION'){
            return $this->materia->getHorasDiscu();
        }
        $horasRequeridas = $this->materia->getTotalHorasRequeridas(); //Horas requeridas por semana
        $horasAsignadas = $this->grupo->getHorasAsignadas(); //Horas que ya han sido asignadas a la materia esta semana
        if($horasRequeridas==3 || $horasRequeridas==1){
            return $horasRequeridas;
        }else if($horasRequeridas-$horasAsignadas==3){
            return 3;
        }else{
            return 2;
        }
    }

     /*
     * Establece en qué turno debe impartirse la materia
     * Si $change = false: Para las materias de los primeros años (menores al septimo semestre):
     * -Su turno abarca las horas de la mañana y parte de la tarde
     * Para las materias de los últimos años (mayores al sexto semestre):
     * -Su turno abarca los horarios de la tarde y noche
     * Si $change = true: Materias menores al ciclo 7 se ubican en turno tarde/noche
     * y materias mayores a ciclo 6 se ubican en turno de la mañana
     */
    public function establecerTurno($change){
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

    public function ajustarTurnoPrioridad($numHoras){
        if ($this->desde==0){
            $this->hasta= $this->desde+$numHoras;
        } else {
            $this->desde = $this->hasta-$numHoras;
        }
    }

    //Asigna al grupo en las horas correspondientes
    public function asignar($horasDisponibles){
        foreach ($horasDisponibles as $hora){
            $hora->setGrupo($this->grupo);
            $hora->setDisponible(FALSE);
            $this->grupo->setHorasAsignadas($this->grupo->getHorasAsignadas()+1);
        }
        if($this->materia->getTotalHorasRequeridas() == $this->grupo->getHorasAsignadas() || preg_match("/^(LABORATORIO|DISCUSION)$/", $this->grupo->getTipo())){
            $this->grupo->setIncompleto(false);
        }
    }
    
    /** Intenta asignar un aula para la materia de acuerdo a su capacidad y al número de alumnos de esa materia
     * Cada materia se divide en uno o más grupos, dependiendo de la cantidad de alumnos que cursan la materia.
     * 
     */
    public function localizarBloque(){
        error_log ("A tratar con ".$this->materia->getNombre()." GT: ".$this->grupo->getId_grupo(),0);
        if(self::asignarDias(false)){ //se trata de asignar el grupo en el aula elegida comprobando si existen choques
            return true;
        }else if($this->agrupacion->getNumGrupos($this->grupo->getTipo()) > 1 && self::asignarDias(true)){ //se asignan las horas que no se pudieron asignar debido a que hubieron choques de horario, esta vez ya no se consideran choques
            return true;
        }
        return false;
    }
    
    public function reiniciarProceso(){
        self::establecerTurno(true);
        if(self::localizarBloque()){
            return;
        } elseif ($this->prioridad){
            $this->prioridad = false;
            $this->establecerTurno(false);
            if(self::localizarBloque()){
                return;
            }
            self::establecerTurno(true);
            if(self::localizarBloque()){
                return;
            }
        }
        if($this->asignarEnUltimoDia()){
            if($this->grupo->isIncompleto() && $this->grupo->getTipo()=="TEORICO"){
                goto regresion;
            } elseif($this->grupo->isIncompleto() && $this->grupo->getTipo()!="TEORICO"){
                throw new Exception("¡Sin cupo para el grupo ".$this->grupo->getId_grupo()." ".$this->grupo->getTipo()." Materia: ".implode(',',ManejadorGrupos::obtenerNombrePropietario($this->grupo->getAgrup()->getMaterias()))." Departamento: ".implode(',', ManejadorGrupos::obtenerIdDepartamento($this->grupo->getAgrup()->getMaterias()))." !");
            } else{
                return;
            }
        }else{
            regresion:
                $this->regresionHolgura();
        }
    }
    
    public function regresionHolgura(){
        $this->establecerTurno(false);
        while($this->holguraAula != 0 && $this->grupo->isIncompleto()){
            $this->holguraAula -= 5;
            $this->aulasPosibles = ManejadorAulas::obtenerAulasPorCapacidad($this->todasAulas,  $this->agrupacion->getNum_alumnos()+$this->holguraAula);
            if(self::localizarBloque()){
                return;
            }else{
                self::reiniciarProceso();
            }
        }    
        if($this->grupo->isIncompleto()){
            throw new Exception("¡Sin cupo para el grupo ".$this->grupo->getId_grupo()." ".$this->grupo->getTipo()." Materia: ".implode(',',ManejadorGrupos::obtenerNombrePropietario($this->grupo->getAgrup()->getMaterias()))." Departamento: ".implode(',', ManejadorGrupos::obtenerIdDepartamento($this->grupo->getAgrup()->getMaterias()))." !");
        }
    }
    
    public function asignarEnUltimoDia(){
        $dia = $this->aulasPosibles[0]->getDia("Sabado");
        $horas = $dia->getHoras();
        $this->desde = $horas[0]->getIdHora()-1;
        $this->hasta = $horas[count($horas)-1]->getIdHora();
        $numHorasContinuas = self::calcularHorasContinuasRequeridas($this->materia, $this->grupo);
        $horasDisponibles = ManejadorHoras::buscarHorasUltimoRecurso($this->grupo->getDocentes(),  $numHorasContinuas,  $this->desde,  $this->hasta,"Sabado",  $this->materia,  $this->aulasPosibles,  $this->todasAulas);
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
    public function asignarDias($choques){
        $dias = $this->aulasPosibles[0]->getDias();
        $diasUsados=array();
        $docentes = $this->grupo->getDocentes();
        //Se repite el proceso hasta que todas las horas del grupo hayan sido asignadas
        while ($this->grupo->isIncompleto()) {
            //Se debe elegir un día diferente para cada clase
            $diaElegido = ManejadorDias::elegirDiaDiferente($dias, $diasUsados); //Elegimos un día entre todos que sea diferente de los días que ya hemos elegido
            if($diaElegido != NULL){
                error_log ("Se probara en dia ".$diaElegido->getNombre(),0);
                foreach ($docentes as $docente){
                    if($docente->getCargo() != null && $docente->getCargo()->getId_dia_exento() == $diaElegido->getId()){
                        error_log ("Docente ".$docente->getIdDocente()." no esta habilitado en dia ".$diaElegido->getNombre());
                        goto fin;
                    }
                }         
                if(!$choques){
                    if(!$this->prioridad){
                        self::asignarHorasSinChoques($diaElegido->getNombre());
                    } else{
                        self::asignarHorasSinChoquesPrioridad($diaElegido->getNombre());
                    }
                } else{
                    self::asignarHorasConChoques($diaElegido->getNombre());
                }
                fin:
                    $diasUsados[] = $diaElegido; //Guardamos el día para no elegirlo de nuevo para esta materia
            }else{
                return FALSE;
            }
        }
        return TRUE;
    }
    
     /** Asignar Horas considerando choques
     * 
     * @param nombreDia = nombre del dia en el que se quiere hacer la asignacion; se utiliza para compbrobar choques
     */
    public function asignarHorasSinChoques($nombreDia){
        if(ManejadorHoras::grupoPresente($this->desde, $this->hasta, $nombreDia, $this->grupo, $this->todasAulas)){
            return;
        }
        $horasDisponibles = NULL;
        $numHorasContinuas = self::calcularHorasContinuasRequeridas(); //Calculamos el numero de horas continuas para la clase
        $horasNivel = ManejadorHoras::getUltimasHoraDeNivel($this->agrupacion, $this->aulasPosibles, $nombreDia);
        foreach ($horasNivel as $materia) {
            foreach ($materia as $hora){
                if($hora+$numHorasContinuas < $this->hasta){
                    $horasDisponibles = ManejadorHoras::buscarHoras($this->grupo->getDocentes(), $numHorasContinuas, $hora+1, $hora+1+$numHorasContinuas, $nombreDia, $this->materia, $this->aulasPosibles, $this->todasAulas);
                    if($horasDisponibles != NULL){
                        goto nextEval;
                    }
                }
            }
        }
        nextEval:
        if($horasDisponibles == NULL){
                $horasDisponibles = ManejadorHoras::buscarHorasUltimoRecurso($this->grupo->getDocentes(),$numHorasContinuas, $this->desde,$this->hasta,$nombreDia,$this->materia,$this->aulasPosibles,$this->todasAulas); //elige las primeras horas disponibles que encuentre ese día
        }
        if($horasDisponibles != NULL){
                self::asignar($horasDisponibles);
        }
    }
    
    public function asignarHorasSinChoquesPrioridad($nombreDia){
        $numHorasContinuas = self::calcularHorasContinuasRequeridas(); //Calculamos el numero de horas continuas para la clase
        $this->ajustarTurnoPrioridad($numHorasContinuas);
        if(ManejadorHoras::grupoPresente($this->desde, $this->hasta, $nombreDia, $this->grupo, $this->todasAulas)){
            return;
        } 
        $horasDisponibles = ManejadorHoras::buscarHoras($this->grupo->getDocentes(), $numHorasContinuas, $this->desde, $this->hasta, $nombreDia, $this->materia, $this->aulasPosibles, $this->todasAulas);
        if ($horasDisponibles != null){
            self::asignar($horasDisponibles);
        }
    }


    //Asignar horas sin considerar choques
    public function asignarHorasConChoques($nombreDia){
        $numHorasContinuas = self::calcularHorasContinuasRequeridas();  //Calculamos el numero de horas continuas para la clase
        if($this->prioridad){
            $this->ajustarTurnoPrioridad($numHorasContinuas);
        }
        if(ManejadorHoras::grupoPresente($this->desde, $this->hasta, $nombreDia, $this->grupo, $this->todasAulas)){
            return;
        } 
        $horasDisponibles = ManejadorHoras::buscarHorasConChoque($numHorasContinuas, $this->desde, $this->hasta, $nombreDia, $this->aulasPosibles, $this->grupo);
        if($horasDisponibles != null){
            self::asignar($horasDisponibles);
        }
    }
}