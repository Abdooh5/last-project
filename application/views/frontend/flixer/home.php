<?php include 'header_browse.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/frontend/' . $selected_theme; ?>/hovercss/demo.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/frontend/' . $selected_theme; ?>/hovercss/set1.css" />

<style>
    .btn_opaque {
        font-size: 20px;
        border: 1px solid #939393;
        text-decoration: none;
        margin: 10px;
        background-color: rgba(0, 0, 0, 0.74);
        color: #fff;
    }
    .btn_opaque:hover {
        background-color: rgba(57, 57, 57, 0.74);
    }
    .featured-section {
        background-size: cover;
        background-position: center;
        width: 100%;
        padding: 100px 50px;
        color: white;
        text-align: left;
    }
    .mobile_responsive {
        max-width: 800px;
    }
    .mobile_responsive h1 {
        font-size: 36px;
        font-weight: bold;
    }
    .mobile_responsive h5 {
        font-size: 18px;
        color: #ccc;
        margin-bottom: 20px;
    }
    .series-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
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

<!-- جلب جميع الأفلام المميزة -->
<?php
$featured_movies = $this->db->get_where('movie', array('featured' => 1))->result();
$top_featured_movie = array_shift($featured_movies); // أخذ أول فيلم لعرضه في الأعلى
?>

<!-- عرض الفيلم الأول كفيلم مميز رئيسي -->
<?php if ($top_featured_movie): ?>
<div class="featured-section" style="background-image: url(<?php echo $this->crud_model->get_poster_url('movie', $top_featured_movie->movie_id); ?>);">
    <div class="mobile_responsive">
        <h1><?php echo $top_featured_movie->title; ?></h1>
        <h5><?php echo $top_featured_movie->description_short; ?></h5>
        <a href="<?php echo base_url(); ?>index.php?browse/playmovie/<?php echo $top_featured_movie->movie_id; ?>" 
           class="btn btn-danger btn-lg">
            <b><i class="fa fa-play"></i> <?php echo get_phrase('PLAY'); ?></b>
        </a>
        <span id="mylist_button_holder"></span>
        <span id="mylist_add_button" style="display:none;">
            <a href="#" class="btn btn-lg btn_opaque"
               onclick="process_list('movie', 'add', <?php echo $top_featured_movie->movie_id; ?>)"> 
            <b><i class="fa fa-plus"></i> <?php echo get_phrase('MY_LIST'); ?></b>
            </a>
        </span>
        <span id="mylist_delete_button" style="display:none;">
            <a href="#" class="btn btn-lg btn_opaque"
               onclick="process_list('movie', 'delete', <?php echo $top_featured_movie->movie_id; ?>)"> 
            <b><i class="fa fa-check"></i> <?php echo get_phrase('MY_LIST'); ?></b>
            </a>
        </span>
    </div>
</div>
<?php endif; ?>

<!-- عرض بقية الأفلام المميزة كشبكة -->
<?php if (!empty($featured_movies)): ?>
<div class="row" style="margin: 20px 60px;">
    <h4>الأفلام المميزة</h4>
    <div class="series-grid">
        <?php foreach ($featured_movies as $movie): ?>
            <div class="thumb-container">
                <a href="<?php echo base_url(); ?>index.php?browse/playmovie/<?php echo $movie->movie_id; ?>">
                    <img src="<?php echo $this->crud_model->get_thumb_url('movie', $movie->movie_id); ?>" 
                         alt="<?php echo $movie->title; ?>" class="thumb-img">
                </a>
                <div class="thumb-title"><?php echo $movie->title; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<script>
    function process_list(type, task, id) {
        $.ajax({
            url: "<?php echo base_url();?>index.php?browse/process_list/" + type + "/" + task + "/" + id, 
            success: function(result){
                if (task == 'add') {
                    $("#mylist_button_holder").html($("#mylist_delete_button").html());
                } else if (task == 'delete') {
                    $("#mylist_button_holder").html($("#mylist_add_button").html());
                }
            }
        });
    }

    $(document).ready(function() {
        mylist_exist_status = "<?php echo $this->crud_model->get_mylist_exist_status('movie', $top_featured_movie->movie_id); ?>";
        if (mylist_exist_status == 'true') {
            $("#mylist_button_holder").html($("#mylist_delete_button").html());
        } else {
            $("#mylist_button_holder").html($("#mylist_add_button").html());
        }
    });
</script>

<hr style="border-top:1px solid #333; margin-top: 50px;">
<div class="container">
    <?php include 'footer.php'; ?>
</div>
