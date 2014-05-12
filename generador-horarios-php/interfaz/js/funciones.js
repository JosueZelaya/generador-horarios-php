$(function (){
    
   $(document).on("click","#generarHorario",function(){
       $('#contenido').html('cargando.php');
       $('#contenido').load("generarHorario.php");
   });
         
});
