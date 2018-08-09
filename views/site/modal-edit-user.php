<?php

$user_data = json_decode( $full_user_data, TRUE );
//var_dump($user_data);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">USER: <i><?=$user_data['phoneNumber']?></i></h4>

    <p class="save-user-profile-status bg-success">Profile saved!</p>
    <p class="save-user-profile-status bg-danger">Error! Plz try again!</p>

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

<form class="save-user-profile" >
    <div class="modal-body">

        <div class="form-group">
            <label for="locale">Locale</label>
            <input type="text" name="locale" class="form-control" value="<?=$user_data['locale']?>" id="locale" placeholder="locale">
        </div>
        <div class="form-group">
            <label for="fullName">Full name</label>
            <input type="text" name="fullName" class="form-control" value="<?=$user_data['fullName']?>" id="fullName" placeholder="full name">
        </div>
        <div class="form-group">
            <label for="nickname">Nickname</label>
            <input type="text" name="nickname" class="form-control" value="<?=$user_data['nickname']?>" id="nickname" placeholder="nickname">
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" name="city" class="form-control" value="<?=$user_data['city']?>" id="city" placeholder="city">
        </div>

    </div>
    <div class="modal-footer">
        <input type="hidden" name="phone" value="<?=$user_data['phoneNumber']?>">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button type="submit" name="submit-save-profile" class="btn btn-success">Save changes</button>
    </div>
</form>