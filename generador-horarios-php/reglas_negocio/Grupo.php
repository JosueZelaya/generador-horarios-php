<?php
/**
 * Description of Grupo
 *
 * @author abs
 */
class Grupo {
    
    private $agrup;
    private $id_grupo;
    private $docente;
    private $horasAsignadas;
    private $incompleto;
    
    function __construct() {
        $this->incompleto=false;
        $this->agrup = null;
        $this->id_grupo = 0;
        $this->docente = null;
        $this->horasAsignadas = 0;
    }
    
    public function getId_grupo() {
        return $this->id_grupo;
    }

    public function getHorasAsignadas() {
        return $this->horasAsignadas;
    }

    public function isIncompleto() {
        return $this->incompleto;
    }

    public function setId_grupo($id_grupo) {
        $this->id_grupo = $id_grupo;
    }

    public function setHorasAsignadas($horasAsignadas) {
        $this->horasAsignadas = $horasAsignadas;
    }

    public function setIncompleto($incompleto) {
        $this->incompleto = $incompleto;
    }

    public function getDocente() {
        return $this->docente;
    }

    public function setDocente($docente) {
        $this->docente = $docente;
    }
    
    public function getAgrup() {
        return $this->agrup;
    }

    public function setAgrup($agrup) {
        $this->agrup = $agrup;
    }
}
