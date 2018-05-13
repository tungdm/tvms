<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<!-- <div class="message error" onclick="this.classList.add('hidden');"><?= $message ?></div> -->
<script type="text/javascript">
$(document).ready(function() {
    PNotify.desktop.permission();
    (new PNotify({
        title: 'Error',
        text: '<?= $message ?>',
        type: 'error',
        desktop: {
            desktop: true
        }
    }))
});
</script>