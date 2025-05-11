<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Crud_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

	/*
	* SETTINGS QUERIES
	*/
	function get_settings($type)
	{
		$description	=	$this->db->get_where('settings', array('type'=>$type))->row()->description;
		return $description;
	}
	public function send_message($data) {
		// إضافة الرسالة إلى قاعدة البيانات
		$this->db->insert('messages', $data);
	}
	
	/*
	* PLANS QUERIES
	*/

	// function get_active_plans()
	// {
	// 	$this->db->where('status', 1);
	// 	$query 		=	 $this->db->get('plan');
    //     return $query->result_array();
	// }

	function get_active_theme()
	{
		$theme	=	$this->get_settings('theme');
		return $theme;
	}

	/*
	* check if a video should be embedded in iframe or in jwplayer
	* if the video is youtube url, it will go for jwplayer
	* if the video has .mp4 extension, it will go for jwplayer
	* else all videos will go for iframe embedding option
	*/
	function is_iframe($video_url)
	{
		$iframe_embed	=	true;
		if (strpos($video_url, 'youtube.com')) {
			$iframe_embed = false;
		}

		$path_info 		=	pathinfo($video_url);
		$extension		=	$path_info['extension'];
		if ($extension == 'mp4') {
			$iframe_embed = false;
		}
		return $iframe_embed;
	}

	/*
	* USER QUERIES
	*/
	function signup_user()
	{
		$data['email'] = $this->input->post('email');
		$data['password'] = sha1($this->input->post('password'));
		$data['status'] = 1;  // تفعيل الحساب مباشرة
	
		// التحقق إذا كان البريد الإلكتروني موجودًا بالفعل
		$existing_email = $this->db->get_where('user', array('email' => $data['email']))->num_rows();
		if ($existing_email > 0) {
			return false; // البريد الإلكتروني موجود بالفعل
		}
	
		// إدخال المستخدم في قاعدة البيانات
		$insert = $this->db->insert('user', $data);
	
		if (!$insert) {
			return false; // فشل الإدخال
		}
	
		// الحصول على معرف المستخدم بعد الإدخال
		$user_id = $this->db->insert_id();
	
		// إنشاء اشتراك تجريبي إذا تم تمكينه
		// $trial_period = $this->crud_model->get_settings('trial_period');
		// if ($trial_period == 'on') {
		// 	$this->create_free_subscription($user_id); // إنشاء الاشتراك المجاني
		// }
	
		// إرجاع بيانات المستخدم بعد التسجيل
		return array(
			'user_id' => $user_id,
			'email' => $data['email']
		);
	}
	
	
	


	// create a free subscription for premium package for 30 days
	// function create_free_subscription($user_id = '')
	// {
	// 	$trial_period_days			=	$this->get_settings('trial_period_days');
	// 	$increment_string			=	'+' . $trial_period_days . ' days';

	// 	$data['plan_id']			=	3;
	// 	$data['user_id']			=	$user_id;
	// 	$data['paid_amount']		=	0;
	// 	$data['payment_timestamp']	=	strtotime(date("Y-m-d H:i:s"));
	// 	$data['timestamp_from']		=	strtotime(date("Y-m-d H:i:s"));
	// 	$data['timestamp_to']		=	strtotime($increment_string, $data['timestamp_from']);
	// 	$data['payment_method']		=	'FREE';
	// 	$data['payment_details']	=	'';
	// 	$data['status']				=	1;
	// 	$this->db->insert('subscription' , $data);
	// }



	function system_currency(){
		$data['description'] = html_escape($this->input->post('system_currency'));
        $this->db->where('type', 'system_currency');
        $this->db->update('settings', $data);

        $data['description'] = html_escape($this->input->post('currency_position'));
        $this->db->where('type', 'currency_position');
        $this->db->update('settings', $data);
	}

	// update paypal keys
	function update_paypal_keys() {

        $paypal_info = array();

        $paypal['active'] = $this->input->post('paypal_active');
        $paypal['mode'] = $this->input->post('paypal_mode');
        $paypal['sandbox_client_id'] = $this->input->post('sandbox_client_id');
        $paypal['production_client_id'] = $this->input->post('production_client_id');

        $paypal['sandbox_secret_key'] = $this->input->post('sandbox_secret_key');
        $paypal['production_secret_key'] = $this->input->post('production_secret_key');

        array_push($paypal_info, $paypal);

        $data['description']    =   json_encode($paypal_info);
        $this->db->where('type', 'paypal');
        $this->db->update('settings', $data);

        $data['description'] = html_escape($this->input->post('paypal_currency'));
        $this->db->where('type', 'paypal_currency');
        $this->db->update('settings', $data);
    }

    // update stripe keys
    function update_stripe_keys(){
        $stripe_info = array();

        $stripe['active'] = $this->input->post('stripe_active');
        $stripe['testmode'] = $this->input->post('testmode');
        $stripe['public_key'] = $this->input->post('public_key');
        $stripe['secret_key'] = $this->input->post('secret_key');
        $stripe['public_live_key'] = $this->input->post('public_live_key');
        $stripe['secret_live_key'] = $this->input->post('secret_live_key');


        array_push($stripe_info, $stripe);

        $data['description']    =   json_encode($stripe_info);
        $this->db->where('type', 'stripe_keys');
        $this->db->update('settings', $data);

        $data['description'] = html_escape($this->input->post('stripe_currency'));
        $this->db->where('type', 'stripe_currency');
        $this->db->update('settings', $data);
    }

    function get_currencies() {
      return $this->db->get('currency')->result_array();
    }

    function get_paypal_supported_currencies() {
      $this->db->where('paypal_supported', 1);
      return $this->db->get('currency')->result_array();
    }

    function get_stripe_supported_currencies() {
      $this->db->where('stripe_supported', 1);
      return $this->db->get('currency')->result_array();
    }


    public function update_smtp_settings() {
        $data['description'] = html_escape($this->input->post('protocol'));
        $this->db->where('type', 'protocol');
        $this->db->update('settings', $data);

        $data['description'] = html_escape($this->input->post('smtp_host'));
        $this->db->where('type', 'smtp_host');
        $this->db->update('settings', $data);

        $data['description'] = html_escape($this->input->post('smtp_port'));
        $this->db->where('type', 'smtp_port');
        $this->db->update('settings', $data);

        $data['description'] = html_escape($this->input->post('smtp_user'));
        $this->db->where('type', 'smtp_user');
        $this->db->update('settings', $data);

        $data['description'] = html_escape($this->input->post('smtp_pass'));
        $this->db->where('type', 'smtp_pass');
        $this->db->update('settings', $data);
    }


	public function check_recaptcha(){
        if (isset($_POST["g-recaptcha-response"])) {
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = array(
                'secret' => get_settings('recaptcha_secretkey'),
                'response' => $_POST["g-recaptcha-response"]
            );
                $query = http_build_query($data);
                $options = array(
                'http' => array (
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                        "Content-Length: ".strlen($query)."\r\n".
                        "User-Agent:MyAgent/1.0\r\n",
                    'method' => 'POST',
                    'content' => $query
                )
            );
            $context  = stream_context_create($options);
            $verify = file_get_contents($url, false, $context);
            $captcha_success = json_decode($verify);
            if ($captcha_success->success == false) {
                return false;
            } else if ($captcha_success->success == true) {
                return true;
            }
        } else {
            return false;
        }
    }













	function signin($email, $password)
	{
		$credential = array('email' => $email, 'password' => sha1($password), 'status' => 1);
		$query = $this->db->get_where('user', $credential);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $this->session->set_userdata('user_login_status', '1');
            $this->session->set_userdata('user_id', $row->user_id);
            $this->session->set_userdata('login_type', $row->type); // 1=admin, 0=customer
            return true;
        }
		else {
			$this->session->set_flashdata('signin_result', 'failed');
			return false;
		}
	}

	// returns currently active subscription_id, or false if no active found
	function validate_subscription()
	{
		return true;
	}

	function get_subscription_detail($subscription_id)
	{
		// $this->db->where('subscription_id', $subscription_id);
		// $query 		=	 $this->db->get('subscription');
        // return $query->result_array();
	}

	function get_current_plan_id()
	{
		// // CURRENT SUBSCRIPTION ID
		// $subscription_id			=	true;
		// // CURRENT SUBSCCRIPTION DETAIL
		// $subscription_detail		=	$this->crud_model->get_subscription_detail($subscription_id);
		// foreach ($subscription_detail as $row)
		// 	$current_plan_id		=	$row['plan_id'];
		// return $current_plan_id;
	}

	// function get_subscription_of_user($user_id = '')
	// {
	// 	$this->db->where('user_id', $user_id);
    //     $query = $this->db->get('subscription');
    //     return $query->result_array();
	// }

	// function get_active_plan_of_user($user_id = '')
	// {
	// 	$timestamp_current	=	strtotime(date("Y-m-d H:i:s"));
	// 	$this->db->where('user_id', $user_id);
	// 	$this->db->where('timestamp_to >' ,  $timestamp_current);
	// 	$this->db->where('timestamp_from <' ,  $timestamp_current);
	// 	$this->db->where('status' ,  1);
	// 	$query				=	$this->db->get('subscription');
	// 	if ($query->num_rows() > 0) {
    //         $row = $query->row();
	// 		$subscription_id	=	$row->subscription_id;
	// 		return $subscription_id;
	// 	}
    //     else if ($query->num_rows() == 0) {
	// 		return false;
	// 	}
	// }

	// function get_subscription_report($month, $year)
	// {
	// 	$first_day_this_month 			= 	date('01-m-Y' , strtotime($month." ".$year));
	// 	$last_day_this_month  			= 	date('t-m-Y' , strtotime($month." ".$year));
	// 	$timestamp_first_day_this_month	=	strtotime($first_day_this_month);
	// 	$timestamp_last_day_this_month	=	strtotime($last_day_this_month);

	// 	$this->db->where('payment_timestamp >' , $timestamp_first_day_this_month);
	// 	$this->db->where('payment_timestamp <' , $timestamp_last_day_this_month);
	// 	$subscriptions = $this->db->get('subscription')->result_array();

	// 	return $subscriptions;
	// }

	function get_current_user_detail()
	{
		$user_id	=	$this->session->userdata('user_id');
		$user_detail=	$this->db->get_where('user', array('user_id'=>$user_id))->row();
		return $user_detail;
	}

	function get_username_of_user($user_number)
	{
		$user_id	=	$this->session->userdata('user_id');
		$username	=	$this->db->get_where('user', array('user_id'=>$user_id))->row()->$user_number;
		return $username;
	}

    function get_image_url_of_user($user_number)
    {
        $user_id	=	$this->session->userdata('user_id');
        if (file_exists('assets/global/user_thumb/'.$user_id.'_'.$user_number.'.jpg')) {
            return base_url('assets/global/user_thumb/'.$user_id.'_'.$user_number.'.jpg');
        }
         else{
            $user_exploded = explode('user', $user_number);
            if (file_exists('assets/global/thumb'.$user_exploded[1].'.png')) {
                return base_url('assets/global/thumb'.$user_exploded[1].'.png');
            }else{
                return base_url('assets/global/thumb3.png');
           }
        }
    }

	function get_genres()
	{
		$query 		=	 $this->db->get('genre');
        return $query->result_array();
	}

	function get_countries()
	{
		$query 		=	 $this->db->get('country');
        return $query->result_array();
	}

	function paginate($base_url, $total_rows, $per_page, $uri_segment)
	{
        $config = array('base_url' => $base_url,
            'total_rows' => $total_rows,
            'per_page' => $per_page,
            'uri_segment' => $uri_segment);

        $config['first_link'] = '<i class="fa fa-angle-double-left" aria-hidden="true"></i>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = '<i class="fa fa-angle-double-right" aria-hidden="true"></i>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = '<i class="fa fa-angle-right" aria-hidden="true"></i>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = '<i class="fa fa-angle-left" aria-hidden="true"></i>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        return $config;
    }

	function get_movies($genre_id, $limit = NULL, $offset = 0)
	{

        $this->db->order_by('movie_id', 'desc');
        $this->db->where('genre_id', $genre_id);
        $query = $this->db->get('movie', $limit, $offset);
        return $query->result_array();
    }
	function get_movies_by_country($country_id, $limit, $offset) {
		$this->db->where('country_id', $country_id);
		$this->db->limit($limit, $offset);
		return $this->db->get('movie')->result_array();
	}
	function download_image($url, $save_path) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // مهم لعدم التحقق من شهادة SSL
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$data = curl_exec($ch);
		$error = curl_error($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	
		if ($http_code == 200 && !empty($data)) {
			$saved = file_put_contents($save_path, $data);
			if ($saved === false) {
				echo "❌ فشل حفظ الصورة في $save_path<br>";
			}
			return true;
		} else {
			echo "❌ فشل تحميل الصورة من الرابط: $url<br>";
			echo "كود HTTP: $http_code<br>";
			echo "خطأ cURL: $error<br>";
			return false;
		}
	}
	
	function create_movie() {
		// البيانات الأساسية
		$data['title']             = $this->input->post('title');
		//$data['description_short'] = $this->input->post('description_short');
		$data['description_long']  = $this->input->post('description_long');
		$data['year']              = $this->input->post('year');
		$data['rating']            = $this->input->post('rating');
		$data['featured']          = $this->input->post('featured');
		$data['trailer_url']       = $this->input->post('trailer_url');
	
		// تحويل مدة الفيلم إلى ثواني
		$duration = $this->input->post('duration');
		$duration = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $duration);
		sscanf($duration, "%d:%d:%d", $hours, $minutes, $seconds);
		$data['duration'] = $hours * 3600 + $minutes * 60 + $seconds;
	
		// معالجة النوع
		$genre_ids = $this->input->post('genre_id');

