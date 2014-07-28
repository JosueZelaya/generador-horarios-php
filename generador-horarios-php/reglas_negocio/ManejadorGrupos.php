<?php

/**
 * Description of ManejadorGrupo
 *
 * @author abs
 */
chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Dia.php';
include_once 'Hora.php';
include_once 'ManejadorAgrupaciones.php';
include_once 'ManejadorDocentes.php';
include_once 'Grupo.php';
include_once 'Departamento.php';
include_once 'Carrera.php';

abstract class ManejadorGrupos {
    
    public static function obtenerGrupos($año,$ciclo,$agrupaciones,$docentes){
        $grupos = array();
        $consulta = "select id_grupo,id_agrupacion,id_docente,tipo_grupo,tipo from docente_grupo dg join tipos_grupos as tg on dg.tipo_grupo=tg.id where dg.año=$año and dg.ciclo=$ciclo order by id_agrupacion,id_grupo,tipo_grupo asc";
        $respuesta = Conexion::consulta($consulta);
        $id_grupo = 0;
        $id_agrup = 0;
        $tipo_grupo = '';
        while ($fila = pg_fetch_array($respuesta)){
            $docente = ManejadorDocentes::obtenerDocente($fila['id_docente'], $docentes);
            if($id_grupo == $fila['id_grupo'] && $id_agrup == $fila['id_agrupacion'] && $tipo_grupo == $fila['tipo_grupo']){
                end($grupos)->addDocente($docente);
                $docente->addGrupo(end($grupos));
            } else{
                $agrupacion = ManejadorAgrupaciones::getAgrupacion($fila['id_agrupacion'], $agrupaciones);
                $grupo = new Grupo();
                $grupo->setId_grupo($fila['id_grupo']);
                $grupo->setAgrup($agrupacion);
                $grupo->addDocente($docente);
                $grupo->setTipo($fila['tipo']);
                $agrupacion->addGrupo($grupo);
                $grupos[] = $grupo;
                $docente->addGrupo($grupo);
                $id_grupo = $fila['id_grupo'];
                $id_agrup = $fila['id_agrupacion'];
                $tipo_grupo = $fila['tipo_grupo'];
            }
        }
        return $grupos;
    }
    
    /** Comparación de grupos para determinar el grupo con mayor cantidad de alumnos (dato se encuentra en el objeto agrupacion)
     * 
     * @param Grupo $a
     * @param Grupo $b
     */
    public static function cmpGruposXAlumnos($a,$b){
        if($a->getAgrup()->getNum_alumnos() == $b->getAgrup()->getNum_alumnos()){
            return 0;
        }
        return ($a->getAgrup()->getNum_alumnos() < $b->getAgrup()->getNum_alumnos()) ? 1 : -1;
    }
    
    /** Clasificar un conjunto de objetos grupo en base a si tienen preferencia de aula o no para su posterior asignacion
     * 
     * @param Grupo[] $grupos array de grupos a clasificar
     */
    public static function clasificarGruposPrefAula($grupos){
        $gruposClaf = array('exclusiv'=>array(),'nonexclusiv'=>array(),'nopref'=>array());
        foreach ($grupos as $grupo){
            if((preg_match("/^(TEORICO|DISCUSION)$/", $grupo->getTipo()) && count($grupo->getAgrup()->getAulas_gtd())!=0 && $grupo->getAgrup()->getAulas_gtd()['exclusiv']) || ($grupo->getTipo()=='LABORATORIO' && count($grupo->getAgrup()->getAulas_gl())!=0 && $grupo->getAgrup()->getAulas_gl()['exclusiv'])){ //grupo con preferencia exclusiva
                $gruposClaf['exclusiv'][] = $grupo;
            } elseif((preg_match("/^(TEORICO|DISCUSION)$/", $grupo->getTipo()) && count($grupo->getAgrup()->getAulas_gtd())!=0 && !$grupo->getAgrup()->getAulas_gtd()['exclusiv']) || ($grupo->getTipo()=='LABORATORIO' && count($grupo->getAgrup()->getAulas_gl())!=0 && !$grupo->getAgrup()->getAulas_gl()['exclusiv'])){ //grupo con preferencia no exclusiva
                $gruposClaf['nonexclusiv'][] = $grupo;
            } else { // grupo sin preferencia de aula
                $gruposClaf['nopref'][] = $grupo;
            }
        }
        return $gruposClaf;
    }

