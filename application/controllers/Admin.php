<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	 public function __construct()
	 { 
            parent::__construct();
            $this->load->helper('url'); 
			$this->load->helper('form');
			$this->load->library('recaptcha');
			$this->load->library('form_validation');
			$this->load->helper('security');
  			$this->load->library('session');
			$this->load->model('Mod_admin');
     } 

     public function index()
	{
		$this->load->view('index');
	}

	public function admin_login_process()
	{
		
	 $this->form_validation->set_rules('email', 'Email Address', 'trim|required|xss_clean');
	 $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

	 	if ($this->form_validation->run() == FALSE) 
			{
				//$this->session->set_flashdata('error_msg',validation_errors());

			if(isset($this->session->userdata['logged_in']))
			{
				unset($this->session->userdata['logged_in']);
			}
				$this->load->view('index');
			}
		else{
			$captcha_answer = $this->input->post('g-recaptcha-response');
			$response = $this->recaptcha->verifyResponse($captcha_answer);

			if ($response['success']) {
			$login = array(
            'email'=> $this->input->post('email'),
            'password' => md5($this->input->post('password'))            
             ); 
			$result = $this->Mod_admin->admin_login($login);	
			if ($result == TRUE) 
				{
					$email = $this->input->post('email'); 
					$this->data['feeder'] = $this->Mod_admin->read_user_information($email);

				if ($this->data['feeder'] != false) 
				{
			    	$session_data = array(
						'user_type' => $this->data['feeder'][0]->user_type_cd,
						'user_id'=>$this->data['feeder'][0]->user_id,
						'user_name'=>$this->data['feeder'][0]->user_name,
							);
					$this->session->set_userdata('logged_in',$session_data);
					$this->load->view('admin/header');
					$this->load->view('admin/nav');
					$this->load->view('admin/home');
					$this->load->view('admin/footer');		
			    }
		    }
			else
			{
				/*$data = array(
					'error_message' => 'Invalid Email address or Password');
				if(isset($this->session->userdata['logged_in']))
					{
						unset($this->session->userdata['logged_in']);
					}
				$this->load->view('index',$data);*/
				echo "Invalid Email OR Password! Please check it carefully";
			} 
		}
		else {
			echo "Invalid Captcha! Please check captcha carefully";
			}
		}	
	}	

	public function logout()
		{
			unset($this->session->userdata['logged_in']);
			$this->session->sess_destroy();			
			redirect('Admin');

		}

	public function admin_home()
		{
			$this->load->view('admin/header');	
			$this->load->view('admin/nav');
			$this->load->view('admin/home');		
			$this->load->view('admin/footer');		

		}			

	public function user_creation_institute()
		{
			$this->load->view('admin/header');	
			$this->load->view('admin/nav');
			$data['get_state']=$this->Mod_admin->get_state();
			$data['get_institute']=$this->Mod_admin->get_institute();
			$this->load->view('admin/user_creation_institute',$data);		
			$this->load->view('admin/footer');	

		}

	public function user_creation_admin()
		{
			$this->load->view('admin/header');	
			$this->load->view('admin/nav');
			$data['get_state']=$this->Mod_admin->get_state();
			$data['get_user']=$this->Mod_admin->get_user();
			$this->load->view('admin/user_creation_admin',$data);		
			$this->load->view('admin/footer');	

		}	

	public function getDistrict()
		{
            $state = $this->input->post('state');
			$data=$this->Mod_admin->getDistrict($state);
			echo json_encode($data);
		}	
	public function getSubdivision()
		{
            $district = $this->input->post('district');
			$data=$this->Mod_admin->getSubdivision($district);
			echo json_encode($data);
		}
		
	public function getBlockMuni()
		{
            $subdivision = $this->input->post('subdivision');
			$data=$this->Mod_admin->getBlockMuni($subdivision);
			echo json_encode($data);
		}			


	public function inst_user_insert()
		{
			$this->form_validation->set_rules('inst_state','State','trim|required|xss_clean');
			$this->form_validation->set_rules('inst_district','District','trim|required|xss_clean');
			$this->form_validation->set_rules('inst_subdivision','Subdivision','trim|required|xss_clean');
			$this->form_validation->set_rules('inst_block','Block / Municipality','trim|required|xss_clean');
			$this->form_validation->set_rules('inst_type','Institution Type','trim|required|xss_clean');
			$this->form_validation->set_rules('inst_name','Institution Name','trim|required|xss_clean');
			$this->form_validation->set_rules('inst_license_no','Institution License Number','trim|required|xss_clean');
			$this->form_validation->set_rules('inst_addr','Institution Address','trim|required|xss_clean');
			$this->form_validation->set_rules('inst_email','Institution Email Id','trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('inst_mobile','Institution Mobile Number','trim|required|xss_clean|integer');
			$this->form_validation->set_rules('inst_phone','Institution Phone Number','trim|xss_clean|integer');
			$this->form_validation->set_rules('inst_owner_name','Institution Owner Name','trim|required|xss_clean');
			$this->form_validation->set_rules('inst_owner_mobile','Institution Owner Mobile','trim|integer|xss_clean');
			$this->form_validation->set_rules('inst_owner_email','Institution Owner Email Id','trim|valid_email|xss_clean');
			$this->form_validation->set_rules('inst_password','Password','trim|required|xss_clean');
			$this->form_validation->set_rules('inst_confirm_password','Confirm Password','trim|required|xss_clean|matches[inst_password]');

			if ($this->form_validation->run() == TRUE) 
				{
					$this->data['feeder'] = $this->Mod_admin->get_max_rs();
					$max_rs = $this->data['feeder'][0]->max_rs;
					$usercd=$this->data['feeder'][0]->max_rs+1;

					$state = $this->input->post('inst_state');
					$district = $this->input->post('inst_district');
					$subdivision = $this->input->post('inst_subdivision');
					$block = $this->input->post('inst_block');
					$institution_type = $this->input->post('inst_type');
					$institution_name = $this->input->post('inst_name');
					$institution_license = $this->input->post('inst_license_no');
					$institution_address = $this->input->post('inst_addr');
					$institution_email = $this->input->post('inst_email'); 
					$institution_mobile = $this->input->post('inst_mobile');
					$institution_phone = $this->input->post('inst_phone');					
					$institution_owner = $this->input->post('inst_owner_name');
					$inst_owner_mobile = $this->input->post('inst_owner_mobile');
					$inst_owner_email = $this->input->post('inst_owner_email');
					$password = md5($this->input->post('inst_password'));
					
					$user_id = $state.$block.$usercd; 

					$result=$this->Mod_admin->get_user_insert($user_id,$state,$district,$subdivision,$block,$institution_type,$institution_name,$institution_license,$institution_address,$institution_email,$institution_mobile,$institution_phone,$institution_owner,$inst_owner_mobile,$inst_owner_email,$password);

						if ($result == TRUE)
			 				{	
								//$data = array(
										//'error_message' => 'Registration successfullly! Please note User Id : '.$user_id,					
										//);	
							$this->session->set_flashdata('msg',"User Registration Successfully ! Remember User ID :".$user_id);				
							} 
						else {
								$this->session->set_flashdata('msg',"User Registration not Successfully !");				
							}

				$this->load->view('admin/header');	
				$this->load->view('admin/nav');
				$data['get_state']=$this->Mod_admin->get_state();
				$data['get_institute']=$this->Mod_admin->get_institute();
				$this->load->view('admin/user_creation_institute',$data);		
				$this->load->view('admin/footer');				
     		 }
			else
				{
					$this->load->view('admin/header');	
					$this->load->view('admin/nav');					
					$data['get_state']=$this->Mod_admin->get_state();
					$data['get_institute']=$this->Mod_admin->get_institute();
					$this->load->view('admin/user_creation_institute',$data);		
					$this->load->view('admin/footer');
				}				 

			}

	public function admin_user_insert()
		{
			$this->form_validation->set_rules('user_state','State','trim|required|xss_clean');
			$this->form_validation->set_rules('user_district','District','trim|required|xss_clean');
			$this->form_validation->set_rules('user_type','User Type','trim|required|xss_clean');
			$this->form_validation->set_rules('user_name','User Name','trim|required|xss_clean');
			$this->form_validation->set_rules('user_desg','User Designation','trim|required|xss_clean');
			$this->form_validation->set_rules('user_email','User Email Id','trim|required|valid_email|xss_clean');
			$this->form_validation->set_rules('user_mobile','User Mobile','trim|required|xss_clean|integer');
			$this->form_validation->set_rules('user_password','Password','trim|required|xss_clean');
			$this->form_validation->set_rules('user_confirm_password','Confirm Password','trim|required|xss_clean|matches[user_password]');

			if ($this->form_validation->run() == TRUE) 
				{
					$this->data['feeder'] = $this->Mod_admin->get_admin_max_rs();
					$max_rs = $this->data['feeder'][0]->max_rs;
					$usercd=$this->data['feeder'][0]->max_rs+1;

					$state = $this->input->post('user_state');
					$district = $this->input->post('user_district');
					$user_type = $this->input->post('user_type');
					$user_name = $this->input->post('user_name');
					$user_desg = $this->input->post('user_desg');
					$user_email = $this->input->post('user_email');
					$user_mobile = $this->input->post('user_mobile');
					$user_password = md5($this->input->post('user_password'));
					
					$user_id = $state.$district.$usercd; 

					$result=$this->Mod_admin->get_admin_insert($user_id,$state,$district,$user_type,$user_name,$user_desg,$user_email,$user_mobile,$user_password);

						if ($result == TRUE)
			 				{	
								//$data = array(
										//'error_message' => 'Registration successfullly! Please note User Id : '.$user_id,					
										//);	
							$this->session->set_flashdata('response',"User Registration Successfully ! Remember User ID :".$user_id);				
							} 
						else {
								$this->session->set_flashdata('response',"User Registration not Successfully !");				
							}

				$this->load->view('admin/header');	
				$this->load->view('admin/nav');
				$data['get_state']=$this->Mod_admin->get_state();
				$data['get_user']=$this->Mod_admin->get_user();
				$this->load->view('admin/user_creation_admin',$data);		
				$this->load->view('admin/footer');					
     		 }
			else
				{
					$this->load->view('admin/header');	
					$this->load->view('admin/nav');
					$data['get_state']=$this->Mod_admin->get_state();
					$data['get_user']=$this->Mod_admin->get_user();
					$this->load->view('admin/user_creation_admin',$data);		
					$this->load->view('admin/footer');	
				}				 

			}				

	public function subcategory_test()
		{
            		$this->load->view('admin/header');	
					$this->load->view('admin/nav');
					$data['get_disease']=$this->Mod_admin->get_disease();
					$this->load->view('admin/addition_subcat_test',$data);		
					$this->load->view('admin/footer');
		}

	public function getsubdisease()
		{
            $disease_category = $this->input->post('disease_category');
			$data=$this->Mod_admin->getsubdisease($disease_category);
			echo json_encode($data);
		}	


	public function add_subcategory()
		{
			$this->form_validation->set_rules('disease_code','Disease Category','trim|required|xss_clean');
			$this->form_validation->set_rules('disease_subcase_code','Disease Sub-category','trim|required|xss_clean');
			

			if ($this->form_validation->run() == TRUE) 
				{
					
					$disease_code = $this->input->post('disease_code');
					$disease_subcase_code = $this->input->post('disease_subcase_code');

					$result=$this->Mod_admin->insert_subcategory($disease_code,$disease_subcase_code);

						if ($result == TRUE)
			 				{	
									
							$this->session->set_flashdata('response',"Data inserted Successfully ! ");				
							} 
						else {
								$this->session->set_flashdata('response',"Data not inserted Successfully !");				
							}

					$this->load->view('admin/header');	
					$this->load->view('admin/nav');
					$data['get_disease']=$this->Mod_admin->get_disease();
					$this->load->view('admin/addition_subcat_test',$data);		
					$this->load->view('admin/footer');					
     		 }
			else
				{
					$this->load->view('admin/header');	
					$this->load->view('admin/nav');
					$data['get_disease']=$this->Mod_admin->get_disease();
					$this->load->view('admin/addition_subcat_test',$data);		
					$this->load->view('admin/footer');	
				}				 

			}


	public function add_test_name()
		{
			$this->form_validation->set_rules('disease_id','Disease Category','trim|required|xss_clean');
			$this->form_validation->set_rules('disease_subcat_id','Disease Sub-category','trim|required|xss_clean');
			$this->form_validation->set_rules('test_name','Test Name','trim|required|xss_clean');
			

			if ($this->form_validation->run() == TRUE) 
				{
					
					$disease_id = $this->input->post('disease_id');
					$disease_subcat_id = $this->input->post('disease_subcat_id');
					$test_name = $this->input->post('test_name');

					$result=$this->Mod_admin->insert_test_name($disease_id,$disease_subcat_id,$test_name);

						if ($result == TRUE)
			 				{	
									
							$this->session->set_flashdata('message',"Data inserted Successfully ! ");				
							} 
						else {
								$this->session->set_flashdata('message',"Data not inserted Successfully !");				
							}

					$this->load->view('admin/header');	
					$this->load->view('admin/nav');
					$data['get_disease']=$this->Mod_admin->get_disease();
					$this->load->view('admin/addition_subcat_test',$data);		
					$this->load->view('admin/footer');					
     		 }
			else
				{
					$this->load->view('admin/header');	
					$this->load->view('admin/nav');
					$data['get_disease']=$this->Mod_admin->get_disease();
					$this->load->view('admin/addition_subcat_test',$data);		
					$this->load->view('admin/footer');	
				}				 

			}

//............from satantan da 23.05.2018 start..............//

	////////////Edit Admin user view/////////////////////
		
		
	public function user_edit_admin()
		{
			$this->load->view('admin/header');	
			$this->load->view('admin/nav');
			$data['get_state']=$this->Mod_admin->get_state();
			$data['get_user']=$this->Mod_admin->get_user();
			$this->load->view('admin/user_edit_admin',$data);		
			$this->load->view('admin/footer');	

		}	
		
/////////////////////Edit Admin user view////////////////////////	


/////////////////////Edit Admit User/////////////////////////
	public function admin_user_edit()
		{
			$user_state=$this->input->post('user_state');
			$user_district=$this->input->post('user_district');
			$user_type= $this->input->post('user_type');
			$data['edit_admin_user']=$this->Mod_admin->edit_admin_user($user_state,$user_district,$user_type);
			$this->load->view('admin/edit_update_form',$data);
		}

////////////////////Edit Admit User///////////////////

//////////////////Update Admin User///////////////////
	public function admin_user_update()
		{
		    $this->form_validation->set_rules('user_name', 'user_name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('user_desg', 'user_desg', 'trim|required|xss_clean');
			$this->form_validation->set_rules('user_email', 'user_email', 'trim|required|xss_clean');
			$this->form_validation->set_rules('user_mobile', 'user_mobile',  'trim|required|xss_clean');

			if ($this->form_validation->run() == TRUE) 
				{
		
					   $user_name = $this->input->post('user_name');
				       $user_desg = $this->input->post('user_desg');
				       $user_email=$this->input->post('user_email');
				       $user_mobile=$this->input->post('user_mobile');
					   $user_id=$this->input->post('user_id');
					   $return=$this->Mod_admin->admin_user_update($user_name,$user_desg,$user_email,$user_mobile,$user_id);
					if($return=="1")
					 {
						$this->session->set_flashdata('message',"Data Updated Successfully ! ");	
						
					    $this->load->view('admin/header');	
						$this->load->view('admin/nav');
						$data['get_state']=$this->Mod_admin->get_state();
						$data['get_user']=$this->Mod_admin->get_user();
						$this->load->view('admin/user_edit_admin',$data);		
						$this->load->view('admin/footer');	
		
					 }
			
					else
					 {
						$this->session->set_flashdata('message',"Data can not update Successfully ! ");	
		
					    $this->load->view('admin/header');	
						$this->load->view('admin/nav');
						$data['get_state']=$this->Mod_admin->get_state();
						$data['get_user']=$this->Mod_admin->get_user();						
						$this->load->view('admin/user_edit_admin',$data);		
						$this->load->view('admin/footer');
					 }
				}

			else
			 {
				    $this->load->view('admin/header');	
					$this->load->view('admin/nav');
					$data['get_state']=$this->Mod_admin->get_state();
					$data['get_user']=$this->Mod_admin->get_user();
					$this->load->view('admin/user_edit_admin',$data);		
					$this->load->view('admin/footer');	
		
			 }
		
		}

////////////////////Update Admin User/////////////////	
						
//////////////////Edit user Institute/////////////////
	public function user_edit_institute()
		{
			$this->load->view('admin/header');	
			$this->load->view('admin/nav');
			$data['get_state']=$this->Mod_admin->get_state();
			$data['get_institute']=$this->Mod_admin->get_institute();
			$this->load->view('admin/user_edit_institute',$data);		
			$this->load->view('admin/footer');	

		}
////////////////Edit user Institute//////////////////
//////////////Fetch Institution Name/////////////////
	public function get_institution_name()
		{
		     $inst_district = $this->input->post('inst_district');
			 $inst_subdivision = $this->input->post('inst_subdivision');
			 $inst_block = $this->input->post('inst_block');
			 $inst_type = $this->input->post('inst_type');
		     $data=$this->Mod_admin->get_institution_name($inst_district,$inst_subdivision,$inst_block,$inst_type);
		     echo json_encode($data);

		}
/////////////Fetch Institution Name////////////

////////////Institution details edit///////////
 
	public function institution_details_edit()
 		{

			 $user_id=$this->input->post('inst_name');
			 $data['edit_institute_details']=$this->Mod_admin->edit_institute_details($user_id);
			$this->load->view('admin/edit_institute_form',$data);
 
 		}
 //////////Institution  user update//////////////

 	public function institution_user_update()
 		{
		 	    $this->form_validation->set_rules('inst_license_no', 'Institute Licecce no', 'trim|required|xss_clean');
				$this->form_validation->set_rules('inst_addr', 'Institute Address', 'trim|required|xss_clean');
				$this->form_validation->set_rules('inst_email', 'Institution Email Id', 'trim|required|xss_clean');
				$this->form_validation->set_rules('inst_mobile', 'Institute Mobile',  'trim|required|xss_clean');
				$this->form_validation->set_rules('inst_owner_name', 'Institute Owner Name',  'trim|required|xss_clean');
				$this->form_validation->set_rules('inst_owner_mobile', 'Institute Owner Mobile',  'trim|required|xss_clean');
				if ($this->form_validation->run() == TRUE) 
					{
						$inst_license_no=$this->input->post('inst_license_no');
						$inst_addr=$this->input->post('inst_addr');
						$inst_email=$this->input->post('inst_email');
						$inst_mobile=$this->input->post('inst_mobile');
						$inst_phone=$this->input->post('inst_phone');
						$inst_owner_name=$this->input->post('inst_owner_name');
						$inst_owner_mobile=$this->input->post('inst_owner_mobile');
						$inst_owner_email=$this->input->post('inst_owner_email');
						$user_id=$this->input->post('user_id');
					   $return=$this->Mod_admin->institution_user_update($inst_license_no,$inst_addr,$inst_email,$inst_mobile,$inst_phone,$inst_owner_name,$inst_owner_mobile,$inst_owner_email,$user_id);
	   				 if($return=="1")
	   					{
						     $this->session->set_flashdata('response',"Data update Successfully ! ");	
							 $this->load->view('admin/header');	
							 $this->load->view('admin/nav');
							 $data['get_state']=$this->Mod_admin->get_state();
							 $data['get_institute']=$this->Mod_admin->get_institute();
							 $this->load->view('admin/user_edit_institute',$data);		
							 $this->load->view('admin/footer');	
	   					}	
					 else
					    {
							 $this->session->set_flashdata('response',"Data can not update Successfully ! ");	
							 $this->load->view('admin/header');	
							 $this->load->view('admin/nav');
							 $data['get_state']=$this->Mod_admin->get_state();
							 $data['get_institute']=$this->Mod_admin->get_institute();
							 $this->load->view('admin/user_edit_institute',$data);		
							 $this->load->view('admin/footer');
						}
					}
				else
					{
						   	$this->load->view('admin/header');	
							$this->load->view('admin/nav');
							$data['get_state']=$this->Mod_admin->get_state();
							$data['get_institute']=$this->Mod_admin->get_institute();
							$this->load->view('admin/user_edit_institute',$data);		
							$this->load->view('admin/footer');	
					} 
 			}
  //////////////Institution  user update/////////////


}
