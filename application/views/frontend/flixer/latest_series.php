<?php include 'header_browse.php'; ?>
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


<!-- عرض أحدث المسلسلات مع سلايدر متحرك -->
<div class="row" style="margin:20px 60px;">
    <h4 style="text-transform: capitalize; color: #e50914; font-weight: bold;">
        <?php echo get_phrase('آخر المسلسلات المضافة'); ?> (<?php echo count($series); ?>)
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
<?php  $movies = $this->db->order_by('movie_id', 'DESC')->limit(20)->get('movie')->result_array();  ?>
<!-- عرض أحدث الأفلام مع سلايدر متحرك -->
<div class="row" style="margin:20px 60px;">
<h4 style="text-transform: capitalize; color: #e50914; font-weight: bold;">
            <?php echo get_phrase('آخر الأفلام المضافة'); ?> (<?php echo count($movies); ?>)
        </h4>
    <div class="content">
        <div class="slider">
            <?php foreach ($movies as $row): ?>
                <?php
                    $title = $row['title'];
                    $link  = base_url() . 'index.php?browse/playmovie/' . $row['movie_id'];  // تغيير الرابط للمسار الخاص بالفيلم
                    $thumb = $this->crud_model->get_thumb_url('movie', $row['movie_id']);  // تغيير استدعاء الصورة لتكون خاصة بالأفلام
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
</div>