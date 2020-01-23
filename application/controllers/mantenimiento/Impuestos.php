<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Impuestos extends CI_Controller {
    private $permisos;
	public function __construct(){
        parent::__construct();
        if($this->session->userdata('usuario_log')=="") {
            redirect(base_url());
        } else{
            $this->permisos = $this->backend_lib->control();
            $this->load->model("Impuestos_model");
            $this->load->library('toastr');
        }
    }
    
    public function index(){
        $data = array(
            'permisos' => $this->permisos,
            'impuestos' => $this->Impuestos_model->getImpuestos(),
        );
        $this->load->view("layouts/header");
        $this->load->view('layouts/aside');
        $this->load->view("admin/impuestos/list",$data);
        $this->load->view("layouts/footer");
    }

    public function store(){
		$nombre = $this->input->post('name');
		$porcentaje = $this->input->post('porcentaje');
		$data = array(
			'nombre' => $nombre,
			'porcentaje' => $porcentaje,
		);

		$this->db->trans_start(); // ******************************************************** iniciamos transaccion **************************************
        $this->Impuestos_model->save($data);

        $this->db ->trans_complete();// ******************************************************** icompletamos transaccion **************************************
              
        if($this->db->trans_status()){ // ******************************************************** iniciamos transaccion **************************************
            $this->toastr->success('Registro guardado!');
            redirect(base_url()."mantenimiento/impuestos");
        }
        else{
            $this->toastr->error('No se pudo completar la operación.');
            redirect(base_url()."mantenimiento/impuestos");
        }

    }
    
    public function update(){
        $id = $this->input->post("id_impuesto_update");
        $nombre = $this->input->post("nombre_impuesto_update");
        $porcentaje = $this->input->post("porcentaje_impuesto_update");
        $data = array(
            'nombre' =>$nombre,
            'porcentaje' => $porcentaje,
        );
		$this->db->trans_start(); // ******************************************************** iniciamos transaccion **************************************
        $this->Impuestos_model->update($id, $data);
        $this->db ->trans_complete();// ******************************************************** icompletamos transaccion **************************************

        if ($this->db->trans_status()) {
            $this->toastr->success('Cambio guardado!');
            redirect(base_url()."mantenimiento/impuestos");
        }
        else{
            $this->toastr->error('No se pudo completar la operación.');
            redirect(base_url()."mantenimiento/impuestos");
        }

    }

    public function delete(){
        $id = $this->input->post("id_update_marca");
        $data = array(
            'estado' =>0, 
        );
        $this->db->trans_start(); // ******************************************************** iniciamos transaccion **************************************
        $this->Impuestos_model->update($id, $data);
        $this->db ->trans_complete();// ******************************************************** icompletamos transaccion **************************************

        if ($this->db->trans_status()) {
            $this->toastr->success('¡Impuesto eliminado!');
            redirect(base_url()."mantenimiento/impuestos");
        }
        else{
            $this->toastr->error('No se pudo completar la operación.');
            redirect(base_url()."mantenimiento/impuestos");
        }
    }
}