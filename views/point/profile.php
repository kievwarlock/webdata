<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/geo-profile.js',
    [
            'depends' => [
                    \yii\web\JqueryAsset::className()
            ]
    ]);

$this->title = 'ADD GEO PROFILE';
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="content-window-title">
    <?=$this->title?>
</div>
<div class="content-window-inner">

    <div class="window-description">


        <?php if ( is_array($all_users) ){ ?>

        <form class="navbar-form" action="/point/profile" method="get">
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


        <?php
        if ( is_array($current_user_data) ) {

             ?>

            <div class="profile-block">

                    <div class="profile-block-col-first">
                        <div class="profile-block-info-name">

                            <?= isset($current_user_data['fullName']) ? $current_user_data['fullName'] : 'empty' ?>
                        </div>
                        <div class="profile-block-avatar">
                            <?php if ( isset($current_user_avatar) and $current_user_avatar !== false ) {?>
                                <img src="<?= $current_user_avatar ?>" alt="">
                            <?php }else{ ?>
                                <img src="/web/img/placeholder-user.png" alt="">
                            <?php } ?>
                        </div>
                    </div>
                    <div class="profile-block-col-last">
                        <div class="profile-block-info">
                            <div class="profile-block-info-head">

                                <div class="profile-block-info-place">
                                    <div class="profile-block-info-city">
                                        <span class="glyphicon glyphicon-map-marker"></span> <?= isset($current_user_data['city']) ? $current_user_data['city'] : 'empty' ?>
                                    </div>
                                    <div class="profile-block-info-locale">
                                        <span class="label label-default"><?= isset($current_user_data['locale']) ? $current_user_data['locale'] : 'empty' ?></span>
                                    </div>

                                    <div class="btn-group" style="margin-left: auto;" >
                                        <a href="/point/view?id=<?=$current_user_data['id']?>" type="button" class="btn  btn-sm btn-info" aria-label="Left Align"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> <span class="hidden-xs hidden-sm">Geo profiles</span></a>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-block-info-phone">
                                <span class="glyphicon glyphicon-earphone"></span><?= isset($current_user_data['phoneNumber']) ? $current_user_data['phoneNumber'] : 'empty' ?>
                            </div>



                        </div>
                    </div>

            </div>


        <?php } ?>





        <?php if( is_array($current_user_data)  ){ ?>

            <input type="hidden" class="point-user-token" value="<?=$current_user_data['token']?>">
            <input type="hidden" class="point-user-id" value="<?=$current_user_data['id']?>">
            <input type="hidden" class="point-user-type" value="PROFILE">


            <div class="main-event-form-block uploadImageForm">



                <div class="row main-event-form-block-row">
                    <div class="col-xs-12 col-sm-12 col-md-6">


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
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="form-group form-item-block form-item-block-map " style="display: block;">
                            <label for="lat">Lat:</label>
                            <input type="text" name="lat" class="form-control map-lat"  placeholder="lat" readonly/>
                        </div>
                        <div class="form-group form-item-block form-item-block-map" style="display: block;">
                            <label for="lng">Lng:</label>
                            <input type="text" name="lng" class="form-control map-lng"  placeholder="lng" readonly />
                        </div>
                        <!--<div class="form-group form-item-block form-item-block-last-visit" style="display: block;">
                            <label for="last-visit">Последнее посещение:</label>
                            <input type="text" id="last-visit" name="last-visit"  class="time-ui form-control" placeholder="Время">
                        </div>-->
                        <div class="form-group form-item-block form-item-block-submit" style="display: block;">
                            <button type="button" class="btn btn-success create-geo-profile"> Create profile</button>
                            <p class="save-image-input-loader">
                                Wait please...
                            </p>
                        </div>
                    </div>


                </div>

            </div>


        <?php } ?>



    </div>



</div>


