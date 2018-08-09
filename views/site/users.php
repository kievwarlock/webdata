<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'User list';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="content-window-title">
     <?=$this->title?>&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-success generateUser"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> add user</button>
</div>
<div class="content-window-inner">

   <!-- <div class="window-title">Users</div>-->
    <div class="window-description">

        <!--<div class="alert alert-warning" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>
            Enter "add user" to generate new user with random phone number

        </div>-->
        <!--<p>
            <button type="button" class="btn btn-success generateUser"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> add user</button>
        </p>-->

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



    <table class="table table-striped  sort-table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">User phone</th>
            <th class="hidden" scope="col">User token</th>
            <th class="hidden" scope="col">Profile ID</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php

        if( $datausers ){
            $cnt = 1;
            foreach ($datausers as $datauser ) {
                $new_user_class = '';
                if( isset($new_user) and $new_user != false ){
                    if( $new_user == $datauser['profile_id']){
                        $new_user_class = 'new-user';
                    }
                }
                ?>
                <tr class="<?=$new_user_class?> edit-user-profile"   data-phone="<?=$datauser['phone']?>">
                    <td scope="row"><?=$cnt?></td>
                    <td><?=$datauser['phone']?></td>
                    <td class="hidden"><?=$datauser['token']?></td>
                    <td class="hidden"><?=$datauser['profile_id']?></td>
                    <td >

                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-danger"  data-phone="<?=$datauser['phone']?>" aria-label="Left Align" data-toggle="modal" data-target="#edit-user-profile" ><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> <span class="hidden-xs hidden-sm">Edit profile</span></button>
                            </div>
                            <div class="btn-group">
                                <a href="/event/add/<?=$datauser['profile_id']?>" type="button" class="btn  btn-sm btn-success" aria-label="Left Align"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> <span class="hidden-xs hidden-sm">Add event</span></a>
                            </div>
                            <div class="btn-group">
                                <a href="/event/list/<?=$datauser['profile_id']?>" type="button" class="btn  btn-sm btn-info" aria-label="Left Align"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> <span class="hidden-xs hidden-sm">User events</span></a>
                            </div>

                    </td>
                </tr>
                <?php
                $cnt++;
            }
        }
        ?>

        </tbody>
    </table>


</div>


<!-- Modal -->
<div class="modal fade" id="edit-user-profile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>