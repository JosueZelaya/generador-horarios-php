<?php 
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorCarreras.php';
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
                <label for="carrera" class="col-lg-2 control-label">Carrera</label>
                <div class="col-lg-4">
                    <select id="carrera" name="carrera" class="form-control">
                    <?php
                        $departamentos = ManejadorDepartamentos::quitarDepartamentosEspeciales(ManejadorDepartamentos::getDepartamentos());
                        $carreras = array();
                        if($id_departamento!="todos"){                            
                            $carreras = ManejadorCarreras::getCarrerasDeDepartamento($id_departamento);
                        }else{
                            $carreras = ManejadorCarreras::getTodasCarreras($departamentos);
                        }                        
                        $cont=1;
                        foreach ($carreras as $carrera) {
                            echo "<option id='".$cont."' plan='".$carrera->getPlanEstudio()."' codigo='".$carrera->getCodigo()."' value='".$carrera->getCodigo()."'>".$carrera->getNombre().", plan: ".$carrera->getPlanEstudio()."</option>";
                            $cont++;
                        }
                    ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="nombre" class="col-lg-2 control-label">Nombre</label>
                <div class="col-lg-4">
                    <input type="text" id="nombre" name="nombre" class="form-control formulario" placeholder="Matematica X" required/>
                </div>
            </div>
            <div class="form-group">
                <label for="codigo" class="col-lg-2 control-label">Codigo</label>
                <div class="col-lg-4">
                    <input type="text" id="codigo" name="codigo" class="form-control formulario" placeholder="MATXYZ" required/>
                </div>
            </div>            
            <div class="form-group">
                <label for="tipo" class="col-lg-2 control-label">Tipo</label>
                <div class="col-lg-4">
                    <select id="tipo" name="tipo" class="form-control">
                        <option value='Obligatoria'>Obligatoria</option>
                        <option value='Optativa'>Optativa</option>                                         
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="ciclo" class="col-lg-2 control-label">Ciclo</label>
                <div class="col-lg-4">
                    <select id="ciclo" name="ciclo" class="form-control">
                        <?php 
                            for ($index = 1; $index <= 14; $index++) {
                                echo "<option value='".$index."'>".$index."</option>";
                            }
                        ?>                                              
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="uv" class="col-lg-2 control-label">Unidades Valorativas</label>
                <div class="col-lg-4">
                    <input type="text" id="uv" name="uv" class="form-control formulario" placeholder="Ingrese un numero" required/>
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