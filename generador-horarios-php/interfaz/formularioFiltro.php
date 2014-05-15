<?php

include_once '../reglas_negocio/Facultad.php';

session_start();
$facultad = $_SESSION['facultad'];

if(isset($_GET['criterio'])){
    $criterio = $_GET['criterio'];?>    
    <ul class='nav nav-tabs'>
    <?php    
    if($criterio=='departamento'){ //Muestra el filtro para los departamentos?>
        <li id='filtroTODO'><a href='#'>TODO</a></li>
        <li  id='filtroDepartamento' class='active'><a href='#'>Departamento</a></li>        
        </ul> 
        <br/>
        <form class='form-inline' role='form'>
            <label for="departamento">Departamentos:</label>
            <select id='departamento' class='departamento form-control'>
                <option value='todos'>Ninguno</option>    
            <?php
            $departamentos = $facultad->departamentos;
            for ($index = 0; $index < count($departamentos); $index++) {    
                echo "<option value='".$departamentos[$index]->getId()."'>".$departamentos[$index]->getNombre()."</option>";    
            }
            ?>
            </select>
            <label for="carrera">Carreras:</label>
            <select id='carrera' class='carrera form-control'>
                <option value='todos'>TODAS</option>    
            </select>    
            <label for="aulaDepartamento">Aulas:</label>
            <select id='aulaDepartamento' class='aula form-control'>
            
            </select>
            <input type='button' name='mostrarHorario' id='mostrarHorarioDepartamento' class='btn btn-primary' value='Filtrar' tabindex='4'>    
        </form>
        <br/>
    <?php    
    }else{ //Muestra el filtro por defecto?>
        <li  id='filtroTODO' class='active'><a href='#'>TODO</a></li>
        <li id='filtroDepartamento'><a href='#'>Departamento</a></li>        
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
            <option value='todos'>TODOS</option>    
        <?php
        $departamentos = $facultad->departamentos;
        for ($index = 0; $index < count($departamentos); $index++) {    
            echo "<option value='".$departamentos[$index]->getId()."'>".$departamentos[$index]->getNombre()."</option>";    
        }
        ?>
        </select>
        <label for="carrera">Carreras:</label>
        <select id='carrera' class='carrera form-control'>
            <option value='todos'>TODAS</option>    
        </select>
        <input type='button' name='mostrarHorario' id='mostrarHorario' class='btn btn-primary' value='Filtrar' tabindex='4'>    
        </form>
        <br/>
    <?php    
    }     
}else{ //Muestra el filtro por defecto?>
    <ul class='nav nav-tabs'>   
    <li id='filtroTODO' class='active'><a href='#'>TODO</a></li>
    <li id='filtroDepartamento'><a href='#'>Departamento</a></li>    
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
        <option value='todos'>TODOS</option>    
    <?php
    $departamentos = $facultad->departamentos;
    for ($index = 0; $index < count($departamentos); $index++) {    
        echo "<option value='".$departamentos[$index]->getId()."'>".$departamentos[$index]->getNombre()."</option>";    
    }
    ?>
    </select>    
    <label for="carrera">Carreras:</label>
    <select id='carrera' class='carrera form-control'>
        <option value='todos'>TODAS</option>    
    </select>    
    <input type='button' name='mostrarHorario' id='mostrarHorario' class='btn btn-primary' value='Filtrar' tabindex='4'>    
    </form>
    <br/>    
    
<?php
}