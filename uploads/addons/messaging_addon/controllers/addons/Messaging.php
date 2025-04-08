<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messaging extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('addons/Messaging_model');
    }

    public function admin_view() {
        $page_data['page_name'] = 'messaging';
        $page_data['page_title'] = 'Messaging System';
        $page_data['messages'] = $this->Messaging_model->get_all_messages();
        $this->load->view('backend/index', $page_data);
    }

    public function send_message() {
        $data['sender_id'] = $this->input->post('sender_id');
        $data['receiver_id'] = $this->input->post('receiver_id');
        $data['message'] = $this->input->post('message');
        $this->Messaging_model->send_message($data);
        redirect($_SERVER['HTTP_REFERER']);
    }
}
