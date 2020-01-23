var estado= 0;
var series_correlativo;
var condicionNuevaserie=0;
var cambioCorrelativo = 0;
//Validar formulario *******************************************
$('#fecha').datepicker({}); //calendario mas chingon
function validarFormulario($imprimir){
    total2 = 0;
    validar_cantidad = 0;
    document.getElementById("imprimir").click()

    $("#tbCompras tbody tr").each(function(){
        total2++;
        cantidades =Number($(this).find("td:eq(3)").children('input').val());
        if ( cantidades == 0 ) {
            toastr.warning('ingrese una cantidad en la linea: '+total2);
            validar_cantidad = 1;
        }
    });
    if (total2 != 0 & validar_cantidad == 0) {
        $("#impr").val($imprimir); //mandamos el valor de si la persona hizo la impresión o no
        document.getElementById("FormSalida").submit(); s
    }
    if (total2 == 0) {
        toastr.warning('¡Ingrese almenos un producto!');
        
    }
}
//agregamos un cliente
function agregarCliente(){
    $.ajax({
        url: base_url+"movimientos/salidas/addCliente",
        type: "POST",
        dataType: "json",
        data:$("#agregarCliente").serialize(),
        success: function(data){
            toastr.success('Cliente Agregado.');
            $('#idCliente').val(data);
            $('#autocompleteCliente').val($('#nombre').val() + " " + $('#apellido').val());
            document.getElementById("modalAgregar").click()
        },
        error: function() { 
            toastr.warning('Error al ingresar al cliente');
            $('#idCliente').val('');
            $('#agregarCliente').val('');
        }    
    });
}
//creamos la funcion para agregar una nueva serie.
function nuevaSerie(){
    $.ajax({
        url: base_url+"movimientos/salidas/addSerie",
        type: "POST",
        dataType: "json",
        data:{ nuevaSerie: $('#nueva_serie').val(),comprobante:$('#comprobante').val()},
        success: function(data){
            toastr.success('Serie actualizada.');
            alert(data);
            var serie = document.getElementById('serie');
            cont = serie.length;
            serie.options[cont] = new Option( $('#nueva_serie').val(),data,true,true);
            $('#numero_correlativo').val(1);
            $("#nueva_serie").val('');
            $("#nueva_serie").hide();
            $("#serie").show();
        },
        error: function() { 
            toastr.warning('Error al ingresar la nueva serie');
            $('#numero_correlativo').val('');
            $('#nueva_serie').val('');
        }    
    });
    cambioCorrelativo = 1;
}

