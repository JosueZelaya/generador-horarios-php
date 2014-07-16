<?php
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorCarreras.php';

if(isset($_GET['departamento'])){
    $idDepartamento = $_GET['departamento'];
    
    if($idDepartamento=='todos'){
        echo "<option value='todos'>TODAS</option>";
    }else{
        $carreras = ManejadorCarreras::getNombreTodasCarrerasPorDepartamento($idDepartamento);
            echo "<option value='todos'>TODAS</option>";
        for ($index = 0; $index < count($carreras); $index++) {
            echo "<option value='".$carreras[$index]."'>".$carreras[$index]."</option>";
        }
    }
}
