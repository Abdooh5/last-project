<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messaging_model extends CI_Model {

    public function get_all_messages() {
        return $this->db->get('messages')->result_array();
    }

    public function send_message($data) {
        $this->db->insert('messages', $data);
    }

    public function get_messages_between($user1, $user2) {
        $this->db->where("(sender_id = $user1 AND receiver_id = $user2) OR (sender_id = $user2 AND receiver_id = $user1)");
        return $this->db->get('messages')->result_array();
    }
}
