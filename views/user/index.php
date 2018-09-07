<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'User list';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="pjax-container">
    <?php Pjax::begin([
        'enablePushState' => false,
        'timeout' => 10000,
    ]); ?>

    <div class="content-window-title">
        <?=$this->title?>&nbsp;&nbsp;&nbsp;<a  href="/user/create" class="btn btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> add user</a>
    </div>
    <div class="content-window-inner">

        <div class="window-description">


            <div class="alert alert-danger error-form" role="alert" >
                <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
                <span class="sr-only">Error! Please try again later!</span>
                Error! Please try again later!
            </div>
            <div class="alert alert-success success-form" role="alert" >
                <span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>
                <span class="sr-only">Success:</span>
                Success! User was added !
            </div>

        </div>
        <?php if( isset($error) ){ ?>
            <div class="alert alert-danger " role="alert" >
                <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
                <span class="sr-only">Error model!</span>
               <?php echo $error ?>
            </div>
        <?php } ?>

        <?php if( is_array($users) and count($users) > 0 ){ ?>

        <table class="table table-striped  sort-table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">User phone</th>
                <th  scope="col">User token</th>
                <th  scope="col">USER ID</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody>

            <?php




                $cnt = 1;
                foreach ($users as $user_item ) {
                    $new_user_class = '';
                    if( isset($new_user) and $new_user != false ){

                        if( $new_user['id'] == $user_item['id']){
                            $new_user_class = 'new-user';
                        }
                    }
                    ?>
                    <tr class="<?=$new_user_class?> "    data-phone="<?=$user_item['phoneNumber']?>"  data-id="<?=$user_item['id']?>"   data-token="<?=$user_item['token']?>" >
                        <td scope="row"><?=$cnt?></td>
                        <td><?=$user_item['phoneNumber']?></td>
                        <td ><?=$user_item['token']?></td>
                        <td ><?=$user_item['id']?></td>
                        <td >

                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-danger edit-user-profile" data-phone="<?=$user_item['phoneNumber']?>"  data-id="<?=$user_item['id']?>"   data-token="<?=$user_item['token']?>"  ><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> <span class="hidden-xs hidden-sm">Edit profile</span></button>
                            </div>

                            <div class="btn-group">
                                <a href="/point/index?id=<?=$user_item['id']?>" type="button" class="btn  btn-sm btn-success" aria-label="Left Align"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> <span class="hidden-xs hidden-sm">Add event</span></a>
                            </div>
                            <div class="btn-group">
                                <a href="/event/list/<?=$user_item['id']?>" type="button" class="btn  btn-sm btn-info" aria-label="Left Align"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> <span class="hidden-xs hidden-sm">User events</span></a>
                            </div>

                        </td>
                    </tr>
                    <?php
                    $cnt++;
                }

            ?>

            </tbody>
        </table>

        <?php } ?>

    </div>

    <?php Pjax::end(); ?>
</div>
<!-- Modal -->
<div class="modal fade" id="edit-user-profile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>