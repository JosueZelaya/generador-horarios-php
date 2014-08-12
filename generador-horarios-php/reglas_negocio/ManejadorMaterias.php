<?php
/**
 * Description of ManejadorMaterias
 *
 * @author abs
 */

chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Dia.php';
include_once 'Hora.php';
include_once 'Aula.php';
include_once 'Grupo.php';
include_once 'Materia.php';
include_once 'Carrera.php';
include_once 'Departamento.php';
include_once 'ManejadorAgrupaciones.php';
include_once 'ManejadorCarreras.php';
include_once 'ManejadorGrupos.php';
include_once 'ManejadorAulas.php';

abstract class ManejadorMaterias { 
    
    public static function agregarMateria($materia,$año,$ciclo){
        if(!self::exite($materia)){
            $consulta = "INSERT INTO materias(id_carrera,plan_estudio,cod_materia,nombre_materia,tipo_materia,ciclo_carrera,uv) VALUES (";
            $consulta = $consulta."'".$materia->getCarrera()->getCodigo()."',";
            $consulta = $consulta."'".$materia->getCarrera()->getPlanEstudio()."',";
            $consulta = $consulta."'".$materia->getCodigo()."',";
            $consulta = $consulta."'".$materia->getNombre()."',";
            $consulta = $consulta."'".$materia->getTipo()."',";
            $consulta = $consulta."'".$materia->getCiclo()."',";
            $consulta = $consulta."'".$materia->getUnidadesValorativas()."'";
            $consulta = $consulta.");";            
            conexion::consulta2($consulta);
            ManejadorAgrupaciones::crearAgrupacionParaMateria($materia, $año, $ciclo);
        }else{
            throw new Exception("Ya existe esa materia");
        }
    }
    
    public static function exite($materia){
        $consulta = "SELECT count(*) FROM materias WHERE plan_estudio='".$materia->getCarrera()->getPlanEstudio()."' AND id_carrera='".$materia->getCarrera()->getCodigo()."' AND cod_materia='".$materia->getCodigo()."';";
        $respuesta = conexion::consulta2($consulta);
        if($respuesta['count']>0){
            return true;
        }else{
            return false;
        }
    }
    
    /** Devuelve todas las materias del ciclo indicado par/impar
     * 
     * @param type $ciclo
     */
    public static function getTodasMateriasDeCiclo($ciclo){
        $materias = array();
        $restriccion="";
        if($ciclo==2 || $ciclo=="par"){
            $restriccion = "ciclo_carrera%2=0";
        }else{
            $restriccion = "ciclo_carrera%2!=0";
        }
        $consulta = "SELECT * FROM materias WHERE $restriccion ORDER BY cod_materia";
        $respuesta = Conexion::consulta($consulta);
        while($fila = pg_fetch_array($respuesta)){
            $carrera = new Carrera($fila['id_carrera'],$fila['plan_estudio'],"","");
            $materia = new Materia($fila['cod_materia'],$fila['nombre_materia'],$fila['ciclo_carrera'],$fila['uv'],$carrera,"","");
            $materias[] = $materia;
        }
        return $materias;
    }
    
    public static function getTodasMaterias($ciclo,$año,$todas_agrups,$todas_carreras){
        $materias = array();
        if($ciclo == 1){
            $respuesta = Conexion::consulta("select m.cod_materia,m.nombre_materia,m.uv,m.ciclo_carrera,m.id_carrera,m.plan_estudio,ma.id_agrupacion from materias as m natural join materia_agrupacion as ma WHERE m.ciclo_carrera%2!=0 AND ma.año=$año AND ma.ciclo=1 ORDER BY m.cod_materia");
        } else {
            $respuesta = Conexion::consulta("select m.cod_materia,m.nombre_materia,m.uv,m.ciclo_carrera,m.id_carrera,m.plan_estudio,ma.id_agrupacion from materias as m natural join materia_agrupacion as ma WHERE m.ciclo_carrera%2=0 AND ma.año=$año AND ma.ciclo=2 ORDER BY m.cod_materia");
        }
        while($fila = pg_fetch_array($respuesta)){
            $agrupacion = ManejadorAgrupaciones::getAgrupacion($fila['id_agrupacion'], $todas_agrups);
            $materia = new Materia($fila['cod_materia'],$fila['nombre_materia'],$fila['ciclo_carrera'],$fila['uv'],  ManejadorCarreras::getCarrera($fila['id_carrera'],$fila['plan_estudio'], $todas_carreras),$agrupacion,true);
            $materias[] = $materia;
            $agrupacion->setMateria($materia);
        }
        return $materias;
    }
    
