<style>
    @media screen and (max-width: 765px) {
        .mobile_vedio_player{
            width: 100%;
            height: 220px;
        }
        .mobile_vedio_player_html{
            width: 100%;
        }
    }
    @media screen and (min-width: 766px) {
        .mobile_vedio_player{
            width: 100%;
            height: 585px;
        }
        .mobile_vedio_player_html{
            width: 100%;
        }
    }

    .video_cover {
        position: relative;
        padding-bottom: 30px;
    }

    .video_cover:after {
        content: "";
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        background-image: url(<?php echo $this->crud_model->get_poster_url('movie', $row['movie_id']); ?>);
        width: 100%;
        height: 100%;
        opacity: 0.2;
        z-index: -1;
        background-size: cover;
    }

    .select_black { background-color: #000; height: 45px; padding: 12px; font-weight: bold; color: #fff; }
    .profile_manage { font-size: 25px; border: 1px solid #ccc; padding: 5px 30px; text-decoration: none; }
    .profile_manage:hover { font-size: 25px; border: 1px solid #fff; padding: 5px 30px; text-decoration: none; color: #fff; }
</style>

<?php include 'header_browse.php'; ?>
<?php $user_id = $this->session->userdata('user_id'); ?>
<?php $active_user = $this->session->userdata('active_user'); ?>
<?php
$movie_details = $this->db->get_where('movie', array('movie_id' => $movie_id))->result_array();
foreach ($movie_details as $row):
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/frontend/' . $selected_theme; ?>/hovercss/demo.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/frontend/' . $selected_theme; ?>/hovercss/set1.css" />

<!-- VIDEO PLAYER -->
<div class="video_cover">
	<div class="container" style="padding-top:100px; text-align: center;">
		<div class="row">
			<div class="col-lg-12" id="series_div">
			
                <!-- VIDEO PLAYER BASED ON URL TYPE -->

					
                
                    <link rel="stylesheet" href="<?php echo base_url();?>assets/global/plyr/plyr.css">
                    <video poster="<?php echo$this->crud_model->get_thumb_url('movie' , $row['movie_id']); ?>" id="player" playsinline controls>
                        <?php if (get_video_extension($row['url']) == 'webm'): ?>
                            <source src="<?php echo 'assets/global/movie_video/'. $row['url']; ?>" type="video/webm">
                        <?php elseif (get_video_extension( $row['url']) == 'mp4'): ?>
                            <source src="<?php echo 'assets/global/movie_video/'.$row['url']; ?>" type="video/mp4">
                        <?php else: ?>
                            <h4><?php get_phrase('video_url_is_not_supported'); ?></h4>
                        <?php endif; ?>
                    </video>
                    <script src="<?php echo base_url();?>assets/global/plyr/plyr.js"></script>
                    <script>const player = new Plyr('#player');</script>
             
            </div>

			<div class="col-lg-12 hidden" id="trailer_div">




				<!-- Video player generator starts -->
				
					<link rel="stylesheet" href="<?php echo base_url();?>assets/global/plyr/plyr.css">
					<video poster="<?php echo $this->crud_model->get_thumb_url('movie' , $row['movie_id']);?>" id="trailer_url" playsinline controls>
					<?php if (get_video_extension($row['trailer_url']) == 'mp4'): ?>
				      	<source src="<?php echo 'assets/global/movie_trailer/'.$row['trailer_url']; ?>" type="video/mp4">
					<?php elseif (get_video_extension($row['trailer_url']) == 'webm'): ?>
						<source src="<?php echo 'assets/global/movie_trailer/'.$row['trailer_url']; ?>" type="video/webm">
					<?php else: ?>
						<h4><?php get_phrase('video_url_is_not_supported'); ?></h4>
					<?php endif; ?>
					</video>

					<script src="<?php echo base_url();?>assets/global/plyr/plyr.js"></script>
					<script>const trailer_url = new Plyr('#trailer_url');</script>
				
				<!-- Video player generator ends -->

			</div>

		</div>
	</div>
</div>
<!-- VIDEO DETAILS HERE -->
<div class="container" style="margin-top: 30px;">
    <div class="row">
        <div class="col-lg-8">
            <div class="row">
                <div class="col-lg-3">
                    <img src="<?php echo $this->crud_model->get_thumb_url('movie', $row['movie_id']); ?>" style="height: 60px; margin:20px;" />
                </div>
                <div class="col-lg-9">
                    <!-- VIDEO TITLE -->
                    <h3><?php echo $row['title']; ?></h3>
                    <!-- RATING CALCULATION -->
                    <div>
                        <?php for($i = 1 ; $i <= $row['rating']; $i++): ?>
                            <i class="fa fa-star" aria-hidden="true" style="color: #e2cc0c;"></i>
                        <?php endfor; ?>
                        <?php for($i = 5; $i > $row['rating']; $i--): ?>
                            <i class="fa fa-star-o" aria-hidden="true"></i>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- ADD OR DELETE FROM PLAYLIST -->
            <span id="mylist_button_holder"></span>
            <span id="mylist_add_button" style="display:none;">
                <a href="#" class="btn btn-danger btn-md" style="font-size: 16px; margin-top: 20px;" onclick="process_list('movie' , 'add', <?php echo $row['movie_id']; ?>)">
                    <i class="fa fa-plus"></i> <?php echo get_phrase('Add_to_My_list'); ?>
                </a>
            </span>
            <a href="<?php echo base_url() . 'assets/global/movie_video/' . $row['url']; ?>" download class="btn btn-danger btn-md" id="download_button" style="font-size: 16px; margin-top: 20px; margin-left: 10px;">
                <i class="fa fa-download"></i> <?php echo get_phrase('Download'); ?>
            </a>

            <button class="btn btn-danger btn-md" id="watch_button" style="font-size: 16px; margin-top: 20px;" onclick="divToggle()">
                <i class="fa fa-eye"></i> <?php echo get_phrase('watch_trailer'); ?>
            </button>
            <span id="mylist_delete_button" style="display:none;">
                <a href="#" class="btn btn-default btn-md" style="font-size: 16px; margin-top: 20px;" onclick="process_list('movie' , 'delete', <?php echo $row['movie_id']; ?>)">
                    <i class="fa fa-check"></i> <?php echo get_phrase('Added_to_My_list'); ?>
                </a>
            </span>

            <!-- MOVIE GENRE -->
            <div style="margin-top: 10px;">
                <strong><?php echo get_phrase('Genre'); ?></strong> :
                <a href="<?php echo base_url(); ?>index.php?browse/movie/<?php echo $row['genre_id']; ?>">
                    <?php echo $this->db->get_where('genre', array('genre_id' => $row['genre_id']))->row()->name; ?>
                </a>
            </div>
            <!-- MOVIE YEAR -->
            <div>
                <strong><?php echo get_phrase('Year'); ?></strong> : <?php echo $row['year']; ?>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top:20px;">
        <div class="col-lg-12">
            <div class="bs-component">
                <ul class="nav nav-tabs">
                    <li class="active" style="width:25%;">
                        <a href="#about" data-toggle="tab"><?php echo get_phrase('About'); ?></a>
                    </li>
                    <li style="width:25%;">
                        <a href="#cast" data-toggle="tab"><?php echo get_phrase('Cast'); ?></a>
                    </li>
                    <li style="width:25%;">
                        <a href="#category" data-toggle="tab"><?php echo get_phrase('Category'); ?></a>
                    </li>
                    <li style="width:25%;">
                        <a href="#more" data-toggle="tab"><?php echo get_phrase('More'); ?></a>
                    </li>
                </ul>
                <div id="myTabContent" class="tab-content">
                    <!-- TAB FOR TITLE -->
                    <div class="tab-pane active in" id="about">
                        <p><?php echo $row['description_long']; ?></p>
                    </div>
                    <!-- TAB FOR ACTORS -->
                    <div class="tab-pane " id="cast">
                        <p>
                            <?php
                            $actors = json_decode($row['actors']);
                            foreach ($actors as $actor_id) {
                                ?>
                                <div style="float: left; text-align:center; color: #fff; font-weight: bold;">
                                    <img src="<?php echo $this->crud_model->get_actor_image_url($actor_id); ?>" style="height: 160px; margin:5px;" />
                                    <br>
                                    <a href="<?php echo base_url('index.php?browse/filter/movie/all/' . $actor_id . '/all/all'); ?>" style="color: white;">
                                        <?php echo $this->db->get_where('actor', array('actor_id' => $actor_id))->row()->name; ?>
                                    </a>
                                </div>
                                <?php
                            }
                            ?>
                        </p>
                    </div>
                    <!-- TAB FOR CATEGORY -->
                    <div class="tab-pane " id="category">
                        <p>
                            <div style="float: left; text-align:center; color: #fff; font-weight: bold;">
                                <?php
                                $category_id = $this->db->get_where('category', array('category_id' => $row['category']))->row()->category_id;
                                ?>
                                <img src="<?php echo base_url('assets/global/director/' . $category_id . '.jpg'); ?>" style="height: 160px; margin:5px;" />
                                <br>
                                <?php echo $this->db->get_where('category', array('category_id' => $row['category']))->row()->name; ?>
                            </div>
                        </p>
                    </div>
                    <!-- TAB FOR SAME CATEGORY MOVIES -->
                    <div class="tab-pane" id="more">
                        <p>
                            <div class="content">
                                <div class="grid">
                                    <?php
                                    $movies = $this->crud_model->get_movies($row['genre_id'], 10, 0);
                                    foreach ($movies as $movie) {
                                        $title = $movie['title'];
                                        $link = base_url() . 'index.php?browse/playmovie/' . $movie['movie_id'];
                                        $thumb = $this->crud_model->get_thumb_url('movie', $movie['movie_id']);
                                        include 'thumb.php';
                                    }
                                    ?>
                                </div>
                            </div>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr style="border-top:1px solid #333;">
    <?php include 'footer.php'; ?>
</div>

<?php endforeach; ?>


<script type="text/javascript">
	function divToggle() {
		if ($('#trailer_div').hasClass('hidden')) {
			$('#trailer_div').removeClass('hidden');
			$('#series_div').addClass('hidden');
			$('#watch_button').html('<?php echo '<i class="fa fa-eye"></i> '.get_phrase('watch_series') ?>');
			player.pause();
		}else {
			$('#series_div').removeClass('hidden');
			$('#trailer_div').addClass('hidden');
			$('#watch_button').html('<?php echo '<i class="fa fa-eye"></i> '.get_phrase('watch_trailer') ?>');
			trailer_url.pause();
		}
		$("html, body").animate({scrollTop: 0}, 500);

	}
</script>
<script language="javascript" type="text/javascript" src="jquery-1.8.2.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
    $('#watch_button').click(function(){
        $('iframe').attr('src', $('iframe').attr('src'));
    });
});
</script>