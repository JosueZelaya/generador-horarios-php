<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
if(session_status()!=PHP_SESSION_ACTIVE){
    ManejadorSesion::sec_session_start();
}

if (ManejadorSesion::comprobar_sesion() == true) : ?>

<form class="form-horizontal" id="cambiar_password_form">
    <div class="modal-header">
        <h1>Cambiar Password</h1>
        </div>
    <div class="modal-body">        
         <div class="form-group">
            <label for="passwordActual" class="col-lg-2 control-label">Password</label>
            <div class="col-lg-10">
                <input type="password" id="password" name="passwordActual" class="password form-control formulario" placeholder="Escriba su password actual" required/>
            </div>
        </div>
        <div class="form-group">
            <label for="passwordNuevo" class="col-lg-2 control-label">Password Nuevo</label>
            <div class="col-lg-10">
                <input type="password" id="password" name="passwordNuevo" class="password form-control formulario" placeholder="Escriba el password nuevo" required/>
            </div>
        </div>
        <div class="form-group">
            <label for="passwordNuevo2" class="col-lg-2 control-label">Password Nuevo</label>
            <div class="col-lg-10">
                <input type="password" id="password" name="passwordNuevo2" class="password form-control formulario" placeholder="Escriba una vez más el password nuevo" required/>
            </div>
        </div>
    </div>
    <div class="modal-footer"> 
        <div id="resultadoLogin" class="resultadoLogin"></div>
        <!--<button class="btn btn-primary" type="submit" name="submit">Entrar</button>-->
        <input type="submit" name="submit" class="btn btn-primary" value="Actualizar" tabindex="4">
        <!--<a class="btn btn-default" data-dismiss="modal">Salir</a>-->                    
        <!--<a class="btn btn-primary">Entrar</a>-->
    </div>
</form>

<?php else : ?>
    <p>
        <span class="">Usted no está autorizado para acceder a esta página.</span> Por favor <a href="../capa_interfaz/index.php">Autentíquese</a>.
    </p>
<?php endif; ?>