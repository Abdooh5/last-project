<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	// constructor
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('crud_model');
		$this->load->library('session');
		$this->admin_login_check();
	}

	public function index()
	{
		$this->dashboard();
	}

	function dashboard()
	{
		$page_data['page_name']		=	'dashboard';
		$page_data['page_title']	=	'Home - Summary';
		$this->load->view('backend/index', $page_data);
	}

	// WATCH LIST OF GENRE, MANAGE THEM
	function genre_list()
	{
		$page_data['page_name']		=	'genre_list';
		$page_data['page_title']	=	'Manage Genre';
		$this->load->view('backend/index', $page_data);
	}
	public function fetch_movie_data() {
		$title = $this->input->post('title');
		$api_key = 'your_api_key'; // استخدم مفتاحك الحقيقي هنا
		$url = "https://www.omdbapi.com/?apikey={$api_key}&t=" . urlencode($title);
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
	
		echo $response; // سيتم استدعاؤها من الجافاسكربت
	}
	function searchMovie($movieTitle) {
		$apiKey = '550cd509e7933045659e6f893e844d64'; // تأكد أن المفتاح صالح
		$url = 'https://api.themoviedb.org/3/search/movie?api_key=' . $apiKey . '&query=' . urlencode($movieTitle) . '&language=ar';
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		$response = curl_exec($ch);
	
		// التحقق من وجود خطأ في الاتصال
		if (curl_errno($ch)) {
			echo '❌ خطأ في cURL: ' . curl_error($ch) . "<br>";
			curl_close($ch);
			return;
		}
	
		curl_close($ch);
	
		// محاولة تحويل البيانات إلى JSON
		$output = json_decode($response, true);
	
		// طباعة الاستجابة الخام (لتشخيص المشكلة)
		echo "<h3>📦 الاستجابة من API:</h3>";
		echo "<pre>";
		print_r($output);
		echo "</pre>";
	
		// التحقق من وجود خطأ في البيانات
		if (isset($output['success']) && $output['success'] === false) {
			echo "❗ خطأ في الطلب: " . ($output['status_message'] ?? 'غير معروف');
			return;
		}
	
		if (!isset($output['results']) || empty($output['results'])) {
			echo "⚠️ لا توجد نتائج لهذا العنوان: <strong>$movieTitle</strong>";
			return;
		}
	
		// عرض أول نتيجة كمثال
		$firstResult = $output['results'][0];
		echo "<h3>🎬 أول نتيجة:</h3>";
		echo "العنوان: " . ($firstResult['title'] ?? 'غير متوفر') . "<br>";
		echo "التاريخ: " . ($firstResult['release_date'] ?? 'غير متوفر') . "<br>";
		echo "الملخص: " . ($firstResult['overview'] ?? 'غير متوفر') . "<br>";
	}
	
	public function fetch_tmdb_data() {
		$title = $this->input->post('title');
		$apiKey = '550cd509e7933045659e6f893e844d64';
	
		if (empty($title)) {
			echo json_encode(['error' => 'لم يتم إدخال عنوان الفيلم']);
			return;
		}
	
		// دالة مساعدة لعمل cURL
		function curl_get($url) {
			$ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
			]);
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
		}
		function download_image($url, $path) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$data = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
		
			if ($http_code == 200 && $data !== false) {
				file_put_contents($path, $data);
				return true;
			}
			return false;
		}
		
		
		// دالة لإدخال السجلات إذا لم تكن موجودة
		function insert_if_not_exists($table, $name, $profile_image_url = null) {
			$ci = &get_instance();
			$ci->db->where('name', $name);
			$query = $ci->db->get($table);
		
			if ($query->num_rows() == 0) {
				// إضافة السجل للحصول على ID
				$data = ['name' => $name];
				$ci->db->insert($table, $data);
				$insert_id = $ci->db->insert_id();
		
				if ($profile_image_url) {
					// إنشاء مجلد الصور إذا لم يكن موجود
					$image_directory = 'assets/global/actor/'; // هذا هو التصحيح الصحيح
					if (!is_dir($image_directory)) {
						mkdir($image_directory, 0777, true);
					}
					
		
					// استخراج امتداد الصورة
					$image_ext = pathinfo(parse_url($profile_image_url, PHP_URL_PATH), PATHINFO_EXTENSION);
					if (!$image_ext) $image_ext = 'jpg'; // افتراضيًا JPG
		
					$local_filename = $insert_id . '.' . strtolower($image_ext);
					$local_path = $image_directory . $local_filename;
		
					// تحميل الصورة
					$image_data = file_get_contents($profile_image_url);
					if ($image_data !== false) {
						file_put_contents($local_path, $image_data);
						// تحديث السجل مع مسار الصورة
						// $ci->db->where('id', $insert_id);
						// $ci->db->update($table, ['profile_image' => $local_path]);
					}
				}
			}
		}
		
		
		
	
		// Step 1: البحث
		$searchUrl = "https://api.themoviedb.org/3/search/movie?api_key={$apiKey}&query=" . urlencode($title) . "&language=ar";
		$searchResponse = curl_get($searchUrl);
		$searchData = json_decode($searchResponse, true);
	
		if (!isset($searchData['results'][0])) {
			echo json_encode(['error' => '❌ لم يتم العثور على نتائج']);
			return;
		}
	
		$movie = $searchData['results'][0];
		$movieId = $movie['id'];
	
		// Step 2: تفاصيل الفيلم
		$detailsUrl = "https://api.themoviedb.org/3/movie/{$movieId}?api_key={$apiKey}&language=ar";
		$details = json_decode(curl_get($detailsUrl), true);
	
	// Step 3: الكاست
