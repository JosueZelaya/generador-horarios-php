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
    
    /* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    // bind to the submit event of our form
    $("#cambiar_password_form").submit(function(event){
        // abort any pending request

        // setup some local variables
        var $form = $(this);
        // let's select and cache all the fields
        var $inputs = $form.find("input, select, button, textarea");
        // serialize the data in the form
        var serializedData = $form.serialize();

        // let's disable the inputs for the duration of the ajax request
        // Note: we disable elements AFTER the form data has been serialized.
        // Disabled form elements will not be serialized.
        $inputs.prop("disabled", true);

        // fire off the request to /form.php
        $.ajax({
            type: "post",
            url: "administrar_personal/mPassword.php",        
            data: serializedData,
            success: function(data){
                if(data==='ok'){
                    $("#mensaje_modal_password").html("<font color='green'>Password Actualizado</font>");
                }else{
                    $("#mensaje_modal_password").html("<font color='red'>"+data+"</font>");
                }            
            },
            error:function(){
                $("#mensaje_modal_password").html('Los datos no se pudieron enviar');
            }
        });


       // reenable the inputs
       $inputs.prop("disabled", false);


        // prevent default posting of form
        event.preventDefault();

    });

    $("#passwordNuevo").keydown(function (){
        $("#mensaje_modal_password").html("");
    });

    $("#passwordNuevo").keydown(function (){
        $("#mensaje_modal_password").html("");
    });

    $(".formulario").keydown(function (){
        $("#mensaje_modal_password").html("");
    });



    
});