<?php

namespace smileexpo\widgets;

use Yii;

class Map extends Widget {

    public $id = 'map';

    public $height = 300;

    /*
     *  Параметр отвечающий за загрузку картинки вместо карты
     */
    public $imgUrl;

    /*
     *  Позиция (широта и долгота)
     *  $location = [
     *      'lat' => 55.7580,
     *      'lng' => 37.6037,
     *  ]
     */
    public $location;

    /*
     *  Сдвиг центра карты в пикселях
     *  $offset = [
     *      'x' => 20,
     *      'y' => -70,
     *  ]
     */
    public $offset = [
        'x' => 20,
        'y' => -70,
    ];

    /*
     *  HTML до карты
     *  $htmlBefore = "<div class='html-before-map'>HTML before map</div>"
     *
     *  <div id="wrap-id" class="google-map-wrap">
     *      <div class='html-before-map'>HTML before map</div>
     *      <div id="map-id" class="google-map"></div>
     *  </div>
     */
    public $htmlBefore;

    /*
     *  HTML после карты
     *  $htmlAfter = "<div class='html-after-map'>HTML after map</div>"
     *
     *  <div id="wrap-id" class="google-map-wrap">
     *      <div id="map-id" class="google-map"></div>
     *      <div class='html-after-map'>HTML after map</div>
     *  </div>
     */
    public $htmlAfter;

    /*
     *  Пользовательский класс для карты и обертки карты
     *  $customClass = "user-class"
     *  или
     *  $customClass = ["user-class-1", "user-class-2", ...]
     *
     *  <div id="wrap-id" class="google-map-wrap user-class">
     *      <div id="map-id" class="google-map user-class"></div>
     *  </div>
     */
    public $customClass;

    /*
     *  Маркер для позиции
     *
     *  $marker = [
     *      //URL изображения для маркера
     *      "icon" => "http://ccsummit.ru/images/icons/map-marker.png",
     *
     *      //Тайтл маркера
     *      "title" => "Moskva Exhibition Centre",
     *  ]
     */
    public $marker;

    /*
     *  Настройки карты
     */
    public $center = 'location';        // Центр карты
    public $zoom;                       // Увеличение
    public $scrollwheel = false;        // Разрешить масштабирование скроллингом

    public $mapTypeControl = true;      // Показывать переключатель типа карты
    public $mapTypeControlPosition = 'BOTTOM_CENTER';   // Позиция переключателя

    public $zoomControl = true;         // Показывать кнопки масштабирования
    public $zoomControlPosition = 'LEFT_CENTER';        // Позиция кнопок

    public $scaleControl = true;        // Показывать индикатор масштаба

    public $streetViewControl = true;   // Показывать элементы Street View
    public $streetViewControlPosition = 'LEFT_TOP';     // Позиция элементов

    public $apiKey = 'AIzaSyALaNo4RWCqTkx92RqkFOMDUX1X8Cic0vE'; // Ключ карты

    /*
     *  Стилизация карты
     *
     *  Названия типов и методов соответствуют аналогичным типам и методам
     *  в Google Maps - https://developers.google.com/maps/documentation/javascript/styling?hl=ru
     *
     *  $styles => [
     *      [
     *          'featureType' => 'road',        //Стилизуемый тип
     *          'elementType' => 'geometry',    //Элемент стилизуемого типа
     *          'stylers' => [                  //Массив стилей
     *              'lightness' => 100,
     *              'visibility' => 'on',
     *              ...
     *          ]
     *      ],
     *      [
     *          'featureType' => 'road',
     *          'elementType' => 'geometry.fill',
     *          'stylers' => [
     *              'color' => '#06B4E0',
     *              ...
     *          ]
     *      ],
     *      ...
     *  ],
     */
    public $styles;

