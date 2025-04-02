(function($) {
    $(document).ready(function() {
        //console.log("Cache Sync script loaded. Waiting for button press on #enable-elementor-cache-sync.");
        var $cacheSyncButton = $('#enable-elementor-cache-sync');
        if ($cacheSyncButton.length) {
            $cacheSyncButton.on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                $this.toggleClass('active');
                if ($this.hasClass('active')) {
                    console.log("Elementor Cache & Sync Enabled");
                    toggleCacheSync(true);
                } else {
                    console.log("Elementor Cache & Sync Disabled");
                    toggleCacheSync(false);
                }
            });
        } else {
            //console.log("#enable-elementor-cache-sync element not found on this page. (Cache sync functionality may only be active in certain contexts.)");
        }

        function toggleCacheSync(enable) {
            $.ajax({
                url: dm_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'dm_cache_sync',
                    enable: enable.toString()
                },
                success: function(response) {
                    console.log("Cache sync response: ", response);
                },
                error: function(xhr, status, error) {
                    console.error("Cache sync error: ", error);
                }
            });
        }
    });
})(jQuery);
