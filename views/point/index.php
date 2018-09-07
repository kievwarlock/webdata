<?php

/* @var $this yii\web\View */

use yii\helpers\Html;


$this->title = 'Add event';
$this->params['breadcrumbs'][] = $this->title;

if( is_array($all_users) and !empty($current_user_id) ){

    foreach ( $all_users as $user_item ) {
        if( $user_item['token'] == $current_user_id ){
            $current_user_data = [
                'id' => $user_item['id'],
                'phoneNumber' => $user_item['phoneNumber'],
                'token' => $user_item['token'],
            ];
        }
    }
}

?>


<div class="content-window-title">
    <?=$this->title?>
</div>
<div class="content-window-inner">

    <div class="window-description">

        <?php
        if ( isset($current_user_data) and is_array($current_user_data) ) {
             ?>
            <p>
                Selected user : <?=$current_user_data['phoneNumber']?>
            </p>

            <?php
        }else{
            $current_user_data = false;
        }

        if ( is_array($all_users) ){


            ?>

            <form class="navbar-form" action="/point/index" method="get">
                <div class="form-group">
                    <select name="id" class="user-select form-control">
                        <option selected=selected  value="">
                            No user selected
                        </option>
                        <?php foreach ($all_users as $all_user) { ?>

                            <option <?= ($all_user['id'] == $current_user_data['id']) ? 'selected=selected' : ''; ?> value="<?= $all_user['token'] ?>">
                                <?= $all_user['phoneNumber'] ?>
                            </option>

                        <?php } ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-success ">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                        <span class="hidden-xs hidden-sm">Select user</span>
                    </button>
                </div>
            </form>

        <?php } ?>

        <?php if( is_array($current_user_data)  ){ ?>

            <input type="hidden" class="point-user-token" value="<?=$current_user_data['token']?>">
            <input type="hidden" class="point-user-id" value="<?=$current_user_data['id']?>">

            <div class="window-description ">




                <div class="alert alert-warning" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>
                    Image upload rules:
                    <ul>
                        <li>Recomended image size  - 1024 - 1024.</li>
                        <li>All uploaded images will be 1024 - 1024 size</li>
                        <li>Image format - jpg</li>
                        <li>Min image count - 1</li>
                        <li>Max image count - 5</li>
                    </ul>

                </div>

                <div class="alert alert-danger validation-form" role="alert" >
                    <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error!</span>
                    Some field are empty!
                    <ul class="fields-empty"></ul>
                </div>

                <div class="alert alert-danger error-form" role="alert" >
                    <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error! Please try again later!</span>
                    Error! Please try again later!
                </div>

                <div class="alert alert-success success-form" role="alert" >
                    <span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>
                    <span class="sr-only">Success:</span>
                    Success! Event was added !
                </div>

            </div>


            <div class="main-event-form-block">
                <div class="row main-event-form-block-row">
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <form class="uploadImageForm" enctype="multipart/form-data">

                            <div class="form-group">
                                <label for="type">Type event</label>
                                <select name="type" id="type" class="form-control">
                                    <option selected=selected  value="">
                                        No type selected
                                    </option>
                                    <option value="WAS_HERE">
                                        WAS HERE
                                    </option>
                                    <option value="WILL_BE_HERE">
                                        WILL BE HERE
                                    </option>
                                    <option value="PROFILE">
                                        PROFILE
                                    </option>
                                </select>
                            </div>

                            <div class="form-group " >
                                <label>Map point</label>


                                <div class="openstreetmap-search">
                                    <div class="openstreetmap-search-block">
                                        <input type="text" class="openstreetmap-search-input search-input" placeholder="Search...">
                                        <div class="openstreetmap-search-submit btn btn-success search-submit">SEARCH</div>
                                    </div>
                                    <div class="openstreetmap-search-result">

                                    </div>
                                </div>

                                <div class="map-form-block">
                                    <div class="map-form-block-inner">
                                        <div id="map" class="map-form"></div>
                                    </div>
                                </div>
                                <div class="map-export-coordinates">

                                </div>




                            </div>



                            <div class="form-group form-item-block form-item-block-map">
                                <label for="lat">Lat:</label>
                                <input type="text" name="lat" class="form-control map-lat"  placeholder="lat" readonly/>
                            </div>
                            <div class="form-group form-item-block form-item-block-map">
                                <label for="lng">Lng:</label>
                                <input type="text" name="lng" class="form-control map-lng"  placeholder="lng" readonly />
                            </div>
                            <div class="form-group form-item-block form-item-block-description">
                                <label for="description">Description:</label>
                                <textarea name="description" class="form-control"  placeholder="description" ></textarea>
                            </div>
                            <div class="form-group form-item-block form-item-block-last-visit">
                                <label for="last-visit">Последнее посещение:</label>
                                <input type="text" id="last-visit" name="last-visit"  class="time-ui form-control" placeholder="Время">
                            </div>
                            <div class="form-group form-item-block form-item-block-start-time">
                                <label for="start-time">Время начала:</label>
                                <input type="text" id="start-time" name="start-time"  class="time-ui form-control" placeholder="Время">
                            </div>
                            <div class="form-group form-item-block form-item-block-finish-time">
                                <label for="finish-time">Время окончания:</label>
                                <input type="text" id="finish-time" name="finish-time" class="time-ui form-control"  placeholder="Время">
                            </div>


                            <?php if( is_array( $topic ) ) { ?>
                                <div class="form-group form-item-block form-item-block-tags">
                                    <label for="">Tags</label>
                                    <?php
                                    echo \kartik\select2\Select2::widget([
                                        'id' => 'geo-tags',
                                        'name' => 'geo-tags',
                                        'hideSearch' => false,
                                        'data' => \yii\helpers\ArrayHelper::map($topic, 'id', 'text'),
                                        'options' => ['multiple' => true, 'placeholder' => 'Select a tags']
                                    ]);
                                    ?>
                                </div>
                            <?php } ?>


                            <div class="form-group form-item-block form-item-block-submit">
                                <button type="button" class="btn btn-success save-image-input"> Create event</button>
                                <p class="save-image-input-loader">
                                    Wait please...
                                </p>
                            </div>


                            <div class="info-form-block">
                                <div class="alert alert-danger validation-form" role="alert" >
                                    <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
                                    <span class="sr-only">Error!</span>
                                    Some field are empty!
                                    <ul class="fields-empty"></ul>
                                </div>

                                <div class="alert alert-danger error-form" role="alert" >
                                    <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
                                    <span class="sr-only">Error! Please try again later!</span>
                                    Error! Please try again later!
                                </div>

                                <div class="alert alert-success success-form" role="alert" >
                                    <span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>
                                    <span class="sr-only">Success:</span>
                                    Success! Event was added !
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="image-form-block form-item-block form-item-block-image">
                            <p>
                                <label for="type">Add image to event:</label>
                            </p>


                            <div class="upload-images-block">

                                <div class="upload-images-item-template">
                                    <div class="upload-images-item-inner">
                                        <div class="upload-images-item-crop"></div>
                                        <div class="upload-images-item-crop-placeholder"></div>
                                        <div class="upload-images-item-actions">
                                            <div class="btn-group-vertical text-left">
                                                <div class="btn btn-danger upload-images-action-remove" data-toggle="tooltip" data-placement="left" title="Remove" >
                                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                </div>
                                                <div class="btn btn-info upload-images-action-clear" data-toggle="tooltip" data-placement="left" title="Clear">
                                                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                                </div>
                                                <div class="btn btn-primary upload-images-action-rotate" data-toggle="tooltip" data-placement="left" title="Rotate">
                                                    <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>
                                                </div>
                                                <div class="btn btn-warning upload-images-action-upload" data-toggle="tooltip" data-placement="left" title="Upload" >
                                                    <input type="file">
                                                    <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                                                </div>
                                                <div class="btn btn-primary upload-images-action-view" data-toggle="tooltip" data-placement="left" title="View">
                                                    <span class="glyphicon glyphicon-picture" aria-hidden="true"></span>
                                                </div>
                                                <div class="btn btn-success upload-images-action-add" data-toggle="tooltip" data-placement="left" title="Add">
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="upload-images-item">
                                    <div class="upload-images-item-inner">
                                        <div class="upload-images-item-crop"></div>
                                        <div class="upload-images-item-crop-placeholder"></div>
                                        <div class="upload-images-item-actions">
                                            <div class="btn-group-vertical text-left">
                                                <div class="btn btn-danger upload-images-action-remove" data-toggle="tooltip" data-placement="left" title="Remove" >
                                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                </div>
                                                <div class="btn btn-info upload-images-action-clear" data-toggle="tooltip" data-placement="left" title="Clear">
                                                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                                </div>
                                                <div class="btn btn-primary upload-images-action-rotate" data-toggle="tooltip" data-placement="left" title="Rotate">
                                                    <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>
                                                </div>
                                                <div class="btn btn-warning upload-images-action-upload" data-toggle="tooltip" data-placement="left" title="Upload" >
                                                    <input type="file">
                                                    <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                                                </div>
                                                <div class="btn btn-primary upload-images-action-view" data-toggle="tooltip" data-placement="left" title="View">
                                                    <span class="glyphicon glyphicon-picture" aria-hidden="true"></span>
                                                </div>
                                                <div class="btn btn-success upload-images-action-add" data-toggle="tooltip" data-placement="left" title="Add">
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="modal fade upload-image-preview-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                <div class="modal-dialog modal-md" role="document">
                                    <div class="modal-content">
                                        <div class="upload-image-preview">
                                            <img src="" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>


                </div>

            </div>


        <?php } ?>



    </div>



</div>


