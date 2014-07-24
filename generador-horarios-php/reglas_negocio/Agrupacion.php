<?php
/**
 * Description of Agrupacion
 *
 * @author alexander
 */
class Agrupacion {
    
    private $id;
    private $grupos;
    private $num_alumnos;
    private $numGruposAsignados;
    private $materias;
    private $lab_dis_alter;
    private $aulas_gtd;
    private $aulas_gl;
    private $horasRequeridas;
    private $bloquesRequeridos;
    private $horasLab;
    private $horasDiscu;
    
    public function __construct($id,$num_alumnos,$lab_dis,$aulas_gtd,$aulas_gl,$horasReq,$horasLab,$horasDis){
        $this->id=$id;
        $this->num_alumnos=$num_alumnos;
        $this->numGruposAsignados=0;
        $this->materias = array();
        $this->grupos = array();
        $this->lab_dis_alter = $lab_dis;
        $this->aulas_gtd = $aulas_gtd;
        $this->aulas_gl = $aulas_gl;
        $this->horasRequeridas = $horasReq;
        $this->horasLab = $horasLab;
        $this->horasDiscu = $horasDis;
        $this->bloquesRequeridos = null;
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
    
    public function getNumGrupos($tipo) {
        $cuenta = 0;
        foreach ($this->grupos as $grupo){
            if($grupo->getTipo()==$tipo){
                $cuenta++;
            }
        }
        return $cuenta;
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
    
    public function getMaterias() {
        return $this->materias;
    }

    public function setMaterias($materias) {
        $this->materias = $materias;
    }
    
    public function setMateria($materia) {
        $this->materias[] = $materia;
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
    
    public function isLab_dis_alter() {
        if($this->lab_dis_alter=='f'){
            return false;
        } else{
            return true;
        }
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
    
    public function getHorasLab() {
        if(is_array($this->bloquesRequeridos) && count($this->bloquesRequeridos)>0){
            $hora = $this->bloquesRequeridos[0];
            return array($hora);
        } elseif(is_array($this->bloquesRequeridos) && count($this->bloquesRequeridos)==0){
            return null;
        } else{
            return $this->horasLab;
        }
    }

    public function getHorasDiscu() {
        if(is_array($this->bloquesRequeridos) && count($this->bloquesRequeridos)>0){
            $hora = $this->bloquesRequeridos[0];
            return array($hora);
        } elseif(is_array($this->bloquesRequeridos) && count($this->bloquesRequeridos)==0){
            return null;
        } else{
            return $this->horasDiscu;
        }
    }

    public function setHorasLab($horasLab) {
        $this->horasLab = $horasLab;
    }

    public function setHorasDiscu($horasDiscu) {
        $this->horasDiscu = $horasDiscu;
    }
    
    public function setHorasRequeridas($horasRequeridas) {
        $this->horasRequeridas = $horasRequeridas;
    }

    public function getTotalHorasRequeridas(){
        if($this->horasRequeridas == 0){
            $total = round(($this->materias[0]->getUnidadesValorativas()*20)/16,0,PHP_ROUND_HALF_DOWN);
            return $total;
        } elseif(is_array($this->bloquesRequeridos) && count($this->bloquesRequeridos)>0){
            $hora = $this->bloquesRequeridos[0];
            return array($hora);
        } elseif(is_array($this->bloquesRequeridos) && count($this->bloquesRequeridos)==0){
            return null;
        } else{
            return $this->horasRequeridas;
        }
    }
    
    public function limpiarBloqueAsignado(){
        if(is_array($this->bloquesRequeridos) && count($this->bloquesRequeridos)>0){
            unset($this->bloquesRequeridos[0]);
            $this->bloquesRequeridos = array_values($this->bloquesRequeridos);
        }
    }

    public function setBloquesRequeridos($bloquesRequeridos) {
        $this->bloquesRequeridos = $bloquesRequeridos;
    }
}
