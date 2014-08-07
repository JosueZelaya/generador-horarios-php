<?php
/**
 * Description of Grupo
 *
 * @author abs
 */
class Grupo {
    
    private $agrup;
    private $id_grupo;
    private $docentes;
    private $tipo;
    private $horasAsignadas;
    private $incompleto;
    private $procesado;
    
    function __construct() {
        $this->incompleto=true;
        $this->agrup = null;
        $this->id_grupo = 0;
        $this->docentes = array();
        $this->horasAsignadas = 0;
        $this->tipo = "";
        $this->procesado = false;
    }
    
    function __toString() {
        return $this->tipo.$this->id_grupo;
    }

    public function Grupo($id_grupo, $agrup, $docentes) {
        $this->agrup = $agrup;
        $this->id_grupo = $id_grupo;
        $this->docentes = $docentes;
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

    public function getDocentes() {
        return $this->docentes;
    }

    public function setDocentes($docente) {
        $this->docentes = $docente;
    }
    
    public function getAgrup() {
        return $this->agrup;
    }

    public function setAgrup($agrup) {
        $this->agrup = $agrup;
    }
    
    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
    public function addDocente($docente){
        $this->docentes[] = $docente;
    }
    
    public function isProcesado() {
        return $this->procesado;
    }

    public function setProcesado($procesado) {
        $this->procesado = $procesado;
    }
}