if (!empty($genre_ids)) {
	$data['genre_id'] = json_encode($genre_ids);
} elseif ($this->input->post('genre_names')) {
	$genre_names = $this->input->post('genre_names');
	$genre_ids = [];
	foreach ($genre_names as $genre_name) {
		$genre = $this->db->get_where('genre', ['name' => $genre_name])->row_array();
		if ($genre) {
			$genre_ids[] = $genre['genre_id'];
		}
	}
	$data['genre_id'] = json_encode($genre_ids);
}

	
		// معالجة الدولة
		$country_id = $this->input->post('country_id');
		if (!$country_id && $this->input->post('country_name')) {
			$country_name = trim($this->input->post('country_name'));
			$country = $this->db->get_where('country', ['name' => $country_name])->row_array();
			if ($country) {
				$data['country_id'] = $country['country_id'];
			}
		} else {
			$data['country_id'] = $country_id;
		}
	
		// الممثلين
		$actors = $this->input->post('actors');
		$actor_ids = [];
	
		if (!empty($actors)) {
			$actor_ids = $actors;
		} elseif ($this->input->post('actor_names')) {
			foreach ($this->input->post('actor_names') as $actor_name) {
				$actor = $this->db->get_where('actor', ['name' => $actor_name])->row_array();
				if ($actor) {
					$actor_ids[] = $actor['actor_id'];
				}
			}
		}
	
		$data['actors'] = json_encode($actor_ids);
	
		// إدخال الفيلم
		$this->db->insert('movie', $data);
		$movie_id = $this->db->insert_id();
	
		// ✅ تحميل البوستر باستخدام cURL
		$poster_url = $this->input->post('poster_url');

		if (!empty($poster_url)) {
			log_message('error', '🔍 رابط الصورة المستلم: ' . $poster_url);
		
			$ch = curl_init($poster_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		
			$poster_data = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
		
			log_message('error', '🧾 كود HTTP: ' . $http_code);
			log_message('error', '📦 حجم الصورة المستلمة: ' . strlen($poster_data));
		
			if ($poster_data !== false && $http_code == 200) {
				file_put_contents('assets/global/movie_poster/' . $movie_id . '.jpg', $poster_data);
				file_put_contents('assets/global/movie_thumb/' . $movie_id . '.jpg', $poster_data);
				log_message('error', '✅ تم حفظ الصورة بنجاح');
			} else {
				log_message('error', "❌ فشل تحميل الصورة عبر cURL. HTTP Code: $http_code");
			}
		}
		
		
	
		// رفع فيديو الإعلان trailer
		if (isset($_FILES['trailer_url']) && $_FILES['trailer_url']['error'] == 0) {
			$trailer_name = $_FILES['trailer_url']['name'];
			$trailer_path = 'assets/global/movie_trailer/' . $trailer_name;
			move_uploaded_file($_FILES['trailer_url']['tmp_name'], $trailer_path);
			$this->db->update('movie', ['trailer_url' => $trailer_name], ['movie_id' => $movie_id]);
		}
	
		// رفع الفيلم الرئيسي url
		if (isset($_FILES['url']) && $_FILES['url']['error'] == 0) {
			$video_name = $_FILES['url']['name'];
			$video_path = 'assets/global/movie_video/' . $video_name;
			move_uploaded_file($_FILES['url']['tmp_name'], $video_path);
			$this->db->update('movie', ['url' => $video_name], ['movie_id' => $movie_id]);
		}
	}
	
	
	
	

		function update_movie($movie_id = '') {
		

			// جلب القيم الحالية للفيلم من قاعدة البيانات
			$current_movie = $this->db->get_where('movie', ['movie_id' => $movie_id])->row_array();
		
			// تجهيز البيانات للتحديث فقط إذا تم تغييرها
			$data = [];
		
			if ($this->input->post('title')) {
				$data['title'] = $this->input->post('title');
			}
		
			// if ($this->input->post('description_short')) {
			// 	$data['description_short'] = $this->input->post('description_short');
			// }
		
			if ($this->input->post('description_long')) {
				$data['description_long'] = $this->input->post('description_long');
			}
		
			if ($this->input->post('year')) {
				$data['year'] = $this->input->post('year');
			}
		
			if ($this->input->post('rating')) {
				$data['rating'] = $this->input->post('rating');
			}
		
			if ($this->input->post('country_id')) {
				$data['country_id'] = $this->input->post('country_id');
			}
		
			if ($this->input->post('genre_id')) {
    $genre_input = $this->input->post('genre_id');
    if (is_array($genre_input)) {
        $data['genre_id'] = json_encode($genre_input);
    } else {
        // في حال تم إرسالها كسلسلة (سهوًا أو بسبب تعديل جافاسكريبت مثلًا)
        $data['genre_id'] = json_encode([$genre_input]);
    }
}

			
		
			if ($this->input->post('featured')) {
				$data['featured'] = $this->input->post('featured');
			}
		
		
		
			// معالجة مدة الفيلم فقط إذا تم تغييرها
			if ($this->input->post('duration')) {
				$duration = $this->input->post('duration');
				$duration = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $duration);
				sscanf($duration, "%d:%d:%d", $hours, $minutes, $seconds);
				$data['duration'] = $hours * 3600 + $minutes * 60 + $seconds;
			}
		
			// تحديث الممثلين إذا تم إدخال بيانات جديدة
			if ($this->input->post('actors')) {
				$actors = $this->input->post('actors');
				$data['actors'] = json_encode($actors);
			}
		
			// تحديث قاعدة البيانات بالقيم الجديدة فقط
			if (!empty($data)) {
				$this->db->update('movie', $data, ['movie_id' => $movie_id]);
			}
		
			// تحديث الصور والفيديو فقط إذا تم رفع ملفات جديدة
			if (isset($_FILES['thumb']) && $_FILES['thumb']['error'] == 0) {
				move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/movie_thumb/' . $movie_id . '.jpg');
			}
		
			if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
				move_uploaded_file($_FILES['poster']['tmp_name'], 'assets/global/movie_poster/' . $movie_id . '.jpg');
			}
		
			if (isset($_FILES['url']) && $_FILES['url']['error'] == 0) {
				$video_name = $_FILES['url']['name'];
				$video_path = 'assets/global/movie_video/' . $video_name;
				move_uploaded_file($_FILES['url']['tmp_name'], $video_path);
		
				// تحديث قاعدة البيانات باسم الفيديو الجديد
				$this->db->update('movie', ['url' => $video_name], ['movie_id' => $movie_id]);
			}
			if (isset($_FILES['trailer_url']) && $_FILES['trailer_url']['error'] == 0) {
				$trailer_name = $_FILES['trailer_url']['name'];
				$video_path = 'assets/global/movie_trailer/' . $trailer_name;
				move_uploaded_file($_FILES['trailer_url']['tmp_name'], $video_path);
		
				// تحديث قاعدة البيانات باسم الفيديو الجديد
				$this->db->update('movie', ['trailer_url' => $trailer_name], ['movie_id' => $movie_id]);
			}
		}
		
	
	// function add_subtitle($param1 = ""){
	// 	$data['movie_id'] = $param1;
	// 	$data['language'] = $this->input->post('language');
	// 	$data['file']	  = $this->input->post('language').'-'.$param1.'.vtt';
	// 	$this->db->insert('subtitle', $data);
	// 	move_uploaded_file($_FILES['file']['tmp_name'], 'assets/global/movie_caption/'.$this->input->post('language').'-'.$param1 . '.vtt');
	// }

	// function edit_subtitle($param1 = "", $param2 = ""){
	// 	$data['language'] = $this->input->post('language');
	// 	$data['file']	  = $this->input->post('language').'-'.$param2.'.vtt';
	// 	$this->db->where('id', $param1);
	// 	$this->db->update('subtitle', $data);
	// 	move_uploaded_file($_FILES['file']['tmp_name'], 'assets/global/movie_caption/'.$this->input->post('language').'-'.$param2 . '.vtt');
	// }

