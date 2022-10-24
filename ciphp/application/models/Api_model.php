<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model 
{
	public function get_blogs($featured, $recentpost)
	{
		$this->db->select('blog.*, cat.category_name, u.first_name, u.last_name');
		$this->db->from('blogs blog');
		$this->db->join('users u', 'u.id=blog.user_id');
		$this->db->join('categories cat', 'cat.id=blog.category_id', 'left');
		$this->db->where('blog.is_active', 1);

		if($featured) {
			$this->db->where('blog.is_featured', 1);
		}
		if($recentpost){
			$this->db->order_by('blog.created_at', 'desc');
			$this->db->limit($recentpost);
		}
		$query = $this->db->get();
		return $query->result();
	}

	public function get_blog($id)
	{
		$this->db->select('blog.*, cat.category_name, u.first_name, u.last_name');
		$this->db->from('blogs blog');
		$this->db->join('users u', 'u.id=blog.user_id');
		$this->db->join('categories cat', 'cat.id=blog.category_id', 'left');
		$this->db->where('blog.is_active', 1);
		$this->db->where('blog.id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_categories()
	{
		$query = $this->db->get('categories');
		return $query->result();
	}

	public function get_page($slug)
	{
		$this->db->where('slug', $slug);
		$query = $this->db->get('pages');
		return $query->row();
	}

	public function insert_contact($contactData)
	{
		$this->db->insert('contacts', $contactData);
		return $this->db->insert_id();
	}

	public function login($username, $password) 
	{
		$this->db->where('username', $username);
		$this->db->where('password', md5($password));
		$this->db->where('usertype', 1);
		$query = $this->db->get('users');

		if($query->num_rows() == 1) {
			return $query->row();
		}
	}
	public function username($username) 
	{
		$user = $this->db->where('username', $username);		

		if($user == "" || $user == NULL) {
			return 1;
		} else {			
			return 0;
		}
	}
	public function generateToken($formdata){
        $static_str='AL';
        $currenttimeseconds = date("mdY_His");
		$userId = $formdata['username'];
        $token_id=$static_str.$userId.$currenttimeseconds;

			$data = array(
                'username' => $formdata['username'],
				'first_name' => $formdata['firstname'],
				'last_name' => $formdata['lastname'],
				'password' => md5($formdata['password']), 
				'is_active' => $formdata['is_active'], 
				'token' => md5($token_id),
			);	
		$insertStatus=$this->db->insert('users', $data);
		$member = array(
			//'username' => $formdata['username'],
			'familyHead' => $formdata['familyHead'],
			'parentID' => $formdata['parentID'],
			'first_name' => $formdata['firstname'],
			'last_name' => $formdata['lastname'],
			//'password' => md5($formdata['password']), 
			'is_active' => $formdata['is_active'], 
			//'token' => md5($token_id),
			'relationID' => $formdata['relationshipID'],
			'addressLine1' => $formdata['addressLine1'],
			'addressLine2' => $formdata['addressLine2'],
			'addressLine3' => $formdata['addressLine3'],
			'anbiyamID' => $formdata['anbiyamID'],
			'pincode' => $formdata['pincode'],
			'marriageDate' => $formdata['marriageDate'],
			'birthDate' => $formdata['birthDate'],
			'monthlyTax' => $formdata['monthlyTax'],
			'gender' => $formdata['gender'],
			'MartialStatus' => $formdata['maritalStatus'],
			'phoneNumber' => $formdata['phoneNumber'],
			'email' => $formdata['email'],
			'usertypeID' => $formdata['usertypeID'],
			'created_at' => date('Y-m-d H:i:s')
		);
		$insertStatus=$this->db->insert('members', $member);
		return $member['username'];
     }

	 
	public function generateUpdateToken($formdata){
        $static_str='AL';
        $currenttimeseconds = date("mdY_His");
		$userId = $formdata['username'];
        $token_id=$static_str.$userId.$currenttimeseconds;
		$id = $formdata['id'];

		$data = array(
			'username' => $formdata['username'],
			'first_name' => $formdata['firstname'],
			'last_name' => $formdata['lastname'],
			'password' => $formdata['password'], 
			'is_active' => $formdata['is_active'], 
			'token' => md5($token_id),
		);	
		$this->db->where('id', $id)->update('users', $data);
		// $this->db->where('id', $id);
		// $this->db->update('users', $data);
		$member = array(
			'username' => $formdata['username'],
			'first_name' => $formdata['firstname'],
			'last_name' => $formdata['lastname'],
			'password' => $formdata['password'], 
			'is_active' => $formdata['is_active'], 
			'token' => md5($token_id),
			'relationID' => $formdata['relationshipID'],
			'addressLine1' => $formdata['addressLine1'],
			'addressLine2' => $formdata['addressLine2'],
			'addressLine3' => $formdata['addressLine3'],
			'anbiyamID' => $formdata['anbiyamID'],
			'pincode' => $formdata['pincode'],
			'marriageDate' => $formdata['marriageDate'],
			'birthDate' => $formdata['birthDate'],
			'monthlyTax' => $formdata['monthlyTax'],
			'gender' => $formdata['gender'],
			'phoneNumber' => $formdata['phoneNumber'],
			'email' => $formdata['email'],
			'MartialStatus' => $formdata['maritalStatus'],
			'usertypeID' => $formdata['usertypeID']
		);
		$this->db->where('id', $id)->update('members', $member);
		// $this->db->where('id', $id);
		// $this->db->update('members', $member);
		return $member['username'];
     }

	 
	public function updateTaxStatus($formdata){
        $static_str='AL';
        $currenttimeseconds = date("mdY_His");
		$userId = $formdata['username'];
        $token_id=$static_str.$userId.$currenttimeseconds;
		$id = $formdata['id'];

		$data = array(
			'status' => $formdata['status']
		);	
		$this->db->where('id', $id)->update('accountsmgmt', $data);
     }
	public function generateSubMemberToken($formdata){
			$data = array(
				'fullname' => $formdata['fullname'],
				'birthdate' => $formdata['birthDate'],
				'phonenumber' => $formdata['phoneNumber'], 
				'parentID' => $formdata['parentID'], 
				'relationID' => $formdata['relationshipID'], 
				'usertypeID' => $formdata['is_active'],
				'gender' => $formdata['gender'],
				'is_active' => $formdata['is_active']
			);	
		$insertStatus=$this->db->insert('familymembers', $data);
		return $member['username'];
     }
	 
	public function generateIncomeExpenToken($formdata){
		$insertStatus=$this->db->insert('accountsmgmt', $formdata);
		return $member['username'];
     }
	/*public function generateToken($formdata)
	{
	   $insertStatus=$this->db->insert('users',array('username'=>$formdata['username'],'first_name'=>$formdata['firstname'],
	   'last_name'=>$formdata['lastname'],'password'=>md5($formdata['password'])));
	   return $insertStatus;
	}*/
	public function get_admin_blogs()
	{
		$this->db->select('blog.*, u.first_name, u.last_name');
		$this->db->from('blogs blog');
		$this->db->join('users u', 'u.id=blog.user_id');
		$this->db->order_by('blog.created_at', 'desc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_admin_blog($id)
	{
		$this->db->select('blog.*, u.first_name, u.last_name');
		$this->db->from('blogs blog');
		$this->db->join('users u', 'u.id=blog.user_id');
		$this->db->where('blog.id', $id);
		$query = $this->db->get();
		return $query->row();
	}
	public function get_my_family($id)
	{
		$this->db->select('*')->from('familymembers')->where('parentID', $id);
		$query = $this->db->get();
		return $query->result();
		// $query = $this->db->get('members');
		// return $query->result();
	}
	
	public function get_indi_monthlyTax($id)
	{
		$this->db->select('*')->from('accountsmgmt')->where('userId', $id)->order_by("month", "asc");
		$query = $this->db->get();
		return $query->result();
		// $query = $this->db->get('members');
		// return $query->result();
	}
	public function checkToken($token)
	{
		$this->db->where('token', $token);
		$query = $this->db->get('users');

		if($query->num_rows() == 1) {
			return true;
		}
		return false;
	}

	public function insertBlog($blogData)
	{
		$this->db->insert('blogs', $blogData);
		return $this->db->insert_id();
	}

	public function updateBlog($id, $blogData)
	{
		$this->db->where('id', $id);
		$this->db->update('blogs', $blogData);
	}

	public function deleteBlog($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('blogs');
	}
	
	public function get_anbiyam()
	{
		$query = $this->db->get('anbiyam');
		return $query->result();
	}
	public function get_relation()
	{
		$query = $this->db->get('relation');
		return $query->result();
	}
	public function get_users()
	{
		$this->db->select('*')->from('members')->where('is_active', 1);
		$query = $this->db->get();
		return $query->result();
		// $query = $this->db->get('members');
		// return $query->result();
	}
	
	public function get_in()
	{
		$this->db->select('*')->from('accountsmgmt')->where('amountType', 2)->where('incomeExpense', 1);
		$query = $this->db->get();
		return $query->result();
	}
	public function get_inMonth($id)
	{
		$year = date("Y");
		$this->db->select('*')->from('accountsmgmt')->where('amountType', 2)->where('incomeExpense', 1)->where('month', $id)->where('year', $year);
		$query = $this->db->get();
		return $query->result();
	}
	public function get_inYear($year)
	{
		$this->db->select('*')->from('accountsmgmt')->where('amountType', 2)->where('incomeExpense', 1)->where('year', $year);
		$query = $this->db->get();
		return $query->result();
	}
	public function get_ex()
	{
		$this->db->select('*')->from('accountsmgmt')->where('amountType', 2)->where('incomeExpense', 2);
		$query = $this->db->get();
		return $query->result();
	}
	public function get_exMonth($id)
	{
		$year = date("Y");
		$this->db->select('*')->from('accountsmgmt')->where('amountType', 2)->where('incomeExpense', 2)->where('month', $id)->where('year', $year);
		$query = $this->db->get();
		return $query->result();
	}
	public function get_exYear($year)
	{
		$this->db->select('*')->from('accountsmgmt')->where('amountType', 2)->where('incomeExpense', 2)->where('year', $year);
		$query = $this->db->get();
		return $query->result();
	}
	public function get_All_users_Anbiyam($id)
	{
		$this->db->select('*')->from('members')->where('anbiyamID', $id)->where('is_active', 1);
		$query = $this->db->get();
		return $query->result();
	}
	public function deleteMember($username)
	{
		$data = array(
			'is_active' => 0
		 );
		$this->db->where('username', $username);
		$this->db->update('members', $data);
		$this->db->where('username', $username);
		$this->db->update('users', $data);
	}
	public function get_all_MonthlyTax()
	{
		$this->db->select('*')->from('members mem')->join('accountsmgmt am', 'am.userId=mem.ID')->where('amountType', 1)->where('incomeExpense', 1)->where('is_active', 1);
		$query = $this->db->get();
		return $query->result();
	}
}
