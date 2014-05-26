<?php
/**
 * Description of Docente
 *
 * @author abs
 */
class Docente {
    
    private $idDocente;
    private $cargo;
    
    function __construct($idDocente, $cargo) {
        $this->idDocente = $idDocente;
        $this->cargo = $cargo;
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
}