    /*
     *  Допустимые позиции элементов интерфейса
     *
     *  Более подробная информация есть в документации Google Maps
     *  https://developers.google.com/maps/documentation/javascript/controls?hl=ru#ControlPositioning
     */
    private $availablePositions = [
        'TOP_CENTER',       // Верхняя центральная часть карты.
        'TOP_LEFT',         // Левая верхняя часть карты,
                            // при этом подчиненные элементы управления должны быть
                            // ориентированы к верхней центральной части карты.
        'TOP_RIGHT',        // Правая верхняя часть карты,
                            // при этом подчиненные элементы управления должны быть
                            // ориентированы к верхней центральной части карты.
        'LEFT_TOP',         // Левая верхняя часть карты, но под любыми элементами TOP_LEFT.
        'RIGHT_TOP',        // Правая верхная часть карты, но под любыми элементами TOP_RIGHT.
        'LEFT_CENTER',      // Левая сторона карты по центру между позициями TOP_LEFT и BOTTOM_LEFT.
        'RIGHT_CENTER',     // Правая сторона карты по центру между позициями TOP_RIGHT и BOTTOM_RIGHT.
        'LEFT_BOTTOM',      // Левая нижняя часть карты, но над любыми элементами BOTTOM_LEFT.
        'RIGHT_BOTTOM',     // Правая нижняя часть карты, но над любыми элементами BOTTOM_RIGHT.
        'BOTTOM_CENTER',    // Нижняя центральная часть карты.
        'BOTTOM_LEFT',      // Левая нижняя часть карты,
                            // при этом подчиненные элементы управления должны быть
                            // ориентированы к нижней центральной части карты.
        'BOTTOM_RIGHT',     // Правая нижняя часть карты,
                            // при этом подчиненные элементы управления
                            // должны быть ориентированы к нижней центральной части карты.
    ];

    /*
     *  Допустимые значения featureType для стилизаторов
     */
    private $availableFeatureTypes = [
        'administrative',                   // Административные субъекты
        'administrative.country',           // Страны
        'administrative.land_parcel',       // Земельные участки
        'administrative.locality',          // Местность
        'administrative.neighborhood',      // Окрестности
        'administrative.province',          // Провинции
        'all',                              // Все объекты
        'landscape',                        // Ландшафт
        'landscape.man_made',               // Техногенные структуры
        'landscape.natural',                // Естественные структуры
        'landscape.natural.landcover',      // Растительность
        'landscape.natural.terrain',        // Местность без растительности
        'poi',                              // Все точки интереса
        'poi.attraction',                   // Достопримечательности
        'poi.business',                     // Бизнес-структуры
        'poi.government',                   // Государственные структуры
        'poi.medical',                      // Больницы, аптеки, полиция и т.д.
        'poi.park',                         // Парки
        'poi.place_of_worship',             // Церкви, храмы, мечети и т.д.
        'poi.school',                       // Школы
        'poi.sports_complex',               // Спортивные комплексы
        'road',                             // Дороги
        'road.arterial',                    // Магистрали
        'road.highway',                     // Трассы
        'road.highway.controlled_access',   // Трассы с контролируемым доступом
        'road.local',                       // Местные дороги
        'transit',                          // Все станции и пути следования общественного транспорта
        'transit.line',                     // Пути следования общественного транспорта
        'transit.station',                  // Станции общественного транспорта
        'transit.station.airport',          // Аэропорты
        'transit.station.bus',              // Автобусные остановки и автовокзалы
        'transit.station.rail',             // Ж/Д станции и вокзалы
        'water',                            // Вода
    ];

    /*
     *  Допустимые значения elementType для стилизаторов
     */
    private $availableElementTypes = [
        'all',                  // Все элементы
        'geometry',             // Элементы геометрии
        'geometry.fill',        // Заливка элементов геометрии
        'geometry.stroke',      // Обводка элементов геометрии
        'labels',               // Метки элементов
        'labels.icon',          // Иконки меток элементов
        'labels.text',          // Текст меток элементов
        'labels.text.fill',     // Заливка текста меток элементов
        'labels.text.stroke',   // Обводка текста меток элементов
    ];

