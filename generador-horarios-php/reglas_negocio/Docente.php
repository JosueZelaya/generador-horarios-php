<?php
/**
 * Description of Docente
 *
 * @author abs
 */
class Docente {
    
    private $idDocente;
    private $contratacion;
    private $cargo;
    private $grupos;
            
    function __construct($idDocente,$contratacion) {
        $this->idDocente = $idDocente;
        $this->cargo = null;
        $this->contratacion = $contratacion;
        $this->grupos = array();
    }
    
    public function getIdDocente() {
        return $this->idDocente;
    }

    public function setIdDocente($idDocente) {
        $this->idDocente = $idDocente;
    }
    
    public function getCargo() {
        return $this->cargo;
    }

    public function setCargo($cargo) {
        $this->cargo = $cargo;
    }
    
    public function getContratacion() {
        return $this->contratacion;
    }

    public function setContratacion($contratacion) {
        $this->contratacion = $contratacion;
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
