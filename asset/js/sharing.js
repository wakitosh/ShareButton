(function ($) {
    $(document).ready(function () {

        $('li#sharing-embed').on('click', 'a', function (e) {
            e.preventDefault();
            embedUrl = $(this).data('embed-url');
            embedCode = "<iframe src='" + embedUrl + "'></iframe>";
            alert(embedCode);
        });

        // Facebook share (mobile): invoke Share Dialog via FB.ui when available
        $('li#sharing-fb').on('click', 'a.fb-share-js', function (e) {
            e.preventDefault();
            var href = $(this).data('share-url') || window.location.href;
            try {
                if (window.FB && FB.ui) {
                    FB.ui({ method: 'share', href: href }, function () { /* no-op */ });
                    return;
                }
            } catch (err) { /* ignore */ }
            // Fallback: sharer URL (www.facebook.com)
            var url = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(href);
            window.open(url, '_blank');
        });

    });
})(jQuery);