//creamos la funcion para agregar una nueva serie.
function nuevaSerie(){
    $.ajax({
        url: base_url+"movimientos/salidas/addSerie",
        type: "POST",
        dataType: "json",
        data:{ nuevaSerie: $('#nueva_serie').val(),comprobante:$('#comprobante').val()},
        success: function(data){
            toastr.success('Serie actualizada.');
            var serie = document.getElementById('serie');
            cont = serie.length;
            serie.options[cont] = new Option( $('#nueva_serie').val(),data,true,true);
            $('#numero_correlativo').val(1);
            $("#nueva_serie").val('');
            $("#nueva_serie").hide();
            $("#serie").show();
        },
        error: function() { 
            toastr.warning('Error al ingresar la nueva serie');
            $("#nueva_serie").val('');
            $("#nueva_serie").focus();
            $('#numero_correlativo').val('');
            $("#serie").hide();
        }    
    });
    cambioCorrelativo = 1;
}
//obtenermos la serie y correlativo del comprobante seleccionado
$("#comprobante").on('change',function(){  
    generar_serie();
    sumarReabastecimiento();

});
//si elegimos agregar una nueva serie hacemos esto
$("#serie").on('change',function(){

    if ($(this).val()=='nueva_serie') {
        $("#nueva_serie").show();
        $("#nueva_serie").focus();
        $('#numero_correlativo').val('');
        $("#serie").hide();
        
    }else{
        generar_correlativo($(this).val());
    }
});
//Escuchamos al imput de agregar una nueva serie.
$('nueva_serie').on('change',function(){
    alert('podría funcionar');
});
//numero de serie correlativo *************************************************************
function serie_numero(){
   //var x = document.getElementById("mySelect").value;
    var valor = document.getElementById('create_comprobante').value;
    var valores = valor.split('*');
    $('#serie').val(Number(valores[1])+1);
    $('#numero').val(Number(valores[2])+1);
    $('#id_conprobante').val(Number(valores[0]));
}
function generar_serie(){
    $.ajax({
        url: base_url+"movimientos/salidas/getSerie",
        type: "POST",
        dataType: "json",
        data:{ id_comprobante: $("#comprobante").val()},
        success: function(data){
            if(data.length >0){
                var serie = document.getElementById('serie');
                serie.length = 0;
                for(var i = 0; i < data.length; i++){
                serie.options[i] = new Option(data[i].serie,data[i].id_serie);
                }
                serie.options[i] = new Option('[Ingresar nueva serie]','nueva_serie');
                $("#numero_correlativo").val(parseInt(data[0].correlativo)+1);
                serie_numero = data;
            }else{
                $("#nueva_serie").show();
                $("#nueva_serie").focus();
                $('#numero_correlativo').val('');
                $("#serie").hide();
                toastr.info('Ingrese una serie.')
            }
           

        }
    });
}
function generar_correlativo($id_serie){
    $.ajax({
        url: base_url+"movimientos/salidas/getCorrelativo",
        type: "POST",
        dataType: "json",
        data:{ id_serie: $id_serie},
        success: function(data){
            $("#numero_correlativo").val(parseInt(data.correlativo)+1);
        }
    });
}
//borrar el producto si ya ha sido seleccionado alguno
$(document).ready(function(){
	$("#autocompleteProducto").keydown(function(event){
        if (event.which==8) {
            $("#autocompleteProducto").val("");
            $("#btn-agregar-abast").val("");
            estado = 0;
        }
        if (event.which==13 & estado==1) {
            document.getElementById("btn-agregar-abast").click();     
            estado = 0;
        }
        if (event.which==13 & $('#autocompleteProducto').is(":focus") == true) {
            //document.getElementById("btn-agregar-abast").click();
            $.ajax({
                url: base_url+"movimientos/salidas/getProductos",
                type: "POST",
                dataType: "json",
                data:{ autocompleteProducto: $('#autocompleteProducto').val()},
                success: function(data){  
                    if (data[0] == "") {
                        $('#autocompleteProducto').val('');
                        toastr.info('Codigo no existe');                          

                    }else{
                        if (data.length > 1) {
                            toastr.info('Verificar presentación','Verifique la presentación del producto códigos iguales o diferentes lotes');                           
                        }
                        fecha_caducidad = "";
                        cantidad ="";
                            if (data[0].caducidad != null) {
                                fecha_caducidad = " - "+data[0].caducidad;
                                cantidad = data[0].cantidad;
                            }else{
                                cantidad = data[0].existencias;
                            }
                        $("#btn-agregar-abast").val( data[0].codigo+'*'+data[0].nombre+'*'+data[0].precio_compra+'*'+data[0].precio_venta+'*'+data[0].id_producto+'*'+data[0].id_presentacion+'*'+data[0].existencias+'*'+data[0].perecedero+'*'+cantidad+'*'+data[0].lote+"*"+data[0].id_presentacion_producto); 
                        document.getElementById("btn-agregar-abast").click();
                        $('#autocompleteProducto').val('');                    
                    }
                },
               
            });
        }
        //alert( String.fromCharCode(event.which) + " es: " + event.which);

    }); 
    $("#autocompleteCliente").keydown(function(event){
        if (event.which==8) {
            $("#autocompleteCliente").val("");
            $("#idCliente").val("");
            estado = 0;
        }
        //alert( String.fromCharCode(event.which) + " es: " + event.which);

	}); 
});
//numero de serie correlativo *************************************************************
//serie_numero();
function serie_numero(){
   //var x = document.getElementById("mySelect").value;
    var valor = document.getElementById('create_comprobante').value;
    var valores = valor.split('*');
    $('#serie').val(Number(valores[1])+1);
    $('#numero').val(Number(valores[2])+1);
    $('#id_conprobante').val(Number(valores[0]));
}


