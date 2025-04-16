<!-- TOP HEADING SECTION -->

<style>
	.nav_transparent {
	padding: 10px 0px 10px; border: 1px;
	background: rgba(0,0,0,0.8);
	}
	.nav_dark {
	background-color: #000; .
	padding: 10px;
	}
</style>
<?php
	if ($page_name == 'home' || $page_name == 'playmovie')
		$nav_type = 'nav_transparent';
	else
		$nav_type = 'nav_dark';
	?>
<div class="navbar navbar-default navbar-fixed-top <?php echo $nav_type;?>" >
	<div class="container" style=" width: 100%;">
		<div class="navbar-header">
			<a href="<?php echo base_url();?>index.php?browse/home" class="navbar-brand">
				<img src="<?php echo base_url();?>/assets/global/logo.jpg" style=" height: 32px;margin-right: 50px;" />
			</a>
			<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="navbar-collapse collapse" id="navbar-main">
			<ul class="nav navbar-nav">
				<!-- MOVIES GENRE WISE-->

			


<!-- TV Programs GENRE WISE (Static Categories) -->
<li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="" style="color: #e50914; font-weight: bold;">
        <?php echo get_phrase('مسلسلات'); ?> <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" aria-labelledby="themes">
        <?php 
		$category_name='مسلسلات';
		$cate_id = $this->db->select('category_id')->get_where('category', ['name' => trim($category_name)])->row()->category_id ?? null;

        $countries = [
            'تركي' => 'مسلسلات تركية',
            'مصري' => 'مسلسلات مصرية',
            'خليجي' => 'مسلسلات خليجية',
            'شامي' => 'مسلسلات شامية' ,
			 'أجنبي' => 'مسلسلات أجنبية',
            'هندي' => 'مسلسلات هندية',
			'أسيوي' => 'مسلسلات أسيوي',
        ];
        
        foreach ($countries as $country_name => $display_name) {
            $country = $this->db->get_where('country', ['name' => $country_name])->row();
            if ($country) {
                $this->db->where('country_id', $country->country_id);
				$count= $this->db->where('category', $cate_id)->count_all_results('series');
                ?>
                <li>
                    <a href="<?php echo base_url(); ?>index.php?browse/series_by_country/<?php echo $country->country_id; ?>/<?php echo $cate_id ?>">
                        <?php echo $display_name; ?> (<?php echo $count; ?>)
                    </a>
                </li>
                <?php
            }
        }

      
        ?>
    </ul>
</li>
<li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="" style="color: #e50914; font-weight: bold;">
        <?php echo get_phrase('أفلام'); ?> <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" aria-labelledby="themes">
        <?php 
		$category_name=' أفلام';
		$cate_id = $this->db->select('category_id')->get_where('category', ['name' => trim($category_name)])->row()->category_id ?? null;

        $countries = [
            'تركي' => 'أفلام تركية',    
            'عربي' => 'أفلام عربية' ,
			 'أجنبي' => 'أفلام أجنبية',
            'هندي' => 'أفلام هندية',
			'أسيوي' => 'أفلام أسيوي',
        ];
        
        foreach ($countries as $country_name => $display_name) {
            $country = $this->db->get_where('country', ['name' => $country_name])->row();
            if ($country) {
                $count = $this->db->where('country_id', $country->country_id)->count_all_results('movie');
                ?>
                <li>
				
                    <a href="<?php echo base_url(); ?>index.php?browse/movie_by_country/<?php echo $country->country_id; ?>">
                        <?php echo $display_name; ?> (<?php echo $count; ?>)
                    </a>
                </li>
                <?php
            }
        }

      
        ?>
    </ul>
</li>

<!-- TV SERIES anmy WISE-->
<li class="dropdown">
<a class="dropdown-toggle" data-toggle="dropdown" href="" style="color: #e50914; font-weight: bold;">
        <?php echo get_phrase('أنمي'); ?> <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" aria-labelledby="themes">
<?php 
	$category_name='أنمي';
	$cate_id = $this->db->select('category_id')->get_where('category', ['name' => trim($category_name)])->row()->category_id ?? null;
	
$genres = [
	'كرتون' => 'كرتون',
    'أنمي' => 'أنمي',
	'أنمي عربي قديم' => ' أنميات عربية قديمة'
];

foreach ($genres as $genre_name => $display_name) {
	$genre = $this->db->get_where('genre', ['name' => $genre_name])->row();
	if ($genre) {
		$this->db->where('category', $cate_id);
		$count = $this->db->where('genre_id', $genre->genre_id)->count_all_results('series');
		?>
		<li>
			<a href="<?php echo base_url(); ?>index.php?browse/series/<?php echo $genre->genre_id; ?>/<?php echo $cate_id; ?>">
				<?php echo $display_name; ?> (<?php echo $count; ?>)
			</a>
		</li>
		<?php
	}
}
?>
</ul>
</li>

<li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="" style="color: #e50914; font-weight: bold;">
        <?php echo get_phrase('مسلسلات رمضان'); ?> <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" aria-labelledby="themes">
        <?php 
        $category_name = 'مسلسلات رمضان'; 
        $cate_id = $this->db->select('category_id')->get_where('category', ['name' => trim($category_name)])->row()->category_id ?? null;

        $year = [
            '2021' => 'مسلسلات رمضان 2021',
            '2022' => 'مسلسلات رمضان 2022',
            '2023' => 'مسلسلات رمضان 2023',
            '2024' => 'مسلسلات رمضان 2024',
            '2025' => 'مسلسلات رمضان 2025',
        ];

        foreach ($year as $year_name => $display_name) {
			
             $this->db->where('category', $cate_id);
            $count = $this->db->where('year', 2025)->count_all_results('series');

            // التحقق مما إذا كانت هناك مسلسلات لهذه السنة
            if ($count > 0 || true) { // إظهار العنصر حتى لو كانت المسلسلات صفر
                ?>
                <li>
                    <a href="<?php echo base_url(); ?>index.php?browse/series_by_year/<?php echo $year_name; ?>/<?php echo $cate_id; ?>">
                        <?php echo $display_name; ?> (<?php echo $count; ?>)
                    </a>
                </li>
                <?php
            }
        }
        ?>
    </ul>
