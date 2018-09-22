var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};

function initialize_<?=$mapID?>() {

    google.maps.Map.prototype.setCenterWithOffset= function(latlng, offsetX, offsetY) {
        var map = this;
        var ov = new google.maps.OverlayView();
        ov.onAdd = function() {
            var proj = this.getProjection();
            var aPoint = proj.fromLatLngToContainerPixel(latlng);
            aPoint.x = aPoint.x+offsetX;
            aPoint.y = aPoint.y+offsetY;
            map.setCenter(proj.fromContainerPixelToLatLng(aPoint));
        };
        ov.draw = function() {};
        ov.setMap(this);
    };

    // Позиция карты
    var location = <?= json_encode($location)?>;

    // Стили по умолчанию
    var styles = [
        {
            "featureType": "road",
            "elementType": "geometry",
            "stylers": [
                {
                    "lightness": 100
                },
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [
                {
                    "visibility": "on"
                },
                {
                    "color": "#C6E2FF"
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#C5E3BF"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#D1D1B8"
                }
            ]
        },
        <?php
        // Добавление пользовательских стилей
        if ($styles !== null && is_array($styles)) {
            foreach ($styles as $style) {
                echo json_encode($style).",";
            }
        }
        ?>
    ]

    // Отключение перетаскивания карты для мобильных устройств
    var drag = true;
    if( isMobile.any() && <?= $mobileDraggable ?> == 0){
        drag = false;
    }

    // Настройки карты
    var mapOptions = {
        center: <?=$mapOptions['center']?>,
        zoom: <?=$mapOptions['zoom']?>,
        scrollwheel: <?=($mapOptions['scrollwheel'])?'true':'false'?>,
        draggable: drag,
        mapTypeControl: <?=($mapOptions['mapTypeControl'])?'true':'false'?>,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
            position: google.maps.ControlPosition.<?=$mapOptions['mapTypeControlPosition']?>
        },
        zoomControl: <?=($mapOptions['zoomControl'])?'true':'false'?>,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.LARGE,
            position: google.maps.ControlPosition.<?=$mapOptions['zoomControlPosition']?>
        },
        scaleControl: <?=($mapOptions['scaleControl'])?'true':'false'?>,
        streetViewControl: <?=($mapOptions['streetViewControl'])?'true':'false'?>,
        streetViewControlOptions: {
            position: google.maps.ControlPosition.<?=$mapOptions['streetViewControlPosition']?>
        }
    };

    var map = new google.maps.Map(document.getElementById('<?=$mapID?>'), mapOptions);

    var latlng = new google.maps.LatLng( location.lat, location.lng );
    map.setOptions({styles: styles});

    //  Сдвиг карты
    map.setCenterWithOffset(latlng, <?=$offset['x']?>, <?=$offset['y']?>);

    var marker = new google.maps.Marker({
        position: location,
        map: map,
        <?php if ($marker !== null) : ?>
        icon: '<?=$marker['icon']?>',
        title: '<?=$marker['title']?>',
        <?php endif;?>
    });
}
<?= $jsConf ?>