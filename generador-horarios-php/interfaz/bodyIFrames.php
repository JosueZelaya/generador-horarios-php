<html>
    <head>
        <link href="../css/index.css" rel="stylesheet" type="text/css">
        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include_once 'mallaHorario.php'; ?>

        <script type="text/javascript" src="../js/jquery-ui/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../js/funciones.js"></script>        
        <script type="text/javascript">
            $('.verInfoGrupo').popover({
                title : "Informacion del Grupo",
                animation : true,
                trigger : 'hover',  //Se muestra el popover al pasar el puntero sobre la celda. valores que acepta: hover,manual,click,focus                    
                html : true
            });
        </script>
    </body>
    
</html>
