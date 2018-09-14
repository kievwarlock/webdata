<?php
namespace app\components;


if( !is_array($full_user_data) ){
    return 'Error data request';
}
if( !isset($token) or empty($token) ){
    return 'Error ! Token not found! ';
}

$local_lang = [
    '' => 'Chose locale',
    'de' => 'DE',
    'us' => 'US',
    'fr' => 'FR',
    'es' => 'ES',
    'it' => 'IT',
    'ru' => 'RU',
];
$visible_status = [
    '1' => 'Visible',
    '0' => 'Hidden',
];



?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">USER: <i><?=$full_user_data['phoneNumber']?></i></h4>

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

<form class="save-user-profile">
    <div class="modal-body">
        <?php
        //=ProfileFormWidget::widget(['data' => $full_user_data ])
        ?>


        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="hidden" name="id" value="<?=$full_user_data['id'] ?>" id="id">
        <input type="hidden" name="phoneNumber" value="<?=$full_user_data['phoneNumber'] ?>" id="phoneNumber">



        <div class="form-group">


            <input type="hidden" name="avatarId" class="form-control" value="<?=$full_user_data['avatarId'] ?>" id="avatarId" placeholder="Avatar">


            <div class="image-form-block  form-item-block-image">
                <p>
                    <label for="type">Avatar:</label>
                </p>

                <div class="upload-images-block">
                    <div class="upload-images-item">
                        <div class="upload-images-item-inner">
                            <div class="upload-images-item-crop"></div>
                            <div class="upload-images-item-crop-placeholder"></div>
                            <div class="upload-images-item-actions">
                                <div class="btn-group-vertical text-left">
                                    <div class="btn btn-info upload-images-action-clear" data-toggle="tooltip" data-placement="left" title="Clear">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </div>
                                    <div class="btn btn-warning upload-images-action-upload" data-toggle="tooltip" data-placement="left" title="Upload">
                                        <input type="file">
                                        <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ( isset($avatar) and !empty($avatar) ) { ?>
                <script>
                    $(function(){
                        setTimeout( function () {
                                $('.save-user-profile .upload-images-item-crop').croppie({
                                    enableOrientation:true,
                                    enableExif: true,
                                    viewport: {
                                        width: 300,
                                        height: 300,
                                    },
                                    boundary: {
                                        width: 300,
                                        height: 300
                                    },
                                    url:'<?=$avatar?>'
                                });
                            }
                            , 400
                        )

                    })
                </script>
            <?php } ?>

        </div>

        <div class="form-group">
            <label for="locale">User Name</label>
            <input type="text" name="fullName" class="form-control" value="<?=$full_user_data['fullName'] ?>"
                   id="fullName" placeholder="User Name">
        </div>


        <div class="form-group custom-select-block">
            <label for="locale">Locale
                <br>
                <select name="locale" id="locale">
                    <?php
                    foreach ($local_lang as $lang_key => $lang_val ) {

                        if( $full_user_data['locale'] == $lang_key ){
                            $selected_lang = 'selected';
                        }else{
                            $selected_lang = '';
                        }
                        ?>
                        <option value="<?=$lang_key?>" <?=$selected_lang?> >
                            <?=$lang_val?>
                        </option>
                        <?php
                    }
                    ?>

                </select>
            </label>
        </div>


        <div class="form-group">
            <label for="locale">City</label>
            <input type="text" name="city" class="form-control" value="<?=$full_user_data['city'] ?>" id="city"
                   placeholder="City">
        </div>


        <div class="form-group custom-select-block">
            <label for="visible">Visible status
                <br>
                <select name="visible" id="visible">

                    <?php
                    foreach ($visible_status as $visible_status_key => $visible_status_val ) {

                        if( $full_user_data['visible'] == $visible_status_key ){
                            $visible_status_selected = 'selected';
                        }else{
                            $visible_status_selected = '';
                        }
                        ?>
                        <option value="<?=$visible_status_key?>" <?=$visible_status_selected?> >
                            <?=$visible_status_val?>
                        </option>
                        <?php
                    }
                    ?>

                </select>

            </label>
        </div>





    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button type="submit" name="submit-save-profile" class="btn btn-success">Save changes</button>
    </div>

</form>