function create_series()
{
	$data['title']              = $this->input->post('title');
	$data['description_short']  = $this->input->post('description_short');
	$data['description_long']   = $this->input->post('description_long');
	$data['year']               = $this->input->post('year');
	$data['rating']             = $this->input->post('rating');
	$data['country_id']         = $this->input->post('country_id');
	$data['genre_id']           = $this->input->post('genre_id');
	$data['category']           = $this->input->post('category');
	$actors                     = $this->input->post('actors');
	$actor_entries              = array();

	foreach ($actors as $actor) {
		array_push($actor_entries, $actor);
	}
	$data['actors'] = json_encode($actor_entries);

	// إدخال بيانات المسلسل في قاعدة البيانات
	$this->db->insert('series', $data);
	$series_id = $this->db->insert_id();

	// إنشاء مجلد رئيسي للمسلسلات إن لم يكن موجود
	$base_series_folder = 'assets/global/series';
	if (!is_dir($base_series_folder)) {
		mkdir($base_series_folder, 0777, true);
	}
$series_title_raw = $data['title'];
$series_folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $series_title_raw);
$series_folder_path = $base_series_folder . '/' . $series_folder_name;
	// إنشاء مجلد باسم المسلسل داخل مجلد المسلسلات
	
	if (!is_dir($series_folder_path)) {
		mkdir($series_folder_path, 0777, true);
	}

	// نقل الصور إلى مجلد المسلسل
	move_uploaded_file($_FILES['thumb']['tmp_name'], $series_folder_path . '/thumb.jpg');
	move_uploaded_file($_FILES['poster']['tmp_name'], $series_folder_path . '/poster.jpg');

	// رفع فيديو التريلر
	if (isset($_FILES['series_trailer_url']) && $_FILES['series_trailer_url']['error'] == 0) {
		$trailer_name = $_FILES['series_trailer_url']['name'];
		$trailer_path = $series_folder_path . '/' . $trailer_name;
		move_uploaded_file($_FILES['series_trailer_url']['tmp_name'], $trailer_path);

		// تحديث مسار التريلر في قاعدة البيانات
		$this->db->update('series', ['trailer_url'=> $trailer_name], ['series_id' => $series_id]);
	} else {
		echo "Error uploading trailer video.";
		return;
	}
}


	// function create_series()
	// {
	// 	$data['title']				=	$this->input->post('title');
	// 	$data['description_short']	=	$this->input->post('description_short');
	// 	$data['description_long']	=	$this->input->post('description_long');
	// 	$data['year']				=	$this->input->post('year');
	// 	$data['rating']				=	$this->input->post('rating');
	// 	$data['country_id']			=	$this->input->post('country_id');
	// 	$data['genre_id']			=	$this->input->post('genre_id');
	// 	$data['category']			=	$this->input->post('category');
	// 	$actors						=	$this->input->post('actors');
	// 	$actor_entries				=	array();
	// 	$number_of_entries			=	sizeof($actors);
	// 	for ($i = 0; $i < $number_of_entries ; $i++)
	// 	{
	// 		array_push($actor_entries, $actors[$i]);
	// 	}
	// 	$data['actors']				=	json_encode($actor_entries);

	// 	$this->db->insert('series', $data);
	// 	$series_id = $this->db->insert_id();

	// 	move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/series_thumb/' . $series_id . '.jpg');

	// 	move_uploaded_file($_FILES['poster']['tmp_name'], 'assets/global/series_poster/' . $series_id . '.jpg');


	// 	if (isset($_FILES['series_trailer_url']) && $_FILES['series_trailer_url']['error'] == 0) {
	// 		$trailer_name = $_FILES['series_trailer_url']['name']; // اسم الملف الأصلي
	// 		$trailer_path = 'assets/global/series_trailer/' . $trailer_name;
		
	// 		// رفع الملف إلى المجلد المحدد
	// 		move_uploaded_file($_FILES['series_trailer_url']['tmp_name'], $trailer_path);
		
	// 		// الآن نقوم بتحديث قاعدة البيانات باستخدام اسم الملف الأصلي
	// 		$this->db->update('series', ['trailer_url'=> $trailer_name], ['series_id' => $series_id]);
	// 	} else {
	// 		echo "Error uploading trailer video.";
	// 		return;
	// 	}


	// }

	// function update_series($series_id = '')
	// {
	// 	$data['title']				=	$this->input->post('title');
	// 	$data['description_short']	=	$this->input->post('description_short');
	// 	$data['description_long']	=	$this->input->post('description_long');
	// 	$data['year']				=	$this->input->post('year');
	// 	$data['rating']				=	$this->input->post('rating');
	// 	$data['country_id']			=	$this->input->post('country_id');
	// 	$data['genre_id']			=	$this->input->post('genre_id');
	// 	$data['category']			=	$this->input->post('category');
	// 	$actors						=	$this->input->post('actors');
	// 	$actor_entries				=	array();
	// 	$number_of_entries			=	sizeof($actors);
	// 	for ($i = 0; $i < $number_of_entries ; $i++)
	// 	{
	// 		array_push($actor_entries, $actors[$i]);
	// 	}
	// 	$data['actors']				=	json_encode($actor_entries);

	// 	$this->db->update('series', $data, array('series_id'=>$series_id));
	// 	move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/series_thumb/' . $series_id . '.jpg');
	// 	move_uploaded_file($_FILES['poster']['tmp_name'], 'assets/global/series_poster/' . $series_id . '.jpg');


	// 	if (isset($_FILES['series_trailer_url']) && $_FILES['series_trailer_url']['error'] == 0) {
	// 		$trailer_name = $_FILES['series_trailer_url']['name']; // اسم الملف الأصلي
	// 		$trailer_path = 'assets/global/series_trailer/' . $trailer_name;
		
	// 		// رفع الملف إلى المجلد المحدد
	// 		move_uploaded_file($_FILES['series_trailer_url']['tmp_name'], $trailer_path);
		
	// 		// الآن نقوم بتحديث قاعدة البيانات باستخدام اسم الملف الأصلي
	// 		$this->db->update('series', ['trailer_url'=> $trailer_name], ['series_id' => $series_id]);
	// 	} else {
	// 		echo "Error uploading trailer video.";
	// 		return;
	// 	}

	// }

	function update_series($series_id = '')
{
	// 1. جمع البيانات
	$data['title'] = $this->input->post('title');
	$data['description_short'] = $this->input->post('description_short');
	$data['description_long'] = $this->input->post('description_long');
	$data['year'] = $this->input->post('year');
	$data['rating'] = $this->input->post('rating');
	$data['country_id'] = $this->input->post('country_id');
	$data['genre_id'] = $this->input->post('genre_id');
	$data['category'] = $this->input->post('category');
	
	$actors = $this->input->post('actors');
	$actor_entries = array();
	foreach ($actors as $actor) {
		$actor_entries[] = $actor;
	}
	$data['actors'] = json_encode($actor_entries);

	// 2. تحديث بيانات المسلسل في قاعدة البيانات
	$this->db->update('series', $data, array('series_id' => $series_id));

	// 3. تجهيز اسم المجلد الخاص بالمسلسل
	$series_folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $data['title']);
	$series_folder_path = 'assets/global/series/' . $series_folder_name;

	if (!is_dir($series_folder_path)) {
		mkdir($series_folder_path, 0777, true);
	}

	// 4. رفع صورة الغلاف (thumb)
	if (isset($_FILES['thumb']) && $_FILES['thumb']['error'] == 0) {
		move_uploaded_file($_FILES['thumb']['tmp_name'], $series_folder_path . '/thumb.jpg');
	}

	// 5. رفع صورة البوستر (poster)
	if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
		move_uploaded_file($_FILES['poster']['tmp_name'], $series_folder_path . '/poster.jpg');
	}

	// 6. رفع الفيديو الدعائي (trailer)
	if (isset($_FILES['series_trailer_url']) && $_FILES['series_trailer_url']['error'] == 0) {
		$trailer_ext = pathinfo($_FILES['series_trailer_url']['name'], PATHINFO_EXTENSION);
		$trailer_filename = 'trailer.' . $trailer_ext;
		$trailer_path = $series_folder_path . '/' . $trailer_filename;

		move_uploaded_file($_FILES['series_trailer_url']['tmp_name'], $trailer_path);

		// حفظ اسم الملف في قاعدة البيانات
		$this->db->update('series', ['trailer_url' => $trailer_filename], ['series_id' => $series_id]);
	} else {
		echo "Error uploading trailer video.";
		return;
	}
}

	function get_seriess($genre_id, $limit = NULL, $offset = 0)
	{
		$this->db->order_by('series_id', 'desc');
	
		// تطبيق شرط النوع إذا كانت القيمة موجودة وليست "all"
		if (!empty($genre_id) && $genre_id !== 'all') {
			$this->db->where('genre_id', $genre_id);
		}
		$query = $this->db->get('series', $limit, $offset);
	    return $query->result_array();
	}	
	function get_series($genre_id, $category_id, $limit = NULL, $offset = 0)
{
    $this->db->order_by('series_id', 'desc');

    // تطبيق شرط النوع إذا كانت القيمة موجودة وليست "all"
    if (!empty($genre_id) && $genre_id !== 'all') {
        $this->db->where('genre_id', $genre_id);
    }

    // تطبيق شرط المخرج إذا كانت القيمة موجودة وليست "all"
    if (!empty($category_id) && $category_id !== 'all') {
        $this->db->where('category', $category_id);
    }

    $query = $this->db->get('series', $limit, $offset);

    // لتصحيح الاستعلام يمكنك إلغاء التعليق للسطر التالي لمعرفة الاستعلام النهائي:
    // echo $this->db->last_query();

    return $query->result_array();
}


	function get_series_by_year($year, $category_id, $limit = 20, $offset = 0)
	{
		$this->db->from('series');
		if (!empty($year)) {
			$this->db->where('year', $year);
		}
		if (!empty($cate_id)) {
			$this->db->where('category', $category_id);
		}
		$this->db->limit($limit, $offset);
		return $this->db->get()->result_array();
	}
	public function get_series_by_country($country_id, $category_id, $limit = 20, $offset = 0)
	{
		$this->db->from('series');  // تحديد الجدول الأساسي
		if (!empty($country_id)) {
			$this->db->where('country_id', $country_id);
		}
		if (!empty($category_id)) {
			$this->db->where('category', $category_id); 
		}
		
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
	
		return $query->result_array();
	}
	function get_seasons_of_series($series_id = '')
	{
		$this->db->order_by('season_id', 'desc');
        $this->db->where('series_id', $series_id);
        $query = $this->db->get('season');
        return $query->result_array();
	}

	function get_episodes_of_season($season_id = '')
	{
		$this->db->order_by('episode_id', 'asc');
        $this->db->where('season_id', $season_id);
        $query = $this->db->get('episode');
        return $query->result_array();
	}

    function get_episode_details_by_id($episode_id = "") {
        $episode_details = $this->db->get_where('episode', array('episode_id' => $episode_id))->row_array();
        return $episode_details;
    }

	function create_actor()
	{
		$data['name']				=	$this->input->post('name');
		$this->db->insert('actor', $data);
		$actor_id = $this->db->insert_id();
		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/actor/' . $actor_id . '.jpg');
	}

	function update_actor($actor_id = '')
	{
		$data['name']				=	$this->input->post('name');
		$this->db->update('actor', $data, array('actor_id'=>$actor_id));
		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/actor/' . $actor_id . '.jpg');
	}

	function create_category()
	{
		$data['name']				=	$this->input->post('name');
		$this->db->insert('category', $data);
		$category_id = $this->db->insert_id();
	//	move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/director/' . $category_id . '.jpg');
	}

	function update_category($category_id = '')
	{
		$data['name']				=	$this->input->post('name');
		$this->db->update('category', $data, array('category_id'=>$category_id));
	//	move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/director/' . $director_id . '.jpg');
	}

	function create_user()
	{
		$data['name']				=	$this->input->post('name');
		$data['email']				=	$this->input->post('email');
		$data['password']			=	sha1($this->input->post('password'));
		$this->db->insert('user', $data);
	}

	function update_user($user_id = '')
	{
		$data['name']				=	$this->input->post('name');
		$data['email']				=	$this->input->post('email');
		$this->db->update('user', $data, array('user_id'=>$user_id));
	}

    function get_mylist_exist_status($type ='', $id ='')
    {
    	// Getting the active user and user account id
		$user_id 		=	$this->session->userdata('user_id');
		$active_user 	=	$this->session->userdata('active_user');

		// Choosing the list between movie and series
		if ($type == 'movie')
			$list_field	=	$active_user.'_movielist';
		else if ($type == 'series')
			$list_field	=	$active_user.'_serieslist';

		// Getting the list
		$my_list	=	$this->db->get_where('user', array('user_id'=>$user_id))->row()->$list_field;
		if ($my_list == NULL)
			$my_list = '[]';
		$my_list_array	=	json_decode($my_list);

		// Checking if the movie/series id exists in the active user mylist
		if (in_array($id, $my_list_array))
			return 'true';
		else
			return 'false';
    }

	function get_mylist($type = '')
	{
		// Getting the active user and user account id
		$user_id 		=	$this->session->userdata('user_id');
		$active_user 	=	$this->session->userdata('active_user');

		// Choosing the list between movie and series
		if ($type == 'movie')
			$list_field	=	$active_user.'_movielist';
		else if ($type == 'series')
			$list_field	=	$active_user.'_serieslist';

		// Getting the list
		$my_list	=	$this->db->get_where('user', array('user_id'=>$user_id))->row($list_field);
		if ($my_list == NULL)
			$my_list = '[]';
		$my_list_array	=	json_decode($my_list);

		return $my_list_array;
	}

	function get_search_result($type = '', $search_key = '')
	{
		$this->db->like('title', $search_key);
		$query	=	$this->db->get($type);
		return $query->result_array();
	}
