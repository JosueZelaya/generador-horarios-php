/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// bind to the submit event of our form
$("#autenticarse").submit(function(event){
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
        url: "autenticacion/login.php",        
        data: serializedData,
        success: function(data){
            if(data==='Bienvenido Usuario' || data==='Bienvenido Admin'){
                window.location.replace('user/index.php');   
            }else{
                $("#resultadoLogin").html(data);
            }            
        },
        error:function(){
            $("#resultadoLogin").html('Los datos no se pudieron enviar');
        }
    });

    
   // reenable the inputs
   $inputs.prop("disabled", false);
    

    // prevent default posting of form
    event.preventDefault();

});

$("#password").keydown(function (){
    $("#resultadoLogin").html("");
});

$(".formulario").keydown(function (){
    $("#resultado").html("");
});


