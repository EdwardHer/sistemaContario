
            <!-- page title area start -->
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Agregar Venta</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="index.html">Home</a></li>
                                <li><span>Movimientos</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile pull-right">
                        <img src="<?php echo base_url()?>assets/images/ajuste/<?php echo $this->session->userdata('logo')?>" class="avatar user-thumb" alt="avatar">                                       

                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown"> <?php echo $this->session->userdata("usuario_log")?> <i class="fa fa-angle-down"></i></h4>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?php echo base_url();?>ajustes/ajustes/index">Ajustes</a>
                                <a class="dropdown-item" href="<?php echo base_url();?>Auth/logout">Cerrar Sesion</a>
                              </div>
                        </div>
                    </div>
                </div>
           </div>

    <input type="hidden" id="estado_iva" value="<?php echo $this->session->userdata('iva'); ?>">
    <input type="hidden" id="estado_tec" value="<?php echo $this->session->userdata('tec'); ?>">

    <input type="hidden" id="iva_porcentaje" value="<?php echo $impuesto[1]->porcentaje;?>">
    <input type="hidden" id="tec_porcentaje" value="<?php echo $impuesto[0]->porcentaje;?>">

    <div class="main-content-inner">
                    <div class="row">
                        <!-- busqueda de producto -->
                        <div class="col-12 mt-5">
                            <div class="card">
                                <div class="card-body">
                                    <form class="form-control" action="<?php echo base_url();?>movimientos/salidas/store" id="FormSalida" method='POST'  >
                                        <div class='input-group'>
                                            <div class='col-md-3'>
                                            <label>Fecha:</label>
                                            <input name='fecha' id='fecha' type="text" value="<?php echo date("Y-m-d");?>" class='form-control' >
                                           
                                            <input type="hidden" id="p_iva" name="p_iva">
                                            <input type="hidden" id="p_grab" name="p_grab">
                                            <input type="hidden" id="p_exenta" name="p_exenta">
                                            <input type="hidden" id="p_tec" name="p_tec">
                                            
                                            <input type="hidden" id="impr" name="impr" >
                                        </div>
                                        <div class="col-md-3 mt-1">
                                                <label for="comprobante">Comprobante.</label>         
                                                <select name='comprobante' id='comprobante' class='custom-select' required >
                                                    <?php foreach($comprobantes as $comprobante):?>
                                                    <option value='<?php echo $comprobante->id_comprobante;?>'><?php echo $comprobante->nombre;?></option>
                                                    <?php endforeach;?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mt-1">
                                                <label for="serie">Serie.</label>         
                                                <select name='serie' id='serie' class='custom-select' required >
                                                  
                                                </select>
                                                <input name='nueva_serie' id='nueva_serie'  class='form-control' >

                                            </div>
                                            
                                            <div class="col-md-3">
                                                <label for="">Número.</label>
                                                <input name='numero_correlativo' id='numero_correlativo' type='text' ondblclick="actualizarCorrelativo()"  class='form-control' >
                                            </div>  

                                            <div class='col-md-12 mt-3'>
                                                <label>Cliente: </label>
                                                <div class="input-group">
                                                    <input name='valorCliente' required id='autocompleteCliente' type='text' class='form-control' >
                                                    <input type="hidden" id="idCliente" name="idCliente" >
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-primary" id="btnAgregar" type="button" data-toggle="modal" data-target="#modalAgregar">+Cliente</button>
                                                    </div>
                                                </div>
                                            </div>
                                            

                                        <div class="col-md-3">
                                               <input type="hidden" id="total" name="total">
                                               <input type="hidden" id="iva_total" name="iva_total">
                                               <input type="hidden" id="total_grabada" name="total_grabada">
                                               <input type="hidden" id="total_exenta" name="total_exenta">
                                               <input type="hidden" id="tec_total" name="tec_total">


                                        </div>
                                        
                                        <div class='col-md-3'>

                                        </div>   
                                        <div class='col-md-3'>

                                        </div>                       
                                        <br>
                                        <div class="col-md-12">
                                            <label class="col-form-control">Buscar Producto:</label>
                                            <div class="input-group">
                                                <input name="autocompleteProducto" class="form-control" type="text" id="autocompleteProducto">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-primary" id="btn-agregar-abast" type="button">Agregar</button>
                                                </div>
                                            </div>                           
                                            <br>
                                            <input type="hidden" id='cod' value=<?php echo $this->session->userdata('codigo') ?>>

                                            <table id="tbCompras" class="table table-bordered table-striped table-hover table-responsive">
                                                <thead>
                                                    <tr >
                                                    <?php if($this->session->userdata('codigo') == 1){?>
                                                                    <th class="col-md-1">Codigo.</th>
                                                    <?php } ?>        
                                                        <th class="col-md-1">Producto.</th>
                                                        <th class="col-md-1">Presentación.</th>
                                                        <th class="col-md-1">Precio Venta.</th>
                                                        <th class="col-md-1">Cantidad</th>
                                                        <th class="col-md-1">Importes</th>
                                                        <th class="col-md-1">Eliminar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>                               
                                                </tbody>
                                            </table>
                                        </div>                                     
                                            
                                            <div class="col-md-4">
                                                <label for="">Total:</label>
                                                <input name='total_sub'  id='total_sub' type='text' class='form-control alert-success' placeholder='$00.00'>

                                        </div>                   
                                    <div class="col-md-4">
                                                <label for="">Efectivo.</label>
                                                <input name='efectivo'  id='efectivo' type='text' class='form-control alert' placeholder='$00.00'>
                                        </div>  
                                        <div class="col-md-4">
                                                <label for="">Cambio.</label>
                                                <input name='cambio' id='cambio' disabled type='text' class='form-control alert'>
                                        </div>   
                                        <div class="col-md-12 mt-3">
                                                <button type="button" id="procesar"  class="btn col btn-outline-primary mb-3" data-toggle="modal" data-target="#imprimir">Procesar</button>                                       
                                        </div>                               
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
         </div>
    </div>
