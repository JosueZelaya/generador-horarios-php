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

    public static function getNumeroAleatorio($desde,$hasta){
        
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
            $grupo = new Grupo();   //El grupo con la información de la agrupación, este grupo es el que será asignado en un aula
            $grupo->setId_agrup($agrupacion->getId());
            $grupo->setId_grupo($agrupacion->getNumGruposAsignados()+1);
            $grupo.setId_docente($id_docente); //Se le asigna al grupo a cual docente pertenecera para comprobaciones de choques
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
    
}
