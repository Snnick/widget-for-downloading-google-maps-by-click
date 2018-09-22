<?php

use app\helpers\ShowDateHelper;

$mapID = 'map_'.$widgetID;

// Сборка пользовательского класса
if ($customClass !== null && is_array($customClass)) {
    $class = implode(" ", $customClass);
} else if ($customClass !== null && is_string($customClass)) {
    $class = $customClass;
} else {
    $class = "";
}

// В случае нескольких карт на странице гарантируем единственное подключение скрипта googlemaps
$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$apiKey, ['position' => $this::POS_END]);
?>
<div id="wrap-<?=$mapID?>" class="google-map-wrap <?=$class?>">
    <?=($htmlBefore !== null)?$htmlBefore:''?>
    <div id="<?=$mapID?>" class="google-map <?=$class?>" style="height:<?=$height?>px"></div>
    <?=($htmlAfter !== null)?$htmlAfter:''?>
</div>


<?php
$js_location = json_encode($location);
$js_map_center =  $mapOptions['center'];
$js_map_zoom   =  $mapOptions['zoom'];
$js_map_scrollwheel =  $mapOptions['scrollwheel'] ? 'true' : 'false';
$js_map_typeControl =  $mapOptions['mapTypeControl'] ? 'true' : 'false';
$js_map_typeControlPosition = $mapOptions['mapTypeControlPosition'];
$js_map_zoom_control = $mapOptions['zoomControl'] ? 'true' : 'false';
$js_map_zoom_control_position = $mapOptions['zoomControlPosition'];
$js_map_scale_control = $mapOptions['scaleControl'] ? 'true' : 'false';
$js_map_streetViewControl = $mapOptions['streetViewControl'] ? 'true' : 'false';
$js_map_street_controlPosition = $mapOptions['streetViewControlPosition'];
$js_offset_x = $offset['x'];
$js_offset_y = $offset['y'];

$js_marker = '';
if($marker !=null) {
    $js_marker .= 'icon: '.$marker['icon'].", \r\n";
    $js_marker .= 'title: '.$marker['title'].", \r\n";
}
$js_styles = '';
if ($styles !== null && is_array($styles)) {
    foreach ($styles as $style) {
        $js_styles .= json_encode($style).",";
    }
}



$js = <<<JS
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
JS;

$jsFunction = <<<JS
        function initialize_$mapID() {
        var location = $js_location;

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
            $js_styles
        ]

        // Отключение перетаскивания карты для мобильных устройств
        var drag = true;
        if( isMobile.any() && $mobileDraggable == 0){
            drag = false;
        }

        // Настройки карты
        var mapOptions = {
            center: $js_map_center,
            zoom: $js_map_zoom,
            scrollwheel: $js_map_scrollwheel,
            draggable: drag,
            mapTypeControl: $js_map_typeControl,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.$js_map_typeControlPosition
            },
            zoomControl: $js_map_zoom_control,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.$js_map_zoom_control_position
            },
            scaleControl: $js_map_scale_control,
            streetViewControl: $js_map_streetViewControl,
            streetViewControlOptions: {
                position: google.maps.ControlPosition.$js_map_street_controlPosition
            }
        };

        var map = new google.maps.Map(document.getElementById('$mapID'), mapOptions);

        var latlng = new google.maps.LatLng( location.lat, location.lng );
        map.setOptions({styles: styles});

        //  Сдвиг карты
        map.setCenterWithOffset(latlng, $js_offset_x, $js_offset_y);

        var marker = new google.maps.Marker({
            position: location,
            map: map,
            $js_marker
        });
    } 
    google.maps.event.addDomListener(window, 'load', initialize_$mapID);
JS;

$this->registerJs($js, $this::POS_END);
$this->registerJs($jsFunction, $this::POS_END);
