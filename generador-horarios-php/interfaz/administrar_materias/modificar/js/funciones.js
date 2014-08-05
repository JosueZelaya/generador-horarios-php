$(function(){    
    
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
    $(document).on("click",".paginaMaterias",function(){    
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
        $.ajax({
            type: "GET",
            url: "contenidoTablaMaterias.php",
            data: dataString,
            success: function(data) {                
                $('#mostrarMaterias').fadeIn(1000).html(data);
            }            
        });
        dataString = dataString+"&css_class=paginaMaterias";
        $.ajax({
            type: "GET",
            url: "paginadorMaterias.php",
            data: dataString,
            success: function(data){
                $('.pagination').fadeIn(1000).html(data);
            }
        });        
    });
    
    //BUSCADOR DOCENTES
    $(document).on("keydown.autocomplete","#buscar_materia",function(){        
         $(this).autocomplete({   
            delay : 0,
            source : 'buscarMateria.php',
            select : function(event,ui){
                var dataString = "materia="+ui.item.value;
                $.ajax({
                    type: "GET",
                    url: "contenidoTablaMaterias.php",
                    data: dataString,
                    success: function(data){                        
                        $('#mostrarMaterias').html(data);
                        $('#paginacion').load('paginadorMaterias.php');
                    }
                });
            }                        
        });
    });
    
});