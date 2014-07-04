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
    private $horasLab;
    private $horasDiscu;
    private $lab_dis_alter;
    private $aulas_gtd;
    private $aulas_gl;
    private $incompleta;
    
    function __construct($codigo, $nombre, $ciclo, $unidadesValorativas, $carrera, $agrupacion, $horasReq, $horasLab, $horasDis, $lab_dis, $aulas_gtd, $aulas_gl,$incompleta) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->ciclo = $ciclo;
        $this->unidadesValorativas = $unidadesValorativas;
        $this->carrera = $carrera;
        $this->agrupacion = $agrupacion;
        $this->horasRequeridas = $horasReq;
        $this->horasLab = $horasLab;
        $this->horasDiscu = $horasDis;
        $this->lab_dis_alter = $lab_dis;
        $this->aulas_gtd = $aulas_gtd;
        $this->aulas_gl = $aulas_gl;
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
    
    public function getHorasLab() {
        return $this->horasLab;
    }

    public function getHorasDiscu() {
        return $this->horasDiscu;
    }

    public function isLab_dis_alter() {
        if($this->lab_dis_alter=='f'){
            return false;
        } else{
            return true;
        }
    }

    public function setHorasLab($horasLab) {
        $this->horasLab = $horasLab;
    }

    public function setHorasDiscu($horasDiscu) {
        $this->horasDiscu = $horasDiscu;
    }

    public function setLab_dis_alter($lab_dis_alter) {
        $this->lab_dis_alter = $lab_dis_alter;
    }
    
    public function getAulas_gtd() {
        return $this->aulas_gtd;
    }

    public function getAulas_gl() {
        return $this->aulas_gl;
    }

    public function setAulas_gtd($aulas_gtd) {
        $this->aulas_gtd = $aulas_gtd;
    }

    public function setAulas_gl($aulas_gl) {
        $this->aulas_gl = $aulas_gl;
    }
}
