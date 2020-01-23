    function impuestoDelete($num){
    var marc = $('#del_impuesto'+$num).val();
    var data = marc.split('*');
    document.getElementById("id_update_marca").value= parseInt(data[0]);
    }  

    function impuestoUpdate($num){
    var impuesto = $("#up_impuesto"+$num).val();
    var data = impuesto.split('*');
    document.getElementById("id_impuesto_update").value= parseInt(data[0]);
    document.getElementById("nombre_impuesto_update").value= data[1];
    document.getElementById("porcentaje_impuesto_update").value= data[2];

    }
    function limpiar(){
        document.getElementById("form1").reset();
    }
    //********************** creamos funcion para validar el porcentaje **********************//
/*    $('#porcentaje').on('change',function(){
        if ( parseFloat($(this).val())) {
            $(this).val(parseFloat($(this).val()).toFixed(2));
        }
    });*/
    jQuery.validator.addMethod("validarPorcentaje",
        function(value, element) {
            if (parseInt(value)){         
                return true;
            } else {
            
                return false;
            }       
        },
        "Ingrese solamente números ejemplo 15."
    );

$("#form1").validate({
    rules:{
        name: "required", 
        porcentaje: {
            validarPorcentaje:"#porcentaje",
        },
    },
    messages:{
        name: "Ingrese nombre del impuesto.",
    },
    submitHandler: function(form){
        form.submit();
    }
});

