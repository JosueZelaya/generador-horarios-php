<?php
/**
 * Description of Materia
 *
 * @author abs
 */
class Materia {
    private $codigo;
    private $nombre;
    private $ciclo;
    private $unidadesValorativas;
    private $carrera;
    private $agrupacion;
    private $incompleta;
    private $tipo;
    
    function __construct($codigo, $nombre, $ciclo, $unidadesValorativas, $carrera, $agrupacion,$incompleta) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->ciclo = $ciclo;
        $this->unidadesValorativas = $unidadesValorativas;
        $this->carrera = $carrera;
        $this->agrupacion = $agrupacion;
        $this->incompleta = $incompleta;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getCiclo() {
        return $this->ciclo;
    }

    public function getUnidadesValorativas() {
        return $this->unidadesValorativas;
    }

    public function getIncompleta() {
        return $this->incompleta;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setCiclo($ciclo) {
        $this->ciclo = $ciclo;
    }

    public function setUnidadesValorativas($unidadesValorativas) {
        $this->unidadesValorativas = $unidadesValorativas;
    }

    public function setIncompleta($incompleta) {
        $this->incompleta = $incompleta;
    }

    public function getAgrupacion() {
        return $this->agrupacion;
    }

    public function setAgrupacion($agrupacion) {
        $this->agrupacion = $agrupacion;
    }
    
    public function getCarrera() {
        return $this->carrera;
    }

    public function setCarrera($carrera) {
        $this->carrera = $carrera;
    }
    
    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }
}