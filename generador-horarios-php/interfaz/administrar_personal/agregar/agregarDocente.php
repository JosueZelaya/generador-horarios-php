<?php 
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDepartamentos.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorCargos.php';
chdir(dirname(__FILE__));

if(session_status()!=PHP_SESSION_ACTIVE){
    ManejadorSesion::sec_session_start();
}

$id_departamento = $_SESSION['id_departamento'];
$nombre_departamento = $_SESSION['nombre_departamento'];

if (ManejadorSesion::comprobar_sesion() == true) : ?>
<div class="panel panel-default col-sm-12">
    <form class="form-horizontal center center-block" id="formularioAgregarDocente">
        <div class="modal-header">            
            </div>
        <div class="modal-body">                    
            <div class="form-group">
                <label for="nombres" class="col-lg-2 control-label">Nombres</label>
                <div class="col-lg-4">
                    <input type="text" id="nombres" name="nombres" class="form-control formulario" placeholder="Juan José" required/>
                </div>
            </div>
            <div class="form-group">
                <label for="apellidos" class="col-lg-2 control-label">Apellidos</label>
                <div class="col-lg-4">
                    <input type="text" id="apellidos" name="apellidos" class="form-control formulario" placeholder="Perez García" required/>
                </div>
            </div>            
            <div class="form-group">
                <label for="contratacion" class="col-lg-2 control-label">Contratacion</label>
                <div class="col-lg-4">
                    <select id="contratacion" name="contratacion" class="form-control">
                        <option>ADHO</option>
                        <option>CT</option>
                        <option>EVHC</option>
                        <option>EVMT</option>
                        <option>EVTC</option>                        
                        <option>HC</option>
                        <option>MT</option>
                        <option>TC</option>                      
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="departamento" class="col-lg-2 control-label">Departamento</label>
                <div class="col-lg-4">                    
                    <select id='departamento' name='departamento' class='form-control'>
                    <?php                                             
                        if($id_departamento!="todos"){
                            echo "<option value='".$id_departamento."'>".$nombre_departamento."</option>";
                        }else{
                            $departamentos = ManejadorDepartamentos::quitarDepartamentosEspeciales(ManejadorDepartamentos::getDepartamentos());
                            foreach ($departamentos as $departamento) {
                                echo "<option value='".$departamento->getId()."'>".$departamento->getNombre()."</option>";
                            }
                        }                        
                    ?>        
                    </select>    
                </div>
            </div>
            <div class="form-group">
                <label for="cargo" class="col-lg-2 control-label">Cargo</label>
                <div class="col-lg-4">
                    <select id="cargo" name="cargo" class="form-control">
                        <option value=''>ninguno</option>    
                    <?php
                        $cargos = ManejadorCargos::obtenerTodosCargos();
                        foreach ($cargos as $cargo) {
                            echo "<option value='".$cargo->getId_cargo()."'>".$cargo->getNombre()."</option>";
                        }
                    ?>    
                    </select>
                </div>
            </div>
            
        </div>
        <div class="modal-footer"> 
            <div id="resultado" class="resultado"></div>            
            <input type="button" name="add" id="add" class="btn btn-primary" value="Agregar" tabindex="4">
            <input type="reset" name="reset" class="btn btn-default" value="Borrar" tabindex="4">            
        </div>
    </form>
</div>
    


<?php else : ?>
    <p>
        <span class="">Usted no está autorizado para acceder a esta página.</span> Por favor <a href="../../../interfaz/index.php">Autentíquese</a>.
    </p>
<?php endif; ?>

<script type="text/javascript" src="js/picker.js"></script>
<script type="text/javascript" src="js/picker.date.js"></script>
<script type="text/javascript" src="js/es_ES.js"></script>
<script type="text/javascript" src="js/ajaxpost.js"></script>