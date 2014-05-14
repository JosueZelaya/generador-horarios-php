<div class="navbar navbar-inverse navbar-static-top">
    <div class="container">       
        
            <a href="index.php" class="navbar-brand">Generador Horarios</a>        
               
        
        <button id="botonBarraPrincipal" class="navbar-toggle navbar-brand" data-toggle="collapse" data-target=".enlacesBarra">
            <span id="spanBP" class="glyphicon glyphicon-plus"></span>
<!--            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>                        -->
        </button>
        
        <div id="barraPrincipal" class="collapse navbar-collapse enlacesBarra">
            
            <ul class="nav navbar-nav navbar-right">                
                <li><a class="active" href="#" >Inicio</a></li>
                <li><a href="#" >Acerca de</a></li>
                <li><a href="#" >Contactanos</a></li>               
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Bienvenido <?php //echo htmlentities($_SESSION['usuario_nombres']);?>! <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                      <li><a id="miInfo" class="miInfo" href="#">Mi información</a></li>
                      <li><a href="#">Privacidad</a></li>
                      <li><a href="#">Configuración</a></li>
                      <li class="divider"></li>
                      <li class="dropdown-header">¡Hasta Pronto!</li>
                      <li><a href="logout.php">Salir</a></li>                              
                    </ul>
                </li>            
            </ul>
            
        </div>
        
    </div>
    
</div>

