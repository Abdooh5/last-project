<?php
	$movie_detail = $this->db->get_where('movie',array('movie_id'=>$movie_id))->row();
	
// استرجاع اسم الفيديو من قاعدة البيانات
$movie = $this->db->get_where('movie', array('movie_id' => $movie_id))->row();

// هنا نستخدم اسم الملف الذي تم تخزينه في قاعدة البيانات
$video_name = $movie->url;
$trailer_name=$movie->trailer_url;
$video_path = base_url('assets/global/movie_video/' . $video_name);
$trailer_path=base_url('assets/global/movie_trailer/' . $trailer_name);
?>


<div class="row">
    <div class="col-md-6">
    	<form method="post" action="<?php echo base_url();?>index.php?admin/movie_edit/<?php echo $movie_id;?>" enctype="multipart/form-data">
	        <div class="panel panel-primary">
	        	<div class="panel-heading">
	        		<div class="panel-title">
	        			Edit Movie
	        		</div>
	        	</div>
	            <div class="panel-body">
					<div class="form-group mb-3">
	                    <label for="simpleinput1">Movie Title</label>
	                    <input type="text" class="form-control" id = "simpleinput1" name="title" value="<?php echo $movie_detail->title;?>">
	                </div>
					<div class="form-group mb-3">
	                    <label for="url">Movie Trailer Url</label>
						<span class="help">- youtube or any hosted video</span>
	                    <input type="file" class="form-control" name="trailer_url" id="trailer_url" value="<?php echo $movie_detail->trailer_url;?>">
	                </div>
					<div class="form-group mb-3">
	                    <label for="url">Video Url</label>
						<span class="help">- youtube or any hosted video</span>
	                    <input type="file" class="form-control" name="url" id="url" value="<?php echo $movie_detail->url;?>">
	                </div>

					<div class="form-group mb-3">
	                    <label for="">Thumbnail</label>
						<span class="help">- icon image of the movie</span>
	                    <input type="file" class="form-control" name="thumb">
	                </div>

					<div class="form-group mb-3">
	                    <label for="">Poster</label>
						<span class="help">- large banner image of the movie</span>
	                    <input type="file" class="form-control" name="poster">
	                </div>

	                <div class="form-group mb-3">
                        <label for="duration">Duration</label>
                        <div class="input-group">

                        	<!--Convert secoend -> H, M, S  -->
                        	<?php
                        		$seconds = $movie_detail->duration % 60;
                        		$minutes = (($movie_detail->duration - $seconds)/60)%60;
                        		$hours	 = intval(($movie_detail->duration / 60) / 60);
                        	?>
                        	
								<input type="text" name="duration" class="form-control timepicker" data-template="dropdown" value="<?php echo $hours . ':' . $minutes . ':' . $seconds; ?>" data-show-seconds="true" data-show-meridian="false" data-minute-step="1" data-second-step="1" placeholder="Hour : Minutes : Seconds" />
								
								<div class="input-group-addon">
									<a href="#"><i class="entypo-clock"></i></a>
								</div>
                        </div>
                    </div>

					<div class="form-group mb-3">
						<label for="description_long">Long description</label>
						<textarea class="form-control" id="description_long" name="description_long" rows="6"><?php echo $movie_detail->description_long;?></textarea>
					</div>
<!-- 
					<div class="form-group mb-3">
						<label for="description_short">Short description</label>
						<textarea class="form-control" id="description_short" name="description_short" rows="6"><?php echo $movie_detail->description_short;?></textarea>
					</div> -->


					<div class="form-group mb-3">
						<label for="actors">Actors</label>
						<span class="help">- select multiple actors</span>
						<select class="form-control select2" id="actors" multiple name="actors[]">
							<?php
								$actors	=	$this->db->get('actor')->result_array();
								foreach ($actors as $row2):?>
							<option
								<?php
									$actors	=	$movie_detail->actors;
									if ($actors != '')
									{
										$actor_array	=	json_decode($actors);
										if (in_array($row2['actor_id'], $actor_array))
											echo 'selected';
									}
									?>
								value="<?php echo $row2['actor_id'];?>">
								<?php echo $row2['name'];?>
							</option>
							<?php endforeach;?>
						</select>
					</div>

					<div class="form-group mb-3">
						<label for="country_id">Country</label>
						<select class="form-control select2" id="country_id" name="country_id" required>
							<?php
								$countries	=	$this->crud_model->get_countries();
								foreach ($countries as $country):?>
								<option value="<?php echo $country['country_id'];?>" <?php if($country['country_id'] == $movie_detail->country_id) echo 'selected'; ?>>
									<?php echo $country['name'];?>
								</option>
							<?php endforeach;?>
						</select>
					</div>

                 <div class="form-group mb-3">
						<label for="genre_id">Genre</label>
						<span class="help">- genre must be selected</span>
						<select class="form-control select2" id="genre_id" name="genre_id[]" multiple >
							<?php
								$genres	=	$this->crud_model->get_genres();
								    $selected_genres = json_decode($movie_detail->genre_id);
    if (!is_array($selected_genres)) $selected_genres = []; // fallback
								foreach ($genres as $row2):?>

<option
    <?php if (in_array($row2['genre_id'], $selected_genres)) echo 'selected';?>
    value="<?php echo $row2['genre_id'];?>">
    <?php echo $row2['name'];?>
