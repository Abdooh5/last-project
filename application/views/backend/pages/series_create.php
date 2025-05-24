<div class="row">
    <!-- نموذج الإدخال -->
    <div class="col-md-8">
        <div class="panel panel-primary">
        	<div class="panel-heading">
        		<div class="panel-title">Create Series</div>
        	</div>
            <div class="panel-body">
				<form method="post" action="<?php echo base_url();?>index.php?admin/series_create" enctype="multipart/form-data">
	                <div class="form-group mb-3">
	                    <label for="title">Tv Series Title</label>
	                    <input type="text" class="form-control" id="title" name="title">
	                </div>
	                <div class="form-group mb-3">
	                    <label for="series_trailer_url">Tv Series Trailer URL</label>
	                    <input type="file" class="form-control" id="series_trailer_url" name="series_trailer_url">
	                </div>

	                <div class="form-group mb-3">
	                    <label for="thumb">Thumbnail</label>
						<span class="help">- icon image of the series</span>
	                    <input type="file" class="form-control" name="thumb">
	                </div>

	                <div class="form-group mb-3">
	                    <label for="poster">Poster</label>
						<span class="help">- large banner image of the series</span>
	                    <input type="file" class="form-control" name="poster">
	                </div>

					<!-- <div class="form-group mb-3">
						<label for="description_short">Short description</label>
						<textarea class="form-control" id="description_short" name="description_short" rows="6"></textarea>
					</div> -->

					<div class="form-group mb-3">
						<label for="description_long">Long description</label>
						<textarea class="form-control" id="description_long" name="description_long" rows="6"></textarea>
					</div>

					<div class="form-group mb-3">
						<label for="category">Category</label>
						<span class="help">- select single category</span>
						<select class="form-control select2" id="category" name="category" required>
							<option value="">Select an category</option>
							<?php
								$categories = $this->db->get('category')->result_array();
								foreach ($categories as $row3): ?>
							<option value="<?php echo $row3['category_id']; ?>">
								<?php echo $row3['name']; ?>
							</option>
							<?php endforeach; ?>
						</select>
					</div>
					
					<div class="form-group mb-3">
						<label for="actors">Actors</label>
						<span class="help">- select multiple actors</span>
						<select class="form-control select2" id="actors" multiple name="actors[]">
							<?php
								$actors = $this->db->get('actor')->result_array();
								foreach ($actors as $row2): ?>
							<option value="<?php echo $row2['actor_id']; ?>">
								<?php echo $row2['name']; ?>
							</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="form-group mb-3">
						<label for="country_id">Country</label>
						<select class="form-control select2" id="country_id" name="country_id[]" required multiple>
							<?php
								$countries = $this->crud_model->get_countries();
								foreach ($countries as $country): ?>
								<option value="<?php echo $country['country_id']; ?>">
									<?php echo $country['name']; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="form-group mb-3">
						<label for="genre_id">Genre</label>
						<span class="help">- genre must be selected</span>
						<select class="form-control select2" id="genre_id" name="genre_id[]" multiple>
							<?php
								$genres = $this->crud_model->get_genres();
								foreach ($genres as $row2): ?>
							<option value="<?php echo $row2['genre_id']; ?>">
								<?php echo $row2['name']; ?>
							</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="form-group mb-3">
						<label for="year">Publishing Year</label>
						<span class="help">- year of publishing time</span>
						<select class="form-control" id="year" name="year">
							<?php for ($i = date("Y"); $i > 1900 ; $i--): ?>
							<option value="<?php echo $i; ?>">
								<?php echo $i; ?>
							</option>
							<?php endfor; ?>
						</select>
					</div>

					<div class="form-group mb-3">
						<label for="rating">Rating</label>
						<span class="help">- star rating of the movie</span>
						<select class="form-control" id="rating" name="rating">
							<?php for ($i = 0; $i <= 5 ; $i++): ?>
							<option value="<?php echo $i; ?>">
								<?php echo $i; ?>
							</option>
							<?php endfor; ?>
						</select>
					</div>

					<div class="row mt-3">
			        	<div class="col-md-6 text-center">
							<input type="submit" class="btn btn-success w-100" value="Create Series">
			        	</div>
			        	<div class="col-md-6 text-center">
			        		<a href="<?php echo base_url();?>index.php?admin/series_list" class="btn btn-black w-100">Go back</a>
			        	</div>
			        </div>
				</form>
            </div>
        </div>
    </div>

    <!-- عمود صورة المسلسل -->
    <div class="col-md-4">
        <div id="video_player_div" class="text-center">
            <img src="" alt="Poster will appear here" class="img-fluid" style="max-height:500px; display:none;">
        </div>
    </div>
</div>
<script>
	const categoryMap = <?php
		$catMap = [];
		foreach ($categories as $cat) {
			$catMap[$cat['name']] = $cat['category_id'];
		}
		echo json_encode($catMap, JSON_UNESCAPED_UNICODE);
	?>;
</script>

<!-- JavaScript لملء الحقول تلقائيًا -->
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

    $('#title').on('blur', function () {
        let title = $(this).val();

        if (title.length > 0) {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php?admin/fetch_tmdb_series_data',
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
						//console.log(data.anime_categories);
						if (data.anime_categories && data.anime_categories.length > 0) {
    const animeCategoryName = data.anime_categories; // مثل "أنمي مستمر"
    const categoryId = categoryMap[animeCategoryName];

    if (categoryId) {
        $('#category').val(categoryId).trigger('change');
    } else {
        console.warn('التصنيف غير موجود في القائمة:', animeCategoryName);
    }
}

                        $('#description_long').val(data.overview);

                        if (data.release_date) {
                            let year = data.release_date.split('-')[0];
                            $('[name="year"]').val(year);
                        }

                        if (data.vote_average) {
                            let rating = Math.round(data.vote_average / 2);
                            $('[name="rating"]').val(rating);
                        }

                        if (Array.isArray(data.genres)) {
                            data.genres.forEach(function (genre) {
                                selectMatchingOption('#genre_id', genre);
                            });
                        }

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
                    console.log(data.countries[0]); // طباعة القيمة للتأكد
                    if (Array.isArray(data.countries)) {
                            data.countries.forEach(function (country) {
                                selectMatchingOption('#country_id', country);
                            });
                        }

                        if (data.poster_path) {
                            let posterURL = 'https://image.tmdb.org/t/p/w500' + data.poster_path;
                            $('#video_player_div img').attr('src', posterURL).show();

                            if ($('[name="poster_url"]').length === 0) {
                                $('<input type="hidden" name="poster_url" value="' + posterURL + '">').appendTo('form');
                            } else {
                                $('[name="poster_url"]').val(posterURL);
                            }
                        }
					// 	 if ($('#title').next('.alert').length === 0) {
                    //     $('#title').after('<div class="alert alert-info mt-2">🔄 يتم تجهيز الصفحة، يرجى الانتظار لحظة...</div>');
                    // }

                    // setTimeout(function () {
                    //     location.reload();
                    // }, 2000);


                    } catch (e) {
                        console.error("⚠️ خطأ في التحليل:", e, response);
                        alert('حدث خطأ أثناء معالجة بيانات المسلسل.');
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
