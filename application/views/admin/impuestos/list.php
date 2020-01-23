
            <!-- page title area start -->
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Impuestos</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="index.html">Home</a></li>
                                <li><span>Mantenimiento</span></li>
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

            <!--permisos ***************************************** > -->
            <?php if ($permisos->read!=1) {
                # code...
                redirect(base_url(),"dashboard");
            }
            $habilitado_insert ="disabled";

            $habilitado_update="disabled";

            $habilitado_delete="disabled";

            if ($permisos->update == 1) {
                $habilitado_update ="enabled";
            }

            if ($permisos->delete == 1) {
                $habilitado_delete = "enabled";
            }
            if ($permisos->insert == 1) {
                $habilitado_insert = "enabled";
            }

            ?>

            <!-- data table start -->
                                
            <div class="main-content-inner">
                                <div class="col-12 mt-5">
                                    <div class="card">
                                        <div class="card-body">
                                        
                                            <h4 class="header-title">Lista - Impuestos</h4>

                                                <div class="input-group">
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-outline-primary mb-3" <?php echo $habilitado_insert?> data-toggle="modal" onclick="limpiar()"data-target="#add"> Agregar+</button>
                                                    </div>
                                                    
                                                </div>  
                                            <div class="data-tables">
                                                <table id="example" class="table table-striped table-bordered" style="width:100%">                           
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Impuesto</th>
                                                            <th>%</th>
                                                            <th>Opciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody> 
                                                    <?php $cont = 0;?>
                                                    <?php if(!empty($impuestos)):?>
                                                        <?php  foreach($impuestos as $imp):?>
                                                        <?php $cont++;?>
                                                            <tr> 
                                                                <td><?php echo $cont;?></td>
                                                                <td><?php echo $imp->nombre;?></td>
                                                                <?php $dataImpuesto = $imp->id_impuesto."*".$imp->nombre."*".$imp->porcentaje; ?>
                                                                <?php if($imp->estado == 1){?>
                                                                <td>
                                                                   <?php echo $imp->porcentaje;  ?>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group">

                                                                        <button id="up_impuesto<?php echo $cont; ?>" onclick="impuestoUpdate(<?php echo $cont; ?>)" <?php echo $habilitado_update ?> type="button" class="btn btn-warning btn-view-impuesto" data-toggle="modal" data-target="#edit_impuesto" value="<?php echo $dataImpuesto;?>">
                                                                            <span span class="fa fa-pencil" style="color: #fff"></span>
                                                                        </button>                           
                                                                        <button id="del_impuesto<?php echo $cont; ?>" onclick="impuestoDelete(<?php echo $cont; ?>)" <?php echo $habilitado_delete ?> type="button" class="btn btn-danger btn-remove-impuesto" data-toggle="modal" data-target="#delete_impuesto" value="<?php echo $dataImpuesto;?>" >
                                                                            <span class="fa fa-times" style="color: #fff"></span>
                                                                        </button>                  
                                                                    </div>
                                                                </td>
                                                                <?php } ?>
                                                            </tr>
                                                            
                                                        <?php endforeach;?>
                                                        <?php endif;?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                </div>
                                </div>
            </div>

            <?php
            $this->load->view('layouts/alert');
            ?>

            <!-- Modal add-->
                                    <div class="modal fade" id="add">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Agregar</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                <form id="form1" action="<?php echo base_url();?>mantenimiento/impuestos/store" method="POST">
                                                <label >Nombre del impuesto.</label>
                                                <input id="name" name="name" type="text" class="form-control" placeholder="Ingrese nombre del impuesto">
                                                <br>
                                                <label >Porcentaje %.</label>
                                                <input id="porcentaje" name="porcentaje" type="text" class="form-control" placeholder="Ejemplo: 15">                                                
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary"   >Guardar</button>
                                            </form> </div>
                                            </div>
                                        </div>
                                    </div>
            <!-- Modal delete-->
            <div class="modal fade" id="delete_impuesto">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Eliminar</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                <form action="<?php echo base_url();?>mantenimiento/impuestos/delete" method="POST">
                                                <h4>Está seguro de eliminar este impuesto?</H4>
                                                <input id="id_update_marca" name="id_update_marca" type="hidden" class="form-control" >
                                                
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Eliminar</button>
                                                    </form> 
                                                </div>
                                            </div>
                                        </div>
            </div>

            <!-- Modal update-->
            <div class="modal fade" id="edit_impuesto">
                <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Actualizar</h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form2" action="<?php echo base_url();?>mantenimiento/impuestos/update" method="POST">
                                    <input id="id_impuesto_update" name="id_impuesto_update" type="hidden" class="form-control" >
                                    <h4>Nombre</H4>
                                    <input id="nombre_impuesto_update" name="nombre_impuesto_update" class="form-control" >
                                    <h4>Porcentaje %.</H4>
                                    <input id="porcentaje_impuesto_update" name="porcentaje_impuesto_update" class="form-control" >
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar cambio</button>
                                </form> 
                                </div>
                        </div>
                </div>
            </div>
    </div>

<script src="<?php echo base_url();?>assets/js/adminJS/impuestos.js"></script>

    
