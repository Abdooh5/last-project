<?php
/*
* Product Name : Neoflex Video Subscription Cms
* Developer : Creativeitem
* Date : November, 2018
* Support : http://support.creativeitem.com
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class Browse extends CI_Controller {

	// constructor
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->login_check();

		$called_function	=	$this->router->fetch_method();

		// CHECK IF SAME USER HAS LOGGEDIN FROM DIFFERENT DEVICE/SESSION
		// CHECK IF USER HAS ACTIVE SUBSCRIPTION
		// CHECK IF USER HAS ACTIVE SUBSCRIPTION, THEN IS THERE ANY ACTIVE USER
		if($called_function == 'search' || $called_function == 'process_list' || $called_function == 'home' ||
		  		$called_function == 'movie' || $called_function == 'mylist' || $called_function == 'series' ||
		  		$called_function == 'playmovie' || $called_function == 'playseries') {
			$this->subscription_check();
			$this->multi_device_access_check();
			$this->active_user_check();
		}

		// CHECK IF SAME USER HAS LOGGEDIN FROM DIFFERENT DEVICE/SESSION
		if($called_function == 'search' || $called_function == 'process_list' || $called_function == 'home' ||
		  		$called_function == 'movie' || $called_function == 'mylist' || $called_function == 'series' ||
		  		$called_function == 'playmovie' || $called_function == 'playseries') {

		}
	}



	function search($search_key = '')
{
    if ($this->input->post('search_key'))
    {
        $search_key = $this->input->post('search_key', TRUE); // فلترة تلقائية من CodeIgniter

        // إزالة الرموز غير المرغوب فيها مع السماح ببعض الرموز المفيدة للبحث
        $search_key = preg_replace('/[^اأإءئبتثجحخدذرزسشصضطظعغفقكلمنهويةa-zA-Z0-9\s.,?!-]/u', '', $search_key);

        // منع إدخال روابط خارجية
        if (preg_match('/https?:\/\//', $search_key)) {
            show_error('Invalid search query');
        }

        // تحويل الأحرف الخاصة إلى كود HTML عند العرض فقط، وليس أثناء البحث
        $safe_search_key = htmlspecialchars($search_key, ENT_QUOTES, 'UTF-8');

        redirect(base_url().'index.php?browse/search/'.urlencode($safe_search_key), 'refresh');
    }

    // فك ترميز النص (لضمان أنه يظهر بشكل صحيح)
    $search_key = urldecode($search_key);

    // تحويل الأحرف الخاصة عند العرض فقط
    $page_data['search_key'] = htmlspecialchars($search_key, ENT_QUOTES, 'UTF-8');

    $page_data['page_name'] = 'search';
    $page_data['page_title'] = 'Search result';

    $this->load->view('frontend/index', $page_data);
}





	function process_list($type = '', $task = '', $id = '')
	{

		// Getting the active user and user account id
		$user_id 		=	$this->session->userdata('user_id');
		$active_user 	=	$this->session->userdata('active_user');

		// Choosing the list between movie and series
		if ($type == 'movie')
			$list_field	=	$active_user.'_movielist';
		else if ($type == 'series')
			$list_field	=	$active_user.'_serieslist';


		// Getting the old list
		$old_list	=	$this->db->get_where('user', array('user_id'=>$user_id))->row()->$list_field;
		if ($old_list == NULL)
			$old_list = '[]';
		$old_list_array	=	json_decode($old_list);

		// Adding the new element to old list
		if ($task == 'add')
		{
			if (!in_array($id, $old_list_array))
			{
				array_push($old_list_array , $id);
			}

			$new_list	=	json_encode($old_list_array);
		}

		//Delete the submitted element from old list
		else if ($task == 'delete')
		{
			if (in_array($id, $old_list_array))
			{
				$key		=	array_search($id, $old_list_array);
				unset($old_list_array[$key]);
			}

			$new_list_array	=	array_values($old_list_array);
			$new_list	=	json_encode($new_list_array);
		}

		// Push back the new list to old place and update db table
		$this->db->update('user' , array($list_field => $new_list) , array('user_id' => $user_id));
	
	}

	function home()
	{
		$this->subscription_check();
		$this->active_user_check();
		$page_data['check_movie']	=	true;
		$page_data['page_name']		=	'home';
		$page_data['page_title']	=	'Home';
		$this->load->view('frontend/index', $page_data);
	}

	function movie($genre_id = '', $offset = '')
	{
		$page_data['check_movie']   = true;
		$page_data['page_name']		=	'movie';
		$page_data['page_title']	=	'Watch Movie';
		$page_data['genre_id']	=	$genre_id;

		// pagination configuration
		$url = base_url() . 'index.php?browse/movie/' . $genre_id;
        $per_page = 20;
		$this->db->where('genre_id' , $genre_id);
        $total_result = $this->db->count_all_results('movie');
        $config = $this->crud_model->paginate($url, $total_result, $per_page, 4);
        $this->pagination->initialize($config);

        $page_data['movies'] = $this->crud_model->get_movies($genre_id , $per_page, $this->uri->segment(4));

        $this->db->distinct('year');
		$this->db->select('year');
        $page_data['years'] = $this->db->get_where('movie')->result_array();
		$page_data['total_result']	=	$total_result;

		$this->load->view('frontend/index', $page_data);
	}
	function movie_by_country($country_id = '', $offset = '')
	{
		$page_data['check_movie']   = true;
		$page_data['page_name']		=	'movie';
		$page_data['page_title']	=	'Watch Movie';
		$page_data['genre_id']	=	$country_id;

		// pagination configuration
		$url = base_url() . 'index.php?browse/movie_by_country/' . $country_id;
        $per_page = 20;
		$this->db->where('country_id' , $country_id);
        $total_result = $this->db->count_all_results('movie');
        $config = $this->crud_model->paginate($url, $total_result, $per_page, 4);
        $this->pagination->initialize($config);

        $page_data['movies'] = $this->crud_model->get_movies_by_country($country_id , $per_page, $this->uri->segment(4));

        $this->db->distinct('year');
		$this->db->select('year');
        $page_data['years'] = $this->db->get_where('movie')->result_array();
		$page_data['total_result']	=	$total_result;

		$this->load->view('frontend/index', $page_data);
	}
	public function filter($type = '', $genre_id = '', $actor_id = '',  $category_id= '', $year = '', $country = '') {
		if (empty($type)) {
			redirect(base_url().'index.php?browse/home', 'refresh');
		}
		
		// إعداد بيانات الصفحة
		$page_data = [
			'type' => $type,
			'page_name' => 'filter',
			'page_title' => get_phrase('filter_result'),
			'genre_id' => $genre_id,
			'actor_id' => $actor_id,
			'category_id' => $category_id,
			'search_key_year' => $year,
			'search_key_country' => $country,
			'years' => $this->db->distinct()->select('year')->get($type)->result_array()
		];
		
		// جلب العناصر بناءً على الفلاتر
		$page_data['items'] = $this->crud_model->get_filtered_items($type, $genre_id, $actor_id, $category_id, $year, $country);
		$page_data['total_result'] = count($page_data['items']);
		
		$this->load->view('frontend/index', $page_data);
	}
	public function filter_movie( $genre_id = '', $actor_id = '',  $year = '', $country = '') {

		
		// إعداد بيانات الصفحة
		$page_data = [
			'type' => 'movie',
			'page_name' => 'filter',
			'page_title' => get_phrase('filter_result'),
			'genre_id' => $genre_id,
			'actor_id' => $actor_id,
			'search_key_year' => $year,
			'search_key_country' => $country,
			'years' => $this->db->distinct()->select('year')->get('movie')->result_array()
		];
		
		// جلب العناصر بناءً على الفلاتر
		$page_data['items'] = $this->crud_model->get_filtered_movie($genre_id, $actor_id, $year, $country);
		$page_data['total_result'] = count($page_data['items']);
		
		$this->load->view('frontend/index', $page_data);
	}
	//Save movie progress
	function movie_progress($param1 = '', $param2 = '', $param3 = '', $param4 = ''){

		$progreses = $this->db->get_where('progress', array('user_id' => $param1, 'movie_id' => $param2, 'active_user' => $param4));
		if($progreses->num_rows() > 0){
			$data['progress_value'] = $param3;
			$this->db->where('user_id', $param1);
			$this->db->where('movie_id', $param2);
			$this->db->where('active_user', $param4);
			$this->db->update('progress', $data);
		}else{
			$data['user_id'] = $param1;
			$data['movie_id'] = $param2;
			$data['progress_value'] = $param3;
			$data['active_user'] = $param4;
			$this->db->insert('progress', $data);
		}
	}

	function mylist()
	{
		$page_data['page_name']		=	'mylist';
		$page_data['page_title']	=	'My List';
		$this->load->view('frontend/index', $page_data);
	}
	public function latest_series()
	{
		$this->db->order_by('series_id', 'DESC'); // ترتيب تنازلي لجلب الأحدث
		$this->db->limit(20); // عدد النتائج الظاهرة (يمكنك تغييره)
		$page_data['series'] = $this->db->get('series')->result_array();
		
		$page_data['page_name']  = 'latest_series';
		$page_data['page_title'] = "أحدث السلاسل المضافة";
		$this->load->view('frontend/index', $page_data);
	}
	function series($genre_id = '', $category_id = '', $offset = '')
{
    $page_data['page_name']    = 'series';
    $page_data['page_title']   = 'Watch Tv Series';
    $page_data['genre_id']     = $genre_id;
    $page_data['category_id']     = $category_id;

    // تكوين رابط التصفح (pagination)
    $url = base_url() . 'index.php?browse/series/' . $genre_id . '/' . $category_id;
    $per_page = 20;

    // تعديل الاستعلام ليشمل النوع والمخرج فقط
    $this->db->from('series');  // تحديد الجدول الأساسي
    if (!empty($genre_id) && $genre_id !== 'all') {
        $this->db->where('genre_id', $genre_id);  // شرط النوع
    }
    if (!empty($category_id) && $category_id !== 'all') {
        $this->db->where('category', $category_id);  // شرط المخرج
    }

    // حساب العدد الكلي للنتائج
    $total_result = $this->db->count_all_results();

    // تهيئة التصفح (pagination)
    $config = $this->crud_model->paginate($url, $total_result, $per_page, 4);
    $this->pagination->initialize($config);

    // جلب المسلسلات بناءً على النوع والمخرج
    $page_data['series'] = $this->crud_model->get_series($genre_id, $category_id, $per_page, $offset);
    $page_data['total_result'] = $total_result;

    // تحميل الصفحة
    $this->load->view('frontend/index', $page_data);
}

	function series_by_country($country_id = '', $category_id = '', $offset = '')
	{
		$page_data['page_name']  = 'series';
		$page_data['page_title'] = 'Watch TV Series by Country and category';
		$page_data['country_id'] = $country_id;
		$page_data['category_id']    = $category_id;
	
		// تكوين رابط التصفح (pagination)
		$url = base_url() . 'index.php?browse/series_by_country/' . $country_id . '/' . $category_id;
		$per_page = 20;
	
		// تعديل الاستعلام ليشمل الدولة والمخرج
		$this->db->from('series');  // تحديد الجدول الأساسي
		if (!empty($country_id)) {
			$this->db->where('country_id', $country_id);
		}
		if (!empty($cate_id)) {
			$this->db->where('category', $category_id); 
		}
		$total_result = $this->db->count_all_results();
	
		// تهيئة التصفح (pagination)
		$config = $this->crud_model->paginate($url, $total_result, $per_page, 4);
		$this->pagination->initialize($config);
	
		// جلب سنوات المسلسلات المتاحة (Distinct)
		$this->db->distinct();
		$this->db->select('year');
		$page_data['years'] = $this->db->get('series')->result_array();
	
		// جلب المسلسلات بناءً على الدولة و الفئة
		$page_data['series'] = $this->crud_model->get_series_by_country($country_id, $category_id, $per_page, $offset);
		$page_data['total_result'] = $total_result;
	
		// تحميل الصفحة
		$this->load->view('frontend/index', $page_data);
	}
	function series_by_year($year = '', $category_id = '', $offset = '')
{ 
    $page_data['page_name']  = 'series';
    $page_data['page_title'] = 'Watch TV Series by Year and Category';
    $page_data['year']       = $year;
    $page_data['category_id']    = $category_id;

    // إعداد رابط التصفح
    $url = base_url() . 'index.php?browse/series_by_year/' . $year . '/' . $category_id;
    $per_page = 20;

    // تصفية الاستعلام ليشمل السنة والفئة
    $this->db->from('series');
    if (!empty($year)) {
        $this->db->where('year', $year);
    }
    if (!empty($category_id)) {
        $this->db->where('category', $category_id); // ✅ استخدم '' للمطابقة مع الفئة
    }
    $total_result = $this->db->count_all_results();

    // تهيئة التصفح (pagination)
    $config = $this->crud_model->paginate($url, $total_result, $per_page, 4);
    $this->pagination->initialize($config);

    // تحديد السنوات المتاحة
    $this->db->distinct();
    $this->db->select('year');
    $page_data['years'] = $this->db->get('series')->result_array();

    // استرجاع المسلسلات حسب السنة والفئة
    $page_data['series'] = $this->crud_model->get_series_by_year($year, $category_id, $per_page, $offset);
    $page_data['total_result'] = $total_result;

    // تحميل الصفحة
    $this->load->view('frontend/index', $page_data);
}

	function playmovie($movie_id = '')
	{
		$page_data['page_name']		=	'playmovie';
		$page_data['page_title']	=	'Watch Movie';
		$page_data['movie_id']		=	$movie_id;
		$this->load->view('frontend/index', $page_data);
	}

	function playseries($series_id = '', $season_id = '', $episode_id = "")
	{
		if ($season_id == '')
		{
        	$seasons	=	$this->db->get_where('season', array('series_id'=>$series_id))->result_array();
        	$first_season_id = null;
			foreach ($seasons as $row)
			{
				$first_season_id	=	$row['season_id'];
				break;
			}
			$page_data['season_id']		=	$first_season_id;
		}
		else{
			$page_data['season_id']		=	$season_id;
		}

		if ($episode_id == "") {
			$episodes	=	$this->db->get_where('episode', array('season_id'=>$page_data['season_id']))->row_array();
			$page_data['episode_id']		= $episodes['episode_id'];
		}else{
			$page_data['episode_id']		= $episode_id;
		}

		$page_data['series_id']		=	$series_id;
		$page_data['page_name']		=	'playseries';
		$page_data['page_title']	=	'Watch Tv Series';
		//$page_data['series_id']		=	$series_id;
		$this->load->view('frontend/index', $page_data);
	}

	function youraccount()
	{
		$page_data['page_name']		=	'youraccount';
		$page_data['page_title']	=	'Your Account';
		$this->load->view('frontend/index', $page_data);
	}

	function switchprofile()
	{
		$this->subscription_check();
		$page_data['page_name']			=	'switchprofile';
		$page_data['page_title']		=	'Switch Profile';
		$page_data['current_plan_id']	=	$this->crud_model->get_current_plan_id();
		$this->load->view('frontend/index', $page_data);

	}

	function doswitch($user_number)
	{
		$this->session->set_userdata('active_user', $user_number);
		// SET USER SESSION HERE WITH TIMESTAMP FOR MULTI DEVICE ACCESS PROHIBITION
		$user_entering_timestamp		=	strtotime(date("Y-m-d H:i:s"));
		$this->session->set_userdata('user_entering_timestamp' , $user_entering_timestamp);

		$user_id						=	$this->session->userdata('user_id');
		$data[$user_number.'_session']	=	$user_entering_timestamp;
		$this->db->update('user' , $data , array('user_id' => $user_id));

		redirect(base_url().'index.php?browse/home' , 'refresh');
	}

	function manageprofile()
	{
		$this->subscription_check();
		$page_data['page_name']			=	'manageprofile';
		$page_data['page_title']		=	'Manage Profile';
		$page_data['current_plan_id']	=	$this->crud_model->get_current_plan_id();
		$this->load->view('frontend/index', $page_data);

	}

	function editprofile($user = '')
	{
		if (isset($_POST) && !empty($_POST))
		{
			$user_id 		=	$this->session->userdata('user_id');
			$user_field		=	$user;
			$username		=	$this->input->post('username');

			if (!empty($_FILES['userimage']['name'])) {
				$image_name = $user_id.'_'.$user_field.'.jpg';
				$path = 'assets/global/user_thumb';
				if (!file_exists($path)) {
				    mkdir($path, 0777, true);
				}
				move_uploaded_file($_FILES['userimage']['tmp_name'], $path.'/'.$image_name);
			}

			$this->db->update('user', array($user_field => $username), array('user_id' => $user_id));
			redirect(base_url().'index.php?browse/manageprofile' , 'refresh');
		}
		$page_data['page_name']			=	'editprofile';
		$page_data['page_title']		=	'Edit Profile';
		$page_data['user']				=	$user;
		$this->load->view('frontend/index', $page_data);

	}

	function emailchange()
	{
		if (isset($_POST) && !empty($_POST))
		{
			$user_id							=	$this->session->userdata('user_id');
			$old_password_encrypted				=	$this->crud_model->get_current_user_detail()->password;
			$old_password_submitted_encrypted	=	sha1($this->input->post('old_password'));
			$new_email							=	$this->input->post('new_email');

			// DUPLICATE EMAIL DENIES EMAIL CHANGE
			$this->db->where('email' , $new_email);
			$this->db->from('user');
        	$total_number_of_matching_user = $this->db->count_all_results();
			if ($total_number_of_matching_user > 0)
			{
				$this->session->set_flashdata('status', 'email_change_failed');
				redirect(base_url().'index.php?browse/emailchange' , 'refresh');
			}

			// CORRECT PASSWORD NEEDED TO CHANGE EMAIL
			if ($old_password_encrypted 		==	$old_password_submitted_encrypted)
			{
				$this->db->update('user', array('email'=>$new_email), array('user_id'=>$user_id));
				$this->session->set_flashdata('status', 'email_changed');
				redirect(base_url().'index.php?browse/youraccount' , 'refresh');
			}
			else
			{
				$this->session->set_flashdata('status', 'email_change_failed');
				redirect(base_url().'index.php?browse/emailchange' , 'refresh');
			}

			$this->db->update('user', array($user_field => $username), array('user_id' => $user_id));
			redirect(base_url().'index.php?browse/manageprofile' , 'refresh');
		}
		$page_data['page_name']			=	'emailchange';
		$page_data['page_title']		=	'Chane email address';
		$this->load->view('frontend/index', $page_data);

	}

	function passwordchange()
	{
		if (isset($_POST) && !empty($_POST))
		{
			$user_id							=	$this->session->userdata('user_id');
			$old_password_encrypted				=	$this->crud_model->get_current_user_detail()->password;
			$old_password_submitted_encrypted	=	sha1($this->input->post('old_password'));
			$new_password						=	$this->input->post('new_password');
			$new_password_encrypted				=	sha1($this->input->post('new_password'));

			// NEW PASSWORD MUST BE 6 CHARACTER LONG
			if (strlen($new_password) <6)
			{
				$this->session->set_flashdata('status', 'password_change_failed');
				redirect(base_url().'index.php?browse/passwordchange' , 'refresh');
			}

			// CORRECT OLD PASSWORD NEEDED TO CHANGE PASSWORD
			if ($old_password_encrypted 		==	$old_password_submitted_encrypted)
			{
				$this->db->update('user', array('password'=>$new_password_encrypted), array('user_id'=>$user_id));
				$this->session->set_flashdata('status', 'password_changed');
				redirect(base_url().'index.php?browse/youraccount' , 'refresh');
			}
			else
			{
				$this->session->set_flashdata('status', 'password_change_failed');
				redirect(base_url().'index.php?browse/passwordchange' , 'refresh');
			}

			$this->db->update('user', array($user_field => $username), array('user_id' => $user_id));
			redirect(base_url().'index.php?browse/manageprofile' , 'refresh');
		}
		$page_data['page_name']			=	'passwordchange';
		$page_data['page_title']		=	'Change Password';
		$this->load->view('frontend/index', $page_data);

	}

	// CHECK IF LOGGED IN USER ACCOUNT HAS SELECTED ANY OF HIS PROFILE(S), MUST BE CHECKED AFTER SUBSCRIPTION CHECK
	function active_user_check()
	{
		// admin can access all frontend pages
		if ($this->session->userdata('login_type') == 1)
			return;

		$active_user	=	$this->session->userdata('active_user');
		if ($active_user == '')
			redirect(base_url().'index.php?browse/switchphorofile' , 'refresh');
	}

	// CHECK IF LOGGED IN USER HAS ACTIVE SUBSCRIPTION, IF NOT THEN REDIRECT TO ACCOUNT MANAGING PAGE
	function subscription_check()
	{
		// admin can access all frontend pages
		if ($this->session->userdata('login_type') == 1)
			return;

		$subscription_validation	=	$this->crud_model->validate_subscription();
		if ($subscription_validation == false)
			redirect(base_url().'index.php?browse/youraccount' , 'refresh');
	}

	function login_check()
	{
		if ($this->session->userdata('user_login_status') != 1)
			redirect(base_url().'index.php?home/signin' , 'refresh');
	}

	function multi_device_access_check()
	{
		// admin can access all frontend pages
		if ($this->session->userdata('login_type') == 1)
			return;

		// checking the same profile trying to access multiple devices/sessions
		$logged_in_user_id			=	$this->session->userdata('user_id');
		$active_user_session 		=	$this->session->userdata('active_user').'_session';
		$user_entering_db_timestamp	=	$this->db->get_where('user', array('user_id' => $logged_in_user_id))->row($active_user_session);

		$user_entering_timestamp	=	$this->session->userdata('user_entering_timestamp');

		if ($user_entering_timestamp != $user_entering_db_timestamp)
			redirect(base_url().'index.php?browse/switchprofile' , 'refresh');
	}

	
}
