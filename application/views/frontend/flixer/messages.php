<h1>Welcome to your Inbox</h1>
<!-- <pre>
<?php print_r($messages); ?>
</pre> -->
<?php

if (!empty($messages)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sender</th>
                <th>Message</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($messages as $message): ?>
    <tr>
        <td><?php $message->sender_id; ?></td>
        <td><?php echo substr($message->message, 0, 50); ?>...</td>
        <td><?php echo date('Y-m-d H:i:s', $message->timestamp); ?></td>
        <td>
            <a href="<?php echo site_url('index.php?general/messages/view/'.$message->id); ?>" class="btn btn-info">View</a>
        </td>
    </tr>
<?php endforeach; ?>


        </tbody>
    </table>
<?php else: ?>
    <p>No messages available.</p>
<?php endif; ?>
