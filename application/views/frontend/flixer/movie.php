<?php include 'header_browse.php';?>

<div class="row" style="margin:20px 60px;">
	<h4 style="text-transform: capitalize;"><?php echo get_phrase('filter_by_cast'); ?></h4>
	<div class="content">
		<div class="grid">
			<div class="row">
				<div class="col-md-6 col-lg-2">
					<div class="select" style="width: 100%; margin-bottom: 10px">
						<select name="actor_id" id="actor_id" class="custom-select">
							<option value="all"><?php echo get_phrase('all_actors'); ?></option>
							<?php $actors = $this->db->get('actor')->result_array(); ?>
							<?php foreach ($actors as $key => $actor): ?>
								<option value="<?php echo $actor['actor_id']; ?>"><?php echo $actor['name']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="col-md-6 col-lg-2">
                    <div class="select" style="width: 100%; margin-bottom: 10px">
                        <select name="genre_id" id="genre_id" class="custom-select">
                            <option value="all"><?php echo get_phrase('all_genres'); ?></option>
                            <?php $genres = $this->db->get('genre')->result_array(); ?>
                            <?php foreach ($genres as $key => $genre): ?>
                                <option value="<?php echo $genre['genre_id']; ?>"><?php echo $genre['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

				<div class="col-md-6 col-lg-2">
					<div class="select" style="width: 100%; margin-bottom: 10px">
						<select name="year" id="year" class="custom-select">
							<option value="all"><?php echo get_phrase('all_years'); ?></option>
							<?php foreach ($years as $key => $year): ?>
								<option value="<?php echo $year['year']; ?>"><?php echo $year['year']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="col-md-6 col-lg-2">
					<div class="select" style="width: 100%; margin-bottom: 10px">
						<?php $countries = $this->crud_model->get_countries(); ?>
						<select name="country" id="country" class="custom-select">
							<option value="all"><?php echo get_phrase('all_countries'); ?></option>
							<?php foreach ($countries as $key => $country): ?>
								<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="col-md-6 col-lg-2">
					<button type="submit" style="width: 100%; margin-bottom: 10px; margin-top: 2px; height: 38px;" class="btn btn-danger" onclick="submit()"><?php echo get_phrase('filter'); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- MOVIE LIST, GENRE WISE LISTING -->
<div class="row" style="margin:20px 60px;">
	<h4 style="text-transform: capitalize;">
		<?php echo $this->db->get_where('genre', array('genre_id' => $genre_id))->row()->name;?>
		<?php echo get_phrase('movies'); ?>
		(<?php echo $total_result;?>)
	</h4>
	<div class="content">
		<div class="movie-grid">
			<?php
			foreach ($movies as $row)
			{
				$title	=	$row['title'];
				$link	=	base_url().'index.php?browse/playmovie/'.$row['movie_id'];
				$thumb	=	$this->crud_model->get_thumb_url('movie' , $row['movie_id']);
			?>
			<div class="thumb-container">
				<a href="<?php echo $link; ?>">
					<img src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>" class="thumb-img">
				</a>
				<div class="thumb-title"><?php echo $title; ?></div>
			</div>
			<?php } ?>
		</div>
	</div>
	<div style="clear: both;"></div>
	<ul class="pagination">
		<?php echo $this->pagination->create_links(); ?>
	</ul>
</div>
<hr style="border-top:1px solid #333; margin-top: 50px;">
<div class="container">
	<?php include 'footer.php';?>
</div>

<script>
    function submit()
    {
        actor_id  = document.getElementById("actor_id").value;
       // director_id  = document.getElementById("director_id").value;
        genre_id  = document.getElementById("genre_id").value;
        year  = document.getElementById("year").value;
        country  = document.getElementById("country").value;
        window.location = "<?php echo base_url();?>index.php?browse/filter_movie/"+genre_id+ "/" + actor_id+ "/" + year + "/" + country;
    }
</script>

<style>
/* CSS Grid for Movie Thumbs */
.movie-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 15px;
    padding: 10px;
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
