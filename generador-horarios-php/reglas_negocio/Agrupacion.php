<?php
/**
 * Description of Agrupacion
 *
 * @author alexander
 */
class Agrupacion {
    
    private $id;
    private $grupos;
    private $num_alumnos;
    private $numGruposAsignados;
    private $materias;
    
    public function __construct($id,$num_alumnos){
        $this->id=$id;
        $this->num_alumnos=$num_alumnos;
        $this->numGruposAsignados=0;
        $this->materias = array();
        $this->grupos = array();
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
    
    public function getNumGrupos($tipo) {
        $cuenta = 0;
        foreach ($this->grupos as $grupo){
            if($grupo->getTipo()==$tipo){
                $cuenta++;
            }
        }
        return $cuenta;
    }

    public function getNum_alumnos() {
        return $this->num_alumnos;
    }

    public function setNum_alumnos($num_alumnos) {
        $this->num_alumnos = $num_alumnos;
    }

    public function getNumGruposAsignados() {
        return $this->numGruposAsignados;
    }

    public function setNumGruposAsignados($numGruposAsignados) {
        $this->numGruposAsignados = $numGruposAsignados;
    }
    
    public function getMaterias() {
        return $this->materias;
    }

    public function setMaterias($materias) {
        $this->materias = $materias;
    }
    
    public function setMateria($materia) {
        $this->materias[] = $materia;
    }
    
    public function getGrupos() {
        return $this->grupos;
    }

    public function setGrupos($grupos) {
        $this->grupos = $grupos;
    }
    
    public function addGrupo($grupo){
        $this->grupos[] = $grupo;
    }
}