//autocomplete para productos entrada *************************************************************
$("#autocompleteProducto").autocomplete({
    source: function(request, response){
        //alert('ahora si');
        $.ajax({
            url: base_url+"movimientos/salidas/getProductos",
            type: "POST",
            dataType: "json",
            data:{ autocompleteProducto: request.term},
            success: function(data){
                
                response($.map(data, function (item) {

                    fecha_caducidad = "";
                    cantidad ="";
                        if (item.caducidad != null) {
                            fecha_caducidad = " - "+item.caducidad;
                            cantidad = item.cantidad;
                        }else{
                            cantidad = item.existencias;
                        }
                    if ($('#codigo').val() ==1) {
                        return {
                            label: item.codigo + ' - ' + item.nombre+' - '+item.marca+' - '+ item.id_presentacion+fecha_caducidad+' - '+cantidad,
                            id: item.codigo+'*'+item.nombre+'*'+item.precio_compra+'*'+item.precio_venta+'*'+item.id_producto+'*'+item.id_presentacion+'*'+item.existencias+'*'+item.perecedero+'*'+cantidad+'*'+item.lote+"*"+item.id_presentacion_producto+"*"+item.lt_cantidad+'*'+item.exento+'*'+item.TEC,
                        }
                    }else{
                        return {
                            label:  item.nombre+' - '+item.marca+' - '+ item.id_presentacion+fecha_caducidad+' - '+cantidad,
                            id: item.codigo+'*'+item.nombre+'*'+item.precio_compra+'*'+item.precio_venta+'*'+item.id_producto+'*'+item.id_presentacion+'*'+item.existencias+'*'+item.perecedero+'*'+cantidad+'*'+item.lote+"*"+item.id_presentacion_producto+"*"+item.lt_cantidad+'*'+item.exento+'*'+item.TEC,
                        }
                    }
                }))
            },
        });
    }, //indica la informacion a mostrar al momento de comenzar a llenar el campo
    minLength:2, //caracteres que activan el autocomplete
    select: function(event, ui){
       data = ui.item.id;
       estado =1;
       $("#btn-agregar-abast").val(data); 
    },
  });


