<?php 
/**
* 
*/
class Contact extends CI_Controller
{
	
	function __construct()
    {
		parent::__construct();
		// load helper 
        //ロード　ヘルパー           
		$this->load->helper("url");
        $this->load->helper(array('form', 'url'));
        // load library
        //　ロード　ライブラリ
        $this->load->library(array("input","form_validation","session","my_auth","email"));
        //connect DB
        //　データベース　に　接続する
		$this->load->database();
        //load model
        //　ロード　モデル
        $this->load->model("Contact_model");
        //check Authentication
        //　認証　を　チェックする
        if (!$this->my_auth->is_Admin()) {            
            redirect(base_url()."index.php/admin/verify/login");
            exit();
        }
    }
	//list contact
    //　リスト　コンタクト
	function index() 
    {
		// count record
        // レコード　を　カウントする
        $max = $this->Contact_model->num_rows();
        // number of records on one page
        // ページ　に　レコード　の　番号
        $min = 10;
        $data['num_rows'] = $max;
        //--- Paging
        //　ページング
        if ($max != 0) {    
        	//load library
            //　ロード　ライブラリ
            $this->load->library('pagination');                    
            $config['base_url'] = base_url()."index.php/admin/contact/index";
            $config['total_rows'] = $max;
            $config['per_page'] = $min;
            $config['num_link'] = 3; 
            $config['uri_segment'] = 4;
            $this->pagination->initialize($config);                        
            $data['links'] = $this->pagination->create_links();
            //get data from DB
            //データベース　から　データ　を　得る
            $data['contact'] = $this->Contact_model->getAllContact($min,$this->uri->segment($config['uri_segment']));
            // load view
            // ロード　ビュー
            $this->load->view("admin/contact/listContact_view",$data);
        } else {
            $data['contact']=null;
            $this->load->view("admin/contact/listContact_view",$data);
        }
	}
    // List reply contact
    //　リスト　返事　の　コンタクト
    function listReplyContact() 
    {
        // count records
        // レコード　を　カウントする
        $max = $this->Contact_model->num_rows_reply();
        // number of records on one page
        // ページ　に　レコード　の　番号
        $min = 10;
        $data['num_rows_reply'] = $max;
        //--- Paging
        //　ページング
        if ($max!=0) {    
            //load library
            //　ロード　ライブラリ
            $this->load->library('pagination');                    
            $config['base_url'] = base_url()."index.php/admin/contact/listReplyContact";
            $config['total_rows'] = $max;
            $config['per_page'] = $min;
            $config['num_link'] = 3; 
            $config['uri_segment'] = 4;
            $this->pagination->initialize($config);                        
            $data['links'] = $this->pagination->create_links();
            //get data from DB
            //データベース　から　データ　を　得る
            $data['contact'] = $this->Contact_model->getReplyContact($min,$this->uri->segment($config['uri_segment']));
            // load view
            // ロード　ビュー
            $this->load->view("admin/contact/listReplyContact_view",$data);
        } else {
            $data['contact']=null;
            $this->load->view("admin/contact/listReplyContact_view",$data);
        }
    }
    // Rely Contact of User
    // ユーザ　の　返事コンタクト
    function replyContact($contact)
    {
        // get id from url
        // URL　から　id　を　得る
        $contact_id = $this->uri->segment(4);
        // get data from model
        //モデル　から　データ　を　得る
        $data['info'] = $this->Contact_model->getInfo($contact_id);
        $data['error'] = "";
        if (is_numeric($contact_id) && $data['info'] != NULL) {            
            if (isset($_POST['ok'])) {                         
                //form validation  
                //　フォーム　の　検証　                  
                $this->form_validation->set_rules("contact_reply","Reply Content","required|max_length[5000]");                 
                if ($this->form_validation->run() == FALSE) {   
                    $this->load->view("admin/contact/replyContact_view",$data);                
                } else {
                    //get content of contact from view
                    //　ビュー　から　コンタクト　の　内容　を　得る
                    $contact = array("contact_id"     =>    $this->input->post("contact_id"),
                                    "contact_email"   => $this->input->post("contact_email"),
                                    "contact_content" => $this->input->post("contact_content"),
                                    "contact_reply"   =>$this->input->post("contact_reply"),
                                    "contact_status"  => 0
                                    );
                    // update contact to Database 
                    // データベース　まで　コンタクト　を　修正する                                                    
                    $this->Contact_model->updateContact($contact,$contact_id);
                    date_default_timezone_set('GMT');
                    $email= $_POST['contact_email'];
                    $reply=$_POST['contact_reply'];
                    $this->load->helper('string');
                    $this->load->library('email');
                    $this->email->from('datptse02336@gmail.com', 'Phạm Tiến Đạt');
                    $this->email->to(''.$email);   
                    $this->email->subject('Trả lời thắc mắc/ý kiến');
                    $this->email->message('Chúng tôi xin trả lời bạn:'.$reply);
                    //send content of reply contact
                    //　返事コンタクトの内容　を　通信する
                    $this->email->send();
                    redirect(base_url()."index.php/admin/contact"); 
                }
            } else {
                $this->load->view("admin/contact/replyContact_view",$data);   
            }            
        } else {            
            return false;
        }
    }
    // search contact by contact type
    // コンタクトのタイプ　で　コンタクト　を　探す
	function getContactByContactType() 
    {
		$data = "";
        $txtType = "";        
        if (isset($_GET['txtType'])) {
            $txtType = $_GET['txtType'];            
        }
        $txtType = ($txtType===null) ? "" : $txtType;   
        $max = $this->Contact_model->num_rowsBySearch($txtType);
        $min = 10;
        $data['txtType'] = $txtType;
        $data['num_rows'] = $max;
        //--- Paging
        //　ページング
        if ($max != 0) {    
            $this->load->library('pagination');                    
            $config['base_url'] = base_url()."index.php/admin/vocabulary/getContactByContactType?txtType=".$txtType."&search=Search";
            $config['total_rows'] = $max;
            $config['per_page'] = $min;
            $config['num_link'] = 3; 
            
            $config['page_query_string'] = TRUE;
            $this->pagination->initialize($config);                                       
            $data['links'] = $this->pagination->create_links();
            $data['contact'] = $this->Contact_model->getContactByType($txtType,$min,$this->input->get('per_page'));
            $this->load->view("admin/contact/listContact_view",$data);
        } else {
            $data['contact'] = null;            
            $this->load->view("admin/contact/listContact_view",$data);
        }
	}
    //delete contact
    //　コンタクト　を　消す
	function deleteContact() 
    {
        //get contact id from url
        //　URL　から　コンタクト　の　id　を　得る
		$contact_id = $this->uri->segment(4);             
        if (is_numeric($contact_id)) {            
            $this->Contact_model->deleteContact($contact_id);
            redirect(base_url()."index.php/admin/contact/index");        
        } else {
            return false;     
        }
	}
    //delete replied contact
    //　返事したコンタクト　を　消す
    function deleteReplyContact() 
    {
        //get id from url
        //　URL　から　id　を　得る
        $contact_id = $this->uri->segment(4);             
        if (is_numeric($contact_id)) {            
            // delete 
            //　消す
            $this->Contact_model->deleteContact($contact_id);
            redirect(base_url()."index.php/admin/contact/listReplyContact");        
        } else {
            return false;     
        }
    }
}
?>