$creditsUrl = "https://api.themoviedb.org/3/movie/{$movieId}/credits?api_key={$apiKey}&language=ar";
$credits = json_decode(curl_get($creditsUrl), true);
$actors = [];
if (isset($credits['cast'])) {
	foreach (array_slice($credits['cast'], 0, 5) as $actor) {
		$actors[] = $actor['name'];
		
		// جلب صورة الممثل (إذا كانت موجودة)
		$profile_image_url = isset($actor['profile_path']) ? 'https://image.tmdb.org/t/p/w500' . $actor['profile_path'] : null;
		
		// حفظ الممثل مع صورته على الخادم
		insert_if_not_exists('actor', $actor['name'], $profile_image_url);
	}
}



		// Step 4: الأنواع
		$genreNames = [];
		if (isset($details['genres'])) {
			foreach ($details['genres'] as $g) {
				$genreNames[] = $g['name'];
				insert_if_not_exists('genre', $g['name']);
			}
		}
	
		// Step 5: الدول
		$countries = [];
		if (isset($details['production_countries'])) {
			foreach ($details['production_countries'] as $c) {
				$countries[] = $c['name'];
				insert_if_not_exists('country', $c['name']);
			}
		}
	
		// النتيجة النهائية
		$result = [
			'title' => $movie['title'],
			'overview' => $movie['overview'],
			'release_date' => $movie['release_date'],
			'vote_average' => $movie['vote_average'],
			'poster_path' => $movie['poster_path'],
			'actors' => $actors,
			'genres' => $genreNames,
			'countries' => $countries,
			'runtime' => isset($details['runtime']) ? $details['runtime'] : null
		];
	
		echo json_encode($result);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function fetch_omdb_data() {
		$title = $this->input->post('title');
		$url = 'https://www.omdbapi.com/?apikey=c5334055&t=' . urlencode($title);
		
		// استخدام cURL أكثر أمانًا من file_get_contents في بعض السيرفرات
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
	
		echo $output;
	}
	public function upload_poster_from_url() {
		$image_url = $this->input->post('image_url');
	
		if (!$image_url || filter_var($image_url, FILTER_VALIDATE_URL) === false) {
			echo json_encode(['error' => 'Invalid URL']);
			return;
		}
	
		$image_data = file_get_contents($image_url);
		if (!$image_data) {
			echo json_encode(['error' => 'Failed to download image']);
			return;
		}
	
		$filename = uniqid('poster_') . '.jpg';
		$upload_path = 'uploads/posters/';
		if (!file_exists($upload_path)) {
			mkdir($upload_path, 0755, true); // إنشاء المجلد إذا لم يكن موجود
		}
	
		file_put_contents($upload_path . $filename, $image_data);
	
		// إرجاع المسار ليتم حفظه لاحقًا
		echo base_url($upload_path . $filename);
	}
		
	private function get_or_create_id($table, $column, $value)
	{
		$this->db->where($column, $value);
		$query = $this->db->get($table);
		if ($query->num_rows() > 0) {
			return $query->row()->{$table . '_id'};
		} else {
			$this->db->insert($table, [$column => $value]);
			return $this->db->insert_id();
		}
	}	public function fetch_movie_info() {
		$title = $this->input->post('title');
		$apiKey = 'c5334055';
		$url = "https://www.omdbapi.com/?apikey=" . $apiKey . "&t=" . urlencode($title);
	
		// جلب المحتوى
		$response = file_get_contents($url);
	
		// إعادة النتيجة إلى الجافاسكريبت
		echo $response;
	}
	
	// CREATE A NEW GENRE
	function genre_create()
	{
		if (isset($_POST) && !empty($_POST))
		{
			$data['name']			=	$this->input->post('name');
			$this->db->insert('genre', $data);
			redirect(base_url().'index.php?admin/genre_list' , 'refresh');
		}
		$page_data['page_name']		=	'genre_create';
		$page_data['page_title']	=	'Create Genre';
		$this->load->view('backend/index', $page_data);
	}

	// EDIT A GENRE
	function genre_edit($genre_id = '')
	{
		if (isset($_POST) && !empty($_POST))
		{
			$data['name']			=	$this->input->post('name');
			$this->db->update('genre', $data,  array('genre_id' => $genre_id));
			redirect(base_url().'index.php?admin/genre_list' , 'refresh');
		}
		$page_data['genre_id']		=	$genre_id;
		$page_data['page_name']		=	'genre_edit';
		$page_data['page_title']	=	'Edit Genre';
		$this->load->view('backend/index', $page_data);
	}

	// DELETE A GENRE
	function genre_delete($genre_id = '')
	{
		$this->db->delete('genre',  array('genre_id' => $genre_id));
		redirect(base_url().'index.php?admin/genre_list' , 'refresh');
	}





	// WATCH LIST OF country, MANAGE THEM
	function country()
	{
		$page_data['page_name']		=	'country';
		$page_data['page_title']	=	get_phrase('countries');
		$this->load->view('backend/index', $page_data);
	}

	// CREATE A NEW country
	function country_create()
	{
		if (isset($_POST) && !empty($_POST))
		{
			$data['name']			=	$this->input->post('name');
			$this->db->insert('country', $data);
			redirect(base_url().'index.php?admin/country' , 'refresh');
		}
		$page_data['page_name']		=	'country_create';
		$page_data['page_title']	=	get_phrase('add_country');
		$this->load->view('backend/index', $page_data);
	}

	// EDIT A country
	function country_edit($country_id = '')
	{
		if (isset($_POST) && !empty($_POST))
		{
			$data['name']			=	$this->input->post('name');
			$this->db->update('country', $data,  array('country_id' => $country_id));
			redirect(base_url().'index.php?admin/country' , 'refresh');
		}
		$page_data['country_id']		=	$country_id;
		$page_data['page_name']		=	'country_edit';
		$page_data['page_title']	=	get_phrase('edit_country');
		$this->load->view('backend/index', $page_data);
	}

	// DELETE A country
	function country_delete($country_id = '')
	{
		$this->db->delete('country',  array('country_id' => $country_id));
		redirect(base_url().'index.php?admin/country' , 'refresh');
	}

	// WATCH LIST OF MOVIES, MANAGE THEM
	function movie_list($actor_id = "")
	{
		$page_data['actor_id']		=	empty($actor_id) ? 'all' : $actor_id;
		$page_data['page_name']		=	'movie_list';
		$page_data['page_title']	=	'Manage movie';
		$this->load->view('backend/index', $page_data);
	}

	// CREATE A NEW MOVIE
	function movie_create()
	{
		if (isset($_POST) && !empty($_POST))
		{
			$this->crud_model->create_movie();
			redirect(base_url().'index.php?admin/movie_list' , 'refresh');
		}
		$page_data['page_name']		=	'movie_create';
		$page_data['page_title']	=	'Create movie';
		$this->load->view('backend/index', $page_data);
	}





	// // SUBTITLE
	// function subtitle($param1 = '')
	// {
	// 	$page_data['movie_id']		= $param1;
	// 	$page_data['page_name']		=	'subtitle';
	// 	$page_data['page_title']	=	'Manage subtitle : '.$this->db->get_where('movie', array('movie_id' => $param1))->row('title');
	// 	$this->load->view('backend/index', $page_data);
	// }

	// function add_subtitle($param1 = '')
	// {
	// 	if (isset($_POST) && !empty($_POST)){
	// 		$language = $this->input->post('language');
	// 		$subtitle = $this->db->get_where('subtitle', array('movie_id' => $param1, 'language' => $language))->row_array();
	// 		if($subtitle['language'] != $language){
	// 			$this->crud_model->add_subtitle($param1);
	// 		}
	// 		redirect(base_url().'index.php?admin/add_subtitle/'.$param1, 'refresh');
	// 	}
	// 	$page_data['movie_id']		= $param1;
	// 	$page_data['page_name']		=	'add_subtitle';
	// 	$page_data['page_title']	=	'Add subtitle';
	// 	$this->load->view('backend/index', $page_data);
	// }

	// function edit_subtitle($param1 = '', $param2 = '')
	// {
	// 	if (isset($_POST) && !empty($_POST)){
	// 		$language = $this->input->post('language');
	// 		$subtitle = $this->db->get_where('subtitle', array('movie_id' => $param2, 'language' => $language))->row_array();
	// 		if($subtitle['language'] != $language){
	// 			$this->crud_model->edit_subtitle($param1, $param2);
	// 		}
	// 		redirect(base_url().'index.php?admin/subtitle/'.$param2, 'refresh');
	// 	}
	// 	$page_data['subtitle_id']	= $param1;
	// 	$page_data['movie_id']		= $param2;
	// 	$page_data['page_name']		=	'edit_subtitle';
	// 	$page_data['page_title']	=	'Edit subtitle';
	// 	$this->load->view('backend/index', $page_data);
	// }

	// function delete_subtitle($param1 = '', $param2 = ''){
	// 	$this->db->where('id', $param1);
	// 	$this->db->delete('subtitle');
	// 	redirect(base_url().'index.php?admin/subtitle/'.$param2, 'refresh');
	// }


	// EDIT A MOVIE
	function movie_edit($movie_id = '')
	{	//print_r($_POST); exit;
		if (isset($_POST) && !empty($_POST))
		{
			$this->crud_model->update_movie($movie_id);
			redirect(base_url().'index.php?admin/movie_list' , 'refresh');
		}
		$page_data['movie_id']		=	$movie_id;
		$page_data['page_name']		=	'movie_edit';
		$page_data['page_title']	=	'Edit movie';
		$this->load->view('backend/index', $page_data);
	}

	// DELETE A MOVIE
	function movie_delete($movie_id = '')
	{
		 function delete_directory($dir) {
        // تحقق إذا كان المجلد موجودًا
        if (is_dir($dir)) {
            // الحصول على كافة الملفات والمجلدات في المجلد
            $files = array_diff(scandir($dir), array('.', '..'));
            
            foreach ($files as $file) {
                // بناء المسار الكامل للملف أو المجلد
                $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                
                // إذا كان ملفًا، احذفه
                if (is_file($filePath)) {
                    unlink($filePath);
                } 
                // إذا كان مجلدًا، استدعاء الدالة لحذف المجلدات
                else {
                    delete_directory($filePath);
                }
            }
            // أخيرًا، احذف المجلد نفسه
            rmdir($dir);
        }
    }

    // استعلام للحصول على بيانات الفيلم قبل حذفه من قاعدة البيانات
    $query = $this->db->get_where('movie', array('movie_id' => $movie_id));
    $movies = $query->row();

    if ($movies) {
        // تحديد مسار المجلد الخاص بالفيلم بناءً على العنوان
        $base_movies_folder = 'assets/global/movies';
        $movie_folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $movies->title);
        $movie_folder_path = $base_movies_folder . '/' . $movie_folder_name;

        // حذف المجلد المرتبط بالفيلم
        delete_directory($movie_folder_path);
		//حذف الفيلم من قاعدة البيانات
		$this->db->delete('movie',  array('movie_id' => $movie_id));
	}
		redirect(base_url().'index.php?admin/movie_list' , 'refresh');
	}

	// WATCH LIST OF SERIESS, MANAGE THEM
	function series_list($actor_id = "")
	{
		$page_data['actor_id']		=	empty($actor_id) ? 'all' : $actor_id;
		$page_data['page_name']		=	'series_list';
		$page_data['page_title']	=	'Manage Tv Series';
		$this->load->view('backend/index', $page_data);
	}

	// CREATE A NEW SERIES
	function series_create()
	{
		if (isset($_POST) && !empty($_POST))
		{
			$this->crud_model->create_series();
			redirect(base_url().'index.php?admin/series_list' , 'refresh');
		}
		$page_data['page_name']		=	'series_create';
		$page_data['page_title']	=	'Create Tv Series';
		$this->load->view('backend/index', $page_data);
	}

	// EDIT A SERIES
	function series_edit($series_id = '')
	{
		if (isset($_POST) && !empty($_POST))
		{
			$this->crud_model->update_series($series_id);
			redirect(base_url().'index.php?admin/series_edit/'.$series_id , 'refresh');
		}
		$page_data['series_id']		=	$series_id;
		$page_data['page_name']		=	'series_edit';
		$page_data['page_title']	=	'Edit Tv Series. Manage Seasons & Episodes';
		$this->load->view('backend/index', $page_data);
	}

	// DELETE A SERIES
	function series_delete($series_id = '') {
    // دالة لحذف المجلد وكل محتوياته
    function delete_directory($dir) {
        // تحقق إذا كان المجلد موجودًا
        if (is_dir($dir)) {
            // الحصول على كافة الملفات والمجلدات في المجلد
            $files = array_diff(scandir($dir), array('.', '..'));
            
            foreach ($files as $file) {
                // بناء المسار الكامل للملف أو المجلد
                $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                
                // إذا كان ملفًا، احذفه
                if (is_file($filePath)) {
                    unlink($filePath);
                } 
                // إذا كان مجلدًا، استدعاء الدالة لحذف المجلدات
                else {
                    delete_directory($filePath);
                }
            }
            // أخيرًا، احذف المجلد نفسه
            rmdir($dir);
        }
    }

    // استعلام للحصول على بيانات المسلسل قبل حذفه من قاعدة البيانات
    $query = $this->db->get_where('series', array('series_id' => $series_id));
    $series = $query->row();

    if ($series) {
        // تحديد مسار المجلد الخاص بالمسلسل بناءً على العنوان
        $base_series_folder = 'assets/global/series';
        $series_folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $series->title);
        $series_folder_path = $base_series_folder . '/' . $series_folder_name;

        // حذف المجلد المرتبط بالمسلسل
        delete_directory($series_folder_path);

        // حذف السلسلة من قاعدة البيانات
        $this->db->delete('series', array('series_id' => $series_id));
    }

    // إعادة التوجيه إلى قائمة المسلسلات
    redirect(base_url() . 'index.php?admin/series_list', 'refresh');
}


	// CREATE A NEW SEASON
	// function season_create($series_id = '')
	// {
	// 	$this->db->where('series_id' , $series_id);
	// 	$this->db->from('season');
    //     $number_of_season 	=	$this->db->count_all_results();

	// 	$data['series_id']	=	$series_id;
	// 	$data['name']		=	'Season ' . ($number_of_season + 1);
	// 	$this->db->insert('season', $data);
	// 	redirect(base_url().'index.php?admin/series_edit/'.$series_id , 'refresh');

	// }
	function season_create($series_id = '')
{
	// 1. عدّ عدد المواسم الحالية
	$this->db->where('series_id', $series_id);
	$this->db->from('season');
	$number_of_season = $this->db->count_all_results();

	// 2. إنشاء الموسم الجديد في قاعدة البيانات
	$data['series_id'] = $series_id;
	$data['name'] = 'Season ' . ($number_of_season + 1);
	$this->db->insert('season', $data);

	// 3. جلب عنوان المسلسل من قاعدة البيانات
	$this->db->where('series_id', $series_id);
	$series = $this->db->get('series')->row_array();
	$series_title_raw = $series['title'];

	// 4. تحويل اسم المسلسل لمجلد صالح
	$series_folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $series_title_raw);
	$series_base_path = 'assets/global/series/' . $series_folder_name;

	// 5. إنشاء مجلد الموسم داخل مجلد المسلسل
	$season_folder_name = 'Season_' . ($number_of_season + 1);
	$season_folder_path = $series_base_path . '/' . $season_folder_name;

	if (!is_dir($season_folder_path)) {
		mkdir($season_folder_path, 0777, true);
	}

	// 6. الرجوع لصفحة تحرير المسلسل
	redirect(base_url().'index.php?admin/series_edit/'.$series_id, 'refresh');
}


	// EDIT A SEASON
	// function season_edit($series_id = '', $season_id = '')
	// {
	// 	if (isset($_POST) && !empty($_POST))
	// 	{
	// 		$data['title']			=	$this->input->post('title');
	// 		$this->db->update('series', $data,  array('series_id' => $series_id));
	// 		redirect(base_url().'index.php?admin/series_edit/'.$series_id , 'refresh');
	// 	}
	// 	$series_name				=	$this->db->get_where('series', array('series_id'=>$series_id))->row()->title;
	// 	$season_name				=	$this->db->get_where('season', array('season_id'=>$season_id))->row()->name;
	// 	$page_data['page_title']	=	'Manage episodes of ' . $season_name . ' : ' . $series_name;
	// 	$page_data['season_name']	=	$this->db->get_where('season', array('season_id'=>$season_id))->row()->name;
	// 	$page_data['series_id']		=	$series_id;
	// 	$page_data['season_id']		=	$season_id;
	// 	$page_data['page_name']		=	'season_edit';
	// 	$this->load->view('backend/index', $page_data);
	// }