    /*
     *  Допустимые ключи массивов stylers для стилизаторов
     *  и правила для проверки значений массивов
     */
    private $availableStylerTypes = [
        'color' => '/^#[0-9a-fA-F]{6}$/',                       // Цвет, задается в формате HEX (#ff0000)
        'gamma' => '/^[0-9]{1}.[0-9]{1}$|^[0-9]{1}$|^10$/',     // Гамма цвета, задается числом с точкой,
                                                                // допустимые значения [0.1, 10], 1.0 - значение по умолчанию
        'hue' => '/^#[0-9a-fA-F]{6}$/',                         // Оттенок, задается в формате HEX (#ff0000)
        'invert_lightness' => '/true|false/',                   // Инвертирование яркости, допустимые значение ['true', 'false']
        'lightness' => '/^[-]?[0-9]{1,2}$|^[-]?100$/',          // Яркость, допустимые значения [-100, 100]
        'saturation' => '/^[-]?[0-9]{1,2}$|^[-]?100$/',         // Насыщенность, допустимые значения [-100, 100]
        'visibility' => '/on|off|simplified/',                  // Видимость объекта, допустимые значения ['on', 'off', 'simplified'],
                                                                // значение 'simplified' скрывает мелкие детали элемента
        'weight' => '/^[0-9]{1,2}$/',                           // Вес элемента в пикселях, допустимые значения >= 0
    ];

    /* Перетаскивание карты на мобильных устройствах */
    public $mobileDraggable = false;



    /*
     * Инициализация виджета
     */
    public function init() {
        parent::init();

        $this->checkPosition();         // Проверка на корректность позиции
        $this->checkOffset();           // Проверка на корректность сдвига

        if ($this->marker !== null) {   // Проверка на корректность маркера
            $this->checkMarker();
        }

        $this->checkElementPosition();  // Проверка на корректность позиций элементов интерфейса

        if ($this->styles !== null) {
            $this->checkStyles();       // Проверка на корректность стилизаторов
        }

        $mapOptions = $this->configureMapOptions(); // Сборка настроек карты

        $styles = $this->styles;

        if ($styles !== null && is_array($styles)) {
            $styles = $this->configureStylers($styles); // Сборка стайлеров
        }

        $this->data = array_merge($this->data, [
            'widgetID'  => $this->id, //Необходим в случае нескольких карт на странице
            'apiKey'    => $this->apiKey,
            'height'    => $this->height,
            'location'  => $this->location,
            'offset'    => $this->offset,
            'htmlBefore'    => $this->htmlBefore,
            'htmlAfter'     => $this->htmlAfter,
            'customClass'   => $this->customClass,
            'marker'        => $this->marker,
            'mapOptions'    => $mapOptions,
            'styles'        => $styles,
            'mobileDraggable'   => (int)$this->mobileDraggable,
            'imgUrl'    => $this->imgUrl,
        ]);
    }

    /*
     * Функция для сборки стайлеров
     * Необходима для корректного преобразования в JSON массива со стайлерами
     * для использования в JS коде
     */
    private function configureStylers($styles) {
        foreach ($styles as &$style) {
            $stylers = $style["stylers"];
            $style["stylers"] = [];
            foreach ($stylers as $key => $styler) {
                $style["stylers"][] = [$key => $styler];
            }
        }
        return $styles;
    }

    private function configureMapOptions() {
        if (!isset($this->zoom)) {
            $this->zoom = Yii::$app->google_maps_zoom;
        }
        if ($this->zoom == 0) {
            $this->zoom = 16;
        }
        return [
            'center' => $this->center,
            'zoom' => $this->zoom,
            'scrollwheel' => $this->scrollwheel,
            'mapTypeControl' => $this->mapTypeControl,
            'mapTypeControlPosition' => $this->mapTypeControlPosition,
            'zoomControl' => $this->zoomControl,
            'zoomControlPosition' => $this->zoomControlPosition,
            'scaleControl' => $this->scaleControl,
            'streetViewControl' => $this->streetViewControl,
            'streetViewControlPosition' => $this->streetViewControlPosition,
        ];
    }

    /*
     * Методы для валидации конфигурации виджета
     */

