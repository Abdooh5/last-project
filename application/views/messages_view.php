<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>الرسائل</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h3>الرسائل</h3>
    <?php if($this->session->flashdata('flash_message')): ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('flash_message'); ?></div>
    <?php endif; ?>
    <?php if($this->session->flashdata('error_message')): ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>المرسل</th>
                <th>المستلم</th>
                <th>الرسالة</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?php echo get_username_by_id($message['sender_id']); ?></td>
                        <td><?php echo get_username_by_id($message['receiver_id']); ?></td>
                        <td><?php echo $message['message']; ?></td>
                        <td><?php echo $message['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">لا توجد رسائل.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($user_id == 1): // إذا كان المستخدم مديرًا ?>
        <a href="<?php echo site_url('messages/compose/'.$user_id); ?>" class="btn btn-primary">إنشاء رسالة جديدة</a>
    <?php endif; ?>
</div>
</body>
</html>