    public static function materiasMismoNivel($a,$b){
        if(is_array($a) && is_array($b)){
            foreach ($a as $materiaA){
                foreach ($b as $materiaB) {
                    if($materiaA->getCarrera() === $materiaB->getCarrera() && $materiaA->getCiclo() == $materiaB->getCiclo()){
                        return true;
                    }
                }
            }
        } elseif(!is_array($a) && !is_array($b)){
            if($a->getCarrera() === $b->getCarrera() && $a->getCiclo() == $b->getCiclo()){
                return true;
            }
        }
        return false;
    }
    
    public static function getMateriasDeCarrera($materias, $nombre_carrera){
        $materiasCarrera = array();
        foreach($materias as $materia){            
            if(strcmp($materia->getCarrera()->getNombre(),$nombre_carrera)==0){                
                $materiasCarrera[] = $materia;
            }
        }
        return $materiasCarrera;
    }
    
    public static function obtenerMateriasDeDepartamento($materias,$idDepar,$ciclo){
        $materiasDepar = array();
        foreach($materias as $materia){
            if($ciclo=="" || $ciclo=="todos"){
                if(strcmp($materia->getCarrera()->getDepartamento()->getId(), $idDepar)==0){
                    $materiasDepar[] = $materia;
                }
            }else{
                if(strcmp($materia->getCarrera()->getDepartamento()->getId(), $idDepar)==0 && $materia->getCiclo()==$ciclo){
                    $materiasDepar[] = $materia;
                }                
            }            
        }        
        $materiasSinRepetir = ManejadorMaterias::quitarMateriasRepetidas($materiasDepar);
        return $materiasSinRepetir;
    }
    
    public static function quitarMateriasRepetidas($materias){
        $resultado=array();
        for ($index = 0; $index < count($materias); $index++) {
            if($index>0){
                if(strcmp($materias[$index-1]->getNombre(),$materias[$index]->getNombre())==0){
                    
                }else{
                    $resultado[] = $materias[$index];
                }
            }else{
                $resultado[] = $materias[$index];
            }
        }
        return $resultado;
    }
    
