<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}

if (!isset($params['showButtons'])) {
    $showButtons = 'false';
} else {
    $showButtons = 'true';
}

if (!isset($params['width'])) {
    $width = 300;
} else {
    $width = $params['width'];
}


?>
<!-- <div class="message error" onclick="this.classList.add('hidden');"><?= $message ?></div> -->
<script type="text/javascript">
$(document).ready(function() {
    var showButtons = <?= $showButtons ?>;
    var notice = new PNotify({
        title: '<strong>Lỗi</strong>',
        text: '<?= $message ?>',
        type: 'error',
        styling: 'bootstrap3',
        icon: 'fa fa-warning',
        cornerclass: 'ui-pnotify-sharp',
        width: <?= $width ?>,
        buttons: {
            closer: showButtons,
            sticker: showButtons
        }
    });

    if (showButtons === false || typeof showButtons == 'undefined') {
        notice.get().click(function() {
            notice.remove();
        });
    }

    
});
</script>