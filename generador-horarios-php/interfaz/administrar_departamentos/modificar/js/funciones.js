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
    
    //PAGINADOR DE LA TABLA DEPARTAMENTOS
    $(document).on("click",".paginaDepartamentos",function(){    
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
        $.ajax({
            type: "GET",
            url: "contenidoTablaDepartamentos.php",
            data: dataString,
            success: function(data) {                
                $('#mostrarDepartamentos').fadeIn(1000).html(data);
            }            
        });
        dataString = dataString+"&css_class=paginaDepartamentos";
        $.ajax({
            type: "GET",
            url: "paginadorDepartamentos.php",
            data: dataString,
            success: function(data){
                $('.pagination').fadeIn(1000).html(data);
            }
        });        
    });
    
    //BUSCADOR DEPARTAMENTOS
    $(document).on("keydown.autocomplete","#buscar_departamento",function(){        
         $(this).autocomplete({   
            delay : 0,
            source : 'buscarDepartamento.php',
            select : function(event,ui){
                var activo_string = "[{value: 't', text: 'Sí'},{value: 'f', text: 'No'}]";
                $('#mostrarDepartamentos').html("<tr>"+                                           
                                           "<td class='text-left'><div style='cursor: pointer;' id='nombre' class='campoModificable' data-type='text' data-placement='bottom' data-pk='"+ui.item.id+"' data-url='mAulas.php' data-title='Ingrese Capacidad'>"+ui.item.value+"</div></td>"+
                                           "<td class='text-left'><div style='cursor: pointer;' id='activo' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk='"+ui.item.id+"' data-url='mAulas.php' data-title='Exclusiva' data-source=\""+activo_string+"\">"+ui.item.activo+"</div></td>"+                                           
                                           "</tr>");
                $('#paginacion').html("");
            }                        
        });
    });
    
});