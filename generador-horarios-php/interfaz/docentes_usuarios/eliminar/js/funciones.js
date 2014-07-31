$(function(){
    //PESTAÑAS USUARIOS Y DOCENTES
    $(document).on("click","#eliminar_docentes",function(){        
        $("#eliminar_usuarios").removeClass("active");
        $("#eliminar_docentes").addClass("active");
        $("#cuadro-busqueda").load("cuadroBusquedaDocentes.php");
        $("#cabeceraEliminar").load("cabeceraTablaDocentes.php");
        $("#mostrarEliminar").load("contenidoTablaDocentes.php");
        $(".pagination").load("paginadorDocentes.php");
    });
    
    $(document).on("click","#eliminar_usuarios",function(){
        $("#eliminar_docentes").removeClass("active");
        $("#eliminar_usuarios").addClass("active");
        $("#cuadro-busqueda").load("cuadroBusquedaUsuarios.php");
        $("#cabeceraEliminar").load("cabeceraTablaUsuarios.php");
        $("#mostrarEliminar").load("contenidoTablaUsuarios.php");
        $(".pagination").load("paginadorUsuarios.php");
    });
    
    //PAGINADOR DE LA TABLA DOCENTES
    $(document).on("click",".paginaDocentesEliminar",function(){    
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
        $.ajax({
            type: "GET",
            url: "contenidoTablaDocentes.php",
            data: dataString,
            success: function(data) {                
                $('#mostrarEliminar').fadeIn(1000).html(data);
            }            
        });
        dataString = dataString+"&css_class=paginaDocentesEliminar";
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
    $(document).on("keydown.autocomplete","#buscar_docente_eliminar",function(){
         $(this).autocomplete({   
            delay : 0,
            source : 'buscarDocente.php',
            select : function(event,ui){
                $('#mostrarEliminar').slideUp('fast',function(){
                   $('#mostrarEliminar').html(                    
                        "<tr>"+
                        "<td id='nombre"+ui.item.id+"'>"+ui.item.nombres+"</td>"+
                        "<td id='apellido"+ui.item.id+"'>"+ui.item.apellidos+"</td>"+
                        "<td>"+ui.item.contratacion+"</td>"+
                        "<td>"+ui.item.depar+"</td>"+
                        "<td>"+ui.item.cargo+"</td>"+
                        "<td class='text-center'><a usuario='f' id='"+ui.item.id+"' class='row-delete'><span class='glyphicon glyphicon-remove'></span></a></td>"+
                        "<tr/>"
                    );
                });
                $('#mostrarEliminar').slideDown('fast');
                $('.pagination').html("");
            }                        
        });
    });
    
    //ELIMINAR DOCENTES Y USUARIOS
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
        var dataString = 'id='+id;
        var esUsuario = $(this).attr('usuario');
        var mensaje = "";
        if(esUsuario==='t'){
            mensaje = "<font color='red'>¿Realmente desea borrar los datos de: "+$('#login'+id).html()+"?</font>\n\
            <br/>Los datos borrados ya no podran recuperarse";          
        }else{
            mensaje = "<font color='red'>¿Realmente desea borrar los datos de: "+$('#nombre'+id).html()+" "+$('#apellido'+id).html()+"?</font>\n\
            <br/>Los datos borrados ya no podran recuperarse";          
        }
        
        bootbox.confirm(mensaje, function(resultado) {
            if(resultado===true){
                if(esUsuario==='t'){
                   $.ajax({
                        type: "GET",
                        url: "eUsuario.php",
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
                }else{
                    $.ajax({
                        type: "GET",
                        url: "eDocente.php",
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
            }            
         });                   
    });
    
    //PAGINADOR DE LA TABLA USUARIOS
    $(document).on("click",".paginaUsuariosEliminar",function(){    
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
        $.ajax({
            type: "GET",
            url: "contenidoTablaUsuarios.php",
            data: dataString,
            success: function(data) {                
                $('#mostrarEliminar').fadeIn(1000).html(data);
            }            
        });
        dataString = dataString+"&css_class=paginaUsuariosEliminar";
        $.ajax({
            type: "GET",
            url: "paginadorUsuarios.php",
            data: dataString,
            success: function(data){
                $('.pagination').fadeIn(1000).html(data);
            }
        });
    });
    
    //BUSCADOR USUARIOS
    $(document).on("keydown.autocomplete","#buscar_usuario_eliminar",function(){
         $(this).autocomplete({   
            delay : 0,
            source : 'buscarUsuario.php',
            select : function(event,ui){
                $('#mostrarEliminar').slideUp('fast',function(){
                   $('#mostrarEliminar').html(                    
                        "<tr>"+
                        "<td id='login"+ui.item.id+"'>"+ui.item.value+"</td>"+
                        "<td>"+ui.item.docente+"</td>"+                        
                        "<td class='text-center'><a usuario='t' id='"+ui.item.id+"' class='row-delete'><span class='glyphicon glyphicon-remove'></span></a></td>"+
                        "<tr/>"
                    );
                });
                $('#mostrarEliminar').slideDown('fast');
                $('.pagination').html("");
            }                        
        });
    });
    
});