<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Impuestos_model extends CI_Model {
	
	public function getImpuestos(){ 
		$this->db->where("estado",1);
		$resultados = $this->db->get("impuestos");
		return $resultados->result();
	}
	
	public function save($data){
		return $this->db->insert("impuestos",$data);
	}

	public function update($id,$data){
		$this->db->where('id_impuesto',$id);
		return $this->db->update("impuestos",$data);
	}
	
}