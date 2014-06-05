<?php
/**
 * Description of Agrupacion
 *
 * @author alexander
 */
class Agrupacion {
    
    private $id;
    private $num_grupos;
    private $num_alumnos;
    private $numGruposAsignados;
    private $asignaciones;
    private $materias;
    
    public function __construct($id,$num_grupos,$num_alumnos){
        $this->id=$id;
        $this->num_grupos=$num_grupos;
        $this->num_alumnos=$num_alumnos;
        $this->numGruposAsignados=0;
        $this->asignaciones = null;
        $this->materias = array();
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNum_grupos() {
        return $this->num_grupos;
    }

    public function setNum_grupos($num_grupos) {
        $this->num_grupos = $num_grupos;
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
    
    public function getAsignaciones() {
        return $this->asignaciones;
    }

    public function setAsignaciones($asignaciones) {
        $this->asignaciones = $asignaciones;
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
}
