<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AsignacionDocente
 *
 * @author abs
 */
class AsignacionDocente {
    
    private $docente;
    private $num_grupos;
    
    function __construct($docente, $num_grupos) {
        $this->docente = $docente;
        $this->num_grupos = $num_grupos;
    }
    
    public function getDocente() {
        return $this->docente;
    }

    public function setDocente($docente) {
        $this->docente = $docente;
    }

    public function getNum_grupos() {
        return $this->num_grupos;
    }

    public function setNum_grupos($num_grupos) {
        $this->num_grupos = $num_grupos;
    }
}
