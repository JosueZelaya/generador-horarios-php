<?php

chdir(dirname(__FILE__));
require_once 'Departamento.php';
require_once 'Carrera.php';

/**
 * Description of Materia
 *
 * @author alexander
 */
class MateriaAgrupacion {
    
    private $codigo;
    private $nombre;    
    private $departamento;
    private $ciclos;
    private $alumnosNuevos;
    private $otrosAlumnos;
    private $numeroGrupos;
    private $numeroGruposLaboratorio;
    private $numeroGruposDiscusion;
    private $alumnosGrupo;
    private $idAgrupacion;
    private $carrera;
    private $plan_estudio;
    private $materias;
    private $num_horas_clase;
    private $num_horas_laboratorio;
    private $num_horas_discusion;
    private $discuciones_labs_alternados;
    private $unidadesValorativas;
    
    public function __construct() {
        $this->codigo = "";
        $this->nombre = "";
        $this->departamento = new Departamento("","");
        $this->ciclos = array();
        $this->alumnosNuevos = 0;
        $this->otrosAlumnos = 0;
        $this->numeroGrupos = 0;
        $this->idAgrupacion=0;
        $this->carrera=new Carrera("","","","");
        $this->plan_estudio="";
        $this->materias=array();
    }
    
    public function getCodigo(){
        return $this->codigo;
    }
    
    public function getNombre(){
        return $this->nombre;
    }
    
    public function getDepartamento(){
        return $this->departamento;
    }
    
    public function getCiclos(){       
        //$this->ciclos= ['0'];
        if(count($this->ciclos)==0){            
            //$consulta = "SELECT m.id_carrera,m.ciclo_carrera,c.id_depar FROM materias as m JOIN carreras as c on m.id_carrera=c.id_carrera WHERE cod_materia='".$this->codigo."' AND c.id_depar='".$this->departamento->getId()."' ORDER BY ciclo_carrera ASC;";
            $consulta = "SELECT m.ciclo_carrera FROM materias as m NATURAL JOIN materia_agrupacion AS ma NATURAL JOIN carreras as c WHERE ma.id_agrupacion='".$this->getIdAgrupacion()."' AND cod_materia='".$this->getCodigo()."' AND c.id_depar='".$this->getDepartamento()->getId()."' ORDER BY ciclo_carrera ASC;";
            $respuesta = conexion::consulta($consulta);
            while ($row = pg_fetch_array($respuesta)){
                $ciclo = $row['ciclo_carrera'];
                $this->agregarCiclo($ciclo);          
            }
        }                           			
        return $this->ciclos;
    }
    
    private function agregarCiclo($ciclo){
        $yaexiste=FALSE;
        if(count($this->ciclos)!=0){
            for ($index = 0; $index < count($this->ciclos); $index++) {
                if($this->ciclos[$index]==$ciclo){
                    $yaexiste = TRUE;
                }
            }
            if(!$yaexiste){
                $this->ciclos[] = $ciclo;
            }
        }else{
            $this->ciclos[] = $ciclo;
        }        
    }
    
    public function getAlumnosNuevos(){
        return $this->alumnosNuevos;
    }
    
    public function getOtrosAlumnos(){
        return $this->otrosAlumnos;
    }
    
    public function getTotalAlumnos(){
        return $this->alumnosNuevos + $this->otrosAlumnos;
    }
    
    
    public function getNumeroGrupos(){
        return $this->numeroGrupos;
    }
    
    public function getIdAgrupacion(){
        return $this->idAgrupacion;
    }
    
    public function setCodigo($codigo){
        $this->codigo = $codigo;
    }
    
    public function setNombre($nombre){
        $this->nombre = $nombre;
        //$this->materias = $nombre;
    }
    
    public function setDepartamento($departamento){
        $this->departamento = $departamento;
    }
    
    public function setAlumnoNuevos($cantidad){
        $this->alumnosNuevos = $cantidad;
    }
    
    public function setOtrosAlumnos($cantidad){
        $this->otrosAlumnos = $cantidad;
    }
    
    public function setNumeroGrupos($cantidad){
        $this->numeroGrupos = $cantidad;
    }
    
    public function getAlumnosGrupo() {
        return $this->alumnosGrupo;
    }

    public function setAlumnosGrupo($alumnosGrupo) {
        $this->alumnosGrupo = $alumnosGrupo;
    }

        
    public function setIdAgrupacion($id){
        $this->idAgrupacion = $id;
    }
    
    public function getCarrera() {
        return $this->carrera;
    }

    public function getPlan_estudio() {
        return $this->plan_estudio;
    }

    public function setCarrera($carrera) {
        $this->carrera = $carrera;
    }

    public function setPlan_estudio($plan_estudio) {
        $this->plan_estudio = $plan_estudio;
    }

    public function getNumeroGruposLaboratorio() {
        return $this->numeroGruposLaboratorio;
    }

    public function getNumeroGruposDiscusion() {
        return $this->numeroGruposDiscusion;
    }

    public function setNumeroGruposLaboratorio($numeroGruposLaboratorio) {
        $this->numeroGruposLaboratorio = $numeroGruposLaboratorio;
    }

    public function setNumeroGruposDiscusion($numeroGruposDiscusion) {
        $this->numeroGruposDiscusion = $numeroGruposDiscusion;
    }

    public function getMaterias() {
        return $this->materias;
    }

    public function setMaterias($materias) {
        $this->materias = $materias;
    }

    public function addMateria($materia){
        $this->materias[] = $materia;
    }
    
    public function getNum_horas_clase(){
        if($this->num_horas_clase == 0 || $this->num_horas_clase == "0"){
            $total = round(($this->getUnidadesValorativas()*20)/16,0,PHP_ROUND_HALF_DOWN);
            return $total;
        } else{
            return $this->num_horas_clase;
        }
    }
    public function getNum_horas_laboratorio() {
        return $this->num_horas_laboratorio;
    }

    public function getNum_horas_discusion() {
        return $this->num_horas_discusion;
    }

    public function getDiscuciones_labs_alternados() {
        return $this->discuciones_labs_alternados;
    }

    public function setNum_horas_clase($num_horas_clase) {
        $this->num_horas_clase = $num_horas_clase;
    }

    public function setNum_horas_laboratorio($num_horas_laboratorio) {
        $this->num_horas_laboratorio = $num_horas_laboratorio;
    }

    public function setNum_horas_discusion($num_horas_discusion) {
        $this->num_horas_discusion = $num_horas_discusion;
    }

    public function setDiscuciones_labs_alternados($discuciones_labs_alternados) {
        $this->discuciones_labs_alternados = $discuciones_labs_alternados;
    }
    
    public function getUnidadesValorativas() {
        return $this->unidadesValorativas;
    }

    public function setUnidadesValorativas($unidadesValorativas) {
        $this->unidadesValorativas = $unidadesValorativas;
    }
    
}
