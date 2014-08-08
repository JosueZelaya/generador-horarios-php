<?php
    chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/ManejadorAgrupaciones.php';
    chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/ManejadorMaterias.php';
    chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/ManejadorHoras.php';
    chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/ManejadorAulas.php';
    chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/Facultad.php';
    chdir(dirname(__FILE__));
    include_once 'funciones.php';
    chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/ManejadorSesion.php';
    chdir(dirname(__FILE__));
    ManejadorSesion::sec_session_start();
    $facultad = $_SESSION['facultad'];
    $modelo = create_model($facultad);
    $tabla = $modelo;
    if(isset($_GET['aula']) && isset($_GET['departamento']) && isset($_GET['carrera'])){
        $aula = $_GET['aula'];        
        if($aula=="todos"){
            $departamento = $_GET['departamento'];
            $carrera = $_GET['carrera'];
//            if($departamento=="todos"){
//                //Para todos los departamentos
//                $tabla = ManejadorHoras::getHorarioTodasHoras($departamento, $facultad, $modelo);
//            }else if($carrera=="todos"){
//                //Para todas las carreras de un departamento
//                $tabla = ManejadorHoras::getHorarioTodasHoras($departamento, $facultad, $modelo);
//            }else{
//                //Para una carrera específica.
//                
//            }
            $tabla = ManejadorHoras::getHorarioTodasHoras($departamento, $facultad, $modelo);
            echo imprimirMallaTodasAulas($tabla);
        }else{
            $departamento = $_GET['departamento'];
            $carrera = $_GET['carrera'];
            if($carrera!='todos'){                        
                $ids_agrupaciones = ManejadorAgrupaciones::obtenerAgrupacionesDeCarrera($carrera);
                $tabla = ManejadorAulas::getHorarioEnAula_Carrera($facultad->getAulas(),$aula,$ids_agrupaciones,$modelo);
            }else if($departamento!='todos'){
                $tabla = ManejadorAulas::getHorarioEnAula_Depar($aula,$departamento,$modelo,$facultad);
            }else{
                $tabla = ManejadorAulas::getHorarioEnAula($facultad->getAulas(), $aula, $modelo);
            }
            echo imprimirMalla($tabla);
        }        
    } elseif(isset($_GET['departamento']) && isset($_GET['carrera']) && isset($_GET['materia']) && isset ($_GET['ciclo'])){
        $aulas = $facultad->getAulas();
        $cod_materia = $_GET['materia'];
        $id_depar = $_GET['departamento'];
        $ciclo = $_GET['ciclo'];
        $carrera = $_GET['carrera'];
        $departamento = $_GET['departamento'];
        if($carrera=="todos" && $cod_materia=="todos"){
            if($ciclo=="todos"){
                $materias = ManejadorMaterias::obtenerMateriasDeDepartamento($facultad->getMaterias(),$departamento,"");
                foreach ($materias as $materia) {
                    echo "<h3>".$materia->getNombre()."</h3>";
                    $horario = ManejadorMaterias::getHorarioMateria($aulas,$materia->getCodigo(), $id_depar);
                    $horario = ordenarHorarioMateria($horario);
                    imprimirMallaMateria($horario);
                }
            }else{                
                $materias = ManejadorMaterias::obtenerMateriasDeDepartamento($facultad->getMaterias(),$departamento,$ciclo);
                foreach ($materias as $materia) {
                    if($materia->getCiclo()==$ciclo){
                        echo "<h3>".$materia->getNombre()."</h3>";
                        $horario = ManejadorMaterias::getHorarioMateria($aulas,$materia->getCodigo(), $id_depar);
                        $horario = ordenarHorarioMateria($horario);
                        imprimirMallaMateria($horario);
                    }
                }
            }
        }else{
            if($cod_materia=="todos"){
                if($ciclo=="todos"){
                    $materias = ManejadorMaterias::getMateriasDeCarrera($facultad->getMaterias(),$_GET['carrera']);
                    foreach ($materias as $materia) {
                        echo "<h3>".$materia->getNombre()."</h3>";
                        $horario = ManejadorMaterias::getHorarioMateria($aulas,$materia->getCodigo(), $id_depar);
                        $horario = ordenarHorarioMateria($horario);
                        imprimirMallaMateria($horario);
                    }
                }else{                
                    $materias = ManejadorMaterias::getMateriasDeCarrera($facultad->getMaterias(),$_GET['carrera']);                
                    foreach ($materias as $materia) {
                        if($materia->getCiclo()==$ciclo){
                            echo "<h3>".$materia->getNombre()."</h3>";
                            $horario = ManejadorMaterias::getHorarioMateria($aulas,$materia->getCodigo(), $id_depar);
                            $horario = ordenarHorarioMateria($horario);
                            imprimirMallaMateria($horario);
                        }
                    }
                }
            }else{
                $horario = ManejadorMaterias::getHorarioMateria($aulas, $cod_materia, $id_depar);
                $horario = ordenarHorarioMateria($horario);
                imprimirMallaMateria($horario);
            }       
        }        
    }elseif (isset($_GET['aula'])){
        $aula = $_GET['aula'];
        $tabla = ManejadorAulas::getHorarioEnAula($facultad->getAulas(), $aula, $modelo);
        echo imprimirMalla($tabla);
    }
