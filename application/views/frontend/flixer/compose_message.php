<body style="margin: 0; padding: 0; height: 100vh; display: flex; flex-direction: column; font-family: sans-serif;">

<!-- الهيدر -->
<div style="background-color: #075e54; color: white; padding: 15px; text-align: center; display: flex; align-items: center; justify-content: space-between;">
    <a href="<?php echo site_url('index.php?browse/home'); ?>" style="color: white; font-size: 1.2em; text-decoration: none;">🔙</a>
    <h1 style="margin: 0; font-size: 1.2em;"><?php echo $page_title; ?></h1>
    <span style="width: 30px;"></span>
</div>

<!-- صندوق المحادثة -->
<div id="chat-box" style="
    flex-grow: 1;
    overflow-y: auto;
    padding: 15px;
    background-color: #e5ddd5;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    min-height: 0;
">
    <?php if (empty($messages)): ?>
        <div style="margin: auto; text-align: center; color: #777;">لا توجد رسائل بعد.</div>
    <?php else: ?>
        <?php foreach ($messages as $msg): ?>
            <?php
                $is_sender = $msg->sender_id == $this->session->userdata('user_id');
                $align = $is_sender ? 'right' : 'left';
                $bg = $is_sender ? '#dcf8c6' : '#fff';
                $border_radius = $is_sender ? '20px 0 20px 20px' : '0 20px 20px 20px';
            ?>
            <div style="text-align: <?php echo $align; ?>; margin: 8px 0;">
                <div style="display: inline-block; background-color: <?php echo $bg; ?>; padding: 10px 15px; border-radius: <?php echo $border_radius; ?>; max-width: 75%; box-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                    <div style="white-space: pre-wrap;"><?php echo nl2br(htmlspecialchars($msg->message)); ?></div>
                    <div style="font-size: 0.75em; color: #555; text-align: <?php echo $align; ?>; margin-top: 5px;"><?php echo date('H:i', $msg->timestamp); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- نموذج الإرسال -->
<form action="<?php echo site_url('index.php?general/send'); ?>" method="post" style="padding: 10px; background-color: #fff; border-top: 1px solid #ccc;">
    <input type="hidden" name="receiver_id" value="<?php echo $admin_id; ?>">
    <div style="display: flex; gap: 10px; align-items: center;">
        <textarea name="message" rows="1" placeholder="اكتب رسالتك..." required style="flex: 1; resize: none; padding: 10px 15px; border-radius: 25px; border: 1px solid #ccc; font-size: 1em;"></textarea>
        <button type="submit" class="btn btn-success" style="border-radius: 25px; padding: 8px 20px;">إرسال</button>
    </div>
</form>

<script>
    window.onload = function () {
        var chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    };
</script>

</body>
