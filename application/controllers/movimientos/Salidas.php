<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salidas extends CI_Controller {
	private $permisos;
	public function __construct(){
		parent::__construct();
		if($this->session->userdata('usuario_log')=="") {
			redirect(base_url());
	} else{
		$this->permisos = $this->backend_lib->control();		
		$this->load->model("Salidas_model");
		$this->load->model("Productos_model");
		$this->load->model("Kardex_model");
		$this->load->helper('printer_helper');
        $this->load->library('toastr');

		$this->load->library("Pdf");
	}
	}

	public function index(){
        $fecha = date("Y-m-d");
        $data = array(
			'permisos' => $this->permisos,
			'salidas' => $this->Salidas_model->getSalidasDia($fecha),
			'clientes' => $this->Salidas_model->getClientesTodos(),
        );
        $this->load->view("layouts/header");
        $this->load->view('layouts/aside');
        $this->load->view("admin/salidas/list",$data);
        $this->load->view("layouts/footer");
	}
	
	public function add(){
		$data = array(
			'comprobantes' => $this->Salidas_model->getComprobantes(),
			'impuesto' => $this->Salidas_model->getImpuesto(),			
        );
        $this->load->view("layouts/header");
        $this->load->view('layouts/aside');
        $this->load->view("admin/salidas/add",$data);
        $this->load->view("layouts/footer");
    }

    public function getProductos(){
        $valor = $this->input->post("autocompleteProducto");
		$producto = $this->Salidas_model->getProductos($valor);
		echo json_encode($producto);
	}
	public function addCliente(){
		$nombre  = $this->input->post("nombre");
        $apellido  = $this->input->post("apellido");
        $nit  = $this->input->post("nit");
        $telefono  = $this->input->post("telefono");
        $registro  = $this->input->post("registro");
        $direccion  = $this->input->post("direccion");
        
        $data  = array(
            'nombre' => $nombre,
            'apellido' => $apellido,
            'nit' => $nit,
            'telefono' => $telefono,
            'registro' => $registro,
            'direccion' => $direccion,
            'estado' => 1,
        );
		$resultado = $this->Salidas_model->addCliente($data);
		echo json_encode($resultado);
	}

    public function buscar(){
        $this->load->view("layouts/header");
        $this->load->view('layouts/aside');
        $this->load->view("admin/salidas/buscar");
        $this->load->view("layouts/footer");
    }

    public function getResultados(){
        $fecha1 = $this->input->post("fecha_inicio");
        $fecha2 = $this->input->post("fecha_fin");
        $data = array(
            'salidas' => $this->Salidas_model->getSalidasFechas($fecha1, $fecha2),
            'fecha1' => $fecha1,
            'fecha2' => $fecha2,
        );

        $this->load->view("layouts/header");
        $this->load->view('layouts/aside');
        $this->load->view("admin/salidas/buscar", $data);
        $this->load->view("layouts/footer");
    }
	public function getSerie(){
		$id = $this->input->post("id_comprobante");
		$resultado = $this->Salidas_model->getSerie($id);
		echo json_encode($resultado);
	}
	public function addSerie(){
		$nuevaSerie = $this->input->post("nuevaSerie");
		$comprobante = $this->input->post('comprobante');
		$data = array(
			'id_comprobante' => $comprobante,
			'serie' => $nuevaSerie,
		);
		$resultado = $this->Salidas_model->addSerie($data);
		echo json_encode($resultado);
	}

//fncion para guardar las compras
    public function store(){
		$numero_comprobante = $this->input->post('comprobante');
		$serie =  $this->input->post('serie');
		$exento =  $this->input->post('exento');
		
		$p_iva = $this->input->post("p_iva");
		$p_grab = $this->input->post("p_grab");
		$p_exenta = $this->input->post("p_exenta");
		$p_tec = $this->input->post("p_tec");

		$correlativo = $this->input->post('numero_correlativo');
		$nomProd = $this->input->post('nomProd');
		$fecha = $this->input->post("fecha");
		$idproductos =$this->input->post("idProductos");
		$idCliente = $this->input->post("idCliente");
		$estados = $this->input->post('estados');
		$lotes = $this->input->post('lotes');
		if ($idCliente == null) {
			$idCliente = 1;
		}
		$precioVenta =$this->input->post("precioVenta");
		$cantidades =$this->input->post("cantidades");
		$importe =$this->input->post("importes");
		$total = $this->input->post("total");
		$idusuario = $this->session->userdata('id');
		$descripcion = 'venta de producto';
		$infoPresentacion = $this->input->post('tipo_presentacion');
		$codigos = $this->input->post('codigos');

		$data = array(
			'id_usuario' => $idusuario,
			'id_cliente' => $idCliente,
			'fecha' => $fecha,
			'total' => $total,
			'id_movimiento' => 2,
			'descripcion' => $descripcion,
		);
        $this->db->trans_start(); // ******************************************************** iniciamos transaccion **************************************

			$this->Salidas_model->save($data);
			$idSalida = $this->Salidas_model->lastID(); 
			$this->save_detalle($idproductos, $precioVenta, $idSalida, $cantidades, $importe,$fecha,$estados,$lotes,$infoPresentacion,$codigos,$total); //guardando el detalle de la venta

		$this->db ->trans_complete();// ******************************************************** icompletamos transaccion **************************************
	
		if($this->db->trans_status()){ // *************************************************** iniciamos transaccion **************************************
			if($this->input->post('impr') == 1 & $numero_comprobante == 2){
		
				imprrimirTicket( $this->session->userdata('nombre'),$this->session->userdata('direccion'),$this->session->userdata('giro'),$this->session->userdata('telefono'),$correlativo,$infoPresentacion,$cantidades,$nomProd,$importe,$precioVenta,$exento,$total,$p_iva,$p_grab,$p_exenta,$p_tec);
					$data = array(
						'correlativo' => $correlativo,
					);
					$this->Salidas_model->actualizarCorrelativo($serie,$data, $infoPresentacion);
			

			}
            $this->toastr->success('Registro guardado!*****');
			redirect(base_url()."movimientos/salidas"); //redirigiendo a la lista de ventas
           
        }
        else{
            $this->toastr->error('No se pudo completar la operación.');
			redirect(base_url()."movimientos/salidas"); //redirigiendo a la lista de ventas
          
        }
	
	}

	//funcion para guardar el detalle de la venta
	protected function save_detalle($productos, $precioVentas, $idSalida, $cantidades, $importes,$fecha,$estados,$lotes,$infoPresentacion,$codigos){
		for ($i=0; $i < count($productos); $i++) { 
			$infoPre = explode('*',$infoPresentacion[$i]);
			$id_lote=0; //variable que contendera el id del estado si es necesario
				if ($estados[$i] == 1) {
						$loteActual = $this->Salidas_model->getLote($lotes[$i]);
						if ($loteActual->cantidad == ($cantidades[$i]*$infoPre[3])) {
							$data2 = array(
								'estado' => 0,
								'cantidad' => 0,
							);							
						}else{
							$data2 = array(
								'cantidad' => $loteActual->cantidad - ($cantidades[$i]*$infoPre[3]),
							);
						}
						$this->Salidas_model->updateLote($lotes[$i], $data2);
						$id_lote = $lotes[$i];
					}
				$data = array(
					'id_salida' => $idSalida,
					'precio_venta' => $precioVentas[$i],
					'id_producto' => $productos[$i],
					'cantidad' => $cantidades[$i],
					'subtotal' => $importes[$i],
					'id_lote' => $id_lote,
				);
				$kardex = array(
					'fecha' =>$fecha , 
					'id_movimiento' => 2,
					'descripcion'=> 'Salida',
					'id_producto' => $productos[$i],
					'cantidad' =>$cantidades[$i],
					'precio' =>$precioVentas[$i],
					'total' =>$importes[$i],
					'id_salida' => $idSalida,
					'id_presentacion_producto' => $infoPre[0],
					'id_usuario' => $this->session->userdata('id'),					
				);
				$data['id_presentacion_producto'] = $infoPre[0];
				$this->Kardex_model->add($kardex);
				$this->Salidas_model->save_detalle($data);
				$this->updateProducto($productos[$i], $cantidades[$i],$infoPre[3]); //actualizamos el stock del producto
			
		}
	}

	protected function updateProducto($idProducto,$cantidad,$infoPre){
		$productoActual = $this->Productos_model->get2($idProducto);
		$stock = $this->Productos_model->getStock($productoActual->id_stock);
		$cantidad = $cantidad * $infoPre;// valor cantidades representa la cantidad numerica por presentación
		$data2 = array(
			'stock_actual' => $stock->stock_actual - $cantidad,
		);
		$this->Productos_model->updateStock($productoActual->id_stock, $data2);
	}

	public function view(){
		$id = $this->input->post("id");
		$data = array(
			'salida' => $this->Salidas_model->getSalida($id),
			'detalle_salida' => $this->Salidas_model->getDetalleSalida($id)
		);
		$this->load->view("admin/salidas/view", $data);
	}

	public function eliminar(){
		$id = $this->input->post('id-salida-delete');
		//$entrada = $this->Entradas_model->get($id);
		$detalle = $this->Salidas_model->getDetalle($id);
		$data = array(
			'estado' =>0,
		);
        $this->db->trans_start(); // ******************************************************** iniciamos transaccion **************************************

		$this->Salidas_model->updateSalida($id, $data);
		$lote  = $this->Salidas_model->getLote($id);
		//eliminas la venta en kardex
		$salidas = $this->Kardex_model->get_venta($id);
		foreach($salidas as $sa){

			$kardex = array(
				'fecha' =>date('Y-m-d'),
				'id_movimiento' => 4,
				'descripcion'=> 'Venta anulada.',
				'id_producto' => $sa->id_producto,
				'cantidad' =>$sa->cantidad,
				'precio' =>$sa->precio,
				'total' =>$sa->total,
				//'saldo' => $sa->saldo - $sa->total,
				'id_salida' => $id,
				'id_usuario' => $this->session->userdata('id'),					
			);
			$this->Kardex_model->add($kardex);
		}

		foreach( $detalle as $det ):
			$productoActual = $this->Productos_model->get($det->id_producto);
			$stock = $this->Productos_model->getStock($productoActual->id_stock);
			$nuevoValor = $stock->stock_actual + ($det->cantidad*$productoActual->valor);
			$data2 = array(
				'stock_actual' => $nuevoValor,
			);
			$this->Productos_model->updateStock($productoActual->id_stock, $data2);
			if ($det->id_lote != 0) {
				$loteActual = $this->Salidas_model->getLote($det->id_lote);
				$data = array(
					'cantidad' => $loteActual->cantidad + ($det->cantidad*$productoActual->valor),
					'estado' =>1,
				);
				$this->Salidas_model->updateLote($det->id_lote, $data);
			}
		endforeach;
        $this->db ->trans_complete();// ******************************************************** completamos transaccion **************************************
		
		if($this->db->trans_status()){ // ******************************************************** Evaluamos estado **************************************
            $this->toastr->success('Registro eliminado!');
			redirect(base_url()."movimientos/salidas"); //redirigiendo a la lista de ventas
        }
        else{
            $this->toastr->error('No se pudo completar la operación.');
			redirect(base_url()."movimientos/salidas"); //redirigiendo a la lista de ventas
        }
	}

	public function getClientes(){
		$valorCliente = $this->input->post("valorCliente");
		$cli = $this->Salidas_model->getClientes($valorCliente);
		echo json_encode($cli);
	}

	public function getReporteInactivos(){
        $idusuario = $this->session->userdata('id');
        //trayendo informacion
        $data = array(
            'fecha' => date("d-m-Y"),
            'empresa' => $this->Salidas_model->getAjustes(),
            'nomUsuario' => $this->Salidas_model->getUsuario($idusuario),
            'salidas' => $this->Salidas_model->getSalidasInactivos(),
            'estado' => "Inactivos"
        );
        //generando el pdf
        $this->load->view("admin/reportes/salidas", $data);
    }

    public function getReporteFecha(){
        $fecha1 = $this->input->get("fecha1");
        $fecha2 = $this->input->get("fecha2");
        $idusuario = $this->session->userdata('id');
        //trayendo informacion
        $data = array(
            'fecha' => date("d-m-Y"),
            'empresa' => $this->Salidas_model->getAjustes(),
            'nomUsuario' => $this->Salidas_model->getUsuario($idusuario),
			'salidas' => $this->Salidas_model->getSalidasFechas($fecha1, $fecha2),
			'totalVenta' => $this->Salidas_model->totalSalidasFechas($fecha1, $fecha2),
            'estado' => "Por Fechas"
		);
        //generando el pdf
        $this->load->view("admin/reportes/salidasTotal", $data);
    }

	public function getReporteCliente(){
        $fecha1 = $this->input->get("fecha1");
		$fecha2 = $this->input->get("fecha2");
		$cli = $this->input->get("cli");
        $idusuario = $this->session->userdata('id');
        //trayendo informacion
        $data = array(
            'fecha' => date("d-m-Y"),
            'empresa' => $this->Salidas_model->getAjustes(),
            'nomUsuario' => $this->Salidas_model->getUsuario($idusuario),
			'salidas' => $this->Salidas_model->getSalidasCliente($fecha1, $fecha2, $cli),
            'estado' => "Por Cliente"
		);
		//generando el pdf
        $this->load->view("admin/reportes/salidas", $data);
	}
	
	public function getResumen(){
        $fecha1 = $this->input->get("fecha1");
		$fecha2 = $this->input->get("fecha2");
        $idusuario = $this->session->userdata('id');
        //trayendo informacion
        $data = array(
            'fecha' => date("d-m-Y"),
            'empresa' => $this->Salidas_model->getAjustes(),
            'nomUsuario' => $this->Salidas_model->getUsuario($idusuario),
			'salidas' => $this->Salidas_model->resumenDiario($fecha1, $fecha2),
			'totalVenta' => $this->Salidas_model->totalSalidasFechas($fecha1, $fecha2),
            'estado' => "Resumen Diario"
		);
		//generando el pdf
        $this->load->view("admin/reportes/salidasResumen", $data);
	}
}