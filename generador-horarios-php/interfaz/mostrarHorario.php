<?php

require_once './ManejadorInterfaz.php';

if(isset($_GET['aula'])){
    echo imprimir($_GET['aula']);
}

?>
