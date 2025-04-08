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
public function send() {
    // فقط للمستخدمين المسجلين
    if (!$this->session->userdata('user_login')) {
        redirect(site_url('login'), 'refresh');
    }

    $page_data['page_title'] = 'إرسال رسالة إلى الإدارة';
    $page_data['page_name']  = 'send_message';
    $this->load->view('frontend/index', $page_data);
}

public function send_message() {
    if (!$this->session->userdata('user_login')) {
        redirect(site_url('login'), 'refresh');
    }

    $data['sender_id'] = $this->session->userdata('user_id');
    $data['receiver_id'] = 1; // الأدمن معرفه 1
    $data['subject'] = $this->input->post('subject');
    $data['message'] = $this->input->post('message');
    $data['timestamp'] = time();

    $this->Messaging_model->send_message($data);

    $this->session->set_flashdata('flash_message', 'تم إرسال الرسالة بنجاح');
    redirect(site_url('addons/messaging/send'), 'refresh');
}

}