function season_edit($series_id = '', $season_id = '')
{
	if (isset($_POST) && !empty($_POST))
	{
		// 1. الحصول على اسم المسلسل الحالي (قبل التحديث)
		$old_series = $this->db->get_where('series', array('series_id' => $series_id))->row_array();
		$old_title = $old_series['title'];
		$old_folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $old_title);
		$old_folder_path = 'assets/global/series/' . $old_folder_name;

		// 2. الحصول على العنوان الجديد من النموذج
		$new_title = $this->input->post('title');
		$data['title'] = $new_title;

		// 3. تحديث قاعدة البيانات
		$this->db->update('series', $data, array('series_id' => $series_id));

		// 4. تحديث اسم مجلد المسلسل إذا تغيّر العنوان
		$new_folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $new_title);
		$new_folder_path = 'assets/global/series/' . $new_folder_name;

		if ($old_folder_path !== $new_folder_path && is_dir($old_folder_path)) {
			rename($old_folder_path, $new_folder_path);
		}

		// 5. إعادة التوجيه
		redirect(base_url() . 'index.php?admin/series_edit/' . $series_id, 'refresh');
	}

	// 6. إعداد البيانات للعرض
	$series_name = $this->db->get_where('series', array('series_id' => $series_id))->row()->title;
	$season_name = $this->db->get_where('season', array('season_id' => $season_id))->row()->name;

	$page_data['page_title'] = 'Manage episodes of ' . $season_name . ' : ' . $series_name;
	$page_data['season_name'] = $season_name;
	$page_data['series_id'] = $series_id;
	$page_data['season_id'] = $season_id;
	$page_data['page_name'] = 'season_edit';

	$this->load->view('backend/index', $page_data);
}

	// DELETE A SEASON
	function season_delete($series_id = '', $season_id = '')
	{
		$this->db->delete('season',  array('season_id' => $season_id));
		redirect(base_url().'index.php?admin/series_edit/'.$series_id , 'refresh');
	}

	// CREATE A NEW EPISODE
	// function episode_create($series_id = '', $season_id = '')
	// {
		
		
	// 		$data['title']			=	$this->input->post('title');
	// 		//$data['url']			=	$this->input->post('url');
	// 		$data['season_id']		=	$season_id;
	// 		$this->db->insert('episode', $data);
	// 		$episode_id = $this->db->insert_id();
	// 		if (isset($_FILES['url']) && $_FILES['url']['error'] == 0) {
	// 			$video_name = $_FILES['url']['name']; // اسم الملف الأصلي
	// 			$video_path = 'assets/global/episode_video/' . $video_name;
		
	// 			// رفع الملف إلى المجلد المحدد
	// 			move_uploaded_file($_FILES['url']['tmp_name'], $video_path);
		
	// 			// الآن نقوم بتحديث قاعدة البيانات باستخدام اسم الملف الأصلي
	// 			$this->db->update('episode', ['url' => $video_name], ['episode_id' => $episode_id]);
	// 		} else {
	// 			echo "Error uploading episode video.";
	// 			return;
	// 		}
	// 		if (isset($_FILES['thumb']) && $_FILES['thumb']['error'] == 0){
	// 		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/episode_thumb/' . $episode_id . '.jpg');}
			
	// 		redirect(base_url().'index.php?admin/season_edit/'.$series_id.'/'.$season_id , 'refresh');
		
	// }

	// CREATE A NEW EPISODE
	function episode_create($series_id = '', $season_id = '')
{
	// 1. إدخال بيانات الحلقة إلى قاعدة البيانات
	$data['title'] = $this->input->post('title');
	$data['season_id'] = $season_id;
	$this->db->insert('episode', $data);
	$episode_id = $this->db->insert_id();

	// 2. جلب اسم المسلسل
	$this->db->where('series_id', $series_id);
	$series = $this->db->get('series')->row_array();
	$series_title_raw = $series['title'];
	$series_folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $series_title_raw);

	// 3. جلب اسم الموسم
	$this->db->where('season_id', $season_id);
	$season = $this->db->get('season')->row_array();
	$season_name_raw = $season['name'];
	$season_folder_name = preg_replace('/\s+/', '_', $season_name_raw);

	// 4. إعداد المسار الكامل للمجلد
	$folder_path = 'assets/global/series/' . $series_folder_name . '/' . $season_folder_name;

	// 5. إنشاء المجلد إذا لم يكن موجودًا
	if (!is_dir($folder_path)) {
		mkdir($folder_path, 0777, true);
	}

	// 6. حفظ ملف الفيديو باسم مأخوذ من عنوان الحلقة
	if (isset($_FILES['url']) && $_FILES['url']['error'] == 0) {
		$episode_title = $data['title'];
		$clean_filename = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $episode_title) . '.mp4';
		$video_path = $folder_path . '/' . $clean_filename;

		move_uploaded_file($_FILES['url']['tmp_name'], $video_path);

		// تحديث اسم الملف في قاعدة البيانات
		$this->db->update('episode', ['url' => $clean_filename], ['episode_id' => $episode_id]);
	} else {
		echo "Error uploading episode video.";
		return;
	}

	// 7. رفع صورة الحلقة داخل نفس المجلد
	// if (isset($_FILES['thumb']) && $_FILES['thumb']['error'] == 0) {
	// 	$thumb_path = $folder_path . '/thumb_' . $episode_id . '.jpg';
	// 	move_uploaded_file($_FILES['thumb']['tmp_name'], $thumb_path);
	// }

	// 8. إعادة التوجيه
	redirect(base_url().'index.php?admin/season_edit/'.$series_id.'/'.$season_id , 'refresh');
}
// edit existing episode
	// function episode_edit($series_id = '', $season_id = '', $episode_id = '')
	// {
	// 	if (isset($_POST) && !empty($_POST))
	// 	{
	// 		$data['title']			=	$this->input->post('title');
	// 		//$data['url']			=	$this->input->post('url');
	// 		$data['season_id']		=	$season_id;
	// 		$this->db->update('episode', $data, array('episode_id'=>$episode_id));
	// 		if (isset($_FILES['url']) && $_FILES['url']['error'] == 0) {
	// 			$video_name = $_FILES['url']['name']; // اسم الملف الأصلي
	// 			$video_path = 'assets/global/episode_video/' . $video_name;
		
	// 			// رفع الملف إلى المجلد المحدد
	// 			move_uploaded_file($_FILES['url']['tmp_name'], $video_path);
		
	// 			// الآن نقوم بتحديث قاعدة البيانات باستخدام اسم الملف الأصلي
	// 			$this->db->update('episode', ['url' => $video_name], ['episode_id' => $episode_id]);
	// 		} else {
	// 			echo "Error uploading episode video.";
	// 			return;
	// 		}
	// 		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/episode_thumb/' . $episode_id . '.jpg');
	// 		redirect(base_url().'index.php?admin/season_edit/'.$series_id.'/'.$season_id , 'refresh');
	// 	}
	// }