    /**
     * 
     * @param Aula[] $aulas
     * @param String $cod_materia
     * @param Integer $id_depar
     * @return array
     */
    public static function getHorarioMateria($aulas,$cod_materia,$id_depar){
        $horario = array();
        foreach ($aulas as $aula){
            $dias = $aula->getDias();                  
            foreach ($dias as $dia){
                $horas = $dia->getHoras();
                $grupoAnterior=null;
                foreach ($horas as $hora){
                    $grupo = $hora->getGrupo();
                    if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                        $materias = $grupo->getAgrup()->getMaterias();
                        foreach ($materias as $materia){
                            if($materia->getCarrera()->getDepartamento()->getId() == $id_depar && strcmp($materia->getCodigo(),$cod_materia)==0){                                                        
                                if($grupoAnterior!=null && $grupoAnterior===$grupo){
                                    $indiceUltimaHora = count($horario[$grupo->getTipo()])-1;
                                    $horario[$grupo->getTipo()][$indiceUltimaHora]['horaFin'] = $hora->getFin();                                    
                                }else{
                                    $arrayGrupo = [
                                    "aula" => $aula->getNombre(),
                                    "dia" => $dia->getNombre(),
                                    "horaInicio" => $hora->getInicio(),                                
                                    "horaFin" => $hora->getFin(),
                                    "grupo" => $grupo->getId_grupo(),
                                    "tipo" => $grupo->getTipo(),
                                    "more" => false,
                                    "cloned" => false];
                                    $propietarios = ManejadorGrupos::obtenerCodigoPropietario($grupo->getAgrup()->getMaterias());
                                    if(count(array_unique($propietarios))>1){
                                        $arrayGrupo['cloned']=true;
                                    } elseif(count($propietarios)>1){
                                        $arrayGrupo['more']=true;
                                    }
                                    $horario[$grupo->getTipo()][] = $arrayGrupo;
                                    $grupoAnterior = $hora->getGrupo();
                                }
                                break;
                            }
                        }
                    }
                }
            }            
        }
        return $horario;
    }
    
    public static function buscarMateriaParaAgrupar($materia,$ciclo,$departamento){
        if($ciclo=="impar"){
            if($departamento=="todos"){                
                $consulta = "SELECT DISTINCT nombre_materia FROM materias WHERE nombre_materia iLIKE '$materia%' AND ciclo_carrera%2!=0 ORDER BY nombre_materia LIMIT 15;";
            }else{
                $consulta = "SELECT DISTINCT m.nombre_materia FROM materias AS m JOIN carreras AS c ON m.id_carrera=c.id_carrera WHERE m.nombre_materia iLIKE '%$materia%' AND c.id_depar='$departamento' AND m.ciclo_carrera%2!=0 ORDER BY m.nombre_materia LIMIT 15;";
            }            
        }else if($ciclo=="par"){
            if($departamento=="todos"){
                $consulta = "SELECT DISTINCT nombre_materia FROM materias WHERE nombre_materia iLIKE '$materia%' AND ciclo_carrera%2=0 ORDER BY nombre_materia LIMIT 15;";
            }else{
                $consulta = "SELECT DISTINCT m.nombre_materia FROM materias AS m JOIN carreras AS c ON m.id_carrera=c.id_carrera WHERE m.nombre_materia iLIKE '%$materia%' AND c.id_depar='$departamento' AND m.ciclo_carrera%2=0 ORDER BY m.nombre_materia LIMIT 15;";
            }            
        }else{            
            if($departamento=="todos"){
                $consulta = "SELECT DISTINCT nombre_materia FROM materias WHERE nombre_materia iLIKE '$materia%' ORDER BY nombre_materia LIMIT 15;";
            }else{
                $consulta = "SELECT DISTINCT m.nombre_materia FROM materias AS m JOIN carreras AS c ON m.id_carrera=c.id_carrera WHERE m.nombre_materia iLIKE '%$materia%' AND c.id_depar='$departamento' ORDER BY m.nombre_materia LIMIT 15;";
            }            
        }        
        $respuesta = conexion::consulta($consulta);
        $materias = array();
        while ($row = pg_fetch_array($respuesta)){
            $materias[] = array("value"=>$row['nombre_materia']);
        }
        return $materias;
    }
       
    public static function getMateriasParaAgrupar($materia,$ciclo,$departamento){
        if($ciclo=="impar"){
            if($departamento=="todos"){
                $consulta = "SELECT DISTINCT m.cod_materia,m.id_carrera,m.plan_estudio,m.nombre_materia,m.tipo_materia,m.ciclo_carrera,m.uv,c.id_carrera,c.id_depar,c.nombre_carrera,d.nombre_depar FROM materias as m NATURAL JOIN carreras as c NATURAL JOIN departamentos as d WHERE m.nombre_materia='".$materia."' AND m.ciclo_carrera%2!=0 ORDER BY m.cod_materia,m.id_carrera;";
            }else{
                $consulta = "SELECT DISTINCT m.cod_materia,m.id_carrera,m.plan_estudio,m.nombre_materia,m.tipo_materia,m.ciclo_carrera,m.uv,c.id_carrera,c.id_depar,c.nombre_carrera,d.nombre_depar FROM materias as m NATURAL JOIN carreras as c NATURAL JOIN departamentos as d WHERE m.nombre_materia='".$materia."' AND c.id_depar='$departamento' AND m.ciclo_carrera%2!=0 ORDER BY m.cod_materia,m.id_carrera;";
            }
        }else if($ciclo=="par"){
            if($departamento=="todos"){
                $consulta = "SELECT DISTINCT m.cod_materia,m.id_carrera,m.plan_estudio,m.nombre_materia,m.tipo_materia,m.ciclo_carrera,m.uv,c.id_carrera,c.id_depar,c.nombre_carrera,d.nombre_depar FROM materias as m NATURAL JOIN carreras as c NATURAL JOIN departamentos as d WHERE m.nombre_materia='".$materia."' AND m.ciclo_carrera%2=0 ORDER BY m.cod_materia,m.id_carrera;";
            }else{
                $consulta = "SELECT DISTINCT m.cod_materia,m.id_carrera,m.plan_estudio,m.nombre_materia,m.tipo_materia,m.ciclo_carrera,m.uv,c.id_carrera,c.id_depar,c.nombre_carrera,d.nombre_depar FROM materias as m NATURAL JOIN carreras as c NATURAL JOIN departamentos as d WHERE m.nombre_materia='".$materia."' AND c.id_depar='$departamento' AND m.ciclo_carrera%2=0 ORDER BY m.cod_materia,m.id_carrera;";
            }    
        }else{
            if($departamento=="todos"){
                $consulta = "SELECT DISTINCT m.cod_materia,m.id_carrera,m.plan_estudio,m.nombre_materia,m.tipo_materia,m.ciclo_carrera,m.uv,c.id_carrera,c.id_depar,c.nombre_carrera,d.nombre_depar FROM materias as m NATURAL JOIN carreras as c NATURAL JOIN departamentos as d WHERE m.nombre_materia='".$materia."' ORDER BY m.cod_materia,m.id_carrera;";
            }else{
                $consulta = "SELECT DISTINCT m.cod_materia,m.id_carrera,m.plan_estudio,m.nombre_materia,m.tipo_materia,m.ciclo_carrera,m.uv,c.id_carrera,c.id_depar,c.nombre_carrera,d.nombre_depar FROM materias as m NATURAL JOIN carreras as c NATURAL JOIN departamentos as d WHERE m.nombre_materia='".$materia."' AND c.id_depar='$departamento' ORDER BY m.cod_materia,m.id_carrera;";
            }    
        }  
        $materias = array();
        $respuesta = conexion::consulta($consulta);
        while ($row = pg_fetch_array($respuesta)){
            $materia = new Materia("","","","","","","");
            $materia->setCodigo($row['cod_materia']);
            $materia->setNombre($row['nombre_materia']);            
            $departamento = new Departamento($row['id_depar'], $row['nombre_depar']);
            $carrera = new Carrera($row['id_carrera'],$row['plan_estudio'],$row['nombre_carrera'],$departamento);
            $materia->setCarrera($carrera);                    
            $materia->setCiclo($row['ciclo_carrera']);
            $materia->setUnidadesValorativas($row['uv']);
            $materia->setTipo($row['tipo_materia']);
            $materias[] = $materia;
        }                   			
        return $materias;
    }
    
    //Devuelve el número de materias que existen en el departamento para un ciclo par o impar
    public static function getCuantasMateriasExisten($idDepartamento){
        $consulta = "";
        if($idDepartamento=="todos"){
            $consulta = "SELECT COUNT(*) FROM materias;";
        }else{
            $consulta = "SELECT COUNT(*) FROM materias AS m NATURAL JOIN carreras AS c WHERE c.id_depar='$idDepartamento';";
        }        
        $respuesta = Conexion::consulta2($consulta);
        $cantidad = $respuesta['count'];
        return $cantidad;
    }
    
    public static function getMateriasDeAgrupacion($id_agrupacion,$año,$ciclo){
        $materias = array();
        $consulta = "SELECT * FROM agrupacion NATURAL JOIN materia_agrupacion NATURAL JOIN materias NATURAL JOIN carreras NATURAL JOIN departamentos WHERE id_agrupacion='$id_agrupacion' AND año='$año' AND ciclo='$ciclo';";
        $respuesta = conexion::consulta($consulta);
        while ($row = pg_fetch_array($respuesta)){
            $materia = new Materia("","","","","","","");
            $materia->setCodigo($row['cod_materia']);
            $materia->setNombre($row['nombre_materia']);            
            $materia->setCarrera(new Carrera($row['id_carrera'],$row['plan_estudio'],$row['nombre_carrera'],new Departamento($row['id_depar'], $row['nombre_depar'])));
            $materias[] = $materia;
        }                   			
        return $materias;
    }

    public static function materiasSonIguales($materia1,$materia2){
        $carrera1 = $materia1->getCarrera();
        $carrera2 = $materia2->getCarrera();
        return ($materia1->getCodigo() == $materia2->getCodigo() && $carrera1->getCodigo() == $carrera2->getCodigo() && $carrera1->getPlanEstudio()==$carrera2->getPlanEstudio());
    }
    
    public static function getTodasMateriasConPaginacion($pagina,$numeroResultados){
        $materias = array();
        $pagina = ($pagina-1)*$numeroResultados;
        $sql_consulta = "SELECT * FROM materias AS m NATURAL JOIN carreras AS c NATURAL JOIN departamentos AS d ORDER BY nombre_materia ASC LIMIT ".$numeroResultados." OFFSET ".$pagina;
        $respuesta = conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $materia = new Materia("","","","","","","");
            $departamento = new Departamento($fila['id_depar'], $fila['nombre_depar']);
            $carrera = new Carrera($fila['id_carrera'],$fila['plan_estudio'],$fila['nombre_carrera'],$departamento);
            $materia->setCarrera($carrera);
            $materia->setCodigo($fila['cod_materia']);
            $materia->setNombre($fila['nombre_materia']);
            $materia->setCiclo($fila['ciclo_carrera']);
            $materia->setUnidadesValorativas($fila['uv']);
            $materia->setTipo($fila['tipo_materia']);            
            $materias[] = $materia;
        }                   			
        return $materias;
    }
    
    public static function getTodasMateriasSinPaginacion(){
        $materias = array();        
        $sql_consulta = "SELECT * FROM materias AS m NATURAL JOIN carreras AS c NATURAL JOIN departamentos AS d ORDER BY nombre_materia ASC";
        $respuesta = conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $materia = new Materia("","","","","","","");
            $departamento = new Departamento($fila['id_depar'], $fila['nombre_depar']);
            $carrera = new Carrera($fila['id_carrera'],$fila['plan_estudio'],$fila['nombre_carrera'],$departamento);
            $materia->setCarrera($carrera);
            $materia->setCodigo($fila['cod_materia']);
            $materia->setNombre($fila['nombre_materia']);
            $materia->setCiclo($fila['ciclo_carrera']);
            $materia->setUnidadesValorativas($fila['uv']);
            $materia->setTipo($fila['tipo_materia']);            
            $materias[] = $materia;
        }                   			
        return $materias;
    }
    
    public static function eliminarMateria($materia,$año,$ciclo){
        $carrera = $materia->getCarrera();
        if(ManejadorAgrupaciones::materiaEstaFusionada($materia, $año, $ciclo)){
            $consulta = "DELETE FROM materia_agrupacion WHERE cod_materia='".$materia->getCodigo()."' AND plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."';";            
            $consulta = $consulta." DELETE FROM materias WHERE cod_materia='".$materia->getCodigo()."' AND plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."';";
            Conexion::consulta2($consulta);
        }else{
            $consulta = "SELECT id_agrupacion FROM materia_agrupacion WHERE cod_materia='MAT535' AND plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."';";
            $respuesta = Conexion::consulta2($consulta);
            $id_agrupacion = $respuesta['id_agrupacion'];
            if($id_agrupacion!=""){
                $consulta = "DELETE FROM materia_agrupacion WHERE cod_materia='".$materia->getCodigo()."' AND plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."';";            
                $consulta = $consulta." DELETE FROM materias WHERE cod_materia='".$materia->getCodigo()."' AND plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."';";
                $consulta = $consulta." DELETE FROM agrupacion WHERE id_agrupacion='".$id_agrupacion."'";
            }else{
                $consulta = "DELETE FROM materia_agrupacion WHERE cod_materia='".$materia->getCodigo()."' AND plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."';";            
                $consulta = $consulta." DELETE FROM materias WHERE cod_materia='".$materia->getCodigo()."' AND plan_estudio='".$carrera->getPlanEstudio()."' AND id_carrera='".$carrera->getCodigo()."';";                
            }            
            Conexion::consulta2($consulta);
        }
    }
    
    public static function modificarMateria($cod,$plan,$id_carrera,$campo,$valor,$año){
        $ciclo_agrupacion = "";
        if($campo=="nombre"){
            $campo = "nombre_materia";
        }else if($campo=="ciclo"){
            $campo = "ciclo_carrera";
            if(fmod($valor,2)==0){
                $ciclo_agrupacion = 2;
            }else{
                $ciclo_agrupacion = 1;
            }
        }else if($campo=="uv"){
            $campo = "uv";
        }else if($campo=="tipo"){
            $campo = "tipo_materia";
        }else{
            throw new Exception("Error: Campo no permitido");
        }
        $consulta_update = "UPDATE materias SET $campo='$valor' WHERE cod_materia='$cod' AND plan_estudio='$plan' AND id_carrera='$id_carrera';";
        if($campo=="ciclo_carrera"){
            $carrera = new Carrera($id_carrera,$plan,"","");
            $materia = new Materia($cod,"","","", $carrera,"","");                    
            $consulta = "SELECT ciclo FROM materia_agrupacion WHERE cod_materia='$cod' AND plan_estudio='$plan' AND id_carrera='$id_carrera' AND año='$año';";
            $respuesta = Conexion::consulta2($consulta);
            $ciclo_anterior = $respuesta['ciclo'];
            if((fmod($ciclo_agrupacion,2)==0 && fmod($ciclo_anterior,2)==0) || (fmod($ciclo_agrupacion,2)!=0 && fmod($ciclo_anterior,2)!=0)){
                //El ciclo sigue siendo el mismo para la agrupacion.

            }else{
                //El ciclo cambió para la agrupación.
                if(ManejadorAgrupaciones::materiaEstaFusionada($materia, $año,$ciclo_anterior)){
                    ManejadorAgrupaciones::liberarMateria($materia, $año, $ciclo_anterior);
                    ManejadorAgrupaciones::crearAgrupacionParaMateria($materia, $año, $ciclo_agrupacion);
                }else{
                    $consulta = "SELECT id_agrupacion FROM materia_agrupacion WHERE cod_materia='$cod' AND plan_estudio='$plan' AND id_carrera='$id_carrera' AND año='$año' AND ciclo='$ciclo_anterior';";
                    $respuesta = Conexion::consulta2($consulta);
                    $id_agrupacion = $respuesta['id_agrupacion'];                    
                    $consulta_update = $consulta_update." UPDATE agrupacion SET ciclo='$ciclo_agrupacion' WHERE id_agrupacion='$id_agrupacion'";
                }                
            }
            
        }
        Conexion::consulta2($consulta_update);
    }
    
    public static function mismoDepartamentoAgrupacionMateria($agrupacion,$materia){
        $materiasAgrup = $agrupacion->getMaterias();
        foreach ($materiasAgrup as $materiaAgrup){
            if($materiaAgrup->getCarrera()->getDepartamento() == $materia->getCarrera()->getDepartamento()){
                return true;
            }
        }
        return false;
    }
}
