<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Procesador
 *
 * @author arch
 */
class Procesador {
    
    private $facultad;                  //Información de la facultad para la cual se generará el horario.
    private $materia;                    //La materia que se desea asignar, esta se divide en grupos y son estos los que se asignan
    private $materias;        //Todas las materias que se imparten en la facultad
    private $aulas;              //Las aulas en las que se podría asignar, si estuvieran disponibles
    private $holguraAula;                    //La holgura que cada aula debe tener al albergar alumnos
    private $aulasConCapacidad;
    /* Las agrupaciones contienen la información de cuántos grupos contiene una materia,
     * cuántos alumnos son por cada grupo, a qué departamento(o Escuela) pertenece la materia
     * y cuántos grupos de esa materia se han asignado
     */
    private $agrupaciones;
    private $agrupacion;
    private $grupo; //Grupo a asignar
    
    private $desde;
    private $hasta;
    private $limite; //Separacion de hora para turno de primeros años y ultimos años
   
    public function __construct() {
        $this->holguraAula=10;
        $this->limite=10;
    }
    
    public function asignarDatos($facultad){
        $this->facultad = $facultad;
        $this->aulas = $facultad->getAulas();        //Se obtienen las aulas de la facultad en las cuales se podría asignar materias
        $this->materias = $facultad->getMaterias();  //Se obtienen todas las materias de la facultad
        $this->agrupaciones = $facultad->agrupaciones;  //Se obtienen todas las agrupaciones de materias que existen
    }
    
    /** Realiza el procesamiento necesario para generar el horario de una materia.
     * 
     * @param materia = La materia que se quiere asignar
     * @param id_docente = identificador del docente que impartira el grupo a asignar horario
     * @param agrupacion = agrupacion a la que pertenece la materia a asignar
     * @throws Exception = Si no existe la informacion necesario en el objeto Facultad
     */
    public function procesarMateria($materia,$id_docente,$agrupacion){
        if($this->facultad!=null){
            $this->materia = $materia;             //La materia que se debe procesar
            $this->agrupacion = $agrupacion; //Se busca dentro de todas las agrupaciones, cuál es la que pertenece a la materia que se quiere asignar
            $this->grupo = new Grupo();   //El grupo con la información de la agrupación, este grupo es el que será asignado en un aula
            $this->grupo->setId_agrup($agrupacion->getId());
            $this->grupo->setId_grupo($agrupacion->getNumGruposAsignados()+1);
            $this->grupo->setId_docente($id_docente); //Se le asigna al grupo a cual docente pertenecera para comprobaciones de choques
            $this->aulasConCapacidad = ManejadorAulas::obtenerAulasPorCapacidad($this->aulas,  $this->agrupacion->getNum_alumnos()+$this->holguraAula);
            self::establecerTurno();                 //Se establece el turno
            self::localizarBloqueOptimo();  //Debe asignar la materia a un aula de la facultdad
        } else{
            throw new Exception("No se encuentra la información de la facultad");
        }
    }
    
    //Asignar horas sin considerar choques
    public function asignarHorasSinConsiderarChoques($nombreDia){
        $horasDisponibles = array();
        $numHorasContinuas = self::calcularHorasContinuasRequeridas($this->materia, $this->grupo);  //Calculamos el numero de horas continuas para la clase
        $horasDisponibles = ManejadorHoras::buscarHorasConChoque($numHorasContinuas, $this->desde, $this->hasta, $nombreDia, $this->aulasConCapacidad, $this->grupo);
        if($horasDisponibles != null){
            self::asignar($this->grupo, $horasDisponibles);
        }
    }

    //Devuelve un número aleatorio entre los límites desde y hasta, los límites también se incluyen
    public static function getNumeroAleatorio($desde,$hasta){
        return mt_rand($desde, $hasta);
    }
    
