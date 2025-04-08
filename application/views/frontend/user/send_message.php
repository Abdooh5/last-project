<div class="container" style="margin-top: 30px;">
    <h4>إرسال رسالة إلى الإدارة</h4>

    <?php if ($this->session->flashdata('flash_message')): ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('flash_message'); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo site_url('addons/messaging/send_message'); ?>">
        <div class="form-group">
            <label>الموضوع</label>
            <input type="text" name="subject" class="form-control" required>
        </div>

        <div class="form-group">
            <label>الرسالة</label>
            <textarea name="message" class="form-control" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">إرسال</button>
    </form>
</div>
