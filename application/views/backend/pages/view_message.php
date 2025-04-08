<h2>عرض المحادثة</h2>
<div style="background:#eef; padding:10px; margin-bottom:20px">
    <p><strong>من:</strong> <?= $original->sender_id ?></p>
    <p><strong>الموضوع:</strong> <?= $original->subject ?></p>
    <p><?= nl2br($original->message) ?></p>
    <small><?= date('Y-m-d H:i:s', $original->timestamp) ?></small>
</div>

<?php foreach ($replies as $reply): ?>
    <div style="border-left:4px solid #ccc; padding:10px; margin-bottom:10px">
        <p><strong><?= $reply->sender_id ?>:</strong> <?= nl2br($reply->message) ?></p>
        <small><?= date('Y-m-d H:i:s', $reply->timestamp) ?></small>
    </div>
<?php endforeach; ?>

<hr>
<h4>رد جديد</h4>
<form action="<?= site_url('index.php?general/send_reply') ?>" method="post">
    <input type="hidden" name="message_id" value="<?= $original->id ?>">
    <input type="hidden" name="recipient_id" value="<?= $original->sender_id ?>">
    <textarea name="reply" rows="4" class="form-control" required></textarea>
    <br>
    <button type="submit" class="btn btn-primary">إرسال</button>
</form>
<br>
<a href="<?= site_url('index.php?general/admin_messages') ?>" class="btn btn-secondary">العودة للبريد الوارد</a>