    //Calcula el número de horas continuas que se necesitan para impartir las clases
    //La materia contiene varios grupos de trabajo
    //A cada grupo de trabajo se le debe asignar la misma cantidad de horas que requiere la materia a la semana
    public function calcularHorasContinuasRequeridas($materia,$grupo){
        $horasRequeridas = $materia->getTotalHorasRequeridas(); //Horas requeridas por semana
        $horasAsignadas = $grupo->getHorasAsignadas(); //Horas que ya han sido asignadas a la materia esta semana
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
     * Para las materias de los primeros años (menores al quinto semestre):
     * -Su turno abarca las horas de la mañana y parte de la tarde
     * Para las materias de los últimos años (mayores al quinto semestre):
     * -Su turno abarca los horarios de la tarde y noche
     */
    public function establecerTurno(){
        if($this->materia->getCiclo()<=6){
            $this->desde = 0;
            $this->hasta = $this->limite;            
        }else{
            $this->desde = $this->limite;
            $this->hasta = 15;
        }
    }

    //Asigna la materia en las horas correspondientes
    public function asignar($grupo,$horasDisponibles){
        $hora;
        for ($j = 0; $j < count($horasDisponibles); $j++) {
            $hora = $horasDisponibles[$j];
            $hora->setGrupo($grupo);
            $hora->setDisponible(FALSE);
            $grupo->setHorasAsignadas($grupo->getHorasAsignadas()+1);
        }
    }
    
    
     /** Intenta asignar una aula para la materia de acuerdo a su capacidad y al número de alumnos de esa materia
     * Cada materia se divide en uno o más grupos, dependiendo de la cantidad de alumnos que cursan la materia.
     * 
     * @throws Exception = Si no encuentra aulas para asignar la materia
     */
    public function localizarBloqueOptimo(){
        $sePudoAsignar=FALSE; //Informa si el grupo pudo ser asignado.
        if($this->asignarDiasConsiderandoChoques()){ //se trata de asignar el grupo en el aula elegida comprobando si existen choques
            $sePudoAsignar = TRUE;
        }else if($this->agrupacion->getNum_grupos() > 1 && $this->asignarDiasSinConsiderarChoques()){ 
            //se asignan las horas que no se pudieron asignar debido a que hubieron choques de horario, esta vez ya no se consideran choques
            $sePudoAsignar = TRUE;
        }
        if(!$sePudoAsignar){ //Se asigna el día sábado si no se pudieron asignar las horas del grupo durante la semana
            $dia = $this->aulasConCapacidad[0]->getDia("Sabado");
            $horas = $dia->getHoras();
            $this->desde = $horas[0]->getIdHora()-1;
            $this->hasta = $horas[count($horas)-1]->getIdHora();
            $horasDisponibles = ManejadorHoras::buscarHoras($this->grupo->getId_docente(),  $this->materia->getTotalHorasRequeridas()-$this->grupo->getHorasAsignadas(),  $this->desde,  $this->hasta,"Sabado",  $this->materia,  $this->aulasConCapacidad,  $this->aulas,  $this->materias,  $this->grupo);
            if($horasDisponibles != NULL){                  //Si hay horas disponibles
                $this->asignar($this->grupo,$horasDisponibles);    //Asignamos la materia
            }else{
                $this->grupo->setIncompleto(TRUE);
                throw new Exception("¡Ya no hay aulas disponibles para el grupo ".$this->grupo->getId_grupo()." Materia: ".ManejadorAgrupaciones::obtenerNombrePropietario($this->grupo->getId_Agrup(),$this->materias)." Departamento: ".ManejadorAgrupaciones::obtenerIdDepartamento($this->grupo->getId_Agrup(),  $this->agrupaciones));
            }
        }    
    }
    
     /** Asignar dias considerando choques de horario en ellos
     *      
     * @return true si se puede hacer la asignacion de todas las horas que requiere el grupo
     */
    public function asignarDiasConsiderandoChoques(){
        $dias = $this->aulasConCapacidad[0]->getDias();
        $diaElegido;
        $diasUsados=array();
        //Se repite el proceso hasta que todos los grupos de la materia hayan sido asignados
        while ($this->materia->getTotalHorasRequeridas() > $this->grupo->getHorasAsignadas()) {
            //Se debe elegir un día diferente para cada clase
            $diaElegido = ManejadorDias::elegirDiaDiferente($dias, $diasUsados); //Elegimos un día entre todos que sea diferente de los días que ya hemos elegido
            if($diaElegido != NULL){
                $this->asignarHorasConsiderandoChoques($diaElegido->getNombre());
                $diasUsados[] = $diaElegido; //Guardamos el día para no elegirlo de nuevo para esta materia
            }else{
                return FALSE;
            }                
        }
        return TRUE;
    }
    
       //Asiganar dias sin considerar choques en ellos
    public function asignarDiasSinConsiderarChoques(){
        $dias = $this->aulasConCapacidad[0]->getDias();
        $diaElegido;
        $diasUsados=array();
        while ($this->materia->getTotalHorasRequeridas() > $this->grupo->getHorasAsignadas()) {
            $diaElegido = ManejadorDias::elegirDiaDiferente($dias, $diasUsados);
            if($diaElegido != NULL){
                $this->asignarHorasSinConsiderarChoques($diaElegido->getNombre());
                $diasUsados[] = $diaElegido;
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
    public function asignarHorasConsiderandoChoques($nombreDia){
        if(ManejadorHoras::grupoPresente($this->desde, $this->hasta, $nombreDia, $this->grupo, $this->aulas))
                return;
        $horasDisponibles = NULL;
        $numHorasContinuas = $this->calcularHorasContinuasRequeridas($this->materia,  $this->grupo); //Calculamos el numero de horas continuas para la clase
        $horasNivel = ManejadorHoras::getUltimasHoraDeNivel($this->grupo, $this->materia, $this->materias, $this->agrupaciones, $this->aulasConCapacidad, $nombreDia);
        for ($index = 0; $index < count($horasNivel); $index++) {
            $hora = $horasNivel[$index];
            if($hora+$numHorasContinuas < $this->hasta){
                $horasDisponibles = ManejadorHoras::buscarHoras($this->grupo->getId_docente(), $numHorasContinuas, $hora+1, $hora+1+$numHorasContinuas, $nombreDia, $this->materia, $this->aulasConCapacidad, $this->aulas, $this->materias, $this->grupo);
                if($horasDisponibles != NULL && count($horasDisponibles)!=0){
                    break;
                }
            }
        }
        if($horasDisponibles == NULL){
            $horasDisponibles = ManejadorHoras::buscarHorasUltimoRecurso($this->grupo->getId_docente(),$numHorasContinuas, $this->desde,$this->hasta,$nombreDia,$this->materia,$this->aulasConCapacidad,$this->aulas,$this->materias); //elige las primeras horas disponibles que encuentre ese día
        }
        if($horasDisponibles != NULL){
            $this->asignar($this->grupo,$horasDisponibles);
        }
    }
}
