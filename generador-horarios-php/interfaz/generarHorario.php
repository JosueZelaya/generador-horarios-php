<?php

ini_set('max_execution_time', 300);
include_once '../reglas_negocio/Procesador.php';
include_once '../reglas_negocio/Facultad.php';
include_once '../reglas_negocio/ManejadorMaterias.php';
include_once '../reglas_negocio/ManejadorReservaciones.php';
include_once '../reglas_negocio/ManejadorAgrupaciones.php';
include_once '../reglas_negocio/ManejadorDepartamentos.php';
include_once '../reglas_negocio/ManejadorAsignacionesDocs.php'; 
include_once './ManejadorInterfaz.php';
    
    $cicloPar = FALSE;
    $facultad = new Facultad(ManejadorAgrupaciones::getAgrupaciones(),  ManejadorDepartamentos::getDepartamentos(),  ManejadorAsignacionesDocs::obtenerTodasAsignacionesDocs());
    $facultad->setMaterias(ManejadorMaterias::getTodasMaterias($cicloPar));    
    ManejadorReservaciones::asignarRerservaciones($facultad);
    $materias = $facultad->getMaterias();
    $procesador = new Procesador();
    $procesador->asignarDatos($facultad);
    for ($i = 0; $i < count($materias); $i++) {
        $agrup = ManejadorAgrupaciones::getAgrupacion($materias[$i]->getIdAgrupacion(), $facultad->agrupaciones);
        if($agrup->getNum_grupos()==$agrup->getNumGruposAsignados())
            continue;
        $asignaciones = ManejadorAsignacionesDocs::obtenerAsignacionesDeAgrup($agrup->getId(), $facultad->asignaciones_docs);
        for ($j = 0; $j < count($asignaciones); $j++) {
            $asignacion = $asignaciones[$j];
            for ($k = 0; $k < $asignacion->getNum_grupos(); $k++) {
                try {
                    $procesador->procesarMateria($materias[$i], $asignacion->getId_docente(), $agrup);
                } catch (Exception $exc) {
//                    Se produce cuando ya no hay aulas disponibles
                    error_log($exc->getMessage(),0);
                }
                $agrup->setNumGruposAsignados($agrup->getNumGruposAsignados()+1);
            }
        }

    } 

    $_SESSION['facultad'] = $facultad;