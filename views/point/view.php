<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Event list';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="content-window-title">
    <?=$this->title?>
</div>
<div class="content-window-inner">




    <?php
    /*if( $current_user_id ){
        if( $events ) {

            foreach ($events as $key_event => $event) {
                if ($event['ownerId'] != $current_user_id) {
                    unset($events[$key_event]);
                }
            }

        }
        */?><!--
        <div class="alert alert-success" role="alert" >
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
            <span class="sr-only">Success:</span>
            Event for user: <?/*=$current_user_id*/?>; Event count:  <span class="badge"><?/*=count($events)*/?></span> &nbsp;&nbsp;&nbsp; <a class="btn-sm btn-info " href="/event/list">Show all events</a>
        </div>
        --><?php
/*    }*/


    $user_list = array();
    if( $users ){
        foreach ($users as $user ) {
            $user_list[ $user['id'] ] = array(
                'phone' => $user['phoneNumber'],
                'token' => $user['token'],
            );
        }
    }


    ?>


    <table class="table"   >
        <thead>
        <tr>

            <th class="hidden-xs hidden-sm">
                Geo Point Id
            </th>
            <th>
                user
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

          <!--  <pre>
                <?php /*print_r($users);*/?>
            </pre>-->

            <?php


            //array_multisort($events);

            foreach ($points as $point ) {
                ?>
                <tr>
                    <td class="hidden-xs hidden-sm">
                        <?= ($point['id']) ? $point['id'] : 'NULL'?>
                    </td>
                    <td>
                        <?= ( isset( $user_list[$point['ownerId']]) ) ? $user_list[$point['ownerId']]['phone'] : $point['ownerId'] ?>
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


</div>



<!-- Modal -->
<div class="modal fade" id="view-event" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">

        <div class="modal-content">

        </div>
    </div>
</div>