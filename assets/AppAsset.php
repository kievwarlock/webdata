<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [

        'css/notifications.css',
        'css/jquery-ui/jquery-ui.css',
        'css/jquery-ui/jquery-ui.structure.min.css',
        'css/jquery-ui/jquery-ui.theme.min.css',
        'css/jquery-ui/jquery-ui-timepicker-addon.css',

        'https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.2/mapbox-gl.css',

        'https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.css',
        'css/crop/croppie.css',
        'css/site.css',


    ];


    public $js = [

        'js/notifications.js',

        'js/ui/jquery-ui.js',
        'js/ui/jquery-ui-timepicker-addon.js',


        'https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js',
        'js/crop/croppie.min.js',
        'js/crop/croppie.min.js',
        'js/upload-images.js',
        'js/init.js',


        'https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.2/mapbox-gl.js',
        'https://npmcdn.com/@turf/turf/turf.min.js',
        'js/map-init.js',


        'js/import/import.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
