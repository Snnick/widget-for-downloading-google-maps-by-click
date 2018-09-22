<?php

$mapID = 'map_'.$widgetID;

// Сборка пользовательского класса
if ($customClass !== null && is_array($customClass)) {
    $class = implode(" ", $customClass);
} else if ($customClass !== null && is_string($customClass)) {
    $class = $customClass;
} else {
    $class = "";
}

// Регистрируем api googlemaps и гарантируем единственное подключение скрипта,
// при условии если карта не подтягивается по клику на картинку

if($imgUrl === null)
{
    $this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$apiKey, ['position' => $this::POS_BEGIN]);
}

?>
<div id="wrap-<?=$mapID?>" class="google-map-wrap <?=$class?>">
    <?=($htmlBefore !== null)?$htmlBefore:''?>
    <div id="<?=$mapID?>" class="google-map <?=$class?>" style="height:<?=$height?>px"></div>
    <img style="<?= ($imgUrl === null) ? 'display: none' : '' ?>"
         id="imgGoogleMap" src="<?= $imgUrl ?>" alt="">
    <?=($htmlAfter !== null)?$htmlAfter:''?>
</div>

<?php

$jsConf = ($imgUrl !== null) ? '
        var img = $("#imgGoogleMap");
        var btn = $("#btnGoogleMap");                
        if(btn.length == 0)
        {
            btn = img;
        }
        btn.click(function() {
            $.ajax({
               url: "//maps.googleapis.com/maps/api/js?key=' . $apiKey . '&callback=initialize_'.$mapID.'",
               dataType: "script",
               timeout:8000,
               error: function() {
                  // error
               }
            });
            img.hide();
            if(btn.length > 0)
            {
                btn.hide();
            }
        }); 
    ' : 'initialize_'.$mapID.'();';

$this->registerJs(
    $this->render('_js', [
        'mapOptions' => $mapOptions,
        'styles' => $styles,
        'location' => $location,
        'mobileDraggable' => $mobileDraggable,
        'offset' => $offset,
        'marker' => $marker,
        'mapID' => $mapID,
        'imgUrl' => $imgUrl,
        'jsConf' => $jsConf
    ]), ($imgUrl !== null) ? $this::POS_END : $this::POS_READY
);
?>
