$(function(){
    
    $(document).on("click","#modificar_docentes",function(){
        $("#modificar_usuarios").removeClass("active");
        $("#modificar_docentes").addClass("active");
        $("#contenido").load("modificarDocente.php");
    });
    
    $(document).on("click","#modificar_usuarios",function(){
        $("#modificar_docentes").removeClass("active");
        $("#modificar_usuarios").addClass("active");
        $("#contenido").load("modificarUsuario.php");
    });
    
    $.fn.editable.defaults.mode = 'popup';
    
    $(document).on("mouseover","table",function(){
        $('.campoModificable').editable({        
            success: function(response, newValue) {
                respuesta = jQuery.parseJSON(response);
                if(respuesta.status === 'error') return respuesta.msg; //msg will be shown in editable form
            }        
        });
        $('.campoSeleccionable').editable({
            success: function(response, newValue) {
                respuesta = jQuery.parseJSON(response);
                if(respuesta.status === 'error') return respuesta.msg; //msg will be shown in editable form
            }
        });
    });
    
    //PAGINADOR DE LA TABLA DOCENTES
    $(document).on("click",".paginaDocentesModificar",function(){    
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
        $.ajax({
            type: "GET",
            url: "contenidoTablaDocentes.php",
            data: dataString,
            success: function(data) {                
                $('#mostrarDocentes').fadeIn(1000).html(data);
            }            
        });
        dataString = dataString+"&css_class=paginaDocentesModificar";
        $.ajax({
            type: "GET",
            url: "paginadorDocentes.php",
            data: dataString,
            success: function(data){
                $('.pagination').fadeIn(1000).html(data);
            }
        });        
    });
    
    //BUSCADOR DOCENTES
    $(document).on("keydown.autocomplete","#buscar_docente_modificar",function(){
         $(this).autocomplete({   
            delay : 0,
            source : 'buscarDocente.php',
            select : function(event,ui){
                $('#mostrarDocentes').slideUp('fast',function(){
                   $('#mostrarDocentes').html(                    
                        "<tr>"+
                        "<td id='nombre"+ui.item.id+"'>"+ui.item.nombres+"</td>"+
                        "<td id='apellido"+ui.item.id+"'>"+ui.item.apellidos+"</td>"+
                        "<td>"+ui.item.contratacion+"</td>"+
                        "<td>"+ui.item.depar+"</td>"+
                        "<td>"+ui.item.cargo+"</td>"+                        
                        "<tr/>"
                    );
                });
                $('#mostrarDocentes').slideDown('fast');
                $('.pagination').html("");
            }                        
        });
    });
    
});