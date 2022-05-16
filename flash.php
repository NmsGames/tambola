

<?php

    if(isset($flash['error_message'])){?>
    	<div class="row site_flash_msg">
			<div class="col-md-12">
			    <div class="alert alert-danger">
			        <a href="#" class="close" data-dismiss="alert">&times;</a>
			           <?php echo $flash['error_message']; ?>
			    </div>
			</div>
			<br>
		</div>
    <?php }
    elseif (isset($flash['success_message'])) { ?>
    	<div class="row site_flash_msg">
			<div class="col-md-12">
			    <div class="alert alert-success">
			        <a href="#" class="close" data-dismiss="alert">&times;</a>
			          <?php echo $flash['success_message']; ?>
			    </div>
			</div>
			<br>
		</div>
    <?php }
    else{}
?>
 



