<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MessagesController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Message_model');
        $this->load->helper('url');
    }

    public function compose($receiver_id = null) {
        if ($this->session->userdata('user_login') != 1)
            redirect(site_url('login'), 'refresh');

        $current_user = $this->session->userdata('user_id');
        $current_user_type = $this->db->get_where('users', ['user_id' => $current_user])->row()->type;

        if ($current_user_type == 0) {
            $admin = $this->db->get_where('users', ['type' => 1])->row();
            $receiver_id = $admin ? $admin->user_id : null;
        }

        $page_data['receiver_id'] = $receiver_id;
        $this->load->view('compose_message', $page_data);
    }

    public function send_message($sender_id, $receiver_id) {
        $receiver_type = $this->db->get_where('users', ['user_id' => $receiver_id])->row()->type;
        $sender_type = $this->db->get_where('users', ['user_id' => $sender_id])->row()->type;

        if (($sender_type == 0 && $receiver_type == 1) || ($sender_type == 1 && $receiver_type == 0)) {
            $data = [
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'message' => $this->input->post('message'),
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('messages', $data);
            $this->session->set_flashdata('success', 'تم إرسال الرسالة بنجاح.');
        } else {
            $this->session->set_flashdata('error', 'غير مسموح لك بإرسال هذه الرسالة.');
        }

        redirect(site_url('messages/compose/' . $receiver_id));
    }
}
?>
