<h2>User Messages</h2>
<?php foreach($messages as $message): ?>
    <p><strong><?php echo $message['sender_id']; ?></strong>: <?php echo $message['message']; ?></p>
<?php endforeach; ?>

<form method="post" action="<?php echo site_url('index.php/addons/messaging/send_message'); ?>">
    <input type="hidden" name="sender_id" value="<?php echo $this->session->userdata('user_id'); ?>" />
    <input type="hidden" name="receiver_id" value="1" />
    <textarea name="message" placeholder="Your message"></textarea>
    <button type="submit">Send</button>
</form>