</option>
							<?php endforeach;?>
						</select>
					</div>

					<div class="form-group mb-3">
						<label for="year">Publishing Year</label>
						<span class="help">- year of publishing time</span>
						<select class="form-control select2" id="year" name="year">
							<?php for ($i = date("Y"); $i > 2000 ; $i--):?>
							<option
								<?php if ( $movie_detail->year == $i) echo 'selected';?>
								value="<?php echo $i;?>">
								<?php echo $i;?>
							</option>
							<?php endfor;?>
						</select>
					</div>

					<div class="form-group mb-3">
						<label for="rating">Rating</label>
						<span class="help">- star rating of the movie</span>
						<select class="form-control" id="rating" name="rating">
							<?php for ($i = 0; $i <= 5 ; $i++):?>
							<option
								<?php if ( $movie_detail->rating == $i) echo 'selected';?>
								value="<?php echo $i;?>">
								<?php echo $i;?>
							</option>
							<?php endfor;?>
						</select>
					</div>

					<div class="form-group mb-3">
						<label for="featured">Featured</label>
						<span class="help">- featured movie will be shown in home page</span>
						<select class="form-control" id="featured" name="featured">
							<option value="0" <?php if ( $movie_detail->featured == 0) echo 'selected';?>>No</option>
							<option value="1" <?php if ( $movie_detail->featured == 1) echo 'selected';?>>Yes</option>
						</select>
					</div>
					<div class="row mt-3">
			        	<div class="col-md-6 text-center">
							<input type="submit" class="btn btn-success w-100" value="Update Movie">
			        	</div>
			        	<div class="col-md-6 text-center">
			        		<a href="<?php echo base_url();?>index.php?admin/movie_list" class="btn btn-black w-100">Go back</a>
			        	</div>
			        </div>
	            </div>
	        </div>
	    </form>
	</div>
	<div class="col-md-6">
		<div class="panel">
			<div class="panel-body">
				<div class="form-group">
					<label class="form-label">Preview:</label>
					<div class="video-container">
    <iframe src="<?php echo $video_path; ?>" width="600" height="360" frameborder="0" allowfullscreen></iframe>
</div>

				</div>
			</div>
		</div>
		<div class="col-12">
			<div class="panel">
				<div class="panel-body">
					<div class="form-group">
						<label class="form-label">Trailer:</label>

						<div class="video-container">
    <iframe src="<?php echo $trailer_path; ?>" width="580" height="360" frameborder="0" ></iframe>
</div>
						<!-- Video player generator ends -->

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function () {
	

	function selectMatchingOption(selector, valueFromApi) {
		let found = false;
		let cleanApiValue = valueFromApi.trim().toLowerCase();

		$(selector + ' option').each(function () {
			let optionText = $(this).text().trim().toLowerCase();
			if (optionText.includes(cleanApiValue) || cleanApiValue.includes(optionText)) {
				$(this).prop('selected', true);
				found = true;
				return false;
			}
		});

		if (found) {
			$(selector).trigger('change');
		} else {
			console.log(`❌ لم يتم العثور على تطابق لـ: ${valueFromApi} داخل ${selector}`);
		}
	}

	$('#simpleinput1').on('blur', function () {
		let title = $(this).val();

		if (title.length > 0) {
			$.ajax({
				url: '<?php echo base_url(); ?>index.php?admin/fetch_tmdb_data',
				method: 'POST',
				data: { title: title },
				success: function (response) {
					try {
						let data = JSON.parse(response);
						console.log(data);

						if (data.error) {
							alert(data.error);
							return;
						}
						if (data.runtime && !isNaN(data.runtime)) {
    $('#duration').val(data.runtime + ' دقيقة');
} else {
    $('#duration').val('غير متوفرة');
}

if (data.runtime && !isNaN(data.runtime)) {
    let totalMinutes = parseInt(data.runtime);
    let hours = Math.floor(totalMinutes / 60);
    let minutes = totalMinutes % 60;
    let formatted = 
        String(hours).padStart(2, '0') + ':' +
        String(minutes).padStart(2, '0') + ':00';
    $('#duration').val(formatted);
} else {
    $('#duration').val('00:00:00');
}

						// تعبئة البيانات
						$('#description_long').val(data.overview);
						//$('#description_short').val(data.overview);
						//$('[name="duration"]').val(data.runtime);

						if (data.release_date) {
							let year = data.release_date.split('-')[0];
							$('[name="year"]').val(year);
						}

						if (data.vote_average) {
							let rating = Math.round(data.vote_average / 2);
							$('[name="rating"]').val(rating);
						}

						// النوع
						if (Array.isArray(data.genres)) {
    data.genres.forEach(function (genre) {
        selectMatchingOption('#genre_id', genre);
    });
}

						// الممثلين
						if (Array.isArray(data.actors)) {
							$('#actors option').each(function () {
								let optionText = $(this).text().trim().toLowerCase();
								data.actors.forEach(function (actor) {
									if (optionText.includes(actor.trim().toLowerCase())) {
										$(this).prop('selected', true);
									}
								}.bind(this));
							});
							$('#actors').trigger('change');
						}
// الدول
if (Array.isArray(data.countries)) {
	selectMatchingOption('#country_id', data.countries[0]);
}
						
					// البوستر
					if (data.poster_path) {
    let posterURL = 'https://image.tmdb.org/t/p/w500' + data.poster_path;

    // عرض البوستر
    $('#video_player_div').html(`<img src="${posterURL}" class="img-fluid" style="max-height:500px;">`);

    // إرسال الرابط للسيرفر
    if ($('[name="poster_url"]').length === 0) {
        $('<input type="hidden" name="poster_url" value="' + posterURL + '">').appendTo('form');
    } else {
        $('[name="poster_url"]').val(posterURL);
    }
}

					} catch (e) {
						console.error("⚠️ خطأ في التحليل:", e, response);
						alert('حدث خطأ أثناء معالجة البيانات.');
					}
				},
				error: function () {
					alert('❌ خطأ في الاتصال بالسيرفر.');
				}
			});
		}
	});
});
</script>