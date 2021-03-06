<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Salidas_model extends CI_Model {
	
    public function getSalidasDia($fecha){ 
        $this->db->select("s.*, u.usuario, c.nombre, c.apellido");
        $this->db->from("salidas s");
        $this->db->join("usuarios u", "s.id_usuario = u.id_usuario");
        $this->db->join("clientes c", "s.id_cliente = c.id_cliente");
        $this->db->where('s.estado',1);
        $this->db->where("s.fecha", $fecha); 
        $this ->db->order_by( 's.fecha' , 'desc' );
        $resultados = $this->db->get();
        return $resultados->result();
    }

    public function getClientes($valor){
      $this->db->select("id_cliente, nombre as label, apellido");
      $this->db->from("clientes");
      $this->db->like("nombre", $valor);
      $this->db->or_like("apellido", $valor);
      $resultados = $this->db->get();
      return $resultados->result_array();
    }

    public function getClientesTodos(){
        $this->db->select("id_cliente, nombre, apellido");
        $this->db->from("clientes");
        $this->db->where("estado", 1);
        $resultados = $this->db->get();
        return $resultados->result();
      }

  
      public function getProductos($valor){
        $this->db->select("p.*,m.nombre as marca,pr.id_presentacion_producto as id_presentacion_producto, pr.precio_venta as precio_venta,pre.nombre as id_presentacion,pr.codigo as codigo,s.stock_actual as existencias,lt.fecha_caducidad caducidad,lt.cantidad cantidad,lt.id_lote lote, lt.cantidad as lt_cantidad");
        $this->db->from("productos p");
        $this->db->join("marcas m","p.id_marca = m.id_marca");
        $this->db->join("presentaciones_producto pr", "p.id_producto = pr.id_producto");
        $this->db->join("presentacion pre","pr.id_presentacion = pre.id_presentacion");
        $this->db->join("stock s","p.id_stock = s.id_stock");
        $this->db->join("lotes lt", "p.id_producto = lt.id_producto", 'left');
        $this->db->where("p.estado","1");
        $this->db->group_start();
          $this->db->where("lt.cantidad > 0");
          $this->db->or_where("lt.cantidad",null);
          $this->db->group_end();
          $this->db->group_start();
          $this->db->where("lt.estado = 1");
          $this->db->or_where("lt.estado ",null);
          $this->db->group_end();
          $this->db->group_start();
          $this->db->like("p.nombre", $valor);
          $this->db->or_like("pr.codigo", $valor);
          $this->db->group_end();

        $resultados = $this->db->get();
        return $resultados->result_array();
        }
    
    public function getLote($id){
      $this->db->where("id_lote",$id);
      $resultados = $this->db->get('lotes');
      return $resultados->row();	
    }

    public function save($data){
      return $this->db->insert("salidas", $data);
    }

    public function lastID(){
      return $this->db->insert_id();
    }

    public function save_detalle($data){
      $this->db->insert("detalle_salida", $data);
    }
    public function updateP($id,$data){
      $this->db->where("id_producto",$id);		
      $this->db->update("productos",$data);
      return 0;
    }

    public function  getComprobantes(){
      $resultados = $this->db->get('comprobantes');
      return $resultados->result();
    }
    public function getImpuesto(){
      $resultado = $this->db->get('impuestos');
      return $resultado->result();
    }
    public function update_correlativo($id,$cantidad){
      $this->db->where("id_tipo_comprobante",$id);		
      $this->db->update("tipo_comprobante",$cantidad);
      return 0;
    }
    public function updateLote($id, $data){
      $this->db->where("id_lote",$id);		
      $this->db->update("lotes",$data);
      return 0;
    }
    public function addSerie($data){
      
      $this->db->insert('series',$data);
      return $this->db->insert_id();
    }  

    public function addCliente($data){
      $this->db->insert("clientes",$data);
     return $this->db->insert_id();
    }
    
    public function actualizarSerie($comprobante,$serie,$data){
      $this->db->where("id_comprobante", $comprobante);
      $this->db->where("serie", $serie);
      $this->db->update("series",$data);
    } 
    public function getSerie($id){
      $this->db->where("id_comprobante",$id);
      $resultado = $this->db->get("series");
      return $resultado->result();
    }
    public function getCorrelativo($id){
      $this->db->select('correlativo');
      $this->db->from("series");
      $this->db->where("id_serie",$id);
      $resultado = $this->db->get();
      return $resultado->row();
    }
    public function actualizarCorrelativo($serie,$data){
      $this->db->where("id_serie", $serie);
      $this->db->update("series",$data);

    }
    public function getSalida($id){
      $this->db->select("s.*, u.usuario, c.nombre, c.apellido");
      $this->db->from("salidas s");
      $this->db->join("usuarios u", "s.id_usuario = u.id_usuario");
      $this->db->join("clientes c", "s.id_cliente = c.id_cliente");
      $this->db->where("s.id_salida", $id);
      $resultado = $this->db->get();
      return $resultado->row();
    }

    public function getDetalleSalida($id){
            $this->db->select("d.*, p.nombre, pr.codigo as codigo, pn.nombre as nombrePre");
            $this->db->from("detalle_salida d");
            $this->db->join("productos p", "d.id_producto = p.id_producto");
            $this->db->join("presentaciones_producto pr", "d.id_presentacion_producto = pr.id_presentacion_producto");
            $this->db->join("presentacion pn", "pn.id_presentacion = pr.id_presentacion");
            $this->db->where("d.id_salida", $id);
		  $resultados = $this->db->get();
		  return $resultados->result();
    }

    public function getDetalle($id){ 
      $this->db->where("id_salida",$id);
      $resultados = $this->db->get("detalle_salida");
      return $resultados->result();
    }

    public function updateSalida($id,$data){
      $this->db->where("id_salida",$id);		
      $this->db->update("salidas",$data);
      return 0;
    }

    public function getAjustes(){
      $resultado = $this->db->get("ajustes");
      return $resultado->row();
    }
  
    public function getUsuario($id){
        $this->db->select("usuario");
      $this->db->where("id_usuario",$id);
      $resultado = $this->db->get("usuarios");
      return $resultado->row();
    }

    public function getSalidasFechas($fecha1, $fecha2){
      $this->db->select("date_format(s.fecha, '%d-%m-%Y') as fecha, s.total, u.usuario, c.nombre, c.apellido, s.*");
      $this->db->from("salidas s");
      $this->db->join("usuarios u", "s.id_usuario = u.id_usuario");
      $this->db->join("clientes c", "s.id_cliente = c.id_cliente");
      $this->db->where("s.estado",1);
      $this->db->where("s.fecha BETWEEN '$fecha1' AND '$fecha2'");
      $resultado = $this->db->get();
      return $resultado->result();
    }

    public function getSalidasCliente($fecha1, $fecha2, $cli){
        $this->db->select("date_format(s.fecha, '%d-%m-%Y') as fecha, s.total, u.usuario, c.nombre, c.apellido");
        $this->db->from("salidas s");
        $this->db->join("usuarios u", "s.id_usuario = u.id_usuario");
        $this->db->join("clientes c", "s.id_cliente = c.id_cliente");
        $this->db->where("s.id_cliente", $cli);
        $this->db->where("s.estado", 1);
        $this->db->where("s.fecha BETWEEN '$fecha1' AND '$fecha2'");
        $resultado = $this->db->get();
        return $resultado->result();
      }

    public function getSalidasInactivos(){
      $this->db->select("date_format(s.fecha, '%d-%m-%Y') as fecha, s.total, u.usuario, c.nombre, c.apellido");
      $this->db->from("salidas s");
      $this->db->join("usuarios u", "s.id_usuario = u.id_usuario");
      $this->db->join("clientes c", "s.id_cliente = c.id_cliente");
      $this->db->where("s.estado",0);
      $resultado = $this->db->get();
      return $resultado->result();
    }
  
    public function totalSalidasFechas($fecha1, $fecha2){
        $resultado = $this->db->query("
            select truncate(sum(total), 3) as totalTotal
            from salidas
            where estado = 1
            and fecha between cast('$fecha1' as date) and cast('$fecha2' as date)
        ");
        return $resultado->row();
    }

    public function resumenDiario($fecha1, $fecha2){
        $resultado = $this->db->query("
            select date_format(fecha, '%d-%m-%Y') as fecha, sum(total) as totalDia 
            from salidas 
            where estado = 1
            and fecha between cast('$fecha1' as date) and cast('$fecha2' as date) 
            group by fecha
        ");
        return $resultado->result();
    }
}