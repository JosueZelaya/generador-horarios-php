$(function(){
    
    //PAGINADOR DE LA TABLA MATERIAS
    $(document).on("click",".paginaMateriasEliminar",function(){    
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
        $.ajax({
            type: "GET",
            url: "contenidoTablaMaterias.php",
            data: dataString,
            success: function(data) {                
                $('#mostrarEliminar').fadeIn(1000).html(data);
            }            
        });
        dataString = dataString+"&css_class=paginaMateriasEliminar";
        $.ajax({
            type: "GET",
            url: "paginadorMaterias.php",
            data: dataString,
            success: function(data){
                $('.pagination').fadeIn(1000).html(data);
            }
        });
    });
    
    //BUSCADOR MATERIAS
    $(document).on("keydown.autocomplete","#buscar_materia",function(){
         $(this).autocomplete({   
            delay : 0,
            source : 'buscarMateria.php',
            select : function(event,ui){
                var dataString = "materia="+ui.item.value;
                $.ajax({
                    type: "GET",
                    url: "contenidoTablaMaterias.php",
                    data: dataString,
                    success: function(data){
                        $('#cabecera_materias').html(""+                        
                            "<tr>"+                        
                                "<th class='text-center'>Codigo</th>"+
                                "<th class='text-center'>Materia</th>"+
                                "<th class='text-center'>Carrera</th>"+
                                "<th class='text-center'>Plan Estudio</th>"+
                                "<th class='text-center'>Departamento</th>"+
                            "</tr>"                        
                        );
                        $('#mostrarEliminar').html(data);
                        $('#paginacion').load('paginadorMaterias.php');
                    }
                });
            }                        
        });
    });
    
    //ELIMINAR MATERIAS
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
        var dataString = 'codigo='+$('#materia'+id).attr('codigo')+"&plan="+$('#materia'+id).attr('plan')+"&carrera="+$('#materia'+id).attr('carrera');        
        var mensaje = "";
        mensaje = "<font color='red'>¿Realmente desea borrar la materia: "+$('#materia'+id).attr('nombre')+" plan: "+$('#materia'+id).attr('plan')+" carrera: "+$('#materia'+id).attr('nombre_carrera')+"?</font>\n\
            <br/>Se borrarán con ella los datos históricos de la materia. <br/> Los datos borrados ya no podran recuperarse";        
        bootbox.confirm(mensaje, function(resultado) {
            if(resultado===true){
                $.ajax({
                    type: "GET",
                    url: "eMateria.php",
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