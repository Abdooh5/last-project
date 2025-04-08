<h2>Admin Messaging</h2>
<?php foreach($messages as $message): ?>
    <p><strong><?php echo $message['sender_id']; ?></strong>: <?php echo $message['message']; ?></p>
<?php endforeach; ?>

<form method="post" action="<?php echo site_url('index.php/addons/messaging/send_message'); ?>">
    <input type="hidden" name="sender_id" value="1" />
    <input type="number" name="receiver_id" placeholder="Receiver ID" required />
    <textarea name="message" placeholder="Your message"></textarea>
    <button type="submit">Send</button>
</form>
