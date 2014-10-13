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
        url: "../administrar_personal/modificar/mPassword.php",        
        data: serializedData,
        success: function(data){
            data = jQuery.parseJSON(data);
            if(data==='ok'){
                $("#mensaje_modal_password").html("<font color='green'>Password Actualizado, deberá <a href='../../interfaz/index.php'>iniciar sesión</a> nuevamente antes de poder continuar</font>");
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
