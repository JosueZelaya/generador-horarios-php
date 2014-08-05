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
    
    //PAGINADOR DE LA TABLA AULAS
    $(document).on("click",".paginaAulas",function(){    
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
        $.ajax({
            type: "GET",
            url: "contenidoTablaAulas.php",
            data: dataString,
            success: function(data) {                
                $('#mostrarAulas').fadeIn(1000).html(data);
            }            
        });
        dataString = dataString+"&css_class=paginaAulas";
        $.ajax({
            type: "GET",
            url: "paginadorAulas.php",
            data: dataString,
            success: function(data){
                $('.pagination').fadeIn(1000).html(data);
            }
        });        
    });
    
    //BUSCADOR DOCENTES
    $(document).on("keydown.autocomplete","#buscar_aula",function(){        
         $(this).autocomplete({   
            delay : 0,
            source : 'buscarAula.php',
            select : function(event,ui){
                var exclusiva_string = "[{value: 't', text: 'Sí'},{value: 'f', text: 'No'}]";
                $('#mostrarAulas').html("<tr>"+
                                           "<td></td>"+
                                           "<td class='text-left'>"+ui.item.value+"<t/d>"+
                                           "<td class='text-left'><div style='cursor: pointer;' id='capacidad' class='campoModificable' data-type='text' data-placement='bottom' data-pk='"+ui.item.value+"' data-url='mAulas.php' data-title='Ingrese Capacidad'>"+ui.item.capacidad+"</div></td>"+
                                           "<td class='text-left'><div style='cursor: pointer;' id='exclusiva' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk='"+ui.item.value+"' data-url='mAulas.php' data-title='Exclusiva' data-source=\""+exclusiva_string+"\">"+ui.item.exclusiva+"</div></td>"+                                           
                                           "</tr>");
                $('#paginacion').html("");
            }                        
        });
    });
    
});