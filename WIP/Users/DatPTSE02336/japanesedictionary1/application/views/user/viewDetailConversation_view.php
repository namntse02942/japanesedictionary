<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<link href="<?php echo base_url();?>public/css/style.css" rel="stylesheet" type="text/css" />
	<title>Chi tiết Ngữ pháp</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript"> 
/*$(document).ready(function(){ 
  $("a.an_hien").click(function(){ 
    $("div#test").slideToggle(); 
  }); 
}); 
$(document).ready(function(){ 
  $("a.hien_an").click(function(){ 
    $("div#test1").slideToggle(); 
  }); 
}); 
$(document).ready(function(){ 
  $("a.hien_an1").click(function(){ 
    $("div#test2").slideToggle(); 
  }); 
}); */
function showdetail(id) {
		$('.test-' + id).slideToggle();
	} 
</script> 
</head>
<body>
<div id="container">
		<?php 
			if ($this->my_auth->is_User()) {
				$this->load->view("user/top_login_view");
			} else {
				$this->load->view("user/top_view");
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
						<?php foreach ($conver as $row) {?>
						<div align="left"><a style="font-size:20px;" id="btnSearch" href="<?php echo base_url();?>index.php/Home/home/conversation<?php echo $row['c_level']; ?>">Back</a></div>
						<div><h1><?php echo $row['c_title']; ?></h1></div>
						<?php 
							if ($detailConver !==null) {
								foreach ($detailConver as $row) {					
						 ?>
							<div align="left" style="color:blue; margin-left:10px;font-size:20px;"><i><h4 style="font-weight: normal;">Tình huống :
							<?php echo $row->con_title;?></h4></i></div>
							<div align="left"><h3 style="margin-left:80px;"><?php echo $row->con_hiragana;?></h3></div>
							<a href="javascript:void(0)" class="an_hien" onclick="showdetail('<?php echo $row->con_id; ?>')"><i><p align="left" style="color:blue; margin-left:40px;font-size:18px;">Dịch</p></i></a>
							<div class="test-<?php echo $row->con_id; ?>" align="left" style="display: none;margin-left:40px;" >
							<h4 style="margin-left:80px;"><?php echo $row->con_meaning;?></h4>
							</div>
						<?php }?>
						<?php }?>
						<?php }?>
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