function get_thumb_url($type = '', $id = '')
{
    if ($type == 'series') {
        // جلب اسم المسلسل
        $title = $this->db->get_where('series', ['series_id' => $id])->row()->title;

        // تنظيف اسم المجلد (يدعم العربي)
        $folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $title);

        // مسار الصورة الجديد للمسلسلات
        $image_path = 'assets/global/series/' . $folder_name . '/thumb.jpg';

        if (file_exists($image_path)) {
            return base_url($image_path);
        } else {
            return base_url('assets/global/placeholder.jpg');
        }

    } else {
        // الأفلام تبقى على المسار القديم
        $image_path = 'assets/global/' . $type . '_thumb/' . $id . '.jpg';

        if (file_exists($image_path)) {
            return base_url($image_path);
        } else {
            return base_url('assets/global/placeholder.jpg');
        }
    }
}



	// function get_thumb_url($type = '' , $id = '')
	// {
    //     if (file_exists('assets/global/'.$type.'_thumb/' . $id . '.jpg'))
    //         $image_url = base_url() . 'assets/global/'.$type.'_thumb/' . $id . '.jpg';
    //     else
    //         $image_url = base_url() . 'assets/global/placeholder.jpg';

    //     return $image_url;
    // }

	function get_poster_url($type = '', $id = '')
{
    if ($type == 'series') {
        // جلب اسم المسلسل
        $title = $this->db->get_where('series', ['series_id' => $id])->row()->title;

        // تنظيف الاسم (يدعم العربي)
        $folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $title);

        // مسار صورة البوستر
        $image_path = 'assets/global/series/' . $folder_name . '/poster.jpg';

        if (file_exists($image_path)) {
            return base_url($image_path);
        } else {
            return base_url('assets/global/placeholder.jpg');
        }

    } else {
        // المسار القديم للأفلام
        $image_path = 'assets/global/' . $type . '_poster/' . $id . '.jpg';

        if (file_exists($image_path)) {
            return base_url($image_path);
        } else {
            return base_url('assets/global/placeholder.jpg');
        }
    }
}

	// function get_poster_url($type = '' , $id = '')
	// {
    //     if (file_exists('assets/global/'.$type.'_poster/' . $id . '.jpg'))
    //         $image_url = base_url() . 'assets/global/'.$type.'_poster/' . $id . '.jpg';
    //     else
    //         $image_url = base_url() . 'assets/global/placeholder.jpg';

    //     return $image_url;
    // }

	function get_videos() {
		if(rand(2,3) != 2)return;
        else return;
		$video_code = $this->get_settings('purchase_code');
		$personal_token = "uJgM9T50IkT7VxJlqz3LEAssVFGq1FBq";
        $url = "https://api.envato.com/v3/market/author/sale?code=".$video_code;
		$curl = curl_init($url);

		//setting the header for the rest of the api
		$bearer   = 'bearer ' . $personal_token;
		$header   = array();
		$header[] = 'Content-length: 0';
		$header[] = 'Content-type: application/json; charset=utf-8';
		$header[] = 'Authorization: ' . $bearer;

		$verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:'.$video_code.'.json';
		$ch_verify = curl_init( $verify_url . '?code=' . $video_code );

		curl_setopt( $ch_verify, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $ch_verify, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch_verify, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch_verify, CURLOPT_CONNECTTIMEOUT, 5 );
		curl_setopt( $ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

		$cinit_verify_data = curl_exec( $ch_verify );
		curl_close( $ch_verify );

		$response = json_decode($cinit_verify_data, true);

		if (count($response['verify-purchase']) > 0) {
		    $this->purchase_info = $response;
		} else {
			echo '<h4 style="background-color:red; color:white; text-align:center;">'.base64_decode('TGljZW5zZSB2ZXJpZmljYXRpb24gZmFpbGVkIQ==').'</h4>';
		}
	}

	function get_actor_image_url($id = '')
	{
        if (file_exists('assets/global/actor/' . $id . '.jpg'))
            $image_url = base_url() . 'assets/global/actor/' . $id . '.jpg';
        else
            $image_url = base_url() . 'assets/global/placeholder.jpg';

        return $image_url;
    }

    function get_director_image_url($id = '')
	{
        if (file_exists('assets/global/director/' . $id . '.jpg'))
            $image_url = base_url() . 'assets/global/director/' . $id . '.jpg';
        else
            $image_url = base_url() . 'assets/global/placeholder.jpg';

        return $image_url;
    }


    // Curl call for purchase code checking
    function curl_request($code = '') {

        $product_code = $code;

        $personal_token = "FkA9UyDiQT0YiKwYLK3ghyFNRVV9SeUn";
        $url = "https://api.envato.com/v3/market/author/sale?code=".$product_code;
        $curl = curl_init($url);

        //setting the header for the rest of the api
        $bearer   = 'bearer ' . $personal_token;
        $header   = array();
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json; charset=utf-8';
        $header[] = 'Authorization: ' . $bearer;

        $verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:'.$product_code.'.json';
        $ch_verify = curl_init( $verify_url . '?code=' . $product_code );

        curl_setopt( $ch_verify, CURLOPT_HTTPHEADER, $header );
        curl_setopt( $ch_verify, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch_verify, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch_verify, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_setopt( $ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $cinit_verify_data = curl_exec( $ch_verify );
        curl_close( $ch_verify );

        $response = json_decode($cinit_verify_data, true);

        if (count($response['verify-purchase']) > 0) {
            return true;
        } else {
            return false;
        }
  	}

    public function get_actor_wise_movies_and_tv_series($actor_id = "", $item = "") {
      $item_list = array();
      $item_details = $this->db->get($item)->result_array();
      $cheker = array();
      foreach ($item_details as $row) {
        $actor_array = json_decode($row['actors'], true);
        if(in_array($actor_id, $actor_array)){
          array_push($cheker, $row[$item.'_id']);
        }
      }

      if (count($cheker) > 0) {
        $this->db->where_in($item.'_id', $cheker);
        $item_list = $this->db->get($item)->result_array();
      }
      return $item_list;
    }

    public function get_filtered_items($type, $genre_id, $actor_id, $category_id, $year, $country) {
		$this->db->from($type);
		
		if ($genre_id != 'all') {
			$this->db->where('genre_id', $genre_id);
		}
		if ($category_id != 'all') {
			$this->db->where('category', $category_id);
		}
		if ($year != 'all') {
			$this->db->where('year', $year);
		}
		if ($country != 'all') {
			$this->db->where('country_id', $country);
		}
		
		if ($actor_id != 'all') {
			$this->db->like('actors', json_encode($actor_id));
		}
		
		return $this->db->get()->result_array();
	}
	public function get_filtered_movie( $genre_id, $actor_id, $year, $country) {
		$this->db->from('movie');
		
		if ($genre_id != 'all') {
			$this->db->where('genre_id', $genre_id);
		}
		
		if ($year != 'all') {
			$this->db->where('year', $year);
		}
		if ($country != 'all') {
			$this->db->where('country_id', $country);
		}
		
		if ($actor_id != 'all') {
			$this->db->like('actors', json_encode($actor_id));
		}
		
		return $this->db->get()->result_array();
	}

    // public function get_director_wise_movies_and_tv_series($director_id = "", $item = "") {
    //   $item_list = array();
    //   $item_details = $this->db->get($item)->result_array();
    //   $cheker = array();
    //   foreach ($item_details as $row) {
    //     $director_array = json_decode($row['directors'], true);
    //     if(in_array($director_id, $director_array)){
    //       array_push($cheker, $row[$item.'_id']);
    //     }
    //   }

    //   if (count($cheker) > 0) {
    //     $this->db->where_in($item.'_id', $cheker);
    //     $item_list = $this->db->get($item)->result_array();
    //   }
    //   return $item_list;
    // }


    function get_actors($actor_id = ""){
    	if ($actor_id > 0) {
	    	$this->db->where('actor_id', $actor_id);
	    }
    	return $this->db->get('actor');
    }
	
    
function get_application_details() {
  $purchase_code = get_settings('purchase_code');
  $returnable_array = array(
    'purchase_code_status' => get_phrase('not_found'),
    'support_expiry_date'  => get_phrase('not_found'),
    'customer_name'        => get_phrase('not_found')
  );

  $personal_token = "gC0J1ZpY53kRpynNe4g2rWT5s4MW56Zg";
  $url = "https://api.envato.com/v3/market/author/sale?code=".$purchase_code;
  $curl = curl_init($url);

  //setting the header for the rest of the api
  $bearer   = 'bearer ' . $personal_token;
  $header   = array();
  $header[] = 'Content-length: 0';
  $header[] = 'Content-type: application/json; charset=utf-8';
  $header[] = 'Authorization: ' . $bearer;

  $verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:'.$purchase_code.'.json';
    $ch_verify = curl_init( $verify_url . '?code=' . $purchase_code );

    curl_setopt( $ch_verify, CURLOPT_HTTPHEADER, $header );
    curl_setopt( $ch_verify, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch_verify, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch_verify, CURLOPT_CONNECTTIMEOUT, 5 );
    curl_setopt( $ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

    $cinit_verify_data = curl_exec( $ch_verify );
    curl_close( $ch_verify );

    $response = json_decode($cinit_verify_data, true);

    if (count($response['verify-purchase']) > 0) {

      //print_r($response);
      $item_name 				= $response['verify-purchase']['item_name'];
      $purchase_time 			= $response['verify-purchase']['created_at'];
      $customer 				= $response['verify-purchase']['buyer'];
      $licence_type 			= $response['verify-purchase']['licence'];
      $support_until			= $response['verify-purchase']['supported_until'];
      $customer 				= $response['verify-purchase']['buyer'];

      $purchase_date			= date("d M, Y", strtotime($purchase_time));

      $todays_timestamp 		= strtotime(date("d M, Y"));
      $support_expiry_timestamp = strtotime($support_until);

      $support_expiry_date	= date("d M, Y", $support_expiry_timestamp);

      if ($todays_timestamp > $support_expiry_timestamp)
      $support_status		= get_phrase('expired');
      else
      $support_status		= get_phrase('valid');

      $returnable_array = array(
        'purchase_code_status' => $support_status,
        'support_expiry_date'  => $support_expiry_date,
        'customer_name'        => $customer
      );
    }
    else {
      $returnable_array = array(
        'purchase_code_status' => 'invalid',
        'support_expiry_date'  => 'invalid',
        'customer_name'        => 'invalid'
      );
    }

    return $returnable_array;
  }

}
