<form method="post" action="<?php echo site_url('messages/send_message/' . $this->session->userdata('user_id') . '/' . $receiver_id); ?>">
    <textarea name="message" required class="form-control" rows="5" placeholder="اكتب رسالتك للمدير..."></textarea>
    <br>
    <button type="submit" class="btn btn-success">إرسال</button>
</form>
