<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<script type="text/javascript">
$(document).ready(function() {
    var notice = new PNotify({
        title: 'Success',
        text: '<?= $message ?>',
        type: 'success',
        styling: 'bootstrap3',
        icon: 'fa fa-check-circle-o',
        // addclass: 'dark',
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