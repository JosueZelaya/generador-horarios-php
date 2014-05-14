<?php

require_once './ManejadorInterfaz.php';

if(isset($_GET['aula']) && isset($_GET['departamento']) && isset($_GET['carrera'])){
    echo imprimir($_GET['aula'],$_GET['departamento'],$_GET['carrera']);
}

?>

