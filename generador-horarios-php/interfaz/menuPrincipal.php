<div class="navbar navbar-inverse navbar-static-top">
    <div class="container">
        <?php if (ManejadorSesion::comprobar_sesion() == true) : ?>
            <a href="areaUsuarios.php" class="navbar-brand">Generador Horarios</a>
            
            <button id="botonBarraVertical" class="navbar-toggle navbar-brand" data-toggle="collapse" data-target=".barraVertical">                
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>                
                <span class="icon-bar"></span>                
            </button>
        <?php else : ?>
            <a href="index.php" class="navbar-brand">Generador Horarios</a>
        <?php endif; ?>
        
               
        
        <button id="botonBarraPrincipal" class="navbar-toggle navbar-brand" data-toggle="collapse" data-target=".enlacesBarra">
            <span id="spanBP" class="glyphicon glyphicon-plus"></span>
<!--            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>                        -->
        </button>
        
        <div id="barraPrincipal" class="collapse navbar-collapse enlacesBarra">
            
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="#" >Inicio</a></li>
                <li><a href="#" >Acerca de</a></li>
                <li><a href="#" >Contactanos</a></li>
                <?php if (ManejadorSesion::comprobar_sesion() == true) : ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Bienvenido <?php echo htmlentities($_SESSION['usuario_nombres']);?>! <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                          <li><a id="miInfo" class="miInfo" href="#">Mi información</a></li>
                          <li><a href="#">Privacidad</a></li>
                          <li><a href="#">Configuración</a></li>
                          <li class="divider"></li>
                          <li class="dropdown-header">¡Hasta Pronto!</li>
                          <li><a href="autenticacion/logout.php">Salir</a></li>                              
                        </ul>
                    </li>
                <?php else : ?>
                    <li>
                    <a href="#Autenticar" data-toggle="modal"> Iniciar Sesión </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<div class="modal fade" id="Autenticar" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php include 'autenticacion/formularioLogin.php';?>
        </div>
    </div>
</div>
