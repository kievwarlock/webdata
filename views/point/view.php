<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Geo points';
$this->params['breadcrumbs'][] = $this->title;

$selected_user_id = false;
if( isset($_GET['id']) ){
    $selected_user_id = $_GET['id'];
}

?>


<div class="content-window-title">
    <?=$this->title?>
</div>
<div class="content-window-inner">

    <?php

    $user_list = array();
    if( $users ){
        if( is_array($points) ){

            $point_owners = \yii\helpers\ArrayHelper::map($points, 'ownerId', 'id');

            foreach ($users as $user ) {

                if( isset($point_owners[$user['id']]) and !empty($point_owners[$user['id']]) ){
                    $user_list[ $user['id'] ] = array(
                        'id' => $user['id'],
                        'phoneNumber' => $user['phoneNumber'],
                        'token' => $user['token'],
                    );

                }
            }
        }

    }



    ?>

    <?php if ( is_array($user_list) ){ ?>

        <form class="navbar-form" action="/point/view" method="get">
            <div class="form-group">
                <select name="id" class="user-select form-control">
                    <option selected=selected  value="">
                        No user selected
                    </option>
                    <?php foreach ($user_list as $user) { ?>

                        <option <?= ( $user['id'] == $selected_user_id ) ? 'selected=selected' : ''; ?> value="<?= $user['id'] ?>">
                            <?= $user['phoneNumber'] ?>
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
    if ( is_array($user_data) ) {

        foreach( $points as $key => $point ) {
            if( $point['ownerId'] !== $user_data['id'] ){
                unset( $points[$key] );
            }else{
                $array_point_by_type[$point['type']][] = [
                    'id' => $point['id'],
                    'ownerId' => $point['ownerId'],
                    'latitude' => $point['latitude'],
                    'longitude' => $point['longitude'],
                ];
            }
        }
        ?>

        <div class="profile-block">

            <div class="profile-block-col-first">
                <div class="profile-block-info-name">

                    <?= isset($user_data['fullName']) ? $user_data['fullName'] : 'empty' ?>
                </div>
                <div class="profile-block-avatar">
                    <?php if ( isset($user_data['avatarBase64'] ) and !empty($user_data['avatarBase64'])  ) {?>
                        <img src="<?= $user_data['avatarBase64']  ?>" alt="">
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
                                <span class="glyphicon glyphicon-map-marker"></span> <?= isset($user_data['city']) ? $user_data['city'] : 'empty' ?>
                            </div>
                            <div class="profile-block-info-locale">
                                <span class="label label-default"><?= isset($user_data['locale']) ? $user_data['locale'] : 'empty' ?></span>
                            </div>
                            <div class="btn-group" style="margin-left: auto;">
                                <a href="/point/profile?id=<?=$user_data['token']?>" type="button"  class="btn  btn-sm btn-success" aria-label="Left Align"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> <span class="hidden-xs hidden-sm">Add geo profile</span></a>
                            </div>
                        </div>

                    </div>

                    <div class="profile-block-info-phone">
                        <span class="glyphicon glyphicon-earphone"></span><?= isset($user_data['phoneNumber']) ? $user_data['phoneNumber'] : 'empty' ?>
                    </div>
                </div>


                <div class="profile-geo-points">

                    <?php if( isset($array_point_by_type) ){ ?>
                        <?= $this->render('geo-points.php',[
                            'array_point_by_type' => $array_point_by_type,
                            'token' => $user_data['token'],
                        ]) ?>
                    <?php }else{
                        echo 'NO geo points!';
                    } ?>

                </div>


            </div>

        </div>


    <?php }else{ ?>



    <table class="table"   >
        <thead>
        <tr>

            <th class="hidden-xs hidden-sm">
                Geo Point Id
            </th>

            <th class="hidden-xs hidden-sm" >
                latitude
            </th>
            <th class="hidden-xs hidden-sm" >
                longitude
            </th>
            <th  >
                type
            </th>

            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php

        if( isset($points)  ){ ?>


            <?php


            //array_multisort($events);

            foreach ($points as $point ) {
                ?>
                <tr>
                    <td class="hidden-xs hidden-sm">
                        <?= ($point['id']) ? $point['id'] : 'NULL'?>
                    </td>
                    <td class="hidden-xs hidden-sm">
                        <?= ($point['latitude']) ? $point['latitude'] : 'NULL'?>
                    </td>
                    <td class="hidden-xs hidden-sm">
                        <?= ($point['longitude']) ? $point['longitude'] : 'NULL'?>
                    </td>
                    <td >
                        <?= ($point['type']) ? $point['type'] : 'NULL'?>
                    </td>

                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success view-point-data"  data-type="<?=$point['type']?>" data-point="<?=$point['id']?>" data-token="<?= ( isset( $user_list[$point['ownerId']]) ) ? $user_list[$point['ownerId']]['token'] : '' ?>" aria-label="Left Align"  ><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> <span class="hidden-xs hidden-sm">View</span></button>
                        </div>

                    </td>
                </tr>
                <?php

            }
        }
        ?>

        </tbody>
    </table>

    <?php } ?>

</div>



<!-- Modal -->
<div class="modal fade" id="view-event" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">

        <div class="modal-content">

        </div>
    </div>
</div>