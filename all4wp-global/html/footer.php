<script>
    jQuery(function () {
        jQuery('body').on('click', '.all4wp_plugin_list .plugin-card-top', function () {
            let url = $(this).attr('data-url');
            window.open(url, '_blank');
        })
    });
</script>