<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<link href="<?php echo base_url();?>public/css/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?php echo base_url();?>public/js/javascript.js"></script>
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/themes/base/jquery-ui.css" type="text/css" media="all" />  
        <link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/   css" media="all" />  
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js" type="text/javascript"></script>  
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js" type="text/javascript"></script>  
	<title>Cách học chữ Hán</title>
</head>
<body>
<div id="container">
		<?php 
			if ($this->my_auth->is_User()) {
				$this->load->view("user/top_login_view");
			} else {
				$user = $this->facebook->getUser();    
		        if ($user) {
		            $data['logout_url'] = site_url('Home/verify/fblogout'); // Logs off application
		            $data['user_profile'] = $this->facebook->api('/me');
		            // OR 
		            // Logs off FB!
		            // $data['logout_url'] = $this->facebook->getLogoutUrl();
		            $this->load->view("user/top_view",$data); 
		        } else {
		            $data['login_url'] = $this->facebook->getLoginUrl(array(
		                'redirect_uri' => site_url('Home/verify/fblogin'), 
		                'scope' => array("email") // permissions here
		            ));
		            $this->load->view("user/top_view",$data); 
		        } 								
			}
			
		 ?>		
	<div id="content">
		<?php $this->load->view("user/mainmenu_view");?>
		<div id="noidung">
			<center>
				<?php $this->load->view("user/search_view");?>
				<div id="main-content">
					<div id="left-content">
						<div id="intro-content">
							<center><h2 style="color:blue;">Phương pháp học chữ Kanji</h2></center>
	        			<div align="left" style="width:90%;margin-left:5%;margin-right:5%;">
	        				<h3><b>Bước 1: Học chữ kanji dễ trước khó sau.</b></h3>
								Bạn nên học chữ kanji dễ và thông dụng trước, các bạn nên học chữ kanji đơn giản sau khi các bạn đã
								làm quen các chữ kanji đơn giản thì học chữ kanji khó hơn cũng đơn giản vi thực chất chữ kanji nó 
								bao gồm các chữ kanji đơn giản ghép lại. Với cách học chữ kanji này mọi người có thể học được kanji
								mà không thấy khó khăn trong việc nhận dạng mặt chữ nữa . Đây là phương pháp học chữ kanji mà một 
								số giảng viên tại trung tâm tiếng nhật mình theo học chỉ bảo và mình thấy cách học chữ kanji này 
								cũng rất là hay.
							<h3><b>Bước 2: Học chữ kanji hàng ngày.</b></h3>
								Học chữ kanji cũng như học từ vựng vì vậy ngày nào bạn cũng 
								phải học chữ kanji hãy tạo cho mình mốt thói quen học chữ kanji
								ví dụ: bạn có thể đặt ra mỗi ngày học 5 chữ kanji hay là 10 chữ 
								kanji tùy thuộc vào khả năng nhớ của bạn. Nhưng mà phải chấp hành nguyên tắc 
								là ngày nào cũng phải học chữ kanji gia tăng lượng chữ kanji theo ngày.
	        				<h3><b>Bước 3: Học chữ kanji hay dùng nhất.</b></h3>
	        					Bạn có thể học từ những chữ kanji thường xuyên sử dụng để nhớ nó trước, bạn có thể tìm kiếm từ để học 
	        					bằng cách tra cứu . cách học chữ kanji này cũng rất hiệu quả cho những  bạn mới bắt đầu học kanji. 
	        					Vì những chữ kanji thong dụng hay thường gặp thì học sẽ nhớ nhanh hơn là những chữ kanji ít dùng.
	        				<h3><b>Bước 4: Nắm vững các quy tắc của chữ kanji</b></h3>
	        					Bạn phải nắm vững từ cách viết các nét trong chữ kanji và cách ghép từ trong kanji.
	        					 Đó là cách học chữ kanji hiệu quả nhất. 
	        				<h2 style="color:blue;">Chúc các bạn học tốt !</h2>
	        			</div>
						</div>
					</div>
					<?php $this->load->view("user/popup_view");?>			
				</div>
			<div style="clear:both"></div>
			</center>
		</div>
	</div>
		<?php $this->load->view("user/footer_view");?>
</div>
	
</body>
</html>