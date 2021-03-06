<?php 
/**
* 
*/
class Home extends CI_Controller
{
	
	function __construct() 
    {
		parent::__construct();		
        //ロード　ヘルパー 
		$this->load->helper("url");
        $this->load->helper(array('form', 'url'));		
        //　ロード　ライブラリ
		$this->load->library(array("input","form_validation","session","my_auth","email"));         
        //check Authentication
        //　認証　を　チェックする
		if (!$this->my_auth->is_Admin()) {			
            redirect(base_url()."index.php/admin/verify/login");
            exit();
        }
        //load model
        //　ロード　モデル
        $this->load->database();
        $this->load->model("User_model");
	}
	function login() 
    {
		$this->load->view("admin/login_admin_view");
	}
    // list User
    // リスト　ユーザ
    public function index() 
    {             
        $this->User_model->getAllUser();
        $max = $this->User_model->num_rows();
        $min = 10;
        $data['num_rows'] = $max;
        //--- Paging
        //　ページング        
        if ($max != 0) {
            //load library
            //　ロード　ライブラリ    
            $this->load->library('pagination');                    
            $config['base_url'] = base_url()."index.php/admin/home/index";
            $config['total_rows'] = $max;
            $config['per_page'] = $min;
            $config['num_link'] = 3; 
            $config['uri_segment'] = 4;
            $this->pagination->initialize($config);                                     
            $data['links'] = $this->pagination->create_links();
            //get data from DB
            //データベース　から　データ　を　得る
            $data['users'] = $this->User_model->getAllUser($min,$this->uri->segment($config['uri_segment']));
            $this->load->view("admin/homepageadmin_view",$data);
        } else {
            $data['users'] = null;
            $this->load->view("admin/homepageadmin_view",$data);
        }
    }
    // list　Admin
    //　リスト　Admin
	public function listAdmin() 
    {
        if ($this->my_auth->is_SuperAdmin()) {                                            
		    $user = $this->User_model->getListAdmin();
		    $data = "";
    		if (!is_null($user)) {
    			foreach ($user as $key => $value) {
    				$data = array('users' => $user
                                 );
    			}
    		} else {
    			$user = null;
                $data = array('users' => $user);					
    		}
			$this->load->view('admin/listadmin_view', $data);
        } else {
            redirect(base_url()."index.php/admin/verify/login");
        }
	}
	public function redirectAddNewAdmin() 
    {
		$this->load->view('admin/addNewAdmin_view');
	}
    // add　New　Admin
    //　新しい　Admin　を　加える
	public function addNewAdmin() 
    {		        
        $this->form_validation->set_rules("username","Username","required|trim|min_length[6]|alpha_numeric|max_length[32]|callback_checkUser");
        $this->form_validation->set_rules("password","Password","required|trim|min_length[6]|max_length[32]");
        $this->form_validation->set_rules("email","Email","required|valid_email|max_length[100]|callback_checkEmail"); 
        $data['error'] = "";
        if ($this->form_validation->run() == FALSE) {            
            $this->load->view("admin/addNewAdmin_view",array("error"=>""));
        } else {                
            $user = array("u_username"  => $this->input->post("username"),                        
                          "u_password"  => md5($this->input->post("password")),
                          "u_email"     => $this->input->post("email"),                                                
                          "u_role"     => $_POST['role'],                        
                          "u_registerdate"  => date("Y-m-d H:i:s"),
                          "u_status"    => 1, // da kich hoat
                         );                             
			$this->User_model->addNewAdmin($user);
			redirect(base_url()."index.php/admin/home/listAdmin");                                                
        } 
    }
    //--- Edit Admin
    //---　Admin　を　修正する
    function editAdmin()
    {
    	$u_id = $this->uri->segment(4);
        $data['info'] = $this->User_model->getInfo($u_id);
        $data['error'] = "";
        if (is_numeric($u_id) && $data['info'] != NULL) {            
            if (isset($_POST['ok'])) {     
                // form validation  
                // 検証　フォーム                   
                $this->form_validation->set_rules("username","Username","required");                
                $this->form_validation->set_rules("password","Password","required");  
                if ($this->input->post("newpassword") != "") {
                    $this->form_validation->set_rules("newpassword","NewPassword","trim|min_length[6]|max_length[32]|matches[renewpassword]"); 
                    $this->form_validation->set_rules("renewpassword","Re-NewPassword","trim");    
                } else {
                    $this->form_validation->set_rules("newpassword","NewPassword","trim|min_length[6]|max_length[32]"); 
                    $this->form_validation->set_rules("renewpassword","Re-NewPassword","trim|matches[newpassword]");
                }
                
                $this->form_validation->set_rules("fullname", "Fullname", 'trim|max_length[100]');                              
                $this->form_validation->set_rules("email","Email","required|valid_email||max_length[100]|callback_checkEmail");                
                if ($this->form_validation->run() == FALSE) {   
                    $this->load->view("admin/editAdmin_view",$data);                
                } else {
                    if ($this->input->post("newpassword") != "") {
                        $update = array("u_username"  => $this->input->post("username"),
                                        "u_password"  => md5($this->input->post("newpassword")),
                                        "u_fullname"  => $this->input->post("fullname"),
                                        "u_email"     => $this->input->post("email")
                                       );                                    
                                    
                    } else {
                        $update = array("u_username"  => $this->input->post("username"),
                                        "u_password"  => $this->input->post("password"),
                                        "u_fullname"  => $this->input->post("fullname"),
                                        "u_email"     => $this->input->post("email")
                                       );                                                 
                    }
                      
                    $this->User_model->updateUser($update,$u_id);
                    redirect(base_url()."index.php/admin/home"); 
                }
            } else {
                $this->load->view("admin/editAdmin_view",$data);   
            }    
        } else {            
            return false;
        }
    }
    //--- Delete User
    //---　ユーザ　を　消す
    function deleteUser() 
    {
        $u_id = $this->uri->segment(4);        
        if (is_numeric($u_id)) {            
            $this->User_model->deleteUser($u_id);
            redirect(base_url()."index.php/admin/home");        
        } else {
            return false;     
        }
    }
    //--- check valid　user?
    //---　 妥当なユーザ　を　チェックする？
    function checkUser($u_username) 
    {        
        $u_id = "";
        if ($this->User_model->getUser($u_username,$u_id) == TRUE) {
            return TRUE;
        } else {
            $this->form_validation->set_message("checkUser","Your username has been register, please try again");
            return FALSE;
       }
    }
    //---- Check Email exist in Database
    //----　データベースに　Eメールの存在　を　チェックする
    function checkEmail($u_email) 
    {
        $u_id = $this->uri->segment(4);
        if ($this->User_model->checkEmail($u_email,$u_id) == TRUE) {
            return TRUE;
        } else {
            $this->form_validation->set_message("checkEmail","Email has been exit, please try again");
            return FALSE;
        }
    }
    // Search user
    // ユーザ　を　探す
    function getUserByUsername() 
    {
        $txtUsername = "";
        $txtEmail = "";
        if (isset($_GET['txtUsername']) || isset($_GET['txtEmail'])) {
            $txtUsername = $_GET['txtUsername'];
            $txtEmail = $_GET['txtEmail'];
        }
        $txtUsername = ($txtUsername===null) ? "" : $txtUsername;
        $txtEmail = ($txtEmail===null) ? "" : $txtEmail;
        if ($txtUsername === "" && $txtEmail === "") {
            redirect(base_url()."index.php/admin/home");
            exit();
        }   
        $user = $this->User_model->getUserByUsername(trim($txtUsername),trim($txtEmail));
        $data = "";
        if (!is_null($user)) {
            foreach($user as $key => $value){
                $data = array('users'       => $user,
                              'txtUsername' => $txtUsername,
                              'txtEmail'    => $txtEmail
                             );
            }
        } else {
            $user = null;                   
            $data = array('users'       => $user,
                          'txtUsername' => $txtUsername,
                          'txtEmail'    => $txtEmail
                         );
        }
        $this->load->view('admin/listSearchUser_view', $data);
    }
    //　Ban/Unban User
    //　ユーザをブロックする
    function banUser() 
    {
        $u_id = $this->uri->segment(4);
        if (is_numeric($u_id)) {
            $update = array("u_status" => 0
                           );
            $this->User_model->updateUser($update,$u_id);
            redirect(base_url()."index.php/admin/home");        
        } else {
            return false;     
        }
    }
    //　Ban/Unban User
    //　ユーザをブロックする
    function unbanUser() 
    {
        $u_id = $this->uri->segment(4);
        if (is_numeric($u_id)) {
            $update = array("u_status" => 1);
            $this->User_model->updateUser($update,$u_id);
            redirect(base_url()."index.php/admin/home");        
        } else {
            return false;     
        }
    }
}
?>