//agregar producto a vender *************************************************************
$("#btn-agregar-abast").on("click", function(){
    data = $(this).val();
    if (data != 0){
        infoProducto = data.split("*");
        $.ajax({
            url: base_url+"movimientos/entradas/getTipoPresentacion", //buscamos todas las presentaciones de dicho producto
            type: "POST",
            dataType: "json",
            data: {id_producto:infoProducto[4]},
            success: function(data){
                data_informacion_producto=""; //guardaremos una cadena de identificación del producto para comparar si ya existe.
                irrepetible = 0; //si es un producto que existe aumentar la cantidad
                cantidadMaxima = 0; //almacenaremos el número de producto que se puede vender por presentación

                html = "<tr>";
            if ($('#codigo').val()==1) {
                html += "<td><p class='cod_class'>"+infoProducto[0]+"</p></td>";//id y codigo
            }   
                html += "<td><input type='hidden' id='nomProd' name='nomProd[]' value= '"+infoProducto[1]+"' ><input type='hidden' id='exento' name='exento[]' value='"+infoProducto[12]+"'><input type='hidden' id='tec' value='"+infoProducto[13]+"'><input type='hidden' name='idProductos[]' class='id_producto' value='"+infoProducto[4]+"'><input type='hidden' name='codigos[]' class='cod_class'  value='"+infoProducto[0]+"'><p>"+infoProducto[1]+" "+infoProducto[5]+"</p></td>"; //nombre
                html += "<td><select name='tipo_presentacion[]' id='tipo_presentacion' class='custom-select '>";
                for (let i = 0; i < data.length; i++) {
                    if (data[i].id_presentacion_producto == infoProducto[10]) {
                        data_informacion_producto = data[i].id_presentacion_producto+"*"+data[i].codigo+"*"+data[i].venta+"*"+data[i].valor+"*"+data[i].existencias+"*"+infoProducto[9];
                        html+= "<option selected name='presentacion[]' value='"+data_informacion_producto+"'>"+data[i].nombre_pre+"</option>";
                        if(infoProducto[7]==1 & Number(infoProducto[11]) >= Number(data[i].valor)){
                            cantidadMaxima = infoProducto[11] / data[i].valor;
                            cantidadMaxima = myRoundCero(cantidadMaxima);
                        }else if (Number(data[i].existencias) >= Number(data[i].valor)) {
                            cantidadMaxima = data[i].existencias / data[i].valor;
                            cantidadMaxima = myRoundCero(cantidadMaxima);
                        }else{
                            cantidadMaxima = 0;
                        }

                }else{
                        html+= "<option name='presentacion[]' value='"+data[i].id_presentacion_producto+"*"+data[i].codigo+"*"+data[i].venta+"*"+data[i].valor+"*"+data[i].existencias+"*"+infoProducto[9]+"'>"+data[i].nombre_pre+"</option>";                 
                    }             
                }
                html += "<td><input style='width:100px' step='0.01'  min='0.00' type='number' pattern='^\d*(\.\d{0,2})?$' name='precioVenta[]' class='precio-salida' value='"+infoProducto[3]+"'></td>"; //precios
                if (infoProducto[7]==1) {
                    html += "<td><input type='number' style='width:100px' placeholder='Ingrese una cantidad' id='numCantidades' name='cantidades[]' value='1' min='1' max='"+cantidadMaxima+"' pattern='^[0-9]+' class='cantidades'><input type='hidden' name='estados[]' value = '"+infoProducto[7]+"' ><input type='hidden' name='lotes[]' value = '"+infoProducto[9]+"' ></td>"; //cantidades
                }else{
                    html += "<td><input type='number' style='width:100px' placeholder='Ingrese una cantidad' id='numCantidades'name='cantidades[]' value='1' min='1' max='"+cantidadMaxima+"' pattern='^[0-9]+' class='cantidades'><input type='hidden' name='estados[]' value = '"+infoProducto[7]+"' ><input type='hidden' name='lotes[]' value = '"+infoProducto[9]+"' ></td>"; //cantidades
                }
                html += "<td><input type='hidden' id='importes' name='importes[]' value='"+0+"'><p class='importePresentado'>"+0+"</p></td>"; //immportes
                html += "<td><button type='button' class='btn btn-danger btn-remove-producto'><span class='fa fa-times' style='color: #fff'></span></button></td>";
                html += "</tr>";

                $("#tbCompras tbody tr").each(function(){ //funcion para aumentar la cantidad dependiendo el producto leido
                    cadena_caracteres = $(this).closest("tr").find("#tipo_presentacion").val();
                    if ( cadena_caracteres === data_informacion_producto) {
                        irrepetible = 1;
                        cant = Number($(this).closest("tr").find("#numCantidades").val()); // obtenemos la cantidad actual
                        $(this).closest("tr").find("#numCantidades").val(cant+1); //aumentamos el valor
                        // evaluamos que la cantidad no exeda la capacidad máxima y actualizamos los valores
                        cantidad =cant + 1;
                        max =Number($(this).closest("tr").find("#numCantidades").prop('max'));

                        if (cantidad > max) {
                            toastr.warning('¡Cantidad maxima disponible ' + max + ' !');
                            $(this).closest("tr").find("#numCantidades").val(max);
                            cantidad = max;
                        }else{
                            precio = $(this).closest("tr").find(".precio-salida").val();
                            importe = cantidad * precio;
                            totalImporte = parseFloat(importe).toFixed(2);
                            $(this).closest("tr").find(".importePresentado").text(totalImporte);
                            $(this).closest("tr").find("#importes").val(totalImporte);
                            sumarReabastecimiento();
                        }                   
                    }
                });
                if (irrepetible == 0) {
                    $("#tbCompras tbody").append(html);
                    $("#tbCompras tbody tr").each(function(){ //funcion para aumentar la cantidad dependiendo el producto leido
                        cant = Number($(this).closest("tr").find("#numCantidades").val()); // obtenemos la cantidad actual

                        precio = $(this).closest("tr").find(".precio-salida").val();
                        importe = cant * precio;
                        totalImporte = parseFloat(importe).toFixed(2);
                        $(this).closest("tr").find(".importePresentado").text(totalImporte);
                        $(this).closest("tr").find("#importes").val(totalImporte);
                    /*    $(this).closest("tr").find("td:eq(5)").children("p").text(totalImporte);
                        $(this).closest("tr").find("td:eq(5)").children("input").val(totalImporte);*/
                        sumarReabastecimiento();                 
                    });
                }
                $('#btn-agregar-abast').val('');
                $('#autocompleteProducto').val(null);
            
            },
        });
    } else {
        toastr.info('Seleccione un roducto','Agregar');
    }
});

