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
    private $horasRequeridas;
    private $incompleta;
    
    function __construct($codigo, $nombre, $ciclo, $unidadesValorativas, $carrera, $agrupacion, $incompleta) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->ciclo = $ciclo;
        $this->unidadesValorativas = $unidadesValorativas;
        $this->carrera = $carrera;
        $this->agrupacion = $agrupacion;
        $this->incompleta = $incompleta;
        $this->horasRequeridas = null;
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

    public function setHorasRequeridas($horasRequeridas) {
        $this->horasRequeridas = $horasRequeridas;
    }

    public function getTotalHorasRequeridas(){
        if($this->horasRequeridas == null){
            $total = round(($this->unidadesValorativas*20)/16,0,PHP_ROUND_HALF_DOWN);
            return $total;
        } else{
            return $this->horasRequeridas;
        }
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
}
