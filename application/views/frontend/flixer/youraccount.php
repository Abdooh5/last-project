<?php include 'header_browse.php';?>
<div class="container" style="margin-top: 90px;">
	<div class="row">
		<!-- NOTIFICATION MESSAGES HERE -->
		<?php
			if ($this->session->flashdata('status') == 'email_changed'):
			?>
		<div class="alert alert-dismissible alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
				<?php echo get_phrase('Email_changed_successfully.');?>
		</div>
		<?php endif;?>
		<?php
			if ($this->session->flashdata('status') == 'password_changed'):
			?>
		<div class="alert alert-dismissible alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
				<?php echo get_phrase('Password_changed_successfully.');?>
		</div>
		<?php endif;?>
		<!-- NOTIFICATION MESSAGES ENDS -->
		<div class="col-lg-12">
			<h3 class="black_text"><?php echo get_phrase('Account');?></h3>
			<hr>
		</div>
		<div class="col-lg-12">
			<div class="row">
				<div class="col-lg-7">
					<div class="row" style="margin: 5px;">
						<div class="pull-left black_text">
							<b><?php echo $this->crud_model->get_current_user_detail()->email;?></b>
						</div>
						<div class="pull-right">
							<a href="<?php echo base_url();?>index.php?browse/emailchange" class="blue_text">
								<?php echo get_phrase('Change_Email');?></a>
						</div>
					</div>
					<div class="row" style="margin: 5px;">
						<div class="pull-left">
							<?php echo get_phrase('Password');?> : ******</div>
						<div class="pull-right">
							<a href="<?php echo base_url();?>index.php?browse/passwordchange" class="blue_text">
								<?php echo get_phrase('Change_Password');?></a>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-lg-5">
					<span style="font-size: 20px;">
						<?php echo get_phrase('MY_PROFILE');?></span>
					<br>
				</div>
				<div class="col-lg-7">
					<div class="row" style="margin: 5px;">
						<div class="pull-left black_text">
							<?php
							if (isset($active_user)) :
							?>
								<img src="<?php echo base_url();?>assets/global/<?php echo $bar_thumb;?>" style="margin:10px 10px 10px 0px; height: 30px;" />
								<?php echo $bar_text;?>
								<br>
							<?php endif;?>
						</div>
						<div class="pull-right">
							<a href="<?php echo base_url();?>index.php?browse/manageprofile" class="blue_text">
								<?php echo get_phrase('Manage_profiles');?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<hr>
	<?php include 'footer.php';?>
</div>