$(document).on("change", "#tbCompras #tipo_presentacion", function(){

        data  =  $(this).val().split('*');
        $(this).closest("tr").find(".precio-salida").val(data[2]);
        //$(this).closest("tr").find("td:eq(0)").children("p").text($data[1]); primera manera de hacerlo 
        $(this).closest("tr").find(".cod_class").text(data[1]); //segunda manera de hacerlo
        cant = $(this).closest("tr").find("#numCantidades").val();
        precio = $(this).closest("tr").find(".precio-salida").val();
        importe = Number(cant) * Number(precio);
        totalImporte = parseFloat(importe).toFixed(2);
        cantidadMaxima=0;

        if (Number(data[4]) >= Number(data[3]) ) {
            cantidadMaxima = data[4] / data[3];
            cantidadMaxima = myRoundCero(cantidadMaxima);
        }else{
            cantidadMaxima = 0;
        }
     //   alert(cant+'--'+cantidadMaxima+'--'+precio);
        $(this).closest("tr").find("#importes").val(totalImporte);
        $(this).closest("tr").find(".cantidades").prop('max',cantidadMaxima);
        $(this).closest("tr").find(".importePresentado").text(totalImporte);
        sumarReabastecimiento();

});

//procesamiento al ingresar otra cantidad de compra
$(document).on("input", "#tbCompras input.precio-salida", function(){
    pre_compra = $(this).val();
    cant =Number($(this).closest("tr").find("#numCantidades").val()); // $(this).closest("tr").find("td:eq(4)").children("input").val();
    importe = pre_compra * cant;
    totalImporte = parseFloat(importe).toFixed(2);
    $(this).closest("tr").find(".importePresentado").text(totalImporte);
    $(this).closest("tr").find("#importes").val(totalImporte);
   /* $(this).closest("tr").find("td:eq(5)").children("p").text(totalImporte);
    $(this).closest("tr").find("td:eq(5)").children("input").val(totalImporte);*/
    sumarReabastecimiento();
});
//procedimiento al ingresar cantidades
$(document).on("input", "#tbCompras input.cantidades", function(){

    cantidad =Number($(this).val());
    max =Number($(this).prop('max'));
    if (cantidad > max) {
        toastr.warning('¡Cantidad maxima disponible ' + max + ' !');
        $(this).closest("tr").find("#numCantidades").val(max);
     //   $(this).closest("tr").find("td:eq(4)").children('input').val(max);
    }else{
        precio = $(this).closest("tr").find(".precio-salida").val();
        importe = cantidad * precio;
        totalImporte = parseFloat(importe).toFixed(2);
        $(this).closest("tr").find(".importePresentado").text(totalImporte);
        $(this).closest("tr").find("#importes").val(totalImporte);
     /*   $(this).closest("tr").find("td:eq(5)").children("p").text(totalImporte);
        $(this).closest("tr").find("td:eq(5)").children("input").val(totalImporte);*/
        sumarReabastecimiento();
    }
});
//eliminar articulo
$(document).on("click", ".btn-remove-producto", function(){
    $(this).closest("tr").remove();
    sumarReabastecimiento();
});
//anular venta
$(document).on('click', '.eliminar_data', function(){   
    var id = $(this).attr("id");
    document.getElementById("id-salida-delete").value=id;
});

$(document).on("click", ".btn-view-salida", function(){
    valor_id = $(this).val();
    $.ajax({
        url: base_url+"movimientos/salidas/view",
        type:"POST",
        dataType: "html",
        data:{id:valor_id},
        success: function(data){
            $("#modalView .modal-body").html(data);
        }
    });
});

$(document).on("click", ".btnIr", function(){
    fecha1 = $("#fecha_inicio").val();
    fecha2 = $("#fecha_fin").val();
    if (fecha1 == "" || fecha2 =="") {
        toastr.warning('Ingrese las fechas.');
    }else{
        if (fecha2 >= fecha1 || fecha1 === fecha2) {
            ("#formFechas").submit();
        }else{
            toastr.warning('La primera fecha tiene que ser menor a la segunda.');
        }
    }    
});

$("#autocompleteCliente").autocomplete({
    source: function(request, response){
        $.ajax({
            url: base_url+"movimientos/salidas/getClientes",
            type: "POST",
            dataType: "json",
            data:{ valorCliente: request.term},
            success: function(data){
                response($.map(data, function(item){
                    return {
                        label: item.label + " "+ item.apellido,
                        id: item.id_cliente,
                    }
                }));
            }
        });
    }, //indica la informacion a mostrar al momento de comenzar a llenar el campo
    minLength:2, //caracteres que activan el autocomplete
    select: function(event, ui){
        data = ui.item.id;
        $("#idCliente").val(data);
    },
  });

  //funcion para sumar el costo total
