<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class General extends CI_Controller {

	// constructor
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');  // تأكد من أنك قمت بتحميل مكتبة الجلسات


		$this->load->model('crud_model');
	}
	
	public function index()
	{
	}
	
	function faq()
	{
		$page_data['page_name']		=	'faq';
		$page_data['page_title']	=	'Frequently Asked Questions';
		$this->load->view('frontend/index', $page_data);
		
	}
	public function compose() {
		$user_id = $this->session->userdata('user_id');
		$admin_id = 1; // ثابت أو حسب الحالة
	
		// جلب الرسائل بين المستخدم والمدير
		$this->db->where("(sender_id = $user_id AND receiver_id = $admin_id) OR (sender_id = $admin_id AND receiver_id = $user_id)");
		$this->db->order_by('timestamp', 'ASC');
		$page_data['messages'] = $this->db->get('messages')->result();
		$page_data['page_name']		=	'compose_message';
		$page_data['page_title'] = 'مراسلة الإدارة';
		$page_data['admin_id'] = $admin_id;
		$this->load->view('frontend/index', $page_data); // أو حسب مسارك
	}
	

	public function admin_messages() {
		$admin_id = $this->session->userdata('user_id');
	
		// نجلب آخر رسالة من كل مرسل (group by sender)
		$sql = "SELECT * FROM messages 
				WHERE receiver_id = ? AND parent_id IS NULL 
				GROUP BY sender_id 
				ORDER BY MAX(timestamp) DESC";
		
		$page_data['messages'] = $this->db->query($sql, [$admin_id])->result();
	
		$page_data['page_name']  = 'admin_messages';
		$page_data['page_title'] = 'Inbox - Admin';
		$this->load->view('backend/index', $page_data);
	}
	
	// public function get_conversation($user_id) {
	// 	// جلب كل الرسائل بين المدير والمستخدم
	// 	$this->db->where('(sender_id = 1 AND receiver_id = ' . $user_id . ') OR (sender_id = ' . $user_id . ' AND receiver_id = 1)');
	// 	$this->db->order_by('timestamp', 'ASC');
	// 	$messages = $this->db->get('messages')->result();
	
	// 	foreach ($messages as $msg) {
	// 		echo '<div style="margin-bottom: 15px;">
	// 			<div style="
	// 				padding: 10px;
	// 				border-radius: 10px;
	// 				max-width: 70%;
	// 				' . ($msg->sender_id == 1 ? 'background-color: #d1e7dd; margin-left: auto; text-align: right;' : 'background-color: #f8d7da; margin-right: auto; text-align: left;') . '
	// 			">
	// 				<strong>' . ($msg->sender_id == 1 ? 'Admin' : 'User #' . $msg->sender_id) . ':</strong><br>'
	// 				. nl2br($msg->message) . '<br>
	// 				<small>' . date('Y-m-d H:i:s', $msg->timestamp) . '</small>
	// 			</div>
	// 		</div>';
	// 	}
	// }
	
	function messages()
{
    // تأكد أن المستخدم مسجل دخول
    if (!$this->session->userdata('user_id')) {
        redirect(site_url('login'), 'refresh'); // أو أي صفحة تسجيل دخول
    }

    $user_id = $this->session->userdata('user_id');
	echo 'User ID: ' . $user_id;

    // جلب الرسائل التي أرسلها أو استلمها المستخدم
    $this->db->where('sender_id', $user_id);
    $this->db->or_where('receiver_id', $user_id);
    $this->db->order_by('timestamp', 'DESC');
    $messages = $this->db->get('messages')->result();

    $page_data['messages'] = $messages;

    $page_data['page_name']   = 'messages';
    $page_data['page_title']  = 'My Messages';

    $this->load->view('frontend/index', $page_data);
}


public function view_conversation($user_id) {
    $admin_id = $this->session->userdata('user_id');

    // جميع الرسائل بين المدير وهذا المستخدم
    $this->db->where("(sender_id = $user_id AND receiver_id = $admin_id) OR (sender_id = $admin_id AND receiver_id = $user_id)");
    $this->db->order_by('timestamp', 'ASC');
    $page_data['messages'] = $this->db->get('messages')->result();

    $page_data['user_id'] = $user_id;
    $page_data['page_name'] = 'conversation';
    $page_data['page_title'] = 'Chat with User #' . $user_id;

    $this->load->view('backend/pages/conversation', $page_data);
}
	
public function view($user_id) {
    $admin_id = 1;

    // جلب كل الرسائل بين المدير والمستخدم
    $this->db->where("(sender_id = $admin_id AND receiver_id = $user_id) OR (sender_id = $user_id AND receiver_id = $admin_id)");
    $this->db->order_by('timestamp', 'ASC');
    $messages = $this->db->get('messages')->result();

    $this->load->view('backend/pages/view_message', [
        'messages' => $messages,
        'user_id' => $user_id
    ]);
}

 
public function send_reply() {
    $recipient_id = $this->input->post('recipient_id');
    $reply_message = $this->input->post('reply');
    //$sender_id = $this->session->userdata('user_id'); // من الجلسة

    // تأكيد على وجود رسالة
    if (empty($reply_message) || !$recipient_id) {
        $this->session->set_flashdata('error_message', 'يجب كتابة الرد.');
        redirect(site_url('index.php?general/view_conversation/' . $recipient_id), 'refresh');
        return;
    }

    // تجهيز البيانات
    $data = [
        'sender_id'   => 1,
        'receiver_id' => $recipient_id,
        'subject'     => 'رد على محادثة',
        'message'     => $reply_message,
        'timestamp'   => time(),
        'is_read'     => 0
    ];

    $this->db->insert('messages', $data);

    // إعادة توجيه لنفس المحادثة
    redirect(site_url('index.php?general/view_conversation/' . $recipient_id), 'refresh');
}






	function refundpolicy()
	{
		$page_data['page_name']		=	'refundpolicy';
		$page_data['page_title']	=	'Refund Policy';
		$this->load->view('frontend/index', $page_data);
		
	}
	public function send() {
		$sender_id = $this->session->userdata('user_id');
		//$receiver_id = $this->input->post('receiver_id');
		$message = $this->input->post('message');
	
		$data = [
			'sender_id' => $sender_id,
			'receiver_id' => 1,
			'subject' => '', // إذا ما في موضوع
			'message' => $message,
			'timestamp' => time()
		];
	
		$this->db->insert('messages', $data);
	
		// الرجوع لنفس صفحة المحادثة
		redirect(site_url('index.php?general/compose'), 'refresh');
	}
	
	
	
	function privacypolicy()
	{
		$page_data['page_name']		=	'privacypolicy';
		$page_data['page_title']	=	'Privacy Policy';
		$this->load->view('frontend/index', $page_data);
		
	}

	function cookie_policy()
	{
		$page_data['page_name']		=	'cookie_policy';
		$page_data['page_title']	=	get_phrase('cookie_policy');
		$this->load->view('frontend/index', $page_data);
		
	}
	
	


}
