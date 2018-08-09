<?php

namespace app\controllers;

use app\models\AvatarDataModel;
use app\models\ImageDataModel;
use app\models\PointDataModel;
use Yii;
use yii\web\Controller;
use app\controllers\MainController;


class AvatarController extends Controller {


    public function actionCreate()
    {
        if (Yii::$app->request->isAjax and Yii::$app->request->post()) {

            $data = Yii::$app->request->post();
            if( isset( $_FILES['image']['tmp_name'] ) and !empty( $_FILES['image']['tmp_name'] )
                and
                isset( $data['token'] ) and !empty( $data['token'] ) ) {

                $model = new AvatarDataModel();
                $avatar_id = $model->setAvatar($_FILES['image']['tmp_name'] ,  $data['token'] );

                if( $avatar_id ) {
                    return $avatar_id;
                }
            }
        }

        return false;


    }

    public function actionView(){

    }


}
