$(function(){
    
    var docente;

    $(document).on("click",".formulario",function(){        
        $("#resultado").html("");
    });

    $(document).on("change",".form-control",function(){        
        $("#resultado").html("");
    });

    $(document).on("click","#add",function(){
        var $form = $("#formularioAgregarDocente");        
        var $inputs = $form.find("input, select, button, textarea");        
        var serializedData = $form.serialize();        
        $inputs.prop("disabled", true);        
        $.ajax({
            type: "post",
            url: "./aDocente.php",        
            data: serializedData,
            success: function(datos){
                datos = jQuery.parseJSON(datos);            
                if(datos==="¡Docente Agregado!"){
                    $("#resultado").html("<font color='green'>"+datos+"</font>");
                }else{
                    $("#resultado").html("<font color='red'>"+datos+"</font>");
                }            
            },
            error:function(err){                                  
                $("#resultado").html("Los datos no se pudieron enviar!");
            }
        });       
        $inputs.prop("disabled", false);        
        event.preventDefault();
    });
    
    $(document).on("click","#add_user",function(){
        $.ajax({
            type: "POST",
            url: "./aUsuario.php",
            data: { login: $("#login").val(), password: $("#password").val(), docente: docente },
            success: function(datos){
                datos = jQuery.parseJSON(datos);            
                if(datos==="¡Usuario Agregado!"){
                    $("#resultado").html("<font color='green'>"+datos+"</font>");
                }else{
                    $("#resultado").html("<font color='red'>"+datos+"</font>");
                }            
            },
            error:function(err){                                  
                $("#resultado").html("Los datos no se pudieron enviar!");
            }
        });
    });
    
    $(document).on("keydown.autocomplete","#buscar_docente",function(){        
        $(this).autocomplete({
            delay: 0,
            source : 'buscarDocente.php',
            select: function(event,ui){
                docente = ui.item.id;
            }
        });
    });
    
    $(document).on("click","#agregar_docentes",function(){
        $("#agregar_usuarios").removeClass("active");
        $("#agregar_docentes").addClass("active");
        $("#contenido").load("agregarDocente.php");
    });
    
    $(document).on("click","#agregar_usuarios",function(){
        $("#agregar_docentes").removeClass("active");
        $("#agregar_usuarios").addClass("active");
        $("#contenido").load("agregarUsuario.php");
    });
    
    
});



