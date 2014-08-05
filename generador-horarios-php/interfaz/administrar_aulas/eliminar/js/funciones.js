$(function(){
    
    //PAGINADOR DE LA TABLA AULAS
    $(document).on("click",".paginaAulas",function(){    
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
        $.ajax({
            type: "GET",
            url: "contenidoTablaAulas.php",
            data: dataString,
            success: function(data) {                
                $('#mostrarEliminar').fadeIn(1000).html(data);
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
    
    //BUSCADOR AULAS
    $(document).on("keydown.autocomplete","#buscar_aula",function(){
         $(this).autocomplete({   
            delay : 0,
            source : 'buscarAula.php',
            select : function(event,ui){
                $('#mostrarEliminar').html("<tr>"+
                                           "<td></td>"+
                                           "<td class='text-left'>"+ui.item.value+"<t/d>"+
                                           "<td class='text-left'>"+ui.item.capacidad+"</td>"+
                                           "<td class='text-left'>"+ui.item.exclusiva+"</td>"+
                                           "<td class='text-left'><a id='"+ui.item.value+"' class='row-delete'><span class='glyphicon glyphicon-remove'></span></a></td>"+
                                           "</tr>");
                $('#paginacion').html("");
            }                        
        });
    });
    
    //ELIMINAR AULAS
    $('table').footable({
        breakpoints: {
            tiny: 180,                
            phone: 256,
            medium: 512,
            tablet: 768,
            laptop: 1024
        }
    }).on('click','.row-delete',function(e){         
         e.preventDefault();
        //get the footable object
        var footable = $('table').data('footable');
        //get the row we are wanting to delete
        var row = $(this).parents('tr:first');
        var id = $(this).attr('id');
        var dataString = 'codigo='+id;        
        var mensaje = "";
        mensaje = "<font color='red'>¿Realmente desea borrar el aula: "+id+"?</font>\n\
            <br/>Se borrarán con ella los datos históricos del aula. <br/> Los datos borrados ya no podran recuperarse";        
        bootbox.confirm(mensaje, function(resultado) {
            if(resultado===true){
                $.ajax({
                    type: "GET",
                    url: "eAula.php",
                    data: dataString,
                    success: function(respuesta) {  
                        respuesta = jQuery.parseJSON(respuesta);  
                        if(respuesta==="ok"){
                            //eliminar fila
                            footable.removeRow(row);
                        }else{
                            bootbox.alert("<font color='red'>"+respuesta+"</font>");
                        }
                    }            
                });             
            }            
         });                   
    });
    
});