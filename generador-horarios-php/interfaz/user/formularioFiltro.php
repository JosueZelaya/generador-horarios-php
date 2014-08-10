<?php
include_once 'config.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Departamento.php';
include_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
ManejadorSesion::sec_session_start();

$facultad = $_SESSION['facultad'];
$departamentos = ManejadorDepartamentos::quitarDepartamentosEspeciales($facultad->getDepartamentos());

if(isset($_GET['criterio'])){
    $criterio = $_GET['criterio'];?>    
    <ul class='nav nav-tabs'>
    <?php    
    if($criterio=='departamento'){ //Muestra el filtro para los departamentos?>
        <li id='filtroTODO'><a href='#'>TODO</a></li>
        <li id='filtroMateria'><a href='#'>Carrera/Materia</a></li>
        <li id='filtroHora'><a href='#'>Por Hora</a></li>
        </ul> 
        <br/>
        <form class='form-inline' role='form'>
            <label for="departamento">Departamentos:</label>
            <select id='departamento' class='departamento form-control'>
                <option value='todos'>Ninguno</option>    
                <?php      
                    foreach ($departamentos as $departamento) {
                        echo "<option value='".$departamento->getId()."'>".$departamento->getNombre()."</option>";    
                    }
                ?>
            </select>
            <label for="carrera">Carreras:</label>
            <select id='carrera' class='carrera form-control'>
                <option value='todos'>TODAS</option>    
            </select>    
            <label for="aula">Aulas:</label>
            <select id='aula' class='aula form-control'></select>
            <input type='button' name='mostrarHorario' id='mostrarHorarioDepartamento' class='btn btn-primary' value='Filtrar' tabindex='4'>    
        </form>
        <br/>
    <?php    
    }else if($criterio=='materia'){?>        
        <li id='filtroTODO'><a href='#'>TODO</a></li>          
        <li id='filtroMateria' class='active'><a href='#'>Carrera/Materia</a></li>
        <li id='filtroHora'><a href='#'>Por Hora</a></li>
        </ul>
        <br/>
        <form class='form-inline' role='form'>
        <label for="departamento">Departamentos:</label>    
        <select id='departamento' class='departamento form-control'>
            <option value='todos'>TODOS</option>    
            <?php
            foreach ($departamentos as $departamento) {
                echo "<option value='".$departamento->getId()."'>".$departamento->getNombre()."</option>";
            }
            ?>
        </select>
        <label for="carrera">Carreras:</label>
        <select id='carrera' class='carrera form-control'  data-tipo='materia'>
            <option value='todos'>TODAS</option>    
        </select>
        </select>
        <label for="materia">Materia:</label>
        <select id='materia' class='materia form-control'>
            <option value='todos'>TODAS</option>    
        </select>
        <label for="ciclo">Ciclo:</label>
        <select id='ciclo' class='ciclo form-control'>
            <option value='todos'></option>            
            <?php
                $inicio = 2;
                $fin = 14;
                if($ciclo=="1"){
                    $inicio = 1;
                    $fin = 13;
                }        
                for ($i = $inicio; $i <= $fin;$i=$i+2) {
                    echo "<option value='".$i."'>$i</option>";
                }
            ?>
        </select>
        <input type='button' name='mostrarHorarioMateria' id='mostrarHorarioMateria' class='btn btn-primary' value='Filtrar' tabindex='4'>    
        </form>
        <br/>
        <?php
    }else if($criterio=='hora'){?>        
        <li id='filtroTODO'><a href='#'>TODO</a></li>          
        <li id='filtroMateria'><a href='#'>Carrera/Materia</a></li>
        <li id='filtroHora' class='active'><a href='#'>Por Hora</a></li>
        </ul>
        <br/>
        <form class='form-inline' role='form'>
        <label for="departamento">Departamentos:</label>
        <select id='departamento' class='departamento form-control'>
            <option value='todos'>TODOS</option>    
        <?php
        foreach ($departamentos as $departamento) {
            echo "<option value='".$departamento->getId()."'>".$departamento->getNombre()."</option>";    
        }
        ?>
        </select>
<!--        <label for="carrera">Carreras:</label>
        <select id='carrera' class='carrera form-control'>
            <option value='todos'>TODAS</option>    
        </select>        -->
        <input type='button' name='mostrarHorarioHora' id='mostrarHorarioHora' class='btn btn-primary' value='Filtrar' tabindex='4'>    
        </form>
        <br/>
        <?php
    }else{ //Muestra el filtro por defecto?>
        <li  id='filtroTODO' class='active'><a href='#'>TODO</a></li>
        <li id='filtroMateria'><a href='#'>Carrera/Materia</a></li>
        <li id='filtroHora'><a href='#'>Por Hora</a></li>
        </ul> 
        <br/>    
        <form class='form-inline' role='form'>
        <label for="departamento">Departamentos:</label>
        <select id='departamento' class='departamento form-control'>
            <option value='todos'>TODOS</option>    
        <?php
        foreach ($departamentos as $departamento) {
            echo "<option value='".$departamento->getId()."'>".$departamento->getNombre()."</option>";
        }
        ?>
        </select>
        <label for="carrera">Carreras:</label>
        <select id='carrera' class='carrera form-control'>
            <option value='todos'>TODAS</option>    
        </select>
        <label for="aula">Aulas:</label>
        <select id='aula' class='aula form-control'>
        <?php 
        $aulas = $facultad->getAulas();
        for ($index = 0; $index < count($aulas); $index++) {    
            echo "<option value='".$aulas[$index]->getNombre()."'>".$aulas[$index]->getNombre()."</option>";    
        }
        ?>
        </select>
        <input type='button' name='mostrarHorario' id='mostrarHorario' class='btn btn-primary' value='Filtrar' tabindex='4'>    
        </form>
        <br/>
    <?php    
    }     
}else{ //Muestra el filtro por defecto?>
    <ul class='nav nav-tabs'>   
    <li id='filtroTODO' class='active'><a href='#'>TODO</a></li>     
    <li id='filtroMateria'><a href='#'>Carrera/Materia</a></li>
    <li id='filtroHora'><a href='#'>Por Hora</a></li>
    </ul>    
    <br/>    
    <form class='form-inline' role='form'>    
    <label for="departamento">Departamentos:</label>
    <select id='departamento' class='departamento form-control'>
        <option value='todos'>TODOS</option>    
    <?php
    foreach ($departamentos as $departamento) {
        echo "<option value='".$departamento->getId()."'>".$departamento->getNombre()."</option>";
    }
    ?>
    </select>    
    <label for="carrera">Carreras:</label>
    <select id='carrera' class='carrera form-control'>
        <option value='todos'>TODAS</option>    
    </select>
    <label for="aula">Aulas:</label>
    <select id='aula' class='aula form-control'>
    <?php 
    $aulas = $facultad->getAulas();
    for ($index = 0; $index < count($aulas); $index++) {    
        echo "<option value='".$aulas[$index]->getNombre()."'>".$aulas[$index]->getNombre()."</option>";    
    }
    ?>
    </select>
    <input type='button' name='mostrarHorario' id='mostrarHorario' class='btn btn-primary' value='Filtrar' tabindex='4'>    
    </form>
    <br/>     
<?php
}