function episode_edit($series_id = '', $season_id = '', $episode_id = '')
{
    if (isset($_POST) && !empty($_POST)) {
        // 1. جمع البيانات من النموذج
        $data['title'] = $this->input->post('title');
        $data['season_id'] = $season_id;

        // 2. تحديث بيانات الحلقة في قاعدة البيانات
        $this->db->update('episode', $data, array('episode_id' => $episode_id));

        // 3. تجهيز اسم المسلسل والموسم
        $series_title = $this->db->get_where('series', ['series_id' => $series_id])->row()->title;
        $season_name = $this->db->get_where('season', ['season_id' => $season_id])->row()->name;

        // 4. تجهيز مسار مجلد المسلسل والموسم
        $series_folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $series_title);
        $season_folder_name = preg_replace('/\s+/', '_', $season_name);

        $series_folder_path = 'assets/global/series/' . $series_folder_name;
        $season_folder_path = $series_folder_path . '/' . $season_folder_name;

        // 5. إنشاء مجلد الموسم إذا لم يكن موجودًا
        if (!is_dir($season_folder_path)) {
            mkdir($season_folder_path, 0777, true);
        }

        // 6. رفع الفيديو الخاص بالحلقات
        if (isset($_FILES['url']) && $_FILES['url']['error'] == 0) {
            // استخدام عنوان الحلقة كاسم للملف
            $video_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $data['title']) . '.mp4';
            $video_path = $season_folder_path . '/' . $video_name;

            // رفع الملف إلى المجلد المحدد
            move_uploaded_file($_FILES['url']['tmp_name'], $video_path);

            // الآن نقوم بتحديث قاعدة البيانات باستخدام اسم الملف الأصلي
            $this->db->update('episode', ['url' => $video_name], ['episode_id' => $episode_id]);
        } else {
            echo "Error uploading episode video.";
            return;
        }

        // 7. رفع صورة الـ thumb الخاصة بالحلقات
        if (isset($_FILES['thumb']) && $_FILES['thumb']['error'] == 0) {
            $thumb_path = $season_folder_path . '/' . preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $data['title']) . '.jpg';
            move_uploaded_file($_FILES['thumb']['tmp_name'], $thumb_path);
        }

        // 8. إعادة التوجيه إلى صفحة تعديل الموسم
        redirect(base_url() . 'index.php?admin/season_edit/' . $series_id . '/' . $season_id, 'refresh');
    }
}


	// DELETE AN EPISODE
	// function episode_delete($series_id = '', $season_id = '', $episode_id = '')
	// {
	// 	$this->db->delete('episode',  array('episode_id' => $episode_id));
	// 	redirect(base_url().'index.php?admin/season_edit/'.$series_id.'/'.$season_id , 'refresh');
	// }
	function episode_delete($series_id = '', $season_id = '', $episode_id = '')
{
    // 1. الحصول على تفاصيل الحلقة (العنوان) للتأكد من المسارات
    $episode = $this->db->get_where('episode', array('episode_id' => $episode_id))->row();

    // 2. تجهيز مسار المجلد الخاص بالمسلسل والموسم
    $series_title = $this->db->get_where('series', ['series_id' => $series_id])->row()->title;
    $season_name = $this->db->get_where('season', ['season_id' => $season_id])->row()->name;

    // تجهيز مسار مجلد المسلسل والموسم
    $series_folder_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $series_title);
    $season_folder_name = preg_replace('/\s+/', '_', $season_name);

    $series_folder_path = 'assets/global/series/' . $series_folder_name;
    $season_folder_path = $series_folder_path . '/' . $season_folder_name;

    // 3. تحديد مسار الفيديو والصورة الخاصة بالحلقة
    $video_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $episode->title) . '.mp4';
    $thumb_name = preg_replace('/[^\p{Arabic}a-zA-Z0-9_\-]/u', '_', $episode->title) . '.jpg';

    $video_path = $season_folder_path . '/' . $video_name;
    $thumb_path = $season_folder_path . '/' . $thumb_name;

    // 4. حذف الفيديو والصورة إذا كانا موجودين
    if (file_exists($video_path)) {
        unlink($video_path);
    }
    if (file_exists($thumb_path)) {
        unlink($thumb_path);
    }

    // 5. حذف الحلقة من قاعدة البيانات
    $this->db->delete('episode', array('episode_id' => $episode_id));

    // 6. إعادة التوجيه إلى صفحة تعديل الموسم
    redirect(base_url() . 'index.php?admin/season_edit/' . $series_id . '/' . $season_id, 'refresh');
}


	// WATCH LIST OF ACTORS, MANAGE THEM
	function actor_list()
	{
		$page_data['page_name']		=	'actor_list';
		$page_data['page_title']	=	'Manage actor';
		$this->load->view('backend/index', $page_data);
	}

	// CREATE A NEW ACTOR
	function actor_create()
	{
		if (isset($_POST) && !empty($_POST))
		{
			$this->crud_model->create_actor();
			redirect(base_url().'index.php?admin/actor_list' , 'refresh');
		}
		$page_data['page_name']		=	'actor_create';
		$page_data['page_title']	=	'Create actor';
		$this->load->view('backend/index', $page_data);
	}

	// EDIT A ACTOR
	function actor_edit($actor_id = '')
	{
		if (isset($_POST) && !empty($_POST))
		{
			$this->crud_model->update_actor($actor_id);
			redirect(base_url().'index.php?admin/actor_list' , 'refresh');
		}
		$page_data['actor_id']		=	$actor_id;
		$page_data['page_name']		=	'actor_edit';
		$page_data['page_title']	=	'Edit actor';
		$this->load->view('backend/index', $page_data);
	}

	// DELETE A ACTOR
	function actor_delete($actor_id = '')
	{
		$this->db->delete('actor',  array('actor_id' => $actor_id));
		redirect(base_url().'index.php?admin/actor_list' , 'refresh');
	}

	// WATCH LIST OF Category, MANAGE THEM
	function category_list()
	{
		$page_data['page_name']		=	'category_list';
		$page_data['page_title']	=	'Manage Categories';
		$this->load->view('backend/index', $page_data);
	}

	// CREATE A NEW Category
	function category_create()
	{
		if (isset($_POST) && !empty($_POST))
		{
			$this->crud_model->create_category();
			redirect(base_url().'index.php?admin/category_list' , 'refresh');
		}
		$page_data['page_name']		=	'category_create';
		$page_data['page_title']	=	'Create category';
		$this->load->view('backend/index', $page_data);
	}

	// EDIT A Category
	function category_edit($category_id = '')
	{
		if (isset($_POST) && !empty($_POST))
		{
			$this->crud_model->update_category($category_id);
			redirect(base_url().'index.php?admin/category_list' , 'refresh');
		}
		$page_data['category_id']		=	$category_id;
		$page_data['page_name']		=	'category_edit';
		$page_data['page_title']	=	'Edit category';
		$this->load->view('backend/index', $page_data);
	}

	// DELETE A Category
	function category_delete($category_id = '')
	{
		$this->db->delete('category',  array('category_id' => $category_id));
		redirect(base_url().'index.php?admin/category_list' , 'refresh');
	}

	// // WATCH LIST OF PRICING PACKAGES, MANAGE THEM
	// function plan_list()
	// {
	// 	$page_data['page_name']		=	'plan_list';
	// 	$page_data['page_title']	=	'Manage plan';
	// 	$this->load->view('backend/index', $page_data);
	// }

	// EDIT A ACTOR
	// function plan_edit($plan_id = '')
	// {
	// 	if (isset($_POST) && !empty($_POST))
	// 	{
	// 		$data['name']			=	$this->input->post('name');
	// 		$data['price']			=	$this->input->post('price');
	// 		$data['status']			=	$this->input->post('status');
	// 		$this->db->update('plan', $data,  array('plan_id' => $plan_id));
	// 		redirect(base_url().'index.php?admin/plan_list' , 'refresh');
	// 	}
	// 	$page_data['plan_id']		=	$plan_id;
	// 	$page_data['page_name']		=	'plan_edit';
	// 	$page_data['page_title']	=	'Edit plan';
	// 	$this->load->view('backend/index', $page_data);
	// }

	// WATCH LIST OF USERS, MANAGE THEM
	function user_list()
	{
		$page_data['page_name']		=	'user_list';
		$page_data['page_title']	=	'Manage user';
		$this->load->view('backend/index', $page_data);
	}

	// CREATE A NEW USER
	function user_create()
	{
		if (isset($_POST) && !empty($_POST))
		{
			$this->crud_model->create_user();
			redirect(base_url().'index.php?admin/user_list' , 'refresh');
		}
		$page_data['page_name']		=	'user_create';
		$page_data['page_title']	=	'Create user';
		$this->load->view('backend/index', $page_data);
	}

	// EDIT A USER
	function user_edit($edit_user_id = '')
	{
		if (isset($_POST) && !empty($_POST))
		{
			$this->crud_model->update_user($edit_user_id);
			redirect(base_url().'index.php?admin/user_list' , 'refresh');
		}
		$page_data['edit_user_id']	=	$edit_user_id;
		$page_data['page_name']		=	'user_edit';
		$page_data['page_title']	=	'Edit user';
		$this->load->view('backend/index', $page_data);
	}

	// DELETE A USER
	function user_delete($user_id = '')
	{
		$this->db->delete('user',  array('user_id' => $user_id));
		redirect(base_url().'index.php?admin/user_list' , 'refresh');
	}

	// WATCH SUBSCRIPTION, PAYMENT REPORT
	// function report($month = '', $year = '')
	// {
	// 	if ($month == '')
	// 		$month	=	date("F");
	// 	if ($year == '')
	// 		$year = date("Y");

	// 	$page_data['month']			=	$month;
	// 	$page_data['year']			=	$year;
	// 	$page_data['page_name']		=	'report';
	// 	$page_data['page_title']	=	'Customer subscription & payment report';
	// 	$this->load->view('backend/index', $page_data);
	// }

	// WATCH LIST OF FAQS, MANAGE THEM
	function faq_list()
	{
		$page_data['page_name']		=	'faq_list';
		$page_data['page_title']	=	'Manage faq';
		$this->load->view('backend/index', $page_data);
	}

	// CREATE A NEW FAQ
	function faq_create()
	{
		if (isset($_POST) && !empty($_POST))
		{
			$data['question']		=	$this->input->post('question');
			$data['answer']			=	$this->input->post('answer');
			$this->db->insert('faq', $data);
			redirect(base_url().'index.php?admin/faq_list' , 'refresh');
		}
		$page_data['page_name']		=	'faq_create';
		$page_data['page_title']	=	'Create faq';
		$this->load->view('backend/index', $page_data);
	}

	// EDIT A FAQ
	function faq_edit($faq_id = '')
	{
		if (isset($_POST) && !empty($_POST))
		{
			$data['question']		=	$this->input->post('question');
			$data['answer']			=	$this->input->post('answer');
			$this->db->update('faq', $data,  array('faq_id' => $faq_id));
			redirect(base_url().'index.php?admin/faq_list' , 'refresh');
		}
		$page_data['faq_id']		=	$faq_id;
		$page_data['page_name']		=	'faq_edit';
		$page_data['page_title']	=	'Edit faq';
		$this->load->view('backend/index', $page_data);
	}

	// DELETE A FAQ
	function faq_delete($faq_id = '')
	{
		$this->db->delete('faq',  array('faq_id' => $faq_id));
		redirect(base_url().'index.php?admin/faq_list' , 'refresh');
	}

	// EDIT SETTINGS
	function settings()
	{
		if (isset($_POST) && !empty($_POST))
		{
			// Updating website name
			$data['description']		=	$this->input->post('site_name');
			$this->db->update('settings', $data,  array('type' => 'site_name'));

			// Updating website email
			$data['description']		=	$this->input->post('site_email');
			$this->db->update('settings', $data,  array('type' => 'site_email'));

			// Updating trial period enable/disable
			$data['description']		=	$this->input->post('trial_period');
			$this->db->update('settings', $data,  array('type' => 'trial_period'));

			// Updating trial period number of days
			$data['description']		=	$this->input->post('trial_period_days');
			$this->db->update('settings', $data,  array('type' => 'trial_period_days'));

			// Updating website language settings
			$data['description']		=	$this->input->post('language');
			$this->db->update('settings', $data,  array('type' => 'language'));

			// Updating website theme settings
			$data['description']		=	$this->input->post('theme');
			$this->db->update('settings', $data,  array('type' => 'theme'));

			// Updating website paypal merchant email
			$data['description']		=	$this->input->post('paypal_merchant_email');
			$this->db->update('settings', $data,  array('type' => 'paypal_merchant_email'));

			// Updating invoice address
			$data['description']		=	$this->input->post('invoice_address');
			$this->db->update('settings', $data,  array('type' => 'invoice_address'));

			// Updating envato purchase code
			$data['description']		=	$this->input->post('purchase_code');
			$this->db->update('settings', $data,  array('type' => 'purchase_code'));

			// Updating privacy policy
			$data['description']		=	$this->input->post('privacy_policy');
			$this->db->update('settings', $data,  array('type' => 'privacy_policy'));

			// Updating refund policy
			$data['description']		=	$this->input->post('refund_policy');
			$this->db->update('settings', $data,  array('type' => 'refund_policy'));

			// Updating stripe publishable key
			$data['description']		=	$this->input->post('stripe_publishable_key');
			$this->db->update('settings', $data,  array('type' => 'stripe_publishable_key'));

			// Updating stripe secret key
			$data['description']		=	$this->input->post('stripe_secret_key');
			$this->db->update('settings', $data,  array('type' => 'stripe_secret_key'));

			// Updating cookie status
			$data['description']		=	$this->input->post('cookie_status');
			$this->db->update('settings', $data,  array('type' => 'cookie_status'));

			// Updating cookie note
			$data['description']		=	$this->input->post('cookie_note');
			$this->db->update('settings', $data,  array('type' => 'cookie_note'));

			// Updating cookie policy
			$data['description']		=	$this->input->post('cookie_policy');
			$this->db->update('settings', $data,  array('type' => 'cookie_policy'));

			// Updating email verification status
			$data['description']		=	$this->input->post('email_verification');
			$this->db->update('settings', $data,  array('type' => 'email_verification'));

			// Updating recaptcha status
			$data['description']		=	$this->input->post('recaptcha');
			$this->db->update('settings', $data,  array('type' => 'recaptcha'));

			$data['description']		=	$this->input->post('recaptcha_secretkey');
			$this->db->update('settings', $data,  array('type' => 'recaptcha_secretkey'));

			$data['description']		=	$this->input->post('recaptcha_sitekey');
			$this->db->update('settings', $data,  array('type' => 'recaptcha_sitekey'));

			move_uploaded_file($_FILES['logo']['tmp_name'], 'assets/global/logo.png');

			redirect(base_url().'index.php?admin/settings' , 'refresh');
		}

		$page_data['site_name']				=	$this->db->get_where('settings',array('type'=>'site_name'))->row('description');
		$page_data['site_email']			=	$this->db->get_where('settings',array('type'=>'site_email'))->row('description');
		$page_data['trial_period']			=	$this->db->get_where('settings',array('type'=>'trial_period'))->row('description');
		$page_data['trial_period_days']		=	$this->db->get_where('settings',array('type'=>'trial_period_days'))->row('description');
		$page_data['theme']					=	$this->db->get_where('settings',array('type'=>'theme'))->row('description');
		$page_data['paypal_merchant_email']	=	$this->db->get_where('settings',array('type'=>'paypal_merchant_email'))->row('description');
		$page_data['invoice_address']		=	$this->db->get_where('settings',array('type'=>'invoice_address'))->row('description');
		$page_data['purchase_code']			=	$this->db->get_where('settings',array('type'=>'purchase_code'))->row('description');
		$page_data['privacy_policy']		=	$this->db->get_where('settings',array('type'=>'privacy_policy'))->row('description');
		$page_data['refund_policy']			=	$this->db->get_where('settings',array('type'=>'refund_policy'))->row('description');
		$page_data['stripe_publishable_key']=	$this->db->get_where('settings',array('type'=>'stripe_publishable_key'))->row('description');
		$page_data['stripe_secret_key']		=	$this->db->get_where('settings',array('type'=>'stripe_secret_key'))->row('description');
		$page_data['languages']	 = $this->get_all_languages();
		$page_data['page_name']				=	'settings';
		$page_data['page_title']			=	'Website settings';
		$this->load->view('backend/index', $page_data);
	}

	// function payment_settings($param1 = "", $param2 = "") {

	// 	if ($param1 == 'system_currency') {
    //         $this->crud_model->system_currency();
    //         redirect(base_url('index.php?admin/payment_settings'), 'refresh');
    //     }

    //     if ($param1 == 'paypal') {
    //         $this->crud_model->update_paypal_keys();
    //         redirect(base_url('index.php?admin/payment_settings'), 'refresh');
    //     }

    //     if ($param1 == 'stripe') {
    //         $this->crud_model->update_stripe_keys();
    //         redirect(base_url('index.php?admin/payment_settings'), 'refresh');
    //     }

    //     $this->session->set_userdata('last_page', 'payment_settings');
    //     $page_data['page_name'] = 'payment_settings';
    //     $page_data['page_title'] = get_phrase('payment_settings');
    //     $this->load->view('backend/index', $page_data);
    // }

    public function smtp_settings($param1 = "") {
        if ($param1 == 'update') {
            $this->crud_model->update_smtp_settings();
            $this->session->set_flashdata('flash_message', get_phrase('smtp_settings_updated_successfully'));
            redirect(base_url('index.php?admin/smtp_settings'), 'refresh');
        }

        $page_data['page_name'] = 'smtp_settings';
        $page_data['page_title'] = get_phrase('smtp_settings');
        $this->load->view('backend/index', $page_data);
    }

	// function report_invoice($param1 = '', $param2 = ''){
	// 	$page_data['subscription_id'] = $param1;
	// 	$page_data['user_id'] = $param2;
	// 	$page_data['page_title']			=	'Customer subscription & payment invoice';
	// 	$this->load->view('backend/pages/report_invoice', $page_data);
	// }

	function get_list_of_directories_and_files($dir = APPPATH, &$results = array()) {
		$files = scandir($dir);
		foreach($files as $key => $value){
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			if(!is_dir($path)) {
				$results[] = $path;
			} else if($value != "." && $value != "..") {
				$this->get_list_of_directories_and_files($path, $results);
				$results[] = $path;
			}
		}
		return $results;
	}

	function get_all_php_files() {
		$all_files = $this->get_list_of_directories_and_files();
		foreach ($all_files as $file) {
			$info = pathinfo($file);
			if( isset($info['extension']) && strtolower($info['extension']) == 'php') {
				// echo $file.' <br/> ';
				if ($fh = fopen($file, 'r')) {
					while (!feof($fh)) {
						$line = fgets($fh);
						preg_match_all('/get_phrase\(\'(.*?)\'\)\;/s', $line, $matches);
						foreach ($matches[1] as $matche) {
							get_phrase($matche);
						}
					}
					fclose($fh);
				}
			}
		}

		echo 'I Am So Lit';
	}

	function get_list_of_language_files($dir = APPPATH.'/language', &$results = array()) {
		$files = scandir($dir);
		foreach($files as $key => $value){
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			if(!is_dir($path)) {
				$results[] = $path;
			} else if($value != "." && $value != "..") {
				$this->get_list_of_directories_and_files($path, $results);
				$results[] = $path;
			}
		}
		return $results;
	}

	function get_all_languages() {
		$language_files = array();
		$all_files = $this->get_list_of_language_files();
		foreach ($all_files as $file) {
			$info = pathinfo($file);
			if( isset($info['extension']) && strtolower($info['extension']) == 'json') {
				$file_name = explode('.json', $info['basename']);
				array_push($language_files, $file_name[0]);
			}
		}

		return $language_files;
	}

	// Language Functions
	public function manage_language($param1 = '', $param2 = '', $param3 = ''){

		if ($param1 == 'add_language') {
			saveDefaultJSONFile(sanitizer($this->input->post('language')));
			$this->session->set_flashdata('flash_message', get_phrase('language_added_successfully'));
			redirect(base_url().'index.php?admin/manage_language' , 'refresh');
		}

		if ($param1 == 'delete_language') {
		    if (file_exists('application/language/'.$param2.'.json')) {
		        unlink('application/language/'.$param2.'.json');
		        $this->session->set_flashdata('flash_message', get_phrase('language_deleted_successfully'));
			    redirect(base_url().'index.php?admin/manage_language' , 'refresh');
		    }
		}

		if ($param1 == 'add_phrase') {
			$new_phrase = get_phrase(sanitizer($this->input->post('phrase')));
			$this->session->set_flashdata('flash_message', $new_phrase.' '.get_phrase('has_been_added_successfully'));
			redirect(base_url().'index.php?admin/manage_language' , 'refresh');
		}

		if ($param1 == 'edit_phrase') {
			$page_data['edit_profile'] = $param2;
		}

		$page_data['languages']				= $this->get_all_languages();
		$page_data['page_name']				=	'manage_language';
		$page_data['page_title']			=	get_phrase('multi_language_settings');
		$this->load->view('backend/index', $page_data);
	}



	function account()
	{
		$user_id	=	$this->session->userdata('user_id');

		if (isset($_POST) && !empty($_POST))
		{
			$task	=	$this->input->post('task');
			if ($task == 'update_profile')
			{
				$data['name']				=	$this->input->post('name');
				$data['email']				=	$this->input->post('email');
				$this->db->update('user', $data, array('user_id'=>$user_id));
				redirect(base_url().'index.php?admin/account' , 'refresh');
			}
			else if ($task == 'update_password')
			{
				$old_password_encrypted				=	$this->crud_model->get_current_user_detail()->password;
				$old_password_submitted_encrypted	=	sha1($this->input->post('old_password'));
				$new_password						=	$this->input->post('new_password');
				$new_password_encrypted				=	sha1($this->input->post('new_password'));

				// CORRECT OLD PASSWORD NEEDED TO CHANGE PASSWORD
				if ($old_password_encrypted 		==	$old_password_submitted_encrypted)
				{
					$this->db->update('user', array('password'=>$new_password_encrypted), array('user_id'=>$user_id));
					$this->session->set_flashdata('status', 'password_changed');
				}
				redirect(base_url().'index.php?admin/account' , 'refresh');
			}
		}
		$page_data['page_name']				=	'account';
		$page_data['page_title']			=	'Manage account';
		$this->load->view('backend/index', $page_data);
	}


	function admin_login_check()
	{
		$logged_in_user_type			=	$this->session->userdata('login_type');
		if ($logged_in_user_type == 0)
		{
			redirect(base_url().'index.php?home/signin' , 'refresh');
		}
	}

	function actor_wise_movie_and_series($actor_id) {
		$actor_details = $this->db->get_where('actor', array('actor_id' => $actor_id))->row_array();
		$page_data['page_name']				=	'actor_wise_movie_and_series';
		$page_data['page_title']			=	get_phrase('movies_and_TV_series_of').' "'.$actor_details['name'].'"';
		$page_data['actor_id']				=	$actor_id;

		$this->load->view('backend/index', $page_data);
	}

	function director_wise_movie_and_series($category_id) {
		$director_details = $this->db->get_where('category', array('category_id' => $category_id))->row_array();
		$page_data['page_name']				=	'director_wise_movie_and_series';
		$page_data['page_title']			=	get_phrase('TV_series_of').' "'.$director_details['name'].'"';
		$page_data['category_id']			=	$category_id;

		$this->load->view('backend/index', $page_data);
	}

	public function update_phrase_with_ajax() {
		$current_editing_language = sanitizer($this->input->post('currentEditingLanguage'));
		$updatedValue = sanitizer($this->input->post('updatedValue'));
		$key = sanitizer($this->input->post('key'));
		saveJSONFile($current_editing_language, $key, $updatedValue);
		echo $current_editing_language.' '.$key.' '.$updatedValue;
	}

  public function about(){
		$page_data['application_details'] = $this->crud_model->get_application_details();
		$page_data['page_name']  = 'about';
		$page_data['page_title'] = get_phrase('about');
		$this->load->view('backend/index', $page_data);
  }

  //ADDON MANAGER PORTION STARTS HERE
  public function addon($param1 = "", $param2 = "", $param3 = "") {
    // ADD NEW ADDON FORM
    if ($param1 == 'add') {
      $page_data['page_name'] = 'addon_add';
      $page_data['page_title'] = get_phrase('add_addon');
    }

    // INSTALLING AN ADDON
    if ($param1 == 'install') {
        $this->addon_model->install_addon();
    }

    // ACTIVATING AN ADDON
    if ($param1 == 'activate') {
      $update_message = $this->addon_model->addon_activate($param2);
      $this->session->set_flashdata('flash_message', get_phrase($update_message));
      redirect(site_url('index.php?admin/addon'), 'refresh');
    }

    // DEACTIVATING AN ADDON
    if ($param1 == 'deactivate') {
      $update_message = $this->addon_model->addon_deactivate($param2);
      $this->session->set_flashdata('flash_message', get_phrase($update_message));
      redirect(site_url('index.php?admin/addon'), 'refresh');
    }

    // REMOVING AN ADDON
    if ($param1 == 'delete') {
      $this->addon_model->addon_delete($param2);
      $this->session->set_flashdata('flash_message', get_phrase('addon_is_deleted_successfully'));
      redirect(site_url('index.php?admin/addon'), 'refresh');
    }

	// ABOUT THIS ADDON
	if ($param1 == 'about') {
      $page_data['page_name'] = 'about_addon';
	  $this->db->where('id', $param2);
      $page_data['addon'] = $this->db->get('addons')->row_array();
      $page_data['page_title'] = get_phrase('about');
    }

    // SHOWING LIST OF INSTALLED ADDONS
    if (empty($param1)) {
      $page_data['page_name'] = 'addons';
      $page_data['addons'] = $this->addon_model->addon_list()->result_array();
      $page_data['page_title'] = get_phrase('addon_manager');
    }
    $this->load->view('backend/index', $page_data);
  }

  //AVAILABLE_ADDONS
  public function available_addons(){
    $page_data['page_name']  = 'available_addon';
    $page_data['page_title'] = get_phrase('available_addon');
    $this->load->view('backend/index', $page_data);
  }

