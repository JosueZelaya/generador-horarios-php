$(function (){
    
   $(document).on("click","#generarHorario",function(){
       //$('#contenido').html("Generando Horario... Por Favor espere... <br/>\n\
       //                     <img src='imagenes/cargando.gif'/>");
       $('#contenido').load("cargando.php");
       $('#contenido').load("generarHorario.php");
   });
         
});