    public static function yaSeCreoGrupo($id_grupo,$tipo,$grupos){
        foreach ($grupos as $grupo) {
            if($grupo->getId_grupo()==$id_grupo && $grupo->getTipo()==$tipo){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public static function agregar_docente_a_grupo($docente,$id_grupo,$tipo,$grupos){
        foreach ($grupos as $grupo) {
            if($grupo->getId_grupo()==$id_grupo && $grupo->getTipo()==$tipo){
                $grupo->addDocente($docente);
            }
        }
    }
    
    public static function getGruposDeAgrupacion($año,$ciclo,$agrupacion){        
        $grupos = array();
        $consulta = "SELECT * FROM docente_grupo NATURAL JOIN docentes WHERE id_agrupacion='$agrupacion' AND año='$año' AND ciclo='$ciclo' ORDER BY tipo_grupo,id_grupo;";
        $respuesta = Conexion::consulta($consulta);        
        if(pg_num_rows($respuesta)!=0){            
            while($fila = pg_fetch_array($respuesta)){
                if(self::yaSeCreoGrupo($fila['id_grupo'], $fila['tipo_grupo'], $grupos)){
                    $docente = new Docente($fila["id_docente"],"");
                    $docente->setNombre_completo($fila["nombres"]." ".$fila["apellidos"]);
                    self::agregar_docente_a_grupo($docente, $fila['id_grupo'], $fila['tipo_grupo'], $grupos);
                }else{
                    $grupo = new Grupo();
                    $grupo->setAgrup($agrupacion);
                    $grupo->setId_grupo($fila['id_grupo']);
                    $grupo->setTipo($fila['tipo_grupo']);
                    $docentes = array();
                    $docente = new Docente($fila["id_docente"],"");
                    $docente->setNombre_completo($fila["nombres"]." ".$fila["apellidos"]);
                    $docentes[] = $docente;
                    $grupo->setDocentes($docentes);
                    $grupos[] = $grupo;
                }
            }
        }else{
            $consulta = "SELECT * FROM grupo WHERE id_agrupacion='$agrupacion' AND año='$año' AND ciclo='$ciclo' ORDER BY tipo,id;";
            $respuesta = Conexion::consulta($consulta);   
            while($fila = pg_fetch_array($respuesta)){                
                $grupo = new Grupo();
                $grupo->setAgrup($agrupacion);
                $grupo->setId_grupo($fila['id']);
                $grupo->setTipo($fila['tipo']);                
                $grupo->setDocentes("");
                $grupos[] = $grupo;
            }
        }
        return $grupos;
    }

    public static function getGrupoEnHora($aulas,$aulaElegida,$diaElegido,$idHora){
        if(isset($aulaElegida)){
            foreach ($aulas as $aula){
                if(strcmp($aula->getNombre(),$aulaElegida)==0){
                    $dia = $aula->getDia($diaElegido);
                    $horas = $dia->getHoras();
                    foreach ($horas as $hora){
                        if($hora->getIdHora() == $idHora){
                            return $hora->getGrupo();
                        }
                    }
                }
            }
        }
        return null;
    }
    
    public static function gruposIgualesEnBloque($grupos){
        if($grupos[0]->getId_grupo() != 0){
            $base = $grupos[0];
        } else{
            $base = $grupos[1];
        }
        for ($i=1;$i<count($grupos);$i++){
            if($base->getAgrup()->getId() != $grupos[$i]->getAgrup()->getId() || $base->getId_grupo() != $grupos[$i]->getId_grupo()){
                if ($i == count($grupos)-1 && $grupos[$i]->getAgrup() == NULL){
                    break;
                } else{
                    return false;
                }
            }
        }
        return true;
    }
    
    public static function mismoDepartamento($agrup,$id_depar){
        $materias = $agrup->getMaterias();
        foreach ($materias as $materia){
            if($materia->getCarrera()->getDepartamento()->getId() == $id_depar){
                return true;
            }
        }
        return false;
    }
    
    /**
     * Devuelve el nombre del propietario un grupo
     * 
     * @param type $materias = la agrupación
     * @return string = El nombre de la materia que es propietaria de la agrupacion
     */
    public static function obtenerNombrePropietario($materias){
        foreach ($materias as $materia){
            $propietario[] = $materia->getNombre();
        }
        return $propietario;
    }
    
    public static function obtenerCodigoPropietario($materias){
        foreach ($materias as $materia){
            $propietario[] = $materia->getCodigo();
        }
        return $propietario;
    }
    
    /**
     * Sirve para conocer el dia del departamento al que pertenece un grupo
     * 
     * @param type $materias = materias para ver su departamento
     * @return array = id del departamento
     */
    public static function obtenerIdDepartamento($materias){
        foreach ($materias as $materia) {
            $idDepars[] = $materia->getCarrera()->getDepartamento()->getId();
        }
        return $idDepars;
    }
    
    public static function getNombreDepartamento($materias){
        foreach ($materias as $materia){
            $nombreDepar[] = $materia->getCarrera()->getDepartamento()->getNombre();
        }
        return $nombreDepar;
    }
    
    public static function obtenerCarreraPropietario($materias){
        foreach ($materias as $materia){
            $carreras[] = $materia->getCarrera()->getNombre();
        }
        return $carreras;
    }
    
    public static function actualizarGrupos($grupos,$año,$ciclo){
        $consulta = "DELETE FROM docente_grupo WHERE id_agrupacion='".$grupos[0]->getAgrup()."'";
        conexion::consulta($consulta);
        $consulta = "INSERT INTO docente_grupo(id_grupo,id_agrupacion,año,ciclo,tipo_grupo,id_docente) VALUES ";
        $contGrupos=1;
        foreach ($grupos as $grupo) {            
            $contDocentes=1;
            foreach ($grupo->getDocentes() as $docente) {
                $tipo;
                if($grupo->getTipo()=="teorico"){
                    $tipo=1;
                }else if($grupo->getTipo()=="laboratorio"){
                    $tipo=2;
                }else if($grupo->getTipo()=="discusion"){
                    $tipo=3;
                }
                
                if($contGrupos==count($grupos) && $contDocentes==count($grupo->getDocentes())){
                    $consulta = $consulta."('".$grupo->getId_grupo()."',".$grupo->getAgrup().",$año,$ciclo,".$tipo.",$docente)";
                }else{
                    $consulta = $consulta."('".$grupo->getId_grupo()."',".$grupo->getAgrup().",$año,$ciclo,".$tipo.",$docente),";
                }
                $contDocentes++;
            }            
            $contGrupos++;
        }
        conexion::consulta($consulta);
    }
    
}
