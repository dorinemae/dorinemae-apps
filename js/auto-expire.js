(function($) {
    function updateExpiryElements() {
        if ( typeof dm_auto_expire_settings !== 'undefined' && dm_auto_expire_settings.entries ) {
            dm_auto_expire_settings.entries.forEach(function(entry) {
                var cssClass      = entry.css_class;
                var expiryTimeStr = entry.expiry_time;
                var expiryTime    = new Date(expiryTimeStr);
                var currentTime   = new Date();
                
                // Select all elements matching the CSS class.
                $('.' + cssClass).each(function() {
                    var $elem = $(this);
                    if ( currentTime >= expiryTime ) {
                        if ( !$elem.hasClass('expired') ) {
                            //console.log('Hiding element with class "' + cssClass + '" - expired at ' + expiryTime);
                            $elem.fadeOut('slow', function() {
                                $elem.addClass('expired');
                            });
                        }
                    } else {
                        if ( $elem.hasClass('expired') ) {
                            //console.log('Showing element with class "' + cssClass + '" - not yet expired (expires at ' + expiryTime + ')');
                            $elem.fadeIn('slow', function() {
                                $elem.removeClass('expired');
                            });
                        }
                    }
                });
            });
        }
    }

    $(document).ready(function() {
        updateExpiryElements();
        // Check periodically every 30 seconds.
        setInterval(updateExpiryElements, 30000);
    });
})(jQuery);
