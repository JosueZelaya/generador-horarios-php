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
        var contrataciones = "[{value: 'ADHO', text: 'ADHO'}, {value: 'EVHC',text: 'EVHC'},{value: 'EVMT',text: 'EVMT'},{value: 'EVCT',text: 'EVCT'},{value: 'HC',text: 'HC'},{value: 'CT',text: 'CT'},{value: 'TC',text: 'TC'},{value: 'MC',text: 'MT'}]";
         $(this).autocomplete({   
            delay : 0,
            source : 'buscarDocente.php',
            select : function(event,ui){
                $('#mostrarDocentes').slideUp('fast',function(){
                   $('#mostrarDocentes').html(                    
                        "<tr>"+
                        "<td><div style='cursor: pointer;' id='nombres' class='campoModificable' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='mDocentes.php' data-title='Ingrese Nombres'>"+ui.item.nombres+"</div></td>"+
                        "<td><div style='cursor: pointer;' id='apellidos' class='campoModificable' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='mDocentes.php' data-title='Ingrese Apellidos'>"+ui.item.apellidos+"</div></td>"+
                        "<td><div style='cursor: pointer;' id='contratacion' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk="+ui.item.id+" data-url='mDocentes.php' data-title='Contratacion' data-source=\""+contrataciones+"\">"+ui.item.contratacion+"</div></td>"+
                        "<td><div style='cursor: pointer;' id='departamento' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk="+ui.item.id+" data-url='mDocentes.php' data-title='Departamento' data-source=\""+ui.item.depars+"\">"+ui.item.depar+"</div></td>"+
                        "<td><div style='cursor: pointer;' id='cargo' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk="+ui.item.id+" data-url='mDocentes.php' data-title='Cargo' data-source=\""+ui.item.cargos+"\">"+ui.item.cargo+"</div></td>"+                        
                        "<tr/>"
                    );
                });
                $('#mostrarDocentes').slideDown('fast');
                $('.pagination').html("");
            }                        
        });
    });
    
    //BUSCADOR USUARIOS
    $(document).on("keydown.autocomplete","#buscar_usuario_modificar",function(){        
         $(this).autocomplete({   
            delay : 0,
            source : 'buscarUsuario.php',
            select : function(event,ui){
                $('#mostrarUsuarios').slideUp('fast',function(){
                   $('#mostrarUsuarios').html(                    
                        "<tr>"+
                        "<td><div style='cursor: pointer;' id='login' class='campoModificable' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='mUsuarios.php' data-title='Ingrese Login'>"+ui.item.login+"</div></td>"+
                        "<td><div style='cursor: pointer;' id='docente' class='campoModificable' data-type='select' data-placement='bottom' data-pk="+ui.item.id+" data-url='mUsuarios.php' data-title='Ingrese Docente' data-source=\""+ui.item.docentes+"\">"+ui.item.docente+"</div></td>"+                        
                        "<tr/>"
                    );
                });
                $('#mostrarUsuarios').slideDown('fast');
                $('.pagination').html("");
            }                        
        });
    });
    
});