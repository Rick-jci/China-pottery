<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form extends CI_Controller {

	public function index()
	{
        $this->load->helper('url');
        $this->load_>database();
	    $this->load->view('myform');

	}
	public  function chack(){
	    $array[]=$this->db->get('');
        $name=$this->input->post('username');
        $password=$this->input->post('password');
        if (isset($name)){
            echo "<script>alert('请输入用户名');</script>";
        }
        elseif (isset($password)){
            echo "<script>alert('请输入密码');</script>";
        }
    }
}
