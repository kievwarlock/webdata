<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

$this->title = 'Generate';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="content-window-title">
    <?= $this->title ?>
</div>
<div class="content-window-inner">
    <div class="content-window-inner">

        <?php
        if( $data ){ ?>
            <div class="alert alert-success" role="alert">
                File was success generate.</br>
                File name: <b><?=$data?></b>
            </div>
        <?php } ?>

        <?php

        $form = ActiveForm::begin([
            'action' => '/import/generate',
        ]) ?>
            <div class="form-group">
                <label for="user-count" >Count of users</label>
                <input type="number" class="form-control" id="user-count" name="user-count" placeholder="Number users">
            </div>
            <div class="form-group">
                <label for="base-count" >Count of user base</label>
                <input type="number" class="form-control" id="base-count" name="base-count" placeholder="Number user base">
            </div>
            <div class="form-group">
                <label for="file-name" >File name:</label>
                <input type="text" class="form-control" id="file-name" name="file-name" placeholder="File name">
            </div>

            <button type="submit" class="btn btn-success">Submit</button>

        <?php ActiveForm::end() ?>




</div>

</div>

