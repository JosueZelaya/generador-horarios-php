<form class="form-horizontal" id="autenticarse">
    <div class="modal-header">
        <h1>Iniciar Sesión</h1>
        </div>
    <div class="modal-body">                    
        <div class="form-group">
            <label for="usuario" class="col-lg-2 control-label">Usuario</label>
            <div class="col-lg-10">
                <input type="text" id="usuario" name="usuario" class="form-control formulario" placeholder="Usuario" required/>
            </div>
        </div>
         <div class="form-group">
            <label for="password" class="col-lg-2 control-label">Clave</label>
            <div class="col-lg-10">
                <input type="password" id="password" name="password" class="password form-control formulario" placeholder="Clave" required/>
            </div>
        </div>
    </div>
    <div class="modal-footer"> 
        <div id="resultadoLogin" class="resultadoLogin"></div>
        <!--<button class="btn btn-primary" type="submit" name="submit">Entrar</button>-->
        <input type="submit" name="submit" class="btn btn-primary" value="Entrar" tabindex="4">
        <a class="btn btn-default" data-dismiss="modal">Cancelar</a>                    
        <!--<a class="btn btn-primary">Entrar</a>-->
    </div>
</form>