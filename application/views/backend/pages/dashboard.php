<div class="row">
	<!-- TOTAL VIDEO NUMBER -->
	<div class="col-md-4 col-sm-12 ">
		<div class="panel widget-flat">
			<div class="panel-body">
				<i class="fa fa-film" style="float: right; font-size: 25px; color: #859ed0;"></i>
				<h5 class="text-muted font-weight-normal mt-0" title="Number of Customers"><?php echo get_phrase('total_movies'); ?></h5>
				<h3 class="mt-3 mb-3"><?php echo $this->db->from('movie')->count_all_results();?></h3>
			</div>
		</div>
	</div>
	<!-- TOTAL TV SERIES NUMBER -->
	<div class="col-md-4 col-sm-12 ">
		<div class="panel widget-flat">
			<div class="panel-body">
				<i class="fa fa-ticket" style="float: right; font-size: 25px; color: #859ed0;"></i>
				<h5 class="text-muted font-weight-normal mt-0" title="Number of Customers"><?php echo get_phrase('total_tv_series'); ?></h5>
				<h3 class="mt-3 mb-3"><?php echo $this->db->from('series')->count_all_results();?></h3>
			</div>
		</div>
	</div>
	<!-- TOTAL EPISODE NUMBER -->
	<div class="col-md-4 col-sm-12 ">
		<div class="panel widget-flat">
			<div class="panel-body">
				<i class="fa fa-television" style="float: right; font-size: 25px; color: #859ed0;"></i>
				<h5 class="text-muted font-weight-normal mt-0" title="Number of Customers"><?php echo get_phrase('total_episodes'); ?></h5>
				<h3 class="mt-3 mb-3"><?php echo $this->db->from('episode')->count_all_results();?></h3>
			</div>
		</div>
	</div>
</div>
<div style="margin: 20px;"></div>
<div class="row">
	<!-- TOTAL USER NUMBER -->
	<div class="col-md-4 col-sm-12 ">
		<div class="panel widget-flat">
			<div class="panel-body">
				<i class="fa fa-user" style="float: right; font-size: 25px; color: #859ed0;"></i>
				<h5 class="text-muted font-weight-normal mt-0" title="Number of Customers"><?php echo get_phrase('total_registered_user'); ?></h5>
				<h3 class="mt-3 mb-3"><?php echo $this->db->from('user')->count_all_results();?></h3>
			</div>
		</div>
	</div> 
	
	
