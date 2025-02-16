<?php include 'header_browse.php';?>

<!-- تضمين مكتبة Slick المحلية -->
<link rel="stylesheet" type="text/css" href="assets/css/slick.min.css"/>
<link rel="stylesheet" type="text/css" href="assets/css/slick-theme.min.css"/>
<style>
/* تحسين عرض الشبكة */
.slider {
    width: 90%;
    margin: auto;
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
						<select name="director_id" id="director_id" class="custom-select">
							<option value="all"><?php echo get_phrase('all_directors'); ?></option>
							<?php $directors = $this->db->get('director')->result_array(); ?>
							<?php foreach ($directors as $key => $director): ?>
								<option value="<?php echo $director['director_id']; ?>"><?php echo $director['name']; ?></option>
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
					<button type="submit" style="width: 100%; margin-bottom: 10px; margin-top: 2px; height: 38px;" class="btn btn-danger" onclick="submit('<?php echo $genre_id; ?>')"><?php echo get_phrase('filter'); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- TV SERIAL LIST, GENRE WISE LISTING -->
<div class="row" style="margin:20px 60px;">
	<h4 style="text-transform: capitalize;">
		<?php echo $this->db->get_where('genre', array('genre_id' => $genre_id))->row()->name;?>
			<?php echo get_phrase('Tv_series');?> (<?php echo $total_result;?>)
	</h4>
    <div class="content">
        <div class="slider">
            <?php foreach ($series as $row): ?>
                <?php
                    $title = $row['title'];
                    $link  = base_url() . 'index.php?browse/playseries/' . $row['series_id'];
                    $thumb = $this->crud_model->get_thumb_url('series', $row['series_id']);
                ?>
                <div class="thumb-container">
                    <a href="<?php echo $link; ?>">
                        <img src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>" class="thumb-img">
                    </a>
                    <div class="thumb-title"><?php echo $title; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>







	<!-- تضمين مكتبة jQuery و Slick.js محليًا -->
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/slick.min.js"></script>
<script>
$(document).ready(function(){
    $('.slider').slick({
        slidesToShow: 5,  // عدد العناصر الظاهرة
        slidesToScroll: 1,
        autoplay: true,  // التمرير التلقائي
        autoplaySpeed: 3000, // كل 3 ثواني
        arrows: false,  // إخفاء الأسهم
        dots: false,  // إخفاء النقاط
        infinite: true, // التكرار (التكرار الدوري للشرائح)
        pauseOnHover: false, // إيقاف التمرير التلقائي عند مرور الماوس
        responsive: [
            { breakpoint: 1024, settings: { slidesToShow: 3 } },  // لو كان العرض أقل من 1024px
            { breakpoint: 768, settings: { slidesToShow: 2 } },   // لو كان العرض أقل من 768px
            { breakpoint: 480, settings: { slidesToShow: 1 } }    // لو كان العرض أقل من 480px
        ]
    });
});

</script>   



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
    function submit(genre_id)
    {
        actor_id  = document.getElementById("actor_id").value;
        director_id  = document.getElementById("director_id").value;
        year  = document.getElementById("year").value;
        country  = document.getElementById("country").value;
        window.location = "<?php echo base_url();?>index.php?browse/filter/series/"+genre_id+ "/" + actor_id+ "/" + director_id+ "/" + year + "/" + country;
    }
</script>
