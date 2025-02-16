<?php include 'header_browse.php'; ?>

<!-- تنسيق CSS الخاص بالتصميم -->
<link rel="stylesheet" type="text/css" href="assets/css/slick.min.css"/>
<link rel="stylesheet" type="text/css" href="assets/css/slick-theme.min.css"/>

<style>
/* تحسين عرض الشبكة */
.grid-container {
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

<?php
$search_key = str_replace('%20', ' ', $search_key);
$movies = $this->crud_model->get_search_result('movie', $search_key);
$series = $this->crud_model->get_search_result('series', $search_key);
?>

<!-- عرض نتائج البحث -->
<div class="row" style="margin:20px 60px;">
    <h4 style="text-transform: capitalize; color: #e50914; font-weight: bold;">
        <?php echo get_phrase(' نتائج البحث عن'); ?> :[ <?php echo htmlspecialchars($search_key, ENT_QUOTES, 'UTF-8'); ?> ] 
    </h4>

    <div class="grid-container">
        <div class="grid" id="search-results-slider">
            <?php 
            foreach ($movies as $row) {
                $title = $row['title'];
                $link  = base_url() . 'index.php?browse/playmovie/' . $row['movie_id'];
                $thumb = $this->crud_model->get_thumb_url('movie', $row['movie_id']);
                ?>
                <div class="thumb-container">
                    <a href="<?php echo $link; ?>">
                        <img src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>" class="thumb-img" loading="lazy">
                    </a>
                    <div class="thumb-title"><?php echo $title; ?></div>
                </div>
                <?php
            }

            foreach ($series as $row) {
                $title = $row['title'];
                $link  = base_url() . 'index.php?browse/playseries/' . $row['series_id'];
                $thumb = $this->crud_model->get_thumb_url('series', $row['series_id']);
                ?>
                <div class="thumb-container">
                    <a href="<?php echo $link; ?>">
                        <img src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>" class="thumb-img" loading="lazy">
                    </a>
                    <div class="thumb-title"><?php echo $title; ?></div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<!-- إضافة الترقيم (Pagination) -->
<div style="clear: both;"></div>
<ul class="pagination">
    <?php echo $this->pagination->create_links(); ?>
</ul>

<!-- تضمين مكتبة jQuery و Slick.js -->
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/slick.min.js"></script>

<script>
$(document).ready(function(){
    $('#search-results-slider').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        arrows: false,
        dots: false,
        infinite: true,
        pauseOnHover: false,
        responsive: [
            { breakpoint: 1024, settings: { slidesToShow: 3 } },
            { breakpoint: 768, settings: { slidesToShow: 2 } },
            { breakpoint: 480, settings: { slidesToShow: 1 } }
        ]
    });
});
</script>

<!-- إضافة الفوتر -->
<div class="container" style="margin-top: 90px;">
    <hr style="border-top:1px solid #333;">
    <?php include 'footer.php'; ?>
</div>
