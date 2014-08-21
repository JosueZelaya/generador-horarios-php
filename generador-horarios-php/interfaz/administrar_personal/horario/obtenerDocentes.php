<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDocentes.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();

echo json_encode(ManejadorDocentes::getDocentesDepartamento($_SESSION['id_departamento']));