function sumarReabastecimiento(){
    total_grabada = 0;
    total_exenta = 0;
    total = 0;
    estado_tec = $('#estado_tec').val();
    estado_iva = $('#estado_iva').val();

    document.getElementById('total_sub').innerHTML =0.00;

    porcentaje_tec =Number($('#tec_porcentaje').val());
    porcentaje_tec = parseFloat(porcentaje_tec/100).toFixed(2);

    porcentaje_iva =Number($('#iva_porcentaje').val());
   
    porcentaje_iva = parseFloat(porcentaje_iva/100).toFixed(2); 
    precio_tec_por_producto=''; //guardaremos el valor respectivo de su porcentaje. 
    tec_total= 0;
    iva_total = 0;


    $("#tbCompras tbody tr").each(function(){
            if($(this).find("#exento").val() == 1){
                valor = myRound(Number($(this).find("#importes").val()));
                total_exenta = total_exenta + parseFloat(valor);
                if ($(this).find("#tec").val() == 1) {
                    tec =  parseFloat(valor)*parseFloat(porcentaje_tec).toFixed(2);
                    tec_total = tec_total + tec;
                    if (estado_tec == 1) { // le quitamos el porcentaje de tecnología
                        total_exenta =  parseFloat(total_exenta) - parseFloat(tec);
                    }
                }
            }else{
                if (estado_iva == 1) {
                    valor = Number($(this).find("#importes").val());  
                    iva =  myRound(parseFloat(valor)*parseFloat(porcentaje_iva).toFixed(2));  

                    iva_total = iva_total + iva;
                    total_grabada = total_grabada + parseFloat(valor) - iva;                  
                    if ($(this).find("#tec").val() == 1) {
                        tec =  parseFloat(valor)*parseFloat(porcentaje_tec).toFixed(2);
                        tec_total = tec_total + tec;
                        if (estado_tec == 1) { // le quitamos el porcentaje de tecnología
                            total_grabada =  parseFloat(total_grabada) - parseFloat(tec);
                        }
                    }

                }else{

                    valor = Number($(this).find("#importes").val());
                    iva =  myRound(parseFloat(valor)*parseFloat(porcentaje_iva).toFixed(2));
                    iva_total = iva_total + iva;
                    total_grabada = total_grabada + parseFloat(valor);
                    if ($(this).find("#tec").val() == 1) {
                        tec =  parseFloat(valor)*parseFloat(porcentaje_tec).toFixed(2);
                        tec = myRound(tec);
                        tec_total = tec_total + tec;
                        if (estado_tec == 1) { // le quitamos el porcentaje de tecnología
                            total_grabada =  parseFloat(total_grabada) - parseFloat(tec);
                        }
                    }

                }
            }


        
    });
    //alert(myRound(iva_total)+' -  '+myRound(total_grabada)+'  -  '+myRound(total_exenta) + '  -  '+ myRound(tec_total));

    total2 = myRound(iva_total + total_grabada +total_exenta+ tec_total);
    $("#p_iva").val(iva_total);
    $("#p_grab").val(total_grabada);
    $("#p_exenta").val(total_exenta);
    $("#p_tec").val(tec_total);

    $("#total").val(total2.toFixed(2));
    $("#total_sub").val(total2.toFixed(2));
}

$("#btnGenerarInactivos").click(function(){
    window.open(base_url+"movimientos/salidas/getReporteInactivos", "_blank");
});

$("#btnelegirFecha").on("click", function(){
    fecha1 = $("#fecha1").val();
    fecha2 = $("#fecha2").val();
    window.open(base_url+"movimientos/salidas/getReporteFecha?fecha1="+fecha1+"&fecha2="+fecha2, "_blank");
});

$("#btnelegirCliente").on("click", function(){
    fecha1 = $("#fecha1Cli").val();
    fecha2 = $("#fecha2Cli").val();
    cli = $("#txtElegirCliente").val();
    window.open(base_url+"movimientos/salidas/getReporteCliente?fecha1="+fecha1+"&fecha2="+fecha2+"&cli="+cli, "_blank");
});

$("#btnResumen").on("click", function(){
    fecha1 = $("#fecha1Res").val();
    fecha2 = $("#fecha2Res").val();
    window.open(base_url+"movimientos/salidas/getResumen?fecha1="+fecha1+"&fecha2="+fecha2, "_blank");
});
function myRoundCero(num) {
    var exp = Math.pow(10, 0); // 2 decimales por defecto
    return parseInt(num * exp, 10) / exp;
  }
  function myRound(num, dec) {
    var exp = Math.pow(10, dec || 2); // 2 decimales por defecto
    return parseInt(num * exp, 10) / exp;
  }  
