(function ($) {
    $(document).ready(function () {
        var lyon = {lat: 45.750000, lng: 4.850000};
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: lyon
        });

        $('.js-parking').each(function(){
            var markerPosition = {lat: $(this).data('latitude'), lng: $(this).data('longitude')};

            var marker = new google.maps.Marker({
                position: markerPosition,
                map: map
            });

            var link = $(this).data('link');
            marker.addListener('click', function(evt) {
                window.location.href = link;
            });

            marker.link = $(this).data('link');
        });

    });
})(jQuery);