    /*
     * Проверка на корректность позиции
     */
    private function checkPosition() {
        if (!isset($this->location)) {
            $this->location = [
                'lat' => Yii::$app->maps_latitude,
                'lng' => Yii::$app->maps_longitude,
            ];
        }
        if (!is_array($this->location)
            || !isset($this->location['lat'])
            || !isset($this->location['lng'])
        ) {
            throw new InvalidConfigException('Некорректная позиция (location)');
        }
    }

    /*
     * Проверка на корректность сдвига
     */
    private function checkOffset() {
        if (!is_array($this->offset)
            || !isset($this->offset['x'])
            || !isset($this->offset['y'])
        ) {
            throw new InvalidConfigException('Некорректный сдвиг (offset)');
        }
    }

    /*
     * Проверка на корректность маркера
     */
    private function checkMarker() {
        if (!is_array($this->marker)) {
            throw new InvalidConfigException('Свойство marker должно быть массивом ([\'icon\' => "путь к иконке", \'title\' => "тайтл маркера"])');
        } else {
            if (!isset($this->marker['icon'])) {
                throw new InvalidConfigException('Не указан путь к иконке (icon) маркера');
            }
            if (!isset($this->marker['title'])) {
                throw new InvalidConfigException('Не указан тайтл (title) маркера');
            }
        }
    }

    /*
     * Проверка на корректность позиций элементов интерфейса
     */
    private function checkElementPosition() {
        $positions = [
            'mapTypeControlPosition'    => $this->mapTypeControlPosition,
            'zoomControlPosition'       => $this->zoomControlPosition,
            'streetViewControlPosition' => $this->streetViewControlPosition,
        ];
        foreach ($positions as $positionName => $position) {
            if (!in_array($position, $this->availablePositions)) {
                throw new InvalidConfigException('Неверная позиция свойства '.$positionName.' ("'.$position.'"), допускаются позиции '.implode(', ', $this->availablePositions));
            }
        }
    }

    /*
     * Проверка на корректность стилизаторов
     */
    private function checkStyles() {
        if (!is_array($this->styles)) {
            throw new InvalidConfigException('Свойство styles должно быть массивом стилей');
        }
        foreach ($this->styles as $styleNumber => $style) {
            /*
             * Проверка featureType
             */
            if (!isset($style['featureType'])) {
                throw new InvalidConfigException('В стиле '.$styleNumber.' не указан стилизуемый тип (свойство "featureType")');
            }
            if (!in_array($style['featureType'], $this->availableFeatureTypes)) {
                throw new InvalidConfigException('Некорректное свойство "featureType" ('.$style['featureType'].') в стиле '.$styleNumber.', допускаются свойства '.implode(', ', $this->availableFeatureTypes));
            }

            /*
             * Проверка elementType
             */
            if (!isset($style['elementType'])) {
                throw new InvalidConfigException('В стиле '.$styleNumber.' не указан элемент стилизуемого типа (свойство "elementType")');
            }
            if (!in_array($style['elementType'], $this->availableElementTypes)) {
                throw new InvalidConfigException('Некорректное свойство "elementType" ('.$style['elementType'].') в стиле '.$styleNumber.', допускаются свойства '.implode(', ', $this->availableElementTypes));
            }

            /*
             * Проверка stylers
             */
            if (!isset($style['stylers'])) {
                throw new InvalidConfigException('В стиле '.$styleNumber.' не указан массив стилей (свойство "stylers")');
            }
            if (!is_array($style['stylers'])) {
                throw new InvalidConfigException('В стиле '.$styleNumber.' свойство "stylers" не является массивом');
            }
            foreach ($style['stylers'] as $stylerName => $stylerValue) {
                if (!array_key_exists($stylerName, $this->availableStylerTypes)) {
                    throw new InvalidConfigException('Некорректное свойство стилизатора в массиве "stylers" ('.$stylerName.') в стиле '.$styleNumber);
                }
                if (!preg_match($this->availableStylerTypes[$stylerName], $stylerValue)) {
                    throw new InvalidConfigException('Некорректное значение стилизатора в массиве "stylers" ('.$stylerName.', значение '.$stylerValue.') в стиле '.$styleNumber);
                }
            }
        }
    }
}
