<?php

include_once '../reglas_negocio/Facultad.php';

session_start();
$facultad = $_SESSION['facultad'];

if(isset($_GET['criterio'])){
    $criterio = $_GET['criterio'];?>
    <ul class='nav nav-tabs'>
    <?php    
    if($criterio=='departamento'){ //Muestra el filtro para los departamentos?>
        <li><a href='#'>TODO</a></li>"
        <li  class='active'><a href='#'>Departamento</a></li>"
        <li><a href='#'>Carrera</a></li>"
    <?php    
    }else if($criterio=='carrera'){ //Muestra el filtro para las carreras?>
        <li><a href='#'>TODO</a></li>
        <li><a href='#'>Departamento</a></li>
        <li  class='active'><a href='#'>Carrera</a></li>
    <?php    
    }else{ //Muestra el filtro por defecto?>
        <li class='active'><a href='#'>TODO</a></li>
        <li><a href='#'>Departamento</a></li>
        <li><a href='#'>Carrera</a></li>
    <?php    
    }
    echo "</ul>"; 
}else{ //Muestra el filtro por defecto?>
    <ul class='nav nav-tabs'>   
    <li class='active'><a href='#'>TODO</a></li>
    <li><a href='#'>Departamento</a></li>
    <li><a href='#'>Carrera</a></li>
    </ul>
    
    <br/>
    
    <form class='form-inline' role='form'>
    
     <label for="aula">Aulas:</label>
    <select id='aula' class='aula form-control'>
    <?php 
    $aulas = $facultad->getAulas();
    for ($index = 0; $index < count($aulas); $index++) {    
        echo "<option value='".$aulas[$index]->getNombre()."'>".$aulas[$index]->getNombre()."</option>";    
    }
    ?>
    </select>  
    
    <label for="departamento">Departamentos:</label>
    <select id='departamento' class='departamento form-control'>
    <?php
    $departamentos = $facultad->departamentos;
    for ($index = 0; $index < count($departamentos); $index++) {    
        echo "<option value='".$departamentos[$index]->getNombre()."'>".$departamentos[$index]->getNombre()."</option>";    
    }
    ?>
    </select>
    
    <input type='button' name='mostrarHorario' id='mostrarHorario' class='btn btn-primary' value='Mostrar Horario' tabindex='4'>    

    </form>


    
    <br/>    
    
<?php
}
?>

<?php
//if(isset($_GET['criterio'])){
//    $criterio = $_GET['criterio'];
//    echo "<ul class='nav nav-tabs'>";           
//    if($criterio=='departamento'){
//        echo "<li><a href='#'>TODO</a></li>";
//        echo "<li  class='active'><a href='#'>Departamento</a></li>";
//        echo "<li><a href='#'>Carrera</a></li>";
//    }else if($criterio=='carrera'){
//        echo "<li><a href='#'>TODO</a></li>";
//        echo "<li><a href='#'>Departamento</a></li>";
//        echo "<li  class='active'><a href='#'>Carrera</a></li>";
//    }else{
//        echo "<li class='active'><a href='#'>TODO</a></li>";
//        echo "<li><a href='#'>Departamento</a></li>";
//        echo "<li><a href='#'>Carrera</a></li>";
//    }
//    echo "</ul>"; 
//}else{
//    echo "<ul class='nav nav-tabs'>";   
//    echo "<li class='active'><a href='#'>TODO</a></li>";
//    echo "<li><a href='#'>Departamento</a></li>";
//    echo "<li><a href='#'>Carrera</a></li>";
//    echo "</ul>"; 
//}
?>
