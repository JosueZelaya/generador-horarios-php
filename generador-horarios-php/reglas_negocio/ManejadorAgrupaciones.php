<?php

/**
 * Description of ManejadorAgrupaciones
 *
 * @author arch
 */
chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
require_once 'Agrupacion.php';
require_once 'MateriaAgrupacion.php';
require_once 'ManejadorMaterias.php';
require_once 'Facultad.php';

abstract class ManejadorAgrupaciones {
 
    /**
     * Este método devuelve todas las agrupaciones existentes en la base de datos que corresponden al año y al ciclo al cual se generara un horario.
     * @return \Agrupacion = array de tipo agrupacion
     */
    public static function getAgrupaciones($año,$ciclo,$todas_aulas){
        $preferencias = self::getPreferenciasAulas();
        $agrupaciones = array();
        $sql_consulta = 'SELECT id_agrupacion,alumnos_grupo,lab_dis_alter,horas_clase,horas_lab_semana,horas_discu_semana FROM agrupacion WHERE "año"='.$año.' and ciclo='.$ciclo;
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $prefAgrup = self::getPreferenciaAulaAgrup($preferencias, $todas_aulas, $fila['id_agrupacion']);
            $agrupacion = new Agrupacion($fila['id_agrupacion'],$fila['alumnos_grupo'],$fila['lab_dis_alter'],$prefAgrup['gt'],$prefAgrup['gl'],$fila['horas_clase'],$fila['horas_lab_semana'],$fila['horas_discu_semana']);
            $agrupaciones[] = $agrupacion;
        }
        return $agrupaciones;
    }

    /**
     * 
     * Devuelve la agrupación que cumpla con el criterio a evaluar.
     * 
     * @param type $id_agrup = id de la agrupacion buscada
     * @param type $agrupaciones = Todas las agrupaciones en las cuales buscar.
     * @return null,agrupacion = Devuelve la agrupacion que coincida con el criterio, si ninguna coincide devuelve null
     */
    public static function getAgrupacion($id_agrup,$agrupaciones){
        for ($index = 0; $index < count($agrupaciones); $index++) {
            if($agrupaciones[$index]->getId()==$id_agrup){
                return $agrupaciones[$index];
            }
        }      
        return null;
    }
    
    public static function getPreferenciasAulas(){
        $consulta = Conexion::consulta("select cod_aula,id_agrupacion,tipo_grupo,exclusiv_aula from info_agrup_aula natural join lista_agrup_aula natural join aulas order by capacidad");
        $respuesta = pg_fetch_all($consulta);
        return $respuesta;
    }
    
    public static function getPreferenciaAulaAgrup($todas_prefs,$todas_aulas,$id_agrup){
        $aulas_gt = array();
        $aulas_lab = array();
        foreach ($todas_prefs as $preferencia){
            if($preferencia['id_agrupacion']==$id_agrup){
                if(preg_match("/^(1|3)$/", strval($preferencia['tipo_grupo']))){
                    $aulas_gt['aulas'][] = ManejadorAulas::getAula($todas_aulas, $preferencia['cod_aula']);
                    if($preferencia['exclusiv_aula'] == 't'){
                        $aulas_gt['exclusiv'] = true;
                    } else{
                        $aulas_gt['exclusiv'] = false;
                    }
                } else{
                    $aulas_lab['aulas'][] = ManejadorAulas::getAula($todas_aulas, $preferencia['cod_aula']);
                    $aulas_lab['exclusiv'] = true;
                }
            }
        }
        return array("gt"=>$aulas_gt,"gl"=>$aulas_lab);
    }
    
    /**
     * Sirve para conocer cuáles son las agrupaciones que una carrera posee
     * 
     * @param type $carrera = la carrera de la que se desea conocer su agrupaciones
     * @return type = el array que contiene a las agrupaciones de la carrera.
     */
    public static function obtenerAgrupacionesDeCarrera($carrera){
        $ids=array();
        $sql_consulta = "select ma.id_agrupacion from materia_agrupacion as ma join materias as m on ma.cod_materia = m.cod_materia join carreras as c on c.id_carrera = m.id_carrera where c.nombre_carrera='".$carrera."'";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){                        
            $ids[] = $fila['id_agrupacion'];
        }
        return $ids;
    }
    
    public static function getAgrupacionesDepartamento($idDepartamento,$año,$ciclo){        
        $consulta;
        if($idDepartamento=="todos"){
            $consulta = "SELECT DISTINCT a.id_agrupacion,a.alumnos_nuevos,a.otros_alumnos,a.alumnos_grupo,a.horas_clase,a.horas_lab_semana,a.horas_discu_semana,a.lab_dis_alter,m.cod_materia,m.uv,m.nombre_materia,ma.ciclo,ma.año,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='1') AS num_grupos,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='2') AS num_grupos_l,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='3') AS num_grupos_d,c.nombre_carrera,d.nombre_depar,d.id_depar FROM agrupacion AS a NATURAL JOIN materia_agrupacion AS ma NATURAL JOIN materias AS m NATURAL JOIN carreras AS c NATURAL JOIN departamentos AS d WHERE ma.año='$año' AND ma.ciclo='$ciclo' ORDER BY d.nombre_depar,m.nombre_materia;";
        }else{
            $consulta = "SELECT DISTINCT a.id_agrupacion,a.alumnos_nuevos,a.otros_alumnos,a.alumnos_grupo,a.horas_clase,a.horas_lab_semana,a.horas_discu_semana,a.lab_dis_alter,m.cod_materia,m.uv,m.nombre_materia,ma.ciclo,ma.año,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='1') AS num_grupos,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='2') AS num_grupos_l,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='3') AS num_grupos_d,c.nombre_carrera,d.nombre_depar,d.id_depar FROM agrupacion AS a NATURAL JOIN materia_agrupacion AS ma NATURAL JOIN materias AS m NATURAL JOIN carreras AS c NATURAL JOIN departamentos AS d WHERE d.id_depar='$idDepartamento' AND ma.año='$año' AND ma.ciclo='$ciclo' ORDER BY d.nombre_depar,m.nombre_materia;";
        }
        $respuesta = conexion::consulta($consulta);
        $materias=array();
        while ($row = pg_fetch_array($respuesta)){
            $materia = new MateriaAgrupacion();
            $materia->setCodigo($row['cod_materia']);
            $materia->setNombre($row['nombre_materia']);
            $materia->setDepartamento(new Departamento($row['id_depar'],$row['nombre_depar']));
            $materia->setNumeroGrupos($row['num_grupos']);
            $materia->setIdAgrupacion($row['id_agrupacion']);
            $materia->setAlumnoNuevos($row['alumnos_nuevos']);
            $materia->setOtrosAlumnos($row['otros_alumnos']);
            $materia->setAlumnosGrupo($row['alumnos_grupo']);
            $materia->setNumeroGruposLaboratorio($row['num_grupos_l']);
            $materia->setNumeroGruposDiscusion($row['num_grupos_d']);
            $materia->addMateria($row['nombre_materia']." carrera: ".$row['nombre_carrera']);
            $materia->setCarrera($row['nombre_carrera']);
            $materia->setUnidadesValorativas($row['uv']);
            $materia->setNum_horas_clase($row['horas_clase']);
            $materia->setNum_horas_laboratorio($row['horas_lab_semana']);
            $materia->setNum_horas_discusion($row['horas_discu_semana']);
            $materia->setDiscuciones_labs_alternados($row['lab_dis_alter']);
            $materias[] = $materia;
        }                   			
        return $materias;
    }
    
    public static function getAgrupacionesPorNombre($nombre,$idDepartamento,$año,$ciclo){
        if($idDepartamento=="todos"){
            $consulta = "SELECT COUNT(id_agrupacion) FROM materias NATURAL JOIN materia_agrupacion WHERE nombre_materia iLIKE '$nombre' AND año='$año' AND ciclo='$ciclo';";
            $respuesta = conexion::consulta2($consulta); 
            $cantidad = $respuesta['count'];

            $consulta = "SELECT id_agrupacion FROM materias NATURAL JOIN materia_agrupacion WHERE nombre_materia iLIKE '$nombre' AND año='$año' AND ciclo='$ciclo';";
            $respuesta = conexion::consulta($consulta); 

            $consulta = "SELECT DISTINCT a.id_agrupacion,a.alumnos_nuevos,a.otros_alumnos,a.alumnos_grupo,a.horas_clase,a.horas_lab_semana,a.horas_discu_semana,a.lab_dis_alter,m.cod_materia,m.plan_estudio,m.id_carrera,m.uv,m.nombre_materia,ma.ciclo,ma.año,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='1') AS num_grupos,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='2') AS num_grupos_l,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='3') AS num_grupos_d,c.nombre_carrera,d.nombre_depar,d.id_depar FROM agrupacion AS a NATURAL JOIN materia_agrupacion AS ma NATURAL JOIN materias AS m NATURAL JOIN carreras AS c NATURAL JOIN departamentos AS d WHERE ";
            $cont=1;
            while ($row = pg_fetch_array($respuesta)){
                $id_agrupacion = $row['id_agrupacion'];
                if($cont==$cantidad){
                    $consulta = $consulta."ma.id_agrupacion='$id_agrupacion' ";
                }else{
                    $consulta = $consulta."ma.id_agrupacion='$id_agrupacion' OR ";
                }
                $cont++;
            }
            $consulta = $consulta."AND ma.año='$año' AND ma.ciclo='$ciclo' ORDER BY d.nombre_depar,m.nombre_materia;";
        }else{   
            $consulta = "SELECT COUNT(id_agrupacion) FROM materias NATURAL JOIN materia_agrupacion NATURAL JOIN carreras WHERE nombre_materia iLIKE '$nombre' AND id_depar='$idDepartamento' AND año='$año' AND ciclo='$ciclo';";
            $respuesta = conexion::consulta2($consulta); 
            $cantidad = $respuesta['count'];

            $consulta = "SELECT id_agrupacion FROM materias NATURAL JOIN materia_agrupacion NATURAL JOIN carreras WHERE nombre_materia iLIKE '$nombre' AND id_depar='$idDepartamento' AND año='$año' AND ciclo='$ciclo';";
            $respuesta = conexion::consulta($consulta); 

            $consulta = "SELECT DISTINCT a.id_agrupacion,a.alumnos_nuevos,a.otros_alumnos,a.alumnos_grupo,a.horas_clase,a.horas_lab_semana,a.horas_discu_semana,a.lab_dis_alter,m.cod_materia,m.plan_estudio,m.id_carrera,m.uv,m.nombre_materia,ma.ciclo,ma.año,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='1') AS num_grupos,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='2') AS num_grupos_l,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='3') AS num_grupos_d,c.nombre_carrera,d.nombre_depar,d.id_depar FROM agrupacion AS a NATURAL JOIN materia_agrupacion AS ma NATURAL JOIN materias AS m NATURAL JOIN carreras AS c NATURAL JOIN departamentos AS d WHERE ";
            $cont=1;
            while ($row = pg_fetch_array($respuesta)){
                $id_agrupacion = $row['id_agrupacion'];
                if($cont==$cantidad){
                    $consulta = $consulta."ma.id_agrupacion='$id_agrupacion' ";
                }else{
                    $consulta = $consulta."ma.id_agrupacion='$id_agrupacion' OR ";
                }
                $cont++;
            }
            $consulta = $consulta."AND d.id_depar='$idDepartamento' AND ma.año='$año' AND ma.ciclo='$ciclo' ORDER BY d.nombre_depar,m.nombre_materia;";
        }
        $respuesta = conexion::consulta($consulta);
        $materias=array();
        while ($row = pg_fetch_array($respuesta)){
            $materia = new MateriaAgrupacion();
            $materia->setCodigo($row['cod_materia']);
            $materia->setPlan_estudio($row['plan_estudio']);
            $materia->setNombre($row['nombre_materia']);
            $materia->setDepartamento(new Departamento($row['id_depar'],$row['nombre_depar']));
            $materia->setNumeroGrupos($row['num_grupos']);
            $materia->setIdAgrupacion($row['id_agrupacion']);
            $materia->setAlumnoNuevos($row['alumnos_nuevos']);
            $materia->setOtrosAlumnos($row['otros_alumnos']);
            $materia->setAlumnosGrupo($row['alumnos_grupo']);
            $materia->setNumeroGruposLaboratorio($row['num_grupos_l']);
            $materia->setNumeroGruposDiscusion($row['num_grupos_d']);
            $materia->addMateria($row['nombre_materia']." carrera: ".$row['nombre_carrera']);
            $materia->setCarrera($row['nombre_carrera']);
            $materia->setUnidadesValorativas($row['uv']);
            $materia->setNum_horas_clase($row['horas_clase']);
            $materia->setNum_horas_laboratorio($row['horas_lab_semana']);
            $materia->setNum_horas_discusion($row['horas_discu_semana']);
            $materia->setDiscuciones_labs_alternados($row['lab_dis_alter']);
            $materias[] = $materia;
        }                   			
        return $materias;
    }
    
    
   /*
    * Todas las materias tienen una agrupación, aunque esa agrupación solo contenga una materia.
    * Las fusiones son aquellas agrupaciones que tienen más de una materia.
    * Para poder fusionar materias debe verificarse que las materias no estén fusionadas con otras.
    * Luego deben eliminarse sus agrupaciones para crear una nueva y agregarlas a ella.
    */ 
    public static function fusionarMaterias($materias,$año,$ciclo){                
        foreach ($materias as $materia) {
            if(self::materiaEstaFusionada($materia, $año, $ciclo)){
                $carrera = $materia->getCarrera();
                throw new Exception("Error: Ya está fusionada la materia: ".$materia->getNombre()." codigo: ".$materia->getCodigo()." carrera: ".$carrera->getCodigo()." plan: ".$carrera->getPlanEstudio()." Debe liberar esa materia antes de fusionarla con otras.");
            }                
        }   
        //Si ninguna de las materias está fusionada entonces se procede a eliminar su agrupación por defecto para
        //que puedan agregarse a una nueva agrupación.
        foreach ($materias as $materia) {
            self::eliminarAgrupacion($materia, $año, $ciclo);
        }                
        $id_agrupacion = self::crearAgrupacion($año, $ciclo);            
        self::agregarMateriasAgrupacion($materias, $año, $ciclo, $id_agrupacion);
    }
    
    /*
     * Crea una nueva agrupación y devuelve su id.
     */
    public static function crearAgrupacion($año,$ciclo){
        $consulta = "INSERT INTO agrupacion(alumnos_nuevos,otros_alumnos,alumnos_grupo,año,ciclo) VALUES (0,0,0,$año,$ciclo) RETURNING id_agrupacion";
        $respuesta = conexion::consulta2($consulta);
        return $respuesta['id_agrupacion'];
    }
    
    /*
     * Elimina la agrupación.
     */
    public static function eliminarAgrupacion($materia,$año,$ciclo){
        $carrera = $materia->getCarrera();
        $consulta = "SELECT id_agrupacion FROM materia_agrupacion WHERE plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."' AND cod_materia='".$materia->getCodigo()."'  AND año='".$año."' AND ciclo='".$ciclo."';";         
        $respuesta = conexion::consulta2($consulta);
        if($respuesta['id_agrupacion']!=""){
            $consulta = "DELETE FROM agrupacion WHERE id_agrupacion='".$respuesta['id_agrupacion']."';";
            $consulta = $consulta." DELETE FROM materia_agrupacion WHERE cod_materia='".$materia->getCodigo()."' AND plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."' AND año='$año' AND ciclo='$ciclo';";
            conexion::consulta($consulta);
        }        
    }
    
    public static function eliminarAgrupacionPorId($id_agrupacion,$año,$ciclo){
        $materias = ManejadorMaterias::getMateriasDeAgrupacion($id_agrupacion, $año, $ciclo);
        $consulta = "DELETE FROM docente_grupo WHERE id_agrupacion='".$id_agrupacion."' AND año='$año' AND ciclo='$ciclo';";
        $consulta = $consulta." DELETE FROM agrupacion WHERE id_agrupacion='".$id_agrupacion."';";
        $consulta = $consulta." DELETE FROM materia_agrupacion WHERE id_agrupacion='".$id_agrupacion."' AND año='$año' AND ciclo='$ciclo';";
        conexion::consulta($consulta);
        foreach ($materias as $materia) {
            self::crearAgrupacionParaMateria($materia, $año, $ciclo);
        }
    }
        
    public static function buscarAgrupacionSoloNombre($buscarComo,$idDepartamento,$año,$ciclo){
        $datos = array();   
        $consulta="";        
        if($idDepartamento=="todos"){
            $consulta = "SELECT DISTINCT a.id_agrupacion,a.alumnos_nuevos,a.otros_alumnos,a.alumnos_grupo,m.cod_materia,m.nombre_materia,ma.ciclo,ma.año,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='1') AS num_grupos,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='2') AS num_grupos_l,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='3') AS num_grupos_d,d.nombre_depar,d.id_depar FROM agrupacion AS a NATURAL JOIN materia_agrupacion AS ma NATURAL JOIN materias AS m NATURAL JOIN carreras AS c NATURAL JOIN departamentos AS d WHERE m.nombre_materia iLIKE '%$buscarComo%' AND ma.año='$año' AND ma.ciclo='$ciclo' ORDER BY m.nombre_materia;";
        }else{
            $consulta = "SELECT DISTINCT a.id_agrupacion,a.alumnos_nuevos,a.otros_alumnos,a.alumnos_grupo,m.cod_materia,m.nombre_materia,ma.ciclo,ma.año,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='1') AS num_grupos,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='2') AS num_grupos_l,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='3') AS num_grupos_d,d.nombre_depar,d.id_depar FROM agrupacion AS a NATURAL JOIN materia_agrupacion AS ma NATURAL JOIN materias AS m NATURAL JOIN carreras AS c NATURAL JOIN departamentos AS d WHERE m.nombre_materia iLIKE '%$buscarComo%' AND d.id_depar='$idDepartamento' AND ma.año='$año' AND ma.ciclo='$ciclo' ORDER BY m.nombre_materia;";
        }
        
        $respuesta = conexion::consulta($consulta);
        $nombresGuardados=array();
        while ($row = pg_fetch_array($respuesta)){
            if(!self::nombreEstaRepetido($row['nombre_materia'], $nombresGuardados)){
                $datos[] = array("value"=> $row['nombre_materia']);
                $nombresGuardados[] = $row['nombre_materia'];
            }
        }

        return $datos;
        
    }
    
    public static function nombreEstaRepetido($nombre,$nombres){
        foreach ($nombres as $nombreGuardado) {
            if($nombre==$nombreGuardado){            
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public static function buscarAgrupacion($buscarComo,$idDepartamento,$año,$ciclo){
        $datos = array();
        $agrupaciones = array();
//        if (ereg("[^A-Za-z0-9]+",$buscarComo)) {	//EVITAR QUE APAREZCAN CARACTERES ESPECIALES
//                    throw new Exception("¡Nombre invalido!");	
//        } else{
            $consulta="";
            if($idDepartamento=="todos"){
                $consulta = "SELECT DISTINCT m.cod_materia,m.nombre_materia,m.uv,ma.ciclo,ma.id_agrupacion,ma.año,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='1') AS num_grupos,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='2') AS num_grupos_l,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='3') AS num_grupos_d,a.alumnos_nuevos,a.otros_alumnos,a.alumnos_grupo,a.horas_clase,a.horas_lab_semana,a.horas_discu_semana,a.lab_dis_alter,d.nombre_depar,d.id_depar FROM materias as m join materia_agrupacion as ma on ma.cod_materia=m.cod_materia join carreras as c on c.id_carrera=ma.id_carrera join departamentos as d on c.id_depar=d.id_depar join agrupacion as a on a.id_agrupacion = ma.id_agrupacion WHERE m.nombre_materia iLIKE '%$buscarComo%' AND ma.año='$año' AND ma.ciclo='$ciclo' ORDER BY d.nombre_depar,m.nombre_materia LIMIT 15;";                      
            }else{
                $consulta = "SELECT DISTINCT m.cod_materia,m.nombre_materia,m.uv,ma.ciclo,ma.id_agrupacion,ma.año,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='1') AS num_grupos,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='2') AS num_grupos_l,(SELECT COUNT(*) FROM grupo WHERE id_agrupacion=a.id_agrupacion AND tipo='3') AS num_grupos_d,a.alumnos_nuevos,a.otros_alumnos,a.alumnos_grupo,a.horas_clase,a.horas_lab_semana,a.horas_discu_semana,a.lab_dis_alter,d.nombre_depar,d.id_depar FROM materias as m join materia_agrupacion as ma on ma.cod_materia=m.cod_materia join carreras as c on c.id_carrera=ma.id_carrera join departamentos as d on c.id_depar=d.id_depar join agrupacion as a on a.id_agrupacion = ma.id_agrupacion WHERE m.nombre_materia iLIKE '%$buscarComo%' AND c.id_depar='$idDepartamento' AND ma.año='$año' AND ma.ciclo='$ciclo' ORDER BY d.nombre_depar,m.nombre_materia LIMIT 15;";
            }
            $respuesta = conexion::consulta($consulta);
            while ($row = pg_fetch_array($respuesta)){
                $agrupacion = new MateriaAgrupacion();
                $agrupacion->setCodigo($row['cod_materia']);
                $agrupacion->setNombre($row['nombre_materia']);
                $agrupacion->setDepartamento(new Departamento($row['id_depar'],$row['nombre_depar']));
                $agrupacion->setNumeroGrupos($row['num_grupos']);
                $agrupacion->setIdAgrupacion($row['id_agrupacion']);
                $agrupacion->setAlumnoNuevos($row['alumnos_nuevos']);
                $agrupacion->setOtrosAlumnos($row['otros_alumnos']);
                $agrupacion->setNumeroGruposLaboratorio($row['num_grupos_l']);
                $agrupacion->setNumeroGruposDiscusion($row['num_grupos_d']);
                $agrupacion->setAlumnosGrupo($row['alumnos_grupo']);
                $agrupacion->setUnidadesValorativas($row['uv']);
                $agrupacion->setNum_horas_clase($row['horas_clase']);
                $agrupacion->setNum_horas_laboratorio($row['horas_lab_semana']);
                $agrupacion->setNum_horas_discusion($row['horas_discu_semana']);
                $agrupacion->setDiscuciones_labs_alternados($row['lab_dis_alter']);
                $agrupaciones[] = $agrupacion;
            }            
            
            for ($index = 0; $index < count($agrupaciones); $index++) {
                $datos[] = array("value"=> $agrupaciones[$index]->getNombre(),
                    "ciclos"=> $agrupaciones[$index]->getCiclos(),
                    "alumnos_nuevos" => $agrupaciones[$index]->getAlumnosNuevos(),
                    "otros_alumnos" => $agrupaciones[$index]->getOtrosAlumnos(),
                    "num_grupos" => $agrupaciones[$index]->getNumeroGrupos(),
                    "num_grupos_l" => $agrupaciones[$index]->getNumeroGruposLaboratorio(),
                    "num_grupos_d" => $agrupaciones[$index]->getNumeroGruposDiscusion(),
                    "alumnos_grupo" => $agrupaciones[$index]->getAlumnosGrupo(),  
                    "nombre_depar" => $agrupaciones[$index]->getDepartamento()->getNombre(),
                    "label" => $agrupaciones[$index]->getNombre(),
                    "category" => $agrupaciones[$index]->getDepartamento()->getNombre(),  
                    "id_depar" => $agrupaciones[$index]->getDepartamento()->getId(),  
                    "cod_materia" => $agrupaciones[$index]->getCodigo(),
                    "horas_clase" => $agrupaciones[$index]->getCodigo(),
                    "horas_lab" => $agrupaciones[$index]->getCodigo(),
                    "horas_discu" => $agrupaciones[$index]->getCodigo(),
                    "horas_alternadas" => $agrupaciones[$index]->getCodigo(),
                    "id" => $agrupaciones[$index]->getIdAgrupacion()
                    );                    
            }

            return $datos;
//        }
    }    
       
    /*
     * Verifica si alguna materia está fusionada con otras.     
     */
    public static function materiaEstaFusionada($materia,$año,$ciclo){
        $carrera = $materia->getCarrera();
        $consulta = "SELECT id_agrupacion FROM materia_agrupacion WHERE plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."' AND cod_materia='".$materia->getCodigo()."'  AND año='".$año."' AND ciclo='".$ciclo."';";         
        $respuesta = conexion::consulta2($consulta);
        if($respuesta['id_agrupacion']==""){
            return false;
        }        
        $consulta = "SELECT count(*) FROM materia_agrupacion WHERE id_agrupacion='".$respuesta['id_agrupacion']."'";
        $respuesta = conexion::consulta2($consulta);
        if($respuesta['count']>1){
            return true;
        }else{
            return false;
        }        
    }
    
    public static function materiaYaEstaAgrupada($materia,$año,$ciclo){
        $carrera = $materia->getCarrera();
        $consulta = "SELECT count(*) FROM materia_agrupacion WHERE plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."' AND cod_materia='".$materia->getCodigo()."'  AND año='".$año."' AND ciclo='".$ciclo."';";
        $respuesta = conexion::consulta2($consulta);
        if($respuesta['count']>0){
            return true;
        }else{
            return false;
        }
    }
    
    public static function crearAgrupacionParaMateria($materia,$año,$ciclo){
        $carrera = $materia->getCarrera();
        if(!self::materiaYaEstaAgrupada($materia, $año, $ciclo)){
            $consulta = "INSERT INTO agrupacion(alumnos_nuevos,otros_alumnos,alumnos_grupo,año,ciclo) VALUES (0,0,0,$año,$ciclo) RETURNING id_agrupacion";
            $respuesta = conexion::consulta2($consulta);
            $id_agrupacion = $respuesta['id_agrupacion']; 
            $consulta = "INSERT INTO materia_agrupacion(cod_materia,plan_estudio,id_carrera,año,ciclo,id_agrupacion) VALUES ('".$materia->getCodigo()."',".$carrera->getPlanEstudio().",'".$carrera->getCodigo()."',$año,$ciclo,$id_agrupacion);";
            conexion::consulta($consulta);   
        }else{
            throw new Exception("esta materia ya existe");
        }
    }
    
    public static function modificarAgrupacion($id,$campo,$dato,$año,$ciclo){        
        if($campo=="num_grupos_d"){
            if(Facultad::hayDatosEnHorario($año, $ciclo)){
                throw new Exception("Error: No se permite modificar este campo porque ya hay un horario generado.");
            }else{
                $consulta = "DELETE FROM docente_grupo WHERE id_agrupacion='".$id."' AND año='".$año."' AND ciclo='".$ciclo."' AND tipo_grupo='3'";
                Conexion::consulta($consulta);
                $consulta = "DELETE FROM grupo WHERE id_agrupacion='".$id."' AND año='".$año."' AND ciclo='".$ciclo."' AND tipo='3'";
                Conexion::consulta($consulta);
                if($dato!='0' || $dato!='00'){
                    $consulta = "INSERT INTO grupo (id,id_agrupacion,año,ciclo,tipo) VALUES ";            
                    for ($i = 1; $i <= $dato; $i++) {
                        if($i == $dato){
                            $consulta = $consulta."(".$i.",".$id.",$año,$ciclo,3)";
                        }else{
                            $consulta = $consulta."(".$i.",".$id.",$año,$ciclo,3),";                    
                        }
                    }                         
                    Conexion::consulta2($consulta);
                }            
            }            
        }else if($campo=="num_grupos_l"){
            if(Facultad::hayDatosEnHorario($año, $ciclo)){
                throw new Exception("Error: No se permite modificar este campo porque ya hay un horario generado.");
            }else{
                $consulta = "DELETE FROM docente_grupo WHERE id_agrupacion='".$id."' AND año='".$año."' AND ciclo='".$ciclo."' AND tipo_grupo='2'";
                Conexion::consulta($consulta);
                $consulta = "DELETE FROM grupo WHERE id_agrupacion='".$id."' AND año='".$año."' AND ciclo='".$ciclo."' AND tipo='2'";
                Conexion::consulta($consulta);
                if($dato!='0' || $dato!='00'){
                    $consulta = "INSERT INTO grupo (id,id_agrupacion,año,ciclo,tipo) VALUES ";            
                    for ($i = 1; $i <= $dato; $i++) {
                        if($i == $dato){
                            $consulta = $consulta."(".$i.",".$id.",$año,$ciclo,2)";
                        }else{
                            $consulta = $consulta."(".$i.",".$id.",$año,$ciclo,2),";                    
                        }
                    }                         
                    Conexion::consulta2($consulta);            
                }            
            }            
        }else if($campo=="num_grupos"){
            if(Facultad::hayDatosEnHorario($año, $ciclo)){
                throw new Exception("Error: No se permite modificar este campo porque ya hay un horario generado.");
            }else{
                $consulta = "DELETE FROM docente_grupo WHERE id_agrupacion='".$id."' AND año='".$año."' AND ciclo='".$ciclo."' AND tipo_grupo='1'";
                Conexion::consulta($consulta);
                $consulta = "DELETE FROM grupo WHERE id_agrupacion='".$id."' AND año='".$año."' AND ciclo='".$ciclo."' AND tipo='1'";
                Conexion::consulta($consulta);
                if($dato!='0' || $dato!='00'){
                    $consulta = "INSERT INTO grupo (id,id_agrupacion,año,ciclo,tipo) VALUES ";            
                    for ($i = 1; $i <= $dato; $i++) {
                        if($i == $dato){
                            $consulta = $consulta."(".$i.",".$id.",$año,$ciclo,1)";
                        }else{
                            $consulta = $consulta."(".$i.",".$id.",$año,$ciclo,1),";                    
                        }
                    }                         
                    Conexion::consulta2($consulta);
                }            
            }            
        }else{
            $consulta = "UPDATE agrupacion SET ".$campo."='".$dato."' WHERE id_agrupacion='".$id."'";
            Conexion::consulta2($consulta);
        }
    }
    
    public static function obtenerHorasTipoGrupo($grupo,$agrupacion){
        if($grupo->getTipo()=='TEORICO'){
            $horasRequeridas = $agrupacion->getTotalHorasRequeridas();
        } elseif($grupo->getTipo()=='LABORATORIO'){
            $horasRequeridas = $agrupacion->getHorasLab();
        } elseif($grupo->getTipo()=='DISCUSION'){
            $horasRequeridas = $agrupacion->getHorasDiscu();
        }
        return $horasRequeridas;
    }

    /*
     * Borra las materias de la agrupacion y agrega las nuevas materias
     */
    public static function agregarMateriasAgrupacion($materias,$año,$ciclo,$agrupacion){        
        $consulta = "";
        foreach ($materias as $materia) {
            if(self::materiaYaEstaAgrupada($materia, $año, $ciclo)){
                $carrera = $materia->getCarrera();
                $consulta = $consulta." UPDATE materia_agrupacion SET id_agrupacion='$agrupacion' WHERE cod_materia='".$materia->getCodigo()."' AND plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."' AND año='$año' AND ciclo='$ciclo';";
            }else{
                $carrera = $materia->getCarrera();
                $consulta = $consulta."INSERT INTO materia_agrupacion(plan_estudio,id_carrera,cod_materia,id_agrupacion,año,ciclo) VALUES ('".$carrera->getPlanEstudio()."','".$carrera->getCodigo()."','".$materia->getCodigo()."',$agrupacion,$año,$ciclo);";
            }            
        }        
        conexion::consulta($consulta);   
    }
    
    /*
     * Actualiza las materias que pertenecen a determinada agrupación.
     * Sustituye las materias antiguas por las nuevas que se le pasan en el array.
     */
    public static function actualizarMateriasAgrupacion($materias,$año,$ciclo,$id_agrupacion){
        foreach ($materias as $materia) {
            //Se verifica que la materia no esté fusionada en otra agrupación.
            if(self::materiaEstaFusionada($materia, $año, $ciclo)){
                //Si la materia está fusionada debe verificarse que pertenezca a esta agrupación donde se quiere agregar.                
                if(!self::materiaPerteneceAgrupacion($materia, $id_agrupacion)){
                    $carrera = $materia->getCarrera();
                    throw new Exception("Error: Ya está fusionada la materia: ".$materia->getNombre()." codigo: ".$materia->getCodigo()." carrera: ".$carrera->getCodigo()." plan: ".$carrera->getPlanEstudio()." Debe liberar esa materia antes de fusionarla con otras.");
                }
            }                
        }   
        //Se obtienen las materias antiguas de la agrupación
        $materiasAntiguas = ManejadorMaterias::getMateriasDeAgrupacion($id_agrupacion, $año, $ciclo);        
        foreach ($materiasAntiguas as $materiaAntigua) {               
            $continuara_en_agrupacion = FALSE;
            foreach ($materias as $materia) {
                if(ManejadorMaterias::materiasSonIguales($materiaAntigua, $materia)){                    
                    $continuara_en_agrupacion = TRUE;
                }
            }
            if(!$continuara_en_agrupacion){
                //Si la materia no va a continuar en la agrupación entonces debe liberarse y crearse
                //otra agrupación para ella
//                error_log("no continuará ".$materiaAntigua->getCodigo()." carrera: ".$materiaAntigua->getCarrera()->getCodigo(),0);
                self::liberarMateria($materiaAntigua, $año, $ciclo);
                self::crearAgrupacionParaMateria($materiaAntigua, $año, $ciclo);
            }else{
//                error_log("continuará ".$materiaAntigua->getCodigo()." carrera: ".$materiaAntigua->getCarrera()->getCodigo(),0);
            }
        }
        //Debe determinarse cuales materias serán agregadas a la agrupación.
        $materiasNuevas = [];
        foreach ($materias as $materia) {
            $esNueva = TRUE;
            foreach ($materiasAntiguas as $materiaAntigua) {
                if(ManejadorMaterias::materiasSonIguales($materiaAntigua, $materia)){
                    $esNueva = FALSE;
                }
            }
            if($esNueva){
                $materiasNuevas[] = $materia;
            }
        }       
        if(count($materiasNuevas)>0){            
            self::agregarMateriasAgrupacion($materiasNuevas, $año, $ciclo, $id_agrupacion);
        }        
    }
    
    /*
     * Devuelve TRUE si la materia pertenece a la agrupación indicada.
     */
    public static function materiaPerteneceAgrupacion($materia,$id_agrupacion){
        $carrera = $materia->getCarrera();
        $consulta = "SELECT COUNT(*) FROM materia_agrupacion WHERE cod_materia='".$materia->getCodigo()."' AND id_carrera='".$carrera->getCodigo()."' AND plan_estudio='".$carrera->getPlanEstudio()."' AND id_agrupacion='$id_agrupacion'";
        $respuesta = Conexion::consulta2($consulta);
        if($respuesta['count']>0){
            return true;
        }else{
            return false;
        }
    }
    
    /*
     * Sirve para que una materia ya no pertenezca a ninguna agrupación y esté disponible para agregarse a otra.     
     */
    public static function liberarMateria($materia,$año,$ciclo){
        $carrera = $materia->getCarrera();
        $consulta = "DELETE FROM materia_agrupacion WHERE cod_materia='".$materia->getCodigo()."' AND plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."' AND año='$año' AND ciclo='$ciclo';";            
        conexion::consulta($consulta);
    }
    
    /** Obtener las agrupaciones de los grupos
     * 
     * @param Grupo[] $grupos = grupos de los que se obtendran las agrupaciones
     */
    public static function extraerAgrupacionesDeGrupos($grupos){
        $final = array();
        foreach ($grupos as $grupo){
            if($grupo->getAgrup()!=null){
                $agrupaciones[] = $grupo->getAgrup();
            }
        }
        if(!isset($agrupaciones)){
            return $final;
        }
        foreach ($agrupaciones as $current) {  // Eliminar agrupaciones duplicadas
            if (!in_array($current, $final,TRUE)) {
                $final[] = $current;
            }
        }
        return $final;
    }
}
