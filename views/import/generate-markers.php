<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

$this->title = 'Generate markers';
$this->params['breadcrumbs'][] = $this->title;

$sections = array(
    'food',
    'drinks',
    'coffee',
    'shops',
    'arts',
    'outdoors',
    'sights',
    'trending',
    'nextVenues'
);


$russian_names = file_get_contents('./gen-data/address-data.json');
$russian_names_arrays =  json_decode($russian_names, true);


?>
<?php if( is_array( $russian_names_arrays ) ) { ?>
    <pre>
                <?php print_r($russian_names_arrays)?>
            </pre>
<?php } ?>

<div class="content-window-title">
    <?= $this->title ?>
</div>
<div class="content-window-inner">
    <div class="content-window-inner">
        <?php if( is_array( $data ) ) { ?>
            <pre>
                <?php print_r($data)?>
            </pre>
        <?php } ?>
       <?php $form = ActiveForm::begin([
        'action' => '/import/generate-markers',
        ]) ?>



            <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="market-sections">
                    <?php if( is_array($sections) ) {?>

                        <?php foreach ($sections as $item) { ?>
                            <p>
                                <label>
                                    <input type="checkbox" name="markerSection[]" value="<?=$item?>" checked>  <?=$item?>
                                </label>
                            </p>

                        <?php } ?>

                    <?php } ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">

                <div class="map-selection-import">

                    <div class="form-group " >

                        <label>Map point</label>
                        <div class="map-form-block">
                            <div class="map-form-block-inner">
                                <div id="map" class="map-form"></div>
                            </div>
                        </div>


                    </div>
                    <div class="openstreetmap-search">
                        <div class="openstreetmap-search-block">
                            <input type="text" class="openstreetmap-search-input search-input" placeholder="Search...">
                            <div class="openstreetmap-search-submit btn btn-success search-submit">SEARCH</div>
                        </div>
                        <div class="openstreetmap-search-result">
                        </div>
                    </div>
                </div>

                <div class="map-selection-import-data">

                </div>
            </div>
        </div>

            <button type="submit" class="btn btn-success">Submit</button>

        <?php ActiveForm::end() ?>


        <?php if( is_array( $markers ) ) { ?>
            <pre>
                <?php print_r($markers)?>
            </pre>
        <?php } ?>

    </div>

</div>

</div>

