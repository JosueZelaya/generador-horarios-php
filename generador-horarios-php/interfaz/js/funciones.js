$(function (){
      
   $(document).on("click","#generarHorario",function(){
       $('#filtro').html("");
       $('#contenido').load("cargando.php");
       //$('#contenido').load("generarHorario.php");       
       $.ajax({            
            type: "GET",
            url: "generarHorario.php",            
            success: function(data){                
                $('#contenido').html(data);
                $('#filtro').load("formularioFiltro.php");
            }
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
                    html : true
                });
            }
        }); 
   });
         
});
