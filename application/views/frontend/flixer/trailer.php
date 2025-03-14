<div class="col-lg-12" id="trailer_div" style="display: none;">

    <!-- تحقق من وجود الرابط للفيديو الترويجي -->
    <?php if (!empty($row['trailer_url'])): ?>

        <!-- مشغل الفيديو -->
        <video poster="<?php echo $this->crud_model->get_thumb_url('movie', $row['movie_id']); ?>" id="trailer_url" playsinline controls>
            <?php 
            $video_extension = get_video_extension($row['trailer_url']);
            
            // تحقق من نوع الفيديو
            if ($video_extension == 'mp4'): ?>
                <source src="<?php echo base_url('assets/global/movie_video/'.$row['url']); ?>" type="video/mp4">
            <?php elseif ($video_extension == 'webm'): ?>
                <source src="<?php echo base_url('assets/global/movie_video/'.$row['url']); ?>" type="video/webm">
            <?php else: ?>
                <h4><?php echo get_phrase('Video_format_is_not_supported'); ?></h4>
            <?php endif; ?>
        </video>

        <!-- تحميل مكتبة Plyr لتشغيل الفيديو -->
        <script src="<?php echo base_url();?>assets/global/plyr/plyr.js"></script>
        <script>
            const trailer_url = new Plyr('#trailer_url');
        </script>

    <?php else: ?>
        <h4><?php echo get_phrase('No_trailer_available'); ?></h4>
    <?php endif; ?>

</div>
