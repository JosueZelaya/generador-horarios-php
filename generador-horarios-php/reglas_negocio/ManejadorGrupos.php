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
    
    /** Obtener grupo de todos los grupos de la facultad
     * 
     * @param int $id_grupo
     * @param String $tipo
     * @param int $id_agrupacion
     * @param Grupo[] $grupos
     */
    public static function getGrupo($id_grupo,$tipo,$id_agrupacion,$grupos){
        foreach ($grupos as $grupo){
            if($grupo->getId_grupo() == $id_grupo && $grupo->getAgrup()->getId() == $id_agrupacion && $grupo->getTipo() == $tipo){
                return $grupo;
            }
        }
        return null;
    }

    public static function getTipos(){
        $consulta = "SELECT * FROM tipos_grupos";
        $resul = Conexion::consulta($consulta);
        return pg_fetch_all($resul);
    }
    
    public static function getIdTipo($nombreTipo,$tipos){
        foreach ($tipos as $tipo){
            if($tipo['tipo'] == $nombreTipo){
                return $tipo['id'];
            }
        }
        return null;
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
    
    private static function agregar_docente_a_grupo($docente,$id_grupo,$tipo,$grupos){
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
                    $docente = new Docente($fila["id_docente"],"","","","");
                    $docente->setNombres($fila["nombres"]);
                    $docente->setApellidos($fila["apellidos"]);
                    self::agregar_docente_a_grupo($docente, $fila['id_grupo'], $fila['tipo_grupo'], $grupos);
                }else{
                    $grupo = new Grupo();
                    $grupo->setAgrup($agrupacion);
                    $grupo->setId_grupo($fila['id_grupo']);
                    $grupo->setTipo($fila['tipo_grupo']);
                    $docentes = array();
                    $docente = new Docente($fila["id_docente"],"","","","");
                    $docente->setNombres($fila["nombres"]);
                    $docente->setApellidos($fila["apellidos"]);
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
        $array = array_unique($grupos);
        if(count($array)==1){
            return true;
        } else{
            return false;
        }
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
    
    /** Extraer los docentes de cada grupo pasado en array de grupos
     * 
     * @param Grupo[] $grupos = grupos de donde se extraeran los docentes
     */
    public static function extraerDocentesDeGrupos($grupos){
        $docentes = array();
        foreach ($grupos as $grupo){
            if($grupo->getId_grupo() != 0){
                $docentes[] = $grupo->getDocentes();
            } else{
                $docentes[] = array();
            }
        }
        return $docentes;
    }
    
    public static function getGruposEnRangoHoras($desde,$hasta,$aulas,$aula,$dia){
        for($i=$desde;$i<=$hasta;$i++){
            $grupos[] = ManejadorGrupos::getGrupoEnHora($aulas, $aula, $dia, $i);
        }
        return $grupos;
    }
}
