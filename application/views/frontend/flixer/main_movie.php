<div class="col-lg-12" id="movie_div">

<!-- Video player generator starts -->
<?php if (get_video_extension($row['url']) == 'mp4' || get_video_extension($row['url']) == 'webm' || get_video_extension($row['url']) == 'avi' || get_video_extension($row['url']) == 'mov'): ?>
    <!-- Video from local storage -->
    <link rel="stylesheet" href="<?php echo base_url();?>assets/global/plyr/plyr.css">
    <video class="movie_player" poster="<?php echo $this->crud_model->get_thumb_url('movie', $row['movie_id']);?>" id="player" playsinline controls>
        <?php if (get_video_extension($row['url']) == 'mp4'): ?>
            <source src="<?php echo base_url('assets/global/movie_video/' . $row['url']); ?>" type="video/mp4">
        <?php elseif (get_video_extension($row['url']) == 'webm'): ?>
            <source src="<?php echo base_url('assets/global/movie_video/' . $row['url']); ?>" type="video/webm">
        <?php elseif (get_video_extension($row['url']) == 'avi'): ?>
            <source src="<?php echo base_url('assets/global/movie_video/' . $row['url']); ?>" type="video/avi">
        <?php elseif (get_video_extension($row['url']) == 'mov'): ?>
            <source src="<?php echo base_url('assets/global/movie_video/' . $row['url']); ?>" type="video/quicktime">
        <?php else: ?>
            <h4><?php get_phrase('video_url_is_not_supported'); ?></h4>
        <?php endif; ?>

        <?php
            // إضافة الترجمة إذا كانت موجودة
            $captions = $this->db->get_where('subtitle', array('movie_id' => $movie_id))->result_array();
            foreach($captions as $caption):
        ?>
            <!-- Video subtitle -->
            <track kind="captions" label="<?php echo $caption['language']; ?>" src="<?php echo base_url('assets/global/movie_caption/'.$caption['file']); ?>" default>
        <?php endforeach; ?>
    </video>
    <script src="<?php echo base_url();?>assets/global/plyr/plyr.js"></script>
    <script>const player = new Plyr('#player');</script>

<?php endif; ?>
<!-- Video player generator ends -->

</div>
<style>
    .hidebtn {
        width: 110px;
        height: 70px;
        background: #00000000;
        position: absolute;
        right: 13px;
        top: 8px;
    }
</style>
