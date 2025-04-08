<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_messages($user_id) {
        $this->db->where('sender_id', $user_id);
        $this->db->or_where('receiver_id', $user_id);
        return $this->db->get('messages')->result_array();
    }
}
?>
