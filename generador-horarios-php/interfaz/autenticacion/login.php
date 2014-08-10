<?php
    chdir(dirname(__FILE__));
    require_once '../../reglas_negocio/ManejadorSesion.php';
    ManejadorSesion::sec_session_start();
        if(!$_POST['usuario'] || !$_POST['password']) {
            die("<font color='red'>Debe ingresar un usuario y una clave.</font>");
            //echo "<font color='red'>Debe ingresar un usuario y una clave.</font>";
        }
        $login = $_POST['usuario'];
        $password = $_POST['password'];

        try{
                $usuario = ManejadorSesion::iniciarSesion($login, $password);

                //enviamos al usuario a su pagina correspondiente.
                //header("Location: usuarioAutenticado.php");                            
                if($login=="admin"){
                    echo "Bienvenido Admin";
                }else{
                    echo "Bienvenido Usuario";
                }                
        }catch(Exception $e){
                //$respuesta = "error";
                echo "<font color='red'>".$e->getMessage()."</font>";
                //echo json_encode($respuesta);
                  //echo json_encode(array('resultado'=>'error'));

        }