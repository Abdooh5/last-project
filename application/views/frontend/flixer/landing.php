<!-- TOP LANDING SECTION -->
<div style="height:93vh;width:100%;background-image: url(<?php echo base_url().'assets/frontend/flixer/images/home_top_banner.jpg';?>)">
	<!-- logo -->
	<div style="float: left;">
		<a href="<?php echo base_url();?>index.php?home">
		<img src="<?php echo base_url();?>/assets/global/logo.png" style="margin: 18px 18px; width: 160px;" />
		</a>
	</div>
	<div style="float: right;margin: 18px 18px; height: 50px;" class="hidden-xs">
		<a href="<?php echo base_url();?>index.php?home/signin" class="btn btn-danger"><?php echo get_phrase('sign_in');?></a>
	</div>
	<?php if(addon_status('blog')): ?>
		<div style="float: right;margin: 18px 18px; height: 50px;" class="hidden-xs">
			<a href="<?php echo base_url();?>index.php?addons/blog" class="btn btn-danger"><?php echo get_phrase('blog');?></a>
		</div>
	<?php endif; ?>
	
	<!-- promo text visible for large devices -->
	<div style="font-size: 85px;font-weight: bold;clear: both;padding: 100px 0px 0px 0px; text-align:center; color: #fff;" class="hidden-xs">
		<?php echo get_phrase('See_what_is_next.');?>
		<div style="font-size: 30px; letter-spacing: .2px; color: #fff; font-weight: 400;">
			<?php echo get_phrase('WATCH_ANYWHERE.');?> <?php echo get_phrase('CANCEL_ANYTIME.');?>
		</div>
		<a href="<?php echo base_url();?>index.php?home/signup" class="btn btn-danger btn-lg"
			style="padding: 20px 50px; font-size: 30px;">
			<?php echo get_phrase('JOIN_FREE_FOR_A_MONTH');?>
			<i class="fa fa-angle-right" style="margin:0px 0px 0px 20px;"></i>
		</a>
	</div>
	
	<!-- promo text visible for small devices -->
	<div style="font-size: 45px;font-weight: bold;clear: both;padding: 80px 0px 0px 0px; text-align:center; color: #fff;" class="hidden-lg hidden-sm hidden-md">
		<?php echo get_phrase('See_what_is_next.');?>
		<div style="font-size: 25px; letter-spacing: .2px; color: #fff; font-weight: 400;">
			<?php echo get_phrase('WATCH_ANYWHERE.');?> <br> <?php echo get_phrase('CANCEL_ANYTIME.');?>
		</div>
		<a href="<?php echo base_url();?>index.php?home/signup" class="btn btn-danger btn-lg" ><?php echo get_phrase('JOIN_FREE_FOR_A_MONTH');?></a>
		<br>
		<a href="<?php echo base_url();?>index.php?home/signin" class="btn btn-danger btn-lg" ><?php echo get_phrase('SIGN_IN');?></a>
	</div>
</div>

