$(function (){
    $(document).on("click","#generarHorario",function(){
        $('#filtro').html("");
        $('#contenido').load("cargando.php",function(){
            $.ajax({
                type: "GET",
                url: "generarHorario.php",
                complete: function(data){
                    $('#contenido').html(data);
                    $('#filtro').load("formularioFiltro.php");
                }
            });
        });
    });
   
   $(document).on("click","#mostrarHorario",function(){
       var aula = $("#aula").val();
       var dataString = 'aula='+aula;
       $.ajax({
            type: "GET",
            url: "mostrarHorario.php",
            data: dataString,
            success: function(data){                
                $('#contenido').html(data);
                $('.verInfoGrupo').popover({
                    title : "Informacion del Grupo",
                    animation : true,
                    trigger : 'hover',  //Se muestra el popover al pasar el puntero sobre la celda. valores que acepta: hover,manual,click,focus                    
                    html : true
                });
            }
        }); 
   });
         
});
