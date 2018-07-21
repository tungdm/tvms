<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<!-- <div class="message error" onclick="this.classList.add('hidden');"><?= $message ?></div> -->
<script type="text/javascript">
$(document).ready(function() {
    var notice = new PNotify({
        title: '<strong>Lỗi</strong>',
        text: '<?= $message ?>',
        type: 'error',
        styling: 'bootstrap3',
        icon: 'fa fa-warning',
        cornerclass: 'ui-pnotify-sharp',
        buttons: {
            closer: false,
            sticker: false
        }
    });

    notice.get().click(function() {
        notice.remove();
    });
});
</script>