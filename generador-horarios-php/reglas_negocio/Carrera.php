<?php
/**
 * Description of Carrera
 *
 * @author abs
 */
class Carrera {
    private $codigo;
    private $planEstudio;
    private $nombre;
    private $departamento;
    
    function __construct($codigo, $planEstudio, $nombre, $departamento) {
        $this->codigo = $codigo;
        $this->planEstudio = $planEstudio;
        $this->nombre = $nombre;
        $this->departamento = $departamento;
    }
    
    public function getCodigo() {
        return $this->codigo;
    }

    public function getPlanEstudio() {
        return $this->planEstudio;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getDepartamento() {
        return $this->departamento;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function setPlanEstudio($panEstudio) {
        $this->planEstudio = $panEstudio;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setDepartamento($departamento) {
        $this->departamento = $departamento;
    }
}