</li>

<li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="" style="color: #e50914; font-weight: bold;">
        <?php echo get_phrase('برامج تلفيزيونية'); ?> <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" aria-labelledby="themes">
        <?php 
		$category_name='برامج تلفيزيونية';
		$cate_id = $this->db->select('category_id')->get_where('category', ['name' => trim($category_name)])->row()->category_id ?? null;
        $countries = [
            'عربي' => 'برامج عربية',
            'أجنبي' => 'برامج أجنبية',
            
        ];
        
        foreach ($countries as $country_name => $display_name) {
            $country = $this->db->get_where('country', ['name' => $country_name])->row();
            if ($country) {
				$this->db->where('category', $cate_id);
                $count = $this->db->where('country_id', $country->country_id)->count_all_results('series');
                ?>
                <li>
                    <a href="<?php echo base_url(); ?>index.php?browse/series_by_country/<?php echo $country->country_id; ?>/<?php echo $cate_id; ?>">
                        <?php echo $display_name; ?> (<?php echo $count; ?>)
                    </a>
                </li>
                <?php
            }
        }

        $genres = [
            'رمضاني' => 'برامج رمضانية',
            'مصارعة' => ' مصارعة'
        ];
        
        foreach ($genres as $genre_name => $display_name) {
            $genre = $this->db->get_where('genre', ['name' => $genre_name])->row();
            if ($genre) {
				$this->db->where('category', $cate_id);
				$count = $this->db->where('genre_id', $genre->genre_id)->count_all_results('series');
                ?>
                <li>
                    <a href="<?php echo base_url(); ?>index.php?browse/series/<?php echo $genre->genre_id; ?>/<?php echo $cate_id; ?>">
                        <?php echo $display_name; ?> (<?php echo $count; ?>)
                    </a>
                </li>
                <?php
            }
        }
        ?>
    </ul>
</li>





<!-- TV Programs GENRE WISE (Static Categories) -->







<!-- أحدث السلاسل التلفزيونية -->
<li>
    <a href="<?php echo base_url(); ?>index.php?browse/latest_series" style="color: #e50914; font-weight: bold;">
        <?php echo get_phrase('latest_added'); ?>
    </a>
</li>





				<!-- MY LIST -->
				<?php if($this->session->userdata('active_user') != 'admin'): ?>
					<li>
						<a href="<?php echo base_url();?>index.php?browse/mylist"><?php echo get_phrase('My_List');?></a>
					</li>
				<?php endif;	 ?>

				<?php if(addon_status('blog')): ?>
					<li class="<?php if($page_name == 'blogs') echo 'active'; ?>">
						<a class="" href="<?php echo base_url();?>index.php?addons/blog"><?php echo get_phrase('Blog');?></a>
					</li>
				<?php endif; ?>
			</ul>
			<!-- PROFILE, ACCOUNT SECTION -->
			<?php
// By default, show email & general thumb at top
$bar_text = $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row('email');
$bar_thumb = base_url('assets/global/thumb1.png');

// Check if there is an active user session
$active_user = $this->session->userdata('active_user');
if ($active_user) {
    $bar_text = $this->crud_model->get_username_of_user($active_user);
    $bar_thumb = $this->crud_model->get_image_url_of_user($active_user);
}
?>
<ul class="nav navbar-nav navbar-right">
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" style="padding:10px;">
            <img src="<?php echo $bar_thumb; ?>" style="height:30px; border-radius: 50%;" />
            <?php echo $bar_text; ?>
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="themes">
            <li><a href="<?php echo base_url(); ?>index.php?browse/manageprofile"><?php echo get_phrase('Manage_Profiles'); ?></a></li>
            <li class="divider"></li>
            <!-- SHOW ADMIN LINK IF ADMIN LOGGED IN -->
            <?php if ($this->session->userdata('login_type') == 1): ?>
                <li><a href="<?php echo base_url(); ?>index.php?admin/dashboard"><?php echo get_phrase('Admin'); ?></a></li>
            <?php endif; ?>
            <li><a href="<?php echo base_url(); ?>index.php?browse/youraccount"><?php echo get_phrase('Account'); ?></a></li>
            <li><a href="<?php echo base_url(); ?>index.php?home/signout"><?php echo get_phrase('Sign_out'); ?></a></li>
        </ul>
    </li>
</ul>
			<!-- SEARCH FORM -->
			<form class="navbar-form navbar-right" method="post" action="<?php echo base_url();?>index.php?browse/search">
				<div class="form-group">
					<input type="text" class="form-control" placeholder="Titles"
						style="background-color: #000; border: 1px solid #808080; height:35px;" name="search_key">
				</div>
				<button type="submit" class="btn btn-default"><i class="fa fa-search" aria-hidden="true"></i></button>
			</form>
		</div>
	</div>
</div>


<?php
	if ($page_name == 'home' || $page_name == 'playmovie' || $page_name == 'playseries' || $page_name == 'blogs' || $page_name == 'blog_detail_page')
		$padding_amount = '0px';
	else
		$padding_amount = '50px';
	?>
<div style="padding: <?php echo $padding_amount;?>;"></div>
