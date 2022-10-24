<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('api_model');
		$this->load->helper('url');
		$this->load->helper('text');
	}

	public function blogs()
	{
		header("Access-Control-Allow-Origin: *");

		$blogs = $this->api_model->get_blogs($featured=false, $recentpost=false);

		$posts = array();
		if(!empty($blogs)){
			foreach($blogs as $blog){

				$short_desc = strip_tags(character_limiter($blog->description, 70));
				$author = $blog->first_name.' '.$blog->last_name;

				$posts[] = array(
					'id' => $blog->id,
					'title' => $blog->title,
					'short_desc' => html_entity_decode($short_desc),
					'author' => $author,
					'image' => base_url('media/images/'.$blog->image),
					'created_at' => $blog->created_at
				);
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($posts));
	}

	public function featured_blogs()
	{
		header("Access-Control-Allow-Origin: *");

		$blogs = $this->api_model->get_blogs($featured=true, $recentpost=false);

		$posts = array();
		if(!empty($blogs)){
			foreach($blogs as $blog){
				
				$short_desc = strip_tags(character_limiter($blog->description, 70));
				$author = $blog->first_name.' '.$blog->last_name;

				$posts[] = array(
					'id' => $blog->id,
					'title' => $blog->title,
					'short_desc' => html_entity_decode($short_desc),
					'author' => $author,
					'image' => base_url('media/images/'.$blog->image),
					'created_at' => $blog->created_at
				);
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($posts));
	}

	public function blog($id)
	{
		header("Access-Control-Allow-Origin: *");
		
		$blog = $this->api_model->get_blog($id);

		$author = $blog->first_name.' '.$blog->last_name;

		$post = array(
			'id' => $blog->id,
			'title' => $blog->title,
			'description' => $blog->description,
			'author' => $author,
			'image' => base_url('media/images/'.$blog->image),
			'created_at' => $blog->created_at
		);
		
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($post));
	}

	public function recent_blogs()
	{
		header("Access-Control-Allow-Origin: *");

		$blogs = $this->api_model->get_blogs($featured=false, $recentpost=5);

		$posts = array();
		if(!empty($blogs)){
			foreach($blogs as $blog){
				
				$short_desc = strip_tags(character_limiter($blog->description, 70));
				$author = $blog->first_name.' '.$blog->last_name;

				$posts[] = array(
					'id' => $blog->id,
					'title' => $blog->title,
					'short_desc' => html_entity_decode($short_desc),
					'author' => $author,
					'image' => base_url('media/images/'.$blog->image),
					'created_at' => $blog->created_at
				);
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($posts));
	}

	public function categories()
	{
		header("Access-Control-Allow-Origin: *");

		$categories = $this->api_model->get_categories();

		$category = array();
		if(!empty($categories)){
			foreach($categories as $cate){
				$category[] = array(
					'id' => $cate->id,
					'name' => $cate->category_name
				);
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($category));
	}

	public function page($slug)
	{
		header("Access-Control-Allow-Origin: *");
		
		$page = $this->api_model->get_page($slug);

		$pagedata = array(
			'id' => $page->id,
			'title' => $page->title,
			'description' => $page->description
		);
		
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($pagedata));
	}

	public function contact()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
		header('Access-Control-Allow-Headers: Accept,Accept-Language,Content-Language,Content-Type');

		$formdata = json_decode(file_get_contents('php://input'), true);

		if( ! empty($formdata)) {

			$name = $formdata['name'];
			$email = $formdata['email'];
			$phone = $formdata['phone'];
			$message = $formdata['message'];

			$contactData = array(
				'name' => $name,
				'email' => $email,
				'phone' => $phone,
				'message' => $message,
				'created_at' => date('Y-m-d H:i:s', time())
			);
			
			$id = $this->api_model->insert_contact($contactData);

			$this->sendemail($contactData);
			
			$response = array('id' => $id);
		}
		else {
			$response = array('id' => '');
		}
		
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($response));
	}

	public function sendemail($contactData)
	{
		$message = '<p>Hi, <br />Some one has submitted contact form.</p>';
		$message .= '<p><strong>Name: </strong>'.$contactData['name'].'</p>';
		$message .= '<p><strong>Email: </strong>'.$contactData['email'].'</p>';
		$message .= '<p><strong>Phone: </strong>'.$contactData['phone'].'</p>';
		$message .= '<p><strong>Name: </strong>'.$contactData['message'].'</p>';
		$message .= '<br />Thanks';

		$this->load->library('email');

		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'iso-8859-1';
		$config['wordwrap'] = TRUE;
		$config['mailtype'] = 'html';

		$this->email->initialize($config);

		$this->email->from('mail@vsgfi.com', 'Our Lady Of Assumption');
		$this->email->to('mail@vsgfi.com');
		$this->email->cc('mail@vsgfi.com');
		$this->email->bcc('mail@vsgfi.com');

		$this->email->subject('Contact Form');
		$this->email->message($message);

		$this->email->send();
	}

	public function login() 
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
		header('Access-Control-Allow-Headers: Accept,Accept-Language,Content-Language,Content-Type');

		$formdata = json_decode(file_get_contents('php://input'), true);

		$username = $formdata['username'];
		$password = $formdata['password'];

		$user = $this->api_model->login($username, $password);

		if($user) {
			$response = array(
				'user_id' => $user->id,
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
				'token' => $user->token
			);
		}
		else {
			$response = array();
		}

		$this->output
				->set_content_type('application/json')
				->set_output(json_encode($response));
	}
	public function usernamecheck() 
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
		header('Access-Control-Allow-Headers: Accept,Accept-Language,Content-Language,Content-Type');

		$formdata = json_decode(file_get_contents('php://input'), true);

		$username = $formdata['username'];

		$user = $this->api_model->username($username);

		if($user) {
			$response = array(
				$response = 'Already Exists!!'
			);
		}

		$this->output
				->set_content_type('application/json')
				->set_output(json_encode($response));
	}
	public function register() 
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		$formdata = json_decode(file_get_contents('php://input'), true);
		
			if($isValidToken) {
				$data=$this->api_model->generateToken($formdata);
				if($data)
				 {
					echo "success";
				 }else{
					echo "failure";
			   }
			}			 
	}
	public function addSubMember() 
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		$formdata = json_decode(file_get_contents('php://input'), true);
		
			if($isValidToken) {
				$data=$this->api_model->generateSubMemberToken($formdata);
				if($data)
				 {
					echo "success";
				 }else{
					echo "failure";
			   }
			}			 
	}
	public function addIncomeAndExpen() 
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		$formdata = json_decode(file_get_contents('php://input'), true);
		
			if($isValidToken) {
				$data=$this->api_model->generateIncomeExpenToken($formdata);
				if($data)
				 {
					echo "success";
				 }else{
					echo "failure";
			   }
			}			 
	}
	public function adminBlogs()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		$posts = array();
		if($isValidToken) {
			$blogs = $this->api_model->get_admin_blogs();
			foreach($blogs as $blog) {
				$posts[] = array(
					'id' => $blog->id,
					'title' => $blog->title,
					'image' => base_url('media/images/'.$blog->image),
					'created_at' => $blog->created_at
				);
			}

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($posts)); 
		}
	}

	public function adminBlog($id)
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$blog = $this->api_model->get_admin_blog($id);

			$post = array(
				'id' => $blog->id,
				'title' => $blog->title,
				'description' => $blog->description,
				'image' => base_url('media/images/'.$blog->image),
				'is_featured' => $blog->is_featured,
				'is_active' => $blog->is_active
			);
			

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($post)); 
		}
	}

	public function getMyFamily($id)
	{
		
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$members = $this->api_model->get_my_family($id);

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($members)); 
		}
	}
	
	public function allMonthlyTax($id)
	{
		
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$members = $this->api_model->get_indi_monthlyTax($id);

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($members)); 
		}
	}
	public function adminUsers()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$members = $this->api_model->get_users();		

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($members)); 
		}
	}
	public function getAllIncome()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$members = $this->api_model->get_in();		

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($members)); 
		}
	}
	public function getIncomeMonthWiseApi($id)
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$members = $this->api_model->get_inMonth($id);		

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($members)); 
		}
	}
	public function getIncomeByYear($year)
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$members = $this->api_model->get_inYear($year);		

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($members)); 
		}
	}
	public function getExpenMonthWiseApi($id)
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$members = $this->api_model->get_exMonth($id);		

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($members)); 
		}
	}
	public function getExpenByYear($year)
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$members = $this->api_model->get_exYear($year);		

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($members)); 
		}
	}
	public function getAllExpen()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$members = $this->api_model->get_ex();		

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($members)); 
		}
	}
	public function getAllUserInAnbiyam($id)
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$members = $this->api_model->get_All_users_Anbiyam($id);		

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($members)); 
		}
	}
	public function createBlog()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$title = $this->input->post('title');
			$description = $this->input->post('description');
			$is_featured = $this->input->post('is_featured');
			$is_active = $this->input->post('is_active');

			$filename = NULL;

			$isUploadError = FALSE;

			if ($_FILES && $_FILES['image']['name']) {

				$config['upload_path']          = './media/images/';
	            $config['allowed_types']        = 'gif|jpg|png|jpeg';
	            $config['max_size']             = 500;

	            $this->load->library('upload', $config);
	            if ( ! $this->upload->do_upload('image')) {

	            	$isUploadError = TRUE;

					$response = array(
						'status' => 'error',
						'message' => $this->upload->display_errors()
					);
	            }
	            else {
	            	$uploadData = $this->upload->data();
            		$filename = $uploadData['file_name'];
	            }
			}

			if( ! $isUploadError) {
	        	$blogData = array(
					'title' => $title,
					'user_id' => 1,
					'description' => $description,
					'image' => $filename,
					'is_featured' => $is_featured,
					'is_active' => $is_active,
					'created_at' => date('Y-m-d H:i:s', time())
				);

				$id = $this->api_model->insertBlog($blogData);

				$response = array(
					'status' => 'success'
				);
			}

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($response)); 
		}
	}

	public function updateBlog($id)
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$blog = $this->api_model->get_admin_blog($id);
			$filename = $blog->image;

			$title = $this->input->post('title');
			$description = $this->input->post('description');
			$is_featured = $this->input->post('is_featured');
			$is_active = $this->input->post('is_active');

			$isUploadError = FALSE;

			if ($_FILES && $_FILES['image']['name']) {

				$config['upload_path']          = './media/images/';
	            $config['allowed_types']        = 'gif|jpg|png|jpeg';
	            $config['max_size']             = 500;

	            $this->load->library('upload', $config);
	            if ( ! $this->upload->do_upload('image')) {

	            	$isUploadError = TRUE;

					$response = array(
						'status' => 'error',
						'message' => $this->upload->display_errors()
					);
	            }
	            else {
	   
					if($blog->image && file_exists(FCPATH.'media/images/'.$blog->image))
					{
						unlink(FCPATH.'media/images/'.$blog->image);
					}

	            	$uploadData = $this->upload->data();
            		$filename = $uploadData['file_name'];
	            }
			}

			if( ! $isUploadError) {
	        	$blogData = array(
					'title' => $title,
					'user_id' => 1,
					'description' => $description,
					'image' => $filename,
					'is_featured' => $is_featured,
					'is_active' => $is_active
				);

				$this->api_model->updateBlog($id, $blogData);

				$response = array(
					'status' => 'success'
				);
           	}

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($response)); 
		}
	}

	public function deleteBlog($id)
	{
		header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$blog = $this->api_model->get_admin_blog($id);

			if($blog->image && file_exists(FCPATH.'media/images/'.$blog->image))
			{
				unlink(FCPATH.'media/images/'.$blog->image);
			}

			$this->api_model->deleteBlog($id);

			$response = array(
				'status' => 'success'
			);

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($response)); 
		}
	}

	
	public function anbiyam()
	{
		
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$anbiyamss = array();
		$anbiyams = $this->api_model->get_anbiyam();

		if(!empty($anbiyams)){
			foreach($anbiyamss as $anbiyam){
				$anbiyams[] = array(
					'id' => $anbiyam->id,
					'name' => $anbiyam->name
				);
			}
		}

		$this->output
			->set_status_header(200)
			->set_content_type('application/json')
			->set_output(json_encode($anbiyams));
	}
	public function relationship()
	{
		
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$relationss = array();
		$relations = $this->api_model->get_relation();

		if(!empty($relations)){
			foreach($relationss as $relation){
				$relations[] = array(
					'id' => $relation->id,
					'name' => $relation->name
				);
			}
		}

		$this->output
			->set_status_header(200)
			->set_content_type('application/json')
			->set_output(json_encode($relations));
	}
	
	public function usertype()
	{
		
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$relationss = array();
		$relations = $this->api_model->get_relation();

		if(!empty($relations)){
			foreach($relationss as $relation){
				$relations[] = array(
					'id' => $relation->id,
					'name' => $relation->name
				);
			}
		}

		$this->output
			->set_status_header(200)
			->set_content_type('application/json')
			->set_output(json_encode($relations));
	}
	public function deleteMember($username)
	{
		header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$blog = $this->api_model->get_admin_blog($username);

			$this->api_model->deleteMember($username);

			$response = array(
				'status' => 'success'
			);

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($response)); 
		}
	}
	public function updateUser() 
	{
		header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		$formdata = json_decode(file_get_contents('php://input'), true);
		
			if($isValidToken) {
				$data=$this->api_model->generateUpdateToken($formdata);
				if($data)
				 {
					echo "success";
				 }else{
					echo "failure";
			   }
			}			 
	}
	
	public function updateMonthTax() 
	{
		header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		$formdata = json_decode(file_get_contents('php://input'), true);
		
			if($isValidToken) {
				$data=$this->api_model->updateTaxStatus($formdata);
				if($data)
				 {
					echo "success";
				 }else{
					echo "failure";
			   }
			}			 
	}
	
	public function getAllAmountMonthlyTax()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$members = $this->api_model->get_all_MonthlyTax();		

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($members)); 
		}
	}
}
