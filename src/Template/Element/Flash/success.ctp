<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<script type="text/javascript">
$(document).ready(function() {
    PNotify.desktop.permission();
    (new PNotify({
        title: 'Success',
        text: '<?= $message ?>',
        type: 'success',
        desktop: {
            desktop: true
        }
    }))
});
</script>