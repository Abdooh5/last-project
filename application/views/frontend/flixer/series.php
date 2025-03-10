<?php include 'header_browse.php';?>

<!-- تضمين مكتبة CSS الخاصة بالشبكة -->
<style>
    /* تحسين عرض الشبكة */
    .series-grid {
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
                <!-- <div class="col-md-6 col-lg-2">
                    <div class="select" style="width: 100%; margin-bottom: 10px">
                        <select name="director_id" id="director_id" class="custom-select">
                            <option value="all"><?php echo get_phrase('all_directors'); ?></option>
                            <?php $directors = $this->db->get('director')->result_array(); ?>
                            <?php foreach ($directors as $key => $director): ?>
                                <option value="<?php echo $director['director_id']; ?>"><?php echo $director['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div> -->
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
                    <button type="submit" style="width: 100%; margin-bottom: 10px; margin-top: 2px; height: 38px;" class="btn btn-danger" onclick="submit('<?php echo $director_id; ?>')"><?php echo get_phrase('filter'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TV SERIES LISTING IN GRID FORMAT -->
<div class="row" style="margin:20px 60px;">
    <h4 style="text-transform: capitalize;">
        <?php echo $this->db->get_where('director', array('director_id' => $director_id))->row()->name;?>
        <?php echo $this->db->get_where('country', array('country_id' => $country_id))->row()->name;?> (<?php echo $total_result;?>)
        
    </h4>
    <div class="content">
        <div class="series-grid">
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
    function submit(director_id)
    {
        actor_id  = document.getElementById("actor_id").value;
       // director_id  = document.getElementById("director_id").value;
        genre_id  = document.getElementById("genre_id").value;
        year  = document.getElementById("year").value;
        country  = document.getElementById("country").value;
        window.location = "<?php echo base_url();?>index.php?browse/filter/series/"+genre_id+ "/" + actor_id+ "/" + director_id+ "/" + year + "/" + country;
    }
</script>
