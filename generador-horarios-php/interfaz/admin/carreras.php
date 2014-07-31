<?php
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorCarreras.php';

if(isset($_GET['departamento'])){
    $idDepartamento = $_GET['departamento'];
        $carreras = ManejadorCarreras::getNombreTodasCarrerasPorDepartamento($idDepartamento);
            echo "<option value='todos'>TODAS</option>";
            foreach ($carreras as $carrera) {
                echo "<option value='".$carrera."'>".$carrera."</option>";
            }
}
