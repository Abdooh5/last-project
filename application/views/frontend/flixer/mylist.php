<?php include 'header_browse.php';?>
<style>
    /* تحسين عرض الشبكة */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* عدد الأعمدة التلقائي */
        gap: 20px; /* المسافة بين العناصر */
        margin: 20px 60px;
    }

    .thumb-container {
        background-color: #111;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        transition: transform 0.3s ease-in-out;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
    }

    .thumb-container:hover {
        transform: scale(1.05);
    }

    .thumb-img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 8px;
    }

    .thumb-title {
        font-size: 14px;
        font-weight: bold;
        color: white;
        margin-top: 8px;
    }
</style>

<!-- MOVIE LIST, GENRE WISE LISTING -->
<?php
	$movies		=	$this->crud_model->get_mylist('movie');
	$series		=	$this->crud_model->get_mylist('series');
	?>
<div class="row" style="margin:20px 60px;">
	<h4 style="text-transform: capitalize;">
		<?php echo get_phrase('My_List');?> 
			(<?php echo count($movies) + count($series);?>)
	</h4>
	<div class="content">
		<div class="grid">
			<?php 
				for ($i = 0; $i<count($movies) ; $i++)
				{
					$title	=	$this->db->get_where('movie' , array('movie_id' => $movies[$i]))->row()->title;
					$link	=	base_url().'index.php?browse/playmovie/' . $movies[$i];
					$thumb	=	$this->crud_model->get_thumb_url('movie' , $movies[$i]);
					//include 'thumb.php';
					?>
					<div class="thumb-container">
                    <a href="<?php echo $link; ?>">
                        <img src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>" class="thumb-img">
                    </a>
                    <div class="thumb-title"><?php echo $title; ?></div>
                </div>
				<?php 
				}
				
				for ($i = 0; $i<count($series) ; $i++)
				{
					$title	=	$this->db->get_where('series' , array('series_id' => $series[$i]))->row()->title;
					$link	=	base_url().'index.php?browse/playseries/' . $series[$i];
					$thumb	=	$this->crud_model->get_thumb_url('series' , $series[$i]);
					?>
					<div class="thumb-container">
                    <a href="<?php echo $link; ?>">
                        <img src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>" class="thumb-img">
                    </a>
                    <div class="thumb-title"><?php echo $title; ?></div>
                </div>
				<?php 
				}
				?>
				
		</div>
	</div>
	<div style="clear: both;"></div>
	<ul class="pagination">
		<?php echo $this->pagination->create_links(); ?>
	</ul>
</div>
<div class="container" style="margin-top: 90px;">
	<hr style="border-top:1px solid #333;">
	<?php include 'footer.php';?>
</div>