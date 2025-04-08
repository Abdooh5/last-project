<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: sans-serif;
            background-color: #e9ddd0;
            direction: rtl;
        }

        .chat-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .chat-header {
            background-color: #005f51;
            color: white;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-header .back-btn {
            color: white;
            background-color: transparent;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }

        .chat-header .title {
            flex: 1;
            text-align: center;
        }

        #chat-box-container {
            flex: 1;
            overflow: hidden;
            direction: ltr; /* عشان شريط التمرير يروح يسار */
        }

        #chat-box {
            height: 100%;
            overflow-y: auto;
            padding: 20px 15px;
            background-color: #e9ddd0;
            direction: rtl; /* محتوى الدردشة يبقى RTL */
        }

        #chat-box::-webkit-scrollbar {
            width: 8px;
        }

        #chat-box::-webkit-scrollbar-thumb {
            background-color: #aaa;
            border-radius: 4px;
        }

        .message-container {
            margin: 10px 0;
            display: flex;
            flex-direction: column;
        }

        .message-bubble {
            padding: 10px 15px;
            max-width: 70%;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            font-size: 14px;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .from-me {
            align-items: flex-end;
        }

        .from-me .message-bubble {
            background-color: #dcf8c6;
            border-radius: 20px 0 20px 20px;
        }

        .from-other {
            align-items: flex-start;
        }

        .from-other .message-bubble {
            background-color: #ffffff;
            border-radius: 0 20px 20px 20px;
        }

        .timestamp {
            font-size: 11px;
            color: #777;
            margin-top: 3px;
        }

        .chat-footer {
            padding: 10px;
            background-color: #fff;
            border-top: 1px solid #ccc;
            position: sticky;
            bottom: 0;
        }

        .chat-footer form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin: 0;
        }

        .chat-footer textarea {
            flex: 1;
            resize: none;
            padding: 10px 15px;
            border-radius: 25px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .chat-footer button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 14px;
        }

        .chat-footer button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="chat-wrapper">
    <div class="chat-header">
        <button class="back-btn" onclick="history.back()">⬅</button>
        <div class="title">
            المستخدم: #<?php echo $user_id; ?><br>
           
        </div>
    </div>

    <div id="chat-box-container">
        <div id="chat-box">
            <?php if (empty($messages)): ?>
                <div style="text-align: center; color: #777;">لا توجد رسائل بعد.</div>
            <?php else: ?>
                <?php
                    usort($messages, function($a, $b) {
                        return $a->timestamp - $b->timestamp;
                    });
                ?>
                <?php foreach ($messages as $msg): ?>
                    <?php
                        $is_me = $msg->sender_id == $this->session->userdata('user_id');
                        $align_class = $is_me ? 'from-me' : 'from-other';
                    ?>
                    <div class="message-container <?php echo $align_class; ?>">
                        <div class="message-bubble">
                            <?php echo nl2br(htmlspecialchars($msg->message)); ?>
                        </div>
                        <div class="timestamp"><?php echo date('H:i', $msg->timestamp); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="chat-footer">
        <form action="<?php echo site_url('index.php?general/send_reply'); ?>" method="post">
            <input type="hidden" name="recipient_id" value="<?php echo $user_id; ?>">
            <input type="hidden" name="message_id" value="0">
            <textarea name="reply" rows="1" placeholder="اكتب رسالتك..." required></textarea>
            <button type="submit">إرسال</button>
        </form>
    </div>
</div>

<script>
    window.onload = function () {
        var chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    };
</script>

</body>
</html>
