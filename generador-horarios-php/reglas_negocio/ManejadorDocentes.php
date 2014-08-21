<?php

chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Docente.php';
include_once 'ManejadorCargos.php';
include_once 'ManejadorDepartamentos.php';

abstract class ManejadorDocentes{
    
    /**
     * 
     * @param Cargo[] $todos_cargos
     * @param Integer $año año actual
     * @param Integer $ciclo ciclo actual (1|2)
     * @param Departamento[] $todos_depars Todos los departamentos de la facultad
     * @return Docente[] $docentes
     */
    public static function obtenerTodosDocentes($todos_cargos,$año,$ciclo,$todos_depars){
        $sql_consulta = "SELECT id_docente,nombres,apellidos,contratacion,id_depar,cargo FROM docentes where activo=TRUE";
        $respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $docente = new Docente($fila['id_docente'],$fila['nombres'],$fila['apellidos'],$fila['contratacion'], ManejadorDepartamentos::obtenerDepartamento($fila['id_depar'], $todos_depars));
            if(isset($fila['cargo'])){
                $docente->setCargo(ManejadorCargos::obtenerCargo($fila['cargo'], $todos_cargos));
            }
            $docente->setHorario(self::obtenerHorarioDocente($fila['id_docente'], $año, $ciclo));
            $docentes[] = $docente;
        }
        return $docentes;
    }
    
    /**
     * 
     * @param Integer $id_docente = identificador de docente que se desea obtener horario de trabajo
     * @param Integer $año año actual de trabajo
     * @param Integer $ciclo ciclo actual de trabajo (1|2)
     */
    public static function obtenerHorarioDocente($id_docente,$año,$ciclo){
        $horario = null;
        $consulta = "select id_dia,id_hora from docente_horario as dh where id_docente=$id_docente and dh.año=$año and ciclo=$ciclo order by id_dia,id_hora";
        $respuesta = Conexion::consulta($consulta);
        $dia = new Dia(0, "");
        while($fila = pg_fetch_array($respuesta)){
            if($dia->getId() == $fila['id_dia']){
                $hora = new Hora();
                $hora->setIdHora($fila['id_hora']);
                $dia = $horario[count($horario)-1];
                $dia->addHora($hora);
            } else{
                $dia = new Dia($fila['id_dia'], "");
                $hora = new Hora();
                $hora->setIdHora($fila['id_hora']);
                $dia->addHora($hora);
                $horario[] = $dia;
            } 
        }
        return $horario;
    }
    
    public static function horarioDocenteEnDia($horario,$tabla,$dias){
        $horasCompletas = $dias[0]->getHoras();
        if($horario != null){
            $horasDoc = array_reverse($horario[0]->getHoras());
            for($i=0;$i<count($horasCompletas);$i++){
                if(count($horasDoc)>0 && $horasCompletas[$i]->getIdHora() == end($horasDoc)->getIdHora()){
                    $tabla[1][$i] = array("texto"=>"Hora asignada","idHora"=>$horasCompletas[$i]->getIdHora());
                    array_pop($horasDoc);
                } else{
                    $tabla[1][$i] = array("texto"=>"","idHora"=>$horasCompletas[$i]->getIdHora());
                }
            }
        } else{
            for($i=0;$i<count($horasCompletas);$i++){
                $tabla[1][$i] = array("texto"=>"","idHora"=>$horasCompletas[$i]->getIdHora());
            }
        }
        return $tabla;
    }

    /** Asumiendo que un docente tiene el mismo horario de lunes a viernes, se busca la hora comun entre los docentes que imparten un solo grupo
     *  para determinar el inicio de la clase
     * @param Docente[] $docentes
     * @param boolean $horarioFinde true si se desea saber el horario de fin de semana, false para horario de lunes a viernes
     */
    public static function intersectarHorarios($docentes,$horarioFinde){
        $indexDia = (!$horarioFinde) ? 0 : 5;
        $horasComunes = null;
        for ($i=0;$i<count($docentes);$i++){
            $docente = $docentes[$i];
            if($docente->getHorario()!=null){
                $horasDoc = $docente->getHorario()[$indexDia]->getHoras();
                foreach ($horasDoc as $hora) {
                    $horas[$i][] = $hora->getIdHora();
                }
            }
        }
        if(isset($horas) && count($horas)>1){
            $horas = array_values($horas);
            $horarioDoc1 = $horas[0];
            foreach ($horarioDoc1 as $hora){ // Todas las horas del docente 0
                for ($i=1; $i<count($horas);$i++){ // horarios de todos los docentes
                    if(in_array($hora, $horas[$i])){
                        if($i == (count($horas)-1)){
                            $horasComunes[] = $hora;
                        }
                    }else{
                        break;
                    }
                }
            }
        } elseif(isset($horas) && count($horas)==1){
            return current($horas);
        }
        return $horasComunes;
    }
    
    public static function obtenerDocente($idDocente,$todos_docentes){
        foreach ($todos_docentes as $docente) {
            if($docente->getIdDocente() == $idDocente){
                return $docente;
            }
        }
        return null;
    }
    
    /** Determina si un docente puede o no laborar en un dia especifico
     * 
     * @param Docente[] $docentes Docentes que imparten clases a un mismo grupo
     * @param Dia $dia Dia posible para asignar al grupo
     * @return boolean true si docente no puede laborar en ese dia
     */
    public static function docenteInhabilitado($docentes,$dia){
        foreach ($docentes as $docente){
            if($docente->getCargo() != null && $docente->getCargo()->getId_dia_exento() == $dia->getId()){
                error_log ("Docente ".$docente->getIdDocente()." no esta habilitado en dia ".$dia->getNombre(),0);
                return true;
            } elseif(!$docente->diaHabil($dia->getId())){
                error_log ("Docente ".$docente->getIdDocente()." no esta habilitado en dia ".$dia->getNombre(),0);
                return true;
            }
        }
        return false;
    }
    
    public static function existeDocRespaldo($docentes){
        foreach ($docentes as $docente) {
            if($docente->getDepar()->getId()==13){
                return true;
            }
        }
        return false;
    }
    
    /** Separar docentes que poseen un horario especial de trabajo de aquellos que son TC
     * 
     * @param Docente[] $todos_docentes Todos los docentes de la facultad
     * @return Docente[]
     */
    public static function clasificarDocentes($todos_docentes){
        $docentes = array('conprior'=>array(),'noprior'=>array());
        foreach ($todos_docentes as $docente){
            if($docente->getHorario()!=null){
                $docentes['conprior'][] = $docente;
            } else{
                $docentes['noprior'][] = $docente;
            }
        }
        return $docentes;
    }
    
    public static function extraerGruposDeDocentes($docentes){
        foreach ($docentes as $docente) {
            $gruposDoc = $docente->getGrupos();
            foreach ($gruposDoc as $grupo) {
                $grupos[] = $grupo;
            }
        }
        return $grupos;
    }
    
    /** Determina si el docente o los docentes de un grupo ya estan ocupados en una hora específica de un día especifico
     * 
     * @param Docente[] $docentes
     * @param Hora $hora
     */
    public static function docenteTrabajaHora($docentes,$hora){
        $docentesConflict = array();
        $grupo = $hora->getGrupo();
        foreach ($docentes as $docente){
            if(in_array($docente, $grupo->getDocentes(),TRUE)){
                $docentesConflict[] = $docente;
            }
        }
        return $docentesConflict;
    }
    
    public static function buscarDocentes($buscarComo,$idDepartamento){
        $datos = array();
        if($idDepartamento=="todos"){
//            $consulta = "SELECT * FROM docentes WHERE (nombres iLIKE '%$buscarComo%' OR apellidos iLIKE '%$buscarComo%') LIMIT 15";
            $consulta = "SELECT * FROM docentes WHERE (nombres || ' ' || apellidos) iLIKE '%$buscarComo%' AND activo='t' LIMIT 50;";
        }else{
//            $consulta = "SELECT * FROM docentes WHERE (nombres iLIKE '%$buscarComo%' OR apellidos iLIKE '%$buscarComo%') AND id_depar='$idDepartamento' LIMIT 15";
            $consulta = "SELECT * FROM docentes WHERE (nombres || ' ' || apellidos) iLIKE '%$buscarComo%' AND id_depar='$idDepartamento' AND activo='t' LIMIT 50;";
        }        
        $respuesta = conexion::consulta($consulta);
        while ($row = pg_fetch_array($respuesta)){
            $datos[] = array("value"=>$row['nombres']." ".$row['apellidos'],
                            "id"=>$row['id_docente']);
        }        
        return $datos;
    }
    
    public static function getDocentesDepartamento($idDepar){
        $datos = array();
        if($idDepar=="todos"){
            $consulta = "SELECT * FROM docentes WHERE activo='t'";
        }else{
            $consulta = "SELECT * FROM docentes WHERE id_depar='$idDepar' AND activo='t'";
        }        
        $respuesta = conexion::consulta($consulta);
        while ($row = pg_fetch_array($respuesta)){
            $datos[] = array("Name"=>$row['nombres']." ".$row['apellidos'],
                            "Id"=>$row['id_docente']);
        }        
        return $datos;
    }
    
    public static function guardarHorarioDocente($año,$ciclo,$desde,$hasta,$idDocente){
        try{
            self::borrarHorarioDocente($idDocente, $año, $ciclo);
            for($i=1;$i<=5;$i++){
                for($j=$desde;$j<=$hasta;$j++){
                    $query = "INSERT INTO docente_horario VALUES($idDocente,$i,$j,$año,$ciclo)";
                    Conexion::consulta($query);
                }
            }
            return true;
        } catch (Exception $ex){
            return $ex->getMessage();
        }
    }
    
    public static function borrarHorarioDocente($idDocente,$año,$ciclo){
        $query = "DELETE FROM docente_horario WHERE id_docente=$idDocente AND año=$año AND ciclo=$ciclo";
        Conexion::consulta($query);
    }
    
}