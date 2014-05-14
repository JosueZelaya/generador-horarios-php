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
       var dataString;
       var aula = $("#aula").val();
       var departamento = $('#departamento').val();       
       var carrera = $('#carrera').val();
       dataString = 'aula='+aula+"&departamento="+departamento+"&carrera="+carrera;      
       
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
            },
            error: function(data){
                alert("error: "+data);
            }
        }); 
   });
         
    $(document).on("click","#departamento",function(){
        var dataString = 'departamento='+$(this).val();      
        $.ajax({            
            type: "GET",
            url: "carreras.php",
            data: dataString,
            success: function(data){                
                $('#carrera').html(data);
            }
        }); 
    });
         
});
