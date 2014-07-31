$(function(){
    
    //PAGINADOR DE LA TABLA DEPARTAMENTOS
    $(document).on("click",".paginaDepartamentos",function(){    
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
        $.ajax({
            type: "GET",
            url: "contenidoTablaDepartamentos.php",
            data: dataString,
            success: function(data) {                
                $('#mostrarEliminar').fadeIn(1000).html(data);
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
    
    //BUSCADOR AULAS
    $(document).on("keydown.autocomplete","#buscar_departamento",function(){
         $(this).autocomplete({   
            delay : 0,
            source : 'buscarDepartamento.php',
            select : function(event,ui){
                $('#mostrarEliminar').html("<tr>"+                                           
                                           "<td class='text-left'>"+ui.item.value+"<t/d>"+                                           
                                           "<td class='text-left'><a nombre='"+ui.item.value+"' id='"+ui.item.id+"' class='row-delete'><span class='glyphicon glyphicon-remove'></span></a></td>"+
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
        var nombre = $(this).attr('nombre');
        var dataString = 'codigo='+id;        
        var mensaje = "<font color='red'>¿Realmente desea borrar el departamento: "+nombre+"?</font>\n\
            <br/>El departamento será desactivado para matener sus datos históricos. <br/> Los datos borrados pueden recuperarse al volver a crear el departamento";        
        bootbox.confirm(mensaje, function(resultado) {
            if(resultado===true){
                $.ajax({
                    type: "GET",
                    url: "eDepartamento.php",
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