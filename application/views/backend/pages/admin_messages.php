<h1>Welcome Admin - Conversations</h1><?php if (!empty($messages)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Last Message</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $message): ?>
                <tr>
                    <td>
                        <?php
                            // جلب اسم المستخدم (اختياري إذا عندك جدول users)
                            // مثلاً: $user = $this->db->get_where('users', ['id' => $message->sender_id])->row();
                            echo 'User #' . $message->sender_id;
                        ?>
                    </td>
                    <td><?php echo substr($message->message, 0, 50); ?>...</td>
                    <td><?php echo date('Y-m-d H:i:s', $message->timestamp); ?></td>
                    <td>
                        <a href="<?php echo site_url('index.php?general/view_conversation/' . $message->sender_id); ?>" class="btn btn-info">View Chat</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No conversations found.</p>
<?php endif; ?>
