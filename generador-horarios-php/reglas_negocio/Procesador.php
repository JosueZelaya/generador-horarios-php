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

    private $facultad;
    private $materia;
    private $materias;
    private $aulas;
    private $holguraAula;
    private $aulasConCapacidad;
    private $agrupaciones;
    private $agrupacion;
    private $grupo;
    private $desde;
    private $hasta;
    private $limite;
    
    public function __construct() {
        $this->holguraAula=10;
        $this->limite=10;
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
        if($this->materia->getCiclo<=6){
            $this->desde = 0;
            $this->hasta = $this->limite;            
        }else{
            $this->desde = $this->limite;
            $this->hasta = 15;
        }
    }

    //Asigna la materia en las horas correspondientes
    public static function asignar($grupo,$horasDisponibles){
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
    public static function localizarBloqueOptimo(){
        $sePudoAsignar=FALSE; //Informa si el grupo pudo ser asignado.
        if(asignarDiasConsiderandoChoques()){ //se trata de asignar el grupo en el aula elegida comprobando si existen choques
            $sePudoAsignar = TRUE;
        }else if($this->agrupacion->getNum_grupos() > 1 && asignarDiasSinConsiderarChoques()){ 
            //se asignan las horas que no se pudieron asignar debido a que hubieron choques de horario, esta vez ya no se consideran choques
            $sePudoAsignar = TRUE;
        }
        if(!sePudoAsignar){ //Se asigna el día sábado si no se pudieron asignar las horas del grupo durante la semana
            $dia = $this->aulasConCapacidad[0]->getDia("Sabado");
            $horas = $dia->getHoras();
            $this->desde = $horas[0]->getIdHora()-1;
            $this->hasta = $horas[count($horas)-1]->getIdHora();
            $horasDisponibles = buscarHoras($this->grupo->getId_docente(),  $this->materia->getTotalHorasRequeridas()-$this->grupo->getHorasAsignadas(),  $this->desde,  $this->hasta,"Sabado",  $this->materia,  $this->aulasConCapacidad,  $this->aulas,  $this->materias,  $this->grupo);
            if($horasDisponibles != NULL){                  //Si hay horas disponibles
                asignar($this->grupo,$horasDisponibles);    //Asignamos la materia
            }else{
                $this->grupo->setIncompleto(TRUE);
                throw Exception("¡Ya no hay aulas disponibles para el grupo ".$this->grupo.getId_grupo()." Materia: ".ManejadorAgrupaciones::obtenerNombrePropietario($this->grupo.getId_Agrup(),$this->materias)." Departamento: ".ManejadorAgrupaciones::obtenerIdDepartamento($this->grupo.getId_Agrup(),  $this->agrupaciones));
            }
        }    
    }
    

//    /** Asignar dias considerando choques de horario en ellos
//     *      
//     * @return true si se puede hacer la asignacion de todas las horas que requiere el grupo
//     */
//    public boolean asignarDiasConsiderandoChoques(){
//        ArrayList<Dia> dias = aulasConCapacidad.get(0).getDias();
//        Dia diaElegido;
//        ArrayList<Dia> diasUsados = new ArrayList();
//        //Se repite el proceso hasta que todos los grupos de la materia hayan sido asignados
//        while(materia.getTotalHorasRequeridas() > grupo.getHorasAsignadas()){
//           //Se debe elegir un día diferente para cada clase
//            diaElegido = elegirDiaDiferente(dias, diasUsados); //Elegimos un día entre todos que sea diferente de los días que ya hemos elegido
//            if(diaElegido != null){
//                System.out.println("Se probara sin choques en dia "+diaElegido.getNombre());
//
//                asignarHorasConsiderandoChoques(diaElegido.getNombre());
//                diasUsados.add(diaElegido);    //Guardamos el día para no elegirlo de nuevo para esta materia                                                   
//            } else
//                return false;
//        }
//        return true;
//    }
//    
//    //Asiganar dias sin considerar choques en ellos
//    public boolean asignarDiasSinConsiderarChoques(){
//       ArrayList<Dia> dias = aulasConCapacidad.get(0).getDias();
//       Dia diaElegido;
//       ArrayList<Dia> diasUsados = new ArrayList();
//       //Se debe elegir un día diferente para cada clase
//       while(materia.getTotalHorasRequeridas() > grupo.getHorasAsignadas()){
//            diaElegido = elegirDiaDiferente(dias, diasUsados); //Elegimos un día entre todos
//            if(diaElegido != null){
//                System.out.println("Se probara con choques en dia "+diaElegido.getNombre()+" para la materia: "+materia.getCodigo());                
//                asignarHorasSinConsiderarChoques(diaElegido.getNombre());
//                diasUsados.add(diaElegido);    //Guardamos el día para no elegirlo de nuevo para esta materia                                                   
//            }else
//                return false;
//       }
//       return true;
//    }
//    
//    /** Asignar Horas considerando choques
//     * 
//     * @param nombreDia = nombre del dia en el que se quiere hacer la asignacion; se utiliza para compbrobar choques
//     */
//    public void asignarHorasConsiderandoChoques(String nombreDia){
//        if(grupoPresente(desde,hasta,nombreDia,grupo,aulasConCapacidad))
//            return;
//        
//        ArrayList<Hora> horasDisponibles = null;
//        int numHorasContinuas = calcularHorasContinuasRequeridas(materia, grupo);  //Calculamos el numero de horas continuas para la clase
//        ArrayList horasNivel = getUltimasHoraDeNivel(grupo, materia, materias, agrupaciones, aulasConCapacidad, nombreDia);
//        for(Object hora : horasNivel){
//            if(((int)hora+numHorasContinuas)<hasta){
//                horasDisponibles = buscarHoras(grupo.getId_docente(),numHorasContinuas, (int)hora+1, (int)hora+1+numHorasContinuas, nombreDia, materia, aulasConCapacidad, aulas, materias, grupo);
//                if(horasDisponibles != null && !horasDisponibles.isEmpty())
//                    break;
//            }
//        }
//        if(horasDisponibles == null)
//            horasDisponibles = buscarHorasUltimoRecurso(grupo.getId_docente(),numHorasContinuas, desde, hasta, nombreDia, materia, grupo, aulasConCapacidad, aulas, materias); //elige las primeras horas disponibles que encuentre ese día
//        
//        if(horasDisponibles != null){
//            asignar(grupo, horasDisponibles);
//        }
//    }
//    
//    /**Metodo para asginar horas si no se pudo continua a una materia del mismo nivel (se consideran choques)
//     * 
//     * @param nombreDia nombre del Dia en el que se quiere hacer la asignacion de horas
//     */
//    public void ultimoRecursoConsiderandoChoques(String nombreDia){
//        int numHorasContinuas = calcularHorasContinuasRequeridas(materia, grupo);  //Calculamos el numero de horas continuas para la clase
//        ArrayList<Hora> horasDisponibles = buscarHorasUltimoRecurso(grupo.getId_docente(),numHorasContinuas, desde, hasta, nombreDia, materia, grupo, aulasConCapacidad, aulas, materias); //elige las primeras horas disponibles que encuentre ese día
//        if(horasDisponibles != null)
//            asignar(grupo, horasDisponibles);
//    }
//    
//    //Asignar horas sin considerar choques
//    public void asignarHorasSinConsiderarChoques(String nombreDia){
//        ArrayList<Hora> horasDisponibles;
//        int numHorasContinuas = calcularHorasContinuasRequeridas(materia, grupo);  //Calculamos el numero de horas continuas para la clase
//        horasDisponibles = buscarHorasConChoque(numHorasContinuas, desde, hasta, nombreDia, aulasConCapacidad,grupo);
//        if(horasDisponibles != null)
//            asignar(grupo, horasDisponibles);
//    }
//    
//    /** Realiza el procesamiento necesario para generar el horario de una materia.
//     * 
//     * @param materia = La materia que se quiere asignar
//     * @param id_docente = identificador del docente que impartira el grupo a asignar horario
//     * @param agrupacion = agrupacion a la que pertenece la materia a asignar
//     * @throws Exception = Si no existe la informacion necesario en el objeto Facultad
//     */
//    public void procesarMateria(Materia materia, int id_docente, Agrupacion agrupacion) throws Exception{
//        if(facultad!=null){
//            this.materia = materia;             //La materia que se debe procesar
//            this.agrupacion = agrupacion; //Se busca dentro de todas las agrupaciones, cuál es la que pertenece a la materia que se quiere asignar
//            grupo = new Grupo(agrupacion);   //El grupo con la información de la agrupación, este grupo es el que será asignado en un aula
//            grupo.setId_docente(id_docente); //Se le asigna al grupo a cual docente pertenecera para comprobaciones de choques
//            aulasConCapacidad = obtenerAulasPorCapacidad(aulas,agrupacion.getNum_alumnos()+holguraAula);
//            establecerTurno();                 //Se establece el turno
//            localizarBloqueOptimo();  //Debe asignar la materia a un aula de la facultdad
//        }else
//            throw new Exception("No se encuentra la información de la facultad");           
//    }
//    
//    public void asignarDatos(Facultad facultad){
//        this.facultad = facultad;
//        aulas = facultad.getAulas();        //Se obtienen las aulas de la facultad en las cuales se podría asignar materias
//        materias = facultad.getMaterias();  //Se obtienen todas las materias de la facultad
//        agrupaciones = facultad.agrupaciones;  //Se obtienen todas las agrupaciones de materias que existen
//    }
    
}