</div>

<!-- imprimir en ticket o no -->
<div class="modal fade" id="imprimir">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Eliminar</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                <form action="<?php echo base_url();?>mantenimiento/marcas/delete" method="POST">
                                                <h4>Desea imprimir?</H4>                        
                                                </div>
                                                <div class="modal-footer">

                                                    <button type="button" onclick="validarFormulario(0)" class="btn btn-primary">No</button>
                                                    <button type="button" onclick="validarFormulario(1)" class="btn btn-primary">Sí</button>
                                                
                                                    </form> 
                                                </div>
                                            </div>
                                        </div>
            </div>
 <!-- Modal Agregar-->
 <div class="modal fade" id="modalAgregar">
     <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content">
            <form method="POST" id="agregarCliente">
                <div class='modal-header'>
                    <h5 class='modal-title'>Agregar</h5>
                    <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                </div>
                <div class='modal-body'>
                    <div class='form-group'><label>Nombre:</label>
                        <input  id="nombre" name='nombre' type='text' class='form-control' ></div>
                    <div class='form-group'><label>Apellidos: </label>
                        <input id="apellido" name='apellido' type='text' class='form-control' ></div>
                    <div class='form-group'><label>NIT:</label>
                        <input name='nit' type='text' class='form-control' ></div>
                    <div class='form-group'><label>Telefono:</label>
                        <input name='telefono' type='text' class='form-control' ></div>
                    <div class='form-group'><label>Registro</label>
                        <input name='registro' type='text' class='form-control' ></div>
                    <div class='form-group'><label>Dirección:</label>
                        <input name='direccion' type='text' class='form-control' ></div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancelar</button>
                    <button type='button' onclick="agregarCliente()" id="btnGuardar" class='btn btn-primary'>Guardar</button>
                </div>
            </form>
        </div>
     </div>
    </div>
<script src="<?php echo base_url();?>assets/js/adminJS/salidas.js"></script>
<script>

$(document).ready(function(){
estado = 0;
$("#nueva_serie").hide(); //ocultamos, el input donde ingresaremos una nueva serie

//generamos la primer serie
generar_serie();

$(this).on('click',function(){
   if($('#nueva_serie').val() != ""){
        nuevaSerie();
    }else if(document.getElementById('nueva_serie').style.display != 'none'){
       if (condicionNuevaserie!=0) {
            $("#nueva_serie").hide(); //ocultamos, el input donde ingresaremos una nueva serie
            $('#serie').show();
            condicionNuevaserie=0;
            generar_serie();

       }else{
        condicionNuevaserie++;
       }
      
    }
   
});
});
</script>