public function fetch_tmdb_series_data() {
	$title = $this->input->post('title');
	$apiKey = '550cd509e7933045659e6f893e844d64';

	if (empty($title)) {
		echo json_encode(['error' => 'لم يتم إدخال عنوان المسلسل']);
		return;
	}

	function curl_get($url) {
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
		]);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	function insert_if_not_exists($table, $name, $profile_image_url = null) {
		$ci = &get_instance();
		$ci->db->where('name', $name);
		$query = $ci->db->get($table);

		if ($query->num_rows() == 0) {
			$data = ['name' => $name];
			$ci->db->insert($table, $data);
			$insert_id = $ci->db->insert_id();

			if ($profile_image_url) {
				$image_directory = 'assets/global/actor/';
				if (!is_dir($image_directory)) {
					mkdir($image_directory, 0777, true);
				}

				$image_ext = pathinfo(parse_url($profile_image_url, PHP_URL_PATH), PATHINFO_EXTENSION);
				if (!$image_ext) $image_ext = 'jpg';

				$local_filename = $insert_id . '.' . strtolower($image_ext);
				$local_path = $image_directory . $local_filename;

				$image_data = file_get_contents($profile_image_url);
				if ($image_data !== false) {
					file_put_contents($local_path, $image_data);
				}
			}
		}
	}

	// Step 1: البحث عن المسلسل
	$searchUrl = "https://api.themoviedb.org/3/search/tv?api_key={$apiKey}&query=" . urlencode($title) . "&language=ar";
	$searchResponse = curl_get($searchUrl);
	$searchData = json_decode($searchResponse, true);

	if (!isset($searchData['results'][0])) {
		echo json_encode(['error' => '❌ لم يتم العثور على نتائج للمسلسل']);
		return;
	}

	$series = $searchData['results'][0];
	$seriesId = $series['id'];

	// Step 2: تفاصيل المسلسل
	$detailsUrl = "https://api.themoviedb.org/3/tv/{$seriesId}?api_key={$apiKey}&language=ar";
	$details = json_decode(curl_get($detailsUrl), true);

	// Step 3: الممثلين
	$creditsUrl = "https://api.themoviedb.org/3/tv/{$seriesId}/credits?api_key={$apiKey}&language=ar";
	$credits = json_decode(curl_get($creditsUrl), true);

	$actors = [];
	if (isset($credits['cast'])) {
		foreach (array_slice($credits['cast'], 0, 5) as $actor) {
			$actors[] = $actor['name'];
			$profile_image_url = isset($actor['profile_path']) ? 'https://image.tmdb.org/t/p/w500' . $actor['profile_path'] : null;
			insert_if_not_exists('actor', $actor['name'], $profile_image_url);
		}
	}

	// Step 4: الأنواع
	$genreNames = [];
	if (isset($details['genres'])) {
		foreach ($details['genres'] as $g) {
			$genreNames[] = $g['name'];
			insert_if_not_exists('genre', $g['name']);
		}
	}

	// Step 5: الدول
	$countries = [];

	// جلب قائمة الدول من TMDB
	$countryApiUrl = "https://api.themoviedb.org/3/configuration/countries?api_key={$apiKey}";
	$countryList = json_decode(curl_get($countryApiUrl), true);

	$countryMap = [];
	if (is_array($countryList)) {
		foreach ($countryList as $countryItem) {
			$countryMap[$countryItem['iso_3166_1']] = $countryItem['english_name'];
		}
	}

	if (isset($details['origin_country'])) {
		foreach ($details['origin_country'] as $c_code) {
			if (isset($countryMap[$c_code])) {
				$country_name = $countryMap[$c_code];
				$countries[] = $country_name;
				insert_if_not_exists('country', $country_name);
			} else {
				$countries[] = $c_code; // fallback
				insert_if_not_exists('country', $c_code);
			}
		}
	}

	// النتيجة النهائية
	$result = [
		'title' => $details['name'],
		'overview' => $details['overview'],
		'release_date' => $details['first_air_date'],
		'vote_average' => $details['vote_average'],
		'poster_path' => $details['poster_path'],
		'backdrop_path' => $details['backdrop_path'],
		'actors' => $actors,
		'genres' => $genreNames,
		'countries' => $countries,
		
	];

	echo json_encode($result);
}



}
