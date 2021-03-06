<?php 
/**
* 
*/
class Kanji extends CI_Controller
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
        $this->load->model("Kanji_model");
        //check Authentication
        //　認証　を　チェックする
        if (!$this->my_auth->is_Admin()) {            
            redirect(base_url()."index.php/admin/verify/login");
            exit();
        }
	}
    //list kanji
    //　リスト　漢字
	function index() 
    {
		// count record
        // レコード　を　カウントする
        $max = $this->Kanji_model->num_rows();
        // number of records on one page
        // ページ　に　レコード　の　番号
        $min = 10;
        $data['num_rows'] = $max;
        //--- Paging
        //　ページング
        if ($max!=0) {    
        	//load library
            //　ロード　ライブラリ
            $this->load->library('pagination');                    
            $config['base_url'] = base_url()."index.php/admin/kanji/index";
            $config['total_rows'] = $max;
            $config['per_page'] = $min;
            $config['num_link'] = 3; 
            $config['uri_segment'] = 4;
            $this->pagination->initialize($config);                        
            $data['links'] = $this->pagination->create_links();
            //get data from DB
            //データベース　から　データ　を　得る
            $data['kanji'] = $this->Kanji_model->getAllKanji($min,$this->uri->segment($config['uri_segment']));
            // load view
            // ロード　ビュー
            $this->load->view("admin/kanji/listKanji_view",$data);
        } else {
            $data['kanji'] = null;            
            $this->load->view("admin/kanji/listKanji_view",$data);
        }
	}
    // list　Contributed　Kanji
    //　リスト　寄付した漢字
    function listContributedKanji() 
    {
        // count records
        // レコード　を　カウントする
        $max = $this->Kanji_model->num_rows_contributed();
        // number of records on one page
        // ページ　に　レコード　の　番号
        $min = 10;
        $data['num_rows_contributed'] = $max;
        //--- Paging
        //　ページング
        if ($max != 0) {    
            //load library
            //　ロード　ライブラリ
            $this->load->library('pagination');                    
            $config['base_url'] = base_url()."index.php/admin/kanji/listContributedKanji";
            $config['total_rows'] = $max;
            $config['per_page'] = $min;
            $config['num_link'] = 3; 
            $config['uri_segment'] = 4;
            $this->pagination->initialize($config);                        
            $data['links'] = $this->pagination->create_links();
            //get data from DB
            //データベース　から　データ　を　得る
            $data['kanji'] = $this->Kanji_model->getContributedKanji($min,$this->uri->segment($config['uri_segment']));
            // load view
            // ロード　ビュー
            $this->load->view("admin/kanji/listContributedKanji_view",$data);
        } else {
            $data['kanji'] = null;            
            $this->load->view("admin/kanji/listContributedKanji_view",$data);
        }
    }
    // get　By　HanViet
    // HanViet　で　得る
	function getByHanViet() 
    {
		$data = "";
        $txtHanViet = "";     
        if (isset($_GET['txtHanViet'])) {
            $txtHanViet = stripslashes(trim($_GET['txtHanViet']));           
        }
        $txtHanViet = ($txtHanViet === null) ? "" : $txtHanViet; 
        if (strpos($txtHanViet,'%') !== false) {                        
            $data['kanji'] = null;    
            $data['txtHanViet'] = $txtHanViet;         
            $this->load->view("admin/kanji/listKanji_view",$data);
        }
        if ($txtHanViet != "") {
        $max = $this->Kanji_model->num_rowsBySearch($txtHanViet);
        $min = 10;
        $data['txtHanViet'] = $txtHanViet;
        $data['num_rows'] = $max;
        //--- Paging
        //　ページング
            if ($max != 0) {    
                $this->load->library('pagination');                    
                $config['base_url'] = base_url()."index.php/admin/kanji/getByHanViet?txtHanViet=".$txtHanViet."&search=Search";
                $config['total_rows'] = $max;
                $config['per_page'] = $min;
                $config['num_link'] = 3; 
                
                $config['page_query_string'] = TRUE;
                $this->pagination->initialize($config);                                  
                $data['links'] = $this->pagination->create_links();
                $data['kanji'] = $this->Kanji_model->getByHanViet($txtHanViet,$min,$this->input->get('per_page'));
                $this->load->view("admin/kanji/listKanji_view",$data);
            } else {
                $data['kanji'] = null;            
                $this->load->view("admin/kanji/listKanji_view",$data);
            }
        } else {
            $data['kanji'] = null;            
            $this->load->view("admin/kanji/listKanji_view",$data);   
        }
	}
    // add　Kanji
    //　漢字　を　加える
	function addKanji() 
    {
		$data['error'] = "";
        if (isset($_POST['addnew'])) {
            $this->load->view("admin/kanji/addKanji_view",$data);
        } else {
            $this->form_validation->set_rules("k_kanji","Kanji","required|is_unique[kanji.k_kanji]|max_length[10]");
            $this->form_validation->set_rules("k_hanviet","Hanviet","required|max_length[50]");
            $this->form_validation->set_rules("k_onyomi", "Onyomi", 'required|trim|max_length[100]');
            $this->form_validation->set_rules("k_kunyomi", "Kunyomi", 'trim|max_length[100]');
            $this->form_validation->set_rules("k_meaning","Meaning","required|max_length[100]");
            $this->form_validation->set_rules("k_level","Level","required|max_length[10]");                            
            $this->form_validation->set_rules("k_lesson","Lesson","max_length[20]");
            if ($this->form_validation->run() == FALSE) {            
                $this->load->view("admin/kanji/addKanji_view",array("error" => ""));
            } else {                
                $kanji = array("k_kanji"    => $this->input->post("k_kanji"),                        
                               "k_hanviet"  => $this->input->post("k_hanviet"),
                               "k_onyomi"   => $this->input->post("k_onyomi"),
                               "k_kunyomi"  => $this->input->post("k_kunyomi"),
                               "k_meaning"  => $this->input->post("k_meaning"),
                               "k_level"    => $_POST['k_level'],
                               "k_status"   => 1,
                               "k_lesson"   => $this->input->post("k_lesson")
                              );                                 
                try {
                    $this->Kanji_model->addKanji($kanji);
                    redirect(base_url()."index.php/admin/kanji/index");                                                   
                } catch (Exception $e) {
                    show_404();
                    log_message('error', $e->getMessage());
                }                
            }
        }
	}
    // edit　Kanji
    //　漢字　を　修正する
	function editKanji() 
    {
		$k_id = $this->uri->segment(4);
        $data['info'] = $this->Kanji_model->getInfoKanji($k_id);
        $data['error'] = "";
        if (is_numeric($k_id) && $data['info'] != NULL) {            
            if (isset($_POST['ok'])) {           
                $this->form_validation->set_rules("k_kanji","Kanji","required");
                $this->form_validation->set_rules("k_hanviet","Hanviet","required|max_length[50]");
                $this->form_validation->set_rules("k_onyomi","Onyomi", 'trim|max_length[100]');
                $this->form_validation->set_rules("k_kunyomi","Kunyomi", 'trim|max_length[100]');
                $this->form_validation->set_rules("k_meaning","Meaning","required|max_length[100]");
                $this->form_validation->set_rules("k_level","Level","required|max_length[10]");                                                                  
                $this->form_validation->set_rules("k_lesson","Lesson","max_length[20]");                 
                if ($this->form_validation->run() == FALSE) {   
                    $this->load->view("admin/kanji/editKanji_view",$data);                
                } else {
                    $kanji = array("k_id"       => $this->input->post("k_id"),
                                   "k_kanji"    => $this->input->post("k_kanji"),                        
                                   "k_hanviet"  => $this->input->post("k_hanviet"),
                                   "k_onyomi"   => $this->input->post("k_onyomi"),
                                   "k_kunyomi"  => $this->input->post("k_kunyomi"),
                                   "k_meaning"  => $this->input->post("k_meaning"),
                                   "k_level"    => $this->input->post("k_level"),
                                   "k_status"   => 1,
                                   "k_lesson"   => $this->input->post("k_lesson")
                                  );                                       
                    $this->Kanji_model->updateKanji($kanji,$this->input->post("k_id"));
                    redirect(base_url()."index.php/admin/kanji"); 
                }
            } else {
                $this->load->view("admin/kanji/editKanji_view",$data);   
            }     
        } else {            
            return false;
        }
	}
    // approved　Kanji
    //　漢字　を　認める
    function approvedKanji() 
    {
        $k_id = $this->uri->segment(4);
        $data['info'] = $this->Kanji_model->getInfoKanji($k_id);
        $data['error'] = "";
        if (is_numeric($k_id) && $data['info'] != NULL) {            
            if (isset($_POST['ok'])) {                                              
                $this->form_validation->set_rules("k_kanji","Kanji","required|max_length[10]");
                $this->form_validation->set_rules("k_hanviet","Hanviet","required|max_length[50]");
                $this->form_validation->set_rules("k_onyomi", "Onyomi", 'trim|max_length[100]');
                $this->form_validation->set_rules("k_kunyomi", "Kunyomi", 'trim|max_length[100]');
                $this->form_validation->set_rules("k_meaning","Meaning","required|max_length[100]");
                $this->form_validation->set_rules("k_level","Level","required|max_length[10]");
                $this->form_validation->set_rules("k_lesson","Lesson","max_length[20]");                   
                if ($this->form_validation->run() == FALSE) {   
                    $this->load->view("admin/kanji/editKanji_view",$data);                
                } else {
                    $kanji = array("k_id"      => $this->input->post("k_id"),
                                   "k_kanji"   => $this->input->post("k_kanji"),                        
                                   "k_hanviet" => $this->input->post("k_hanviet"),
                                   "k_onyomi"  => $this->input->post("k_onyomi"),
                                   "k_kunyomi" => $this->input->post("k_kunyomi"),
                                   "k_meaning" => $this->input->post("k_meaning"),
                                   "k_level"   => $this->input->post("k_level"),
                                   "k_status"  => 1,
                                   "k_lesson"  => $this->input->post("k_lesson")
                                  );                                         
                    $this->Kanji_model->updateKanji($kanji, $k_id);
                    redirect(base_url()."index.php/admin/kanji/listContributedKanji"); 
                }
            } else {
                $this->load->view("admin/kanji/approvedKanji_view",$data);   
            }    
        } else {            
            return false;
        }
    }
    // delete　Kanji
    //　漢字　を　消す
	function deleteKanji() 
    {
		$k_id = $this->uri->segment(4);             
        if (is_numeric($k_id)) {            
            $this->Kanji_model->deleteKanji($k_id);
            redirect(base_url()."index.php/admin/kanji/index");        
        } else {
            return false;     
        }
	}
    // delete　Contributed　Kanji
    //　漢字　を　消す
    function deleteContributedKanji() 
    {
        $k_id = $this->uri->segment(4);             
        if (is_numeric($k_id)) {            
            $this->Kanji_model->deleteKanji($k_id);
            redirect(base_url()."index.php/admin/kanji/listContributedKanji");        
        } else {
            return false;     
        }
    }
}
?>