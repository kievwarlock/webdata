<?php

namespace app\controllers;

use app\models\ImageDataModel;
use app\models\PointDataModel;
use app\models\TopicDataModel;
use app\models\UserDataModel;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Datausers;
use app\models\UserModel;


class PointController extends MainController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    public function actionViewItem()
    {
        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();

            if(
                isset($data['owner_user_token']) and !empty($data['owner_user_token']) and
                isset($data['point_id']) and !empty($data['point_id'])  and
                isset($data['point_type']) and !empty($data['point_type'])
            ){

                $pointModel = new PointDataModel();
                $pointData = $pointModel->getPointById( $data['point_id'], $data['owner_user_token'], str_replace('_','-',$data['point_type']) );

                if( $pointData["status"] === true ){

                    $point_data = json_decode($pointData["data"], true);

                    $images_array = false;

                    if( is_array( $point_data['contentCard']['imageIds'] ) and count( $point_data['contentCard']['imageIds'] ) > 0 ) {
                        foreach ( $point_data['contentCard']['imageIds'] as $imageId ) {
                            $image_model = new ImageDataModel();
                            $image_data = $image_model->getImage( $imageId, $data['owner_user_token'] );

                            if( $image_data['status'] === true ) {
                                $images_array[] =  'data:image/jpeg;base64,' . base64_encode( $image_data['data'] );
                            }else{
                                //$images_array[] =  $image_data['error'];
                            }
                        }

                    }


                    $topic_model = new TopicDataModel();
                    $topic = $topic_model->getTopics($data['owner_user_token']);
                    $topic_data = false;
                    if( $topic['status'] == true ){
                        $topic_data = json_decode( $topic['data'], true );
                    }

                    //return var_dump($images_array);


                    return $this->renderAjax('modal/point',[
                        'point_data' => $point_data,
                        'images' => $images_array,
                        'token' => $data['owner_user_token'],
                        'topic' => $topic_data,
                    ]);
                }



            }

        }

        return false;

    }

    public function actionView()
    {

        $pointModel = new PointDataModel();
        $allGeoPoints = $pointModel->getPoints();

        $userDataModel = new UserDataModel();
        $usersArray = $userDataModel->getUsersArray();

        if( $allGeoPoints['status']  === true  and $usersArray['status'] === true ){
             return $this->render('view',[
                'points' => $allGeoPoints['data'],
                'users' => $usersArray['data'],
               //'images' => $images,

              // 'current_user_id' => $current_user,
           ]);
        }


       /* $post_id = Yii::$app->getRequest()->getQueryParam('id');
        if( $post_id ){
            $current_user = $post_id;
        }else{
            $current_user = false;
        }
        $token = Datausers::find()
            ->select('token')
            ->asArray()
            ->where(['not',['token'=>null]])
            ->one();

        $users = Datausers::find()
            ->select('profile_id, phone')
            ->asArray()
            ->all();


        $images = false;
        $all_events = false;*/

        /*if( $token["token"] ){
            $UserDataModel = new UserModel();
            $all_events = $UserDataModel->getAllEvents( $token["token"] );

            $image_data = $UserDataModel->getImage(5, $token['token'] );
            if( $image_data ){
                $images[] = $image_data;
            }
            if( $all_events ){
                $all_events = json_decode($all_events, true);
            }
        }*/

       /* return $this->render('event_list',[
            'events' => $all_events,
            //'images' => $images,
           // 'users' => $users,
           // 'current_user_id' => $current_user,
        ]);*/

        /*$user_id = Yii::$app->getRequest()->getQueryParam('id');
        $userDataModel = new UserDataModel();
        $usersArray = $userDataModel->getUsersArray();

        return $this->render('view',[
            'current_user_id' => $user_id,
            'all_users' => $usersArray,
        ]);*/


    }


    public function actionIndex()
    {

        $user_id = Yii::$app->getRequest()->getQueryParam('id');
        $userDataModel = new UserDataModel();
        $usersArray = $userDataModel->getUsersArray();



        if( $usersArray['status'] === true ){

            $topic_model = new TopicDataModel();
            $topic = $topic_model->getTopics($usersArray['data'][0]["token"]);

            $topic_data = false;
            if( $topic['status'] == true ){
                $topic_data = json_decode( $topic['data'], true );
            }

            return $this->render('index',[
                'current_user_id' => $user_id,
                'topic' => $topic_data,
                'all_users' => $usersArray['data'],
            ]);
        }else{
            return $this->render('index',[
                'current_user_id' => '',
                'all_users' => '',
                'error' => $usersArray['error']
            ]);
        }


    }


    public function actionCreate()
    {
        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();

            if( isset($data['type']) and !empty($data['type']) and isset($data['token']) and !empty($data['token'])  ){

                $model = new PointDataModel();
                $createPoint = $model->createPoint( $data, $data['token'] );

                if(  $createPoint ){
                    return $createPoint;
                }



            }

        }

        return false;
    }



    public function actionUpdate()
    {
        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();

            if( isset($data['type']) and !empty($data['type']) and
                isset($data['token']) and !empty($data['token']) and
                isset($data['id']) and !empty($data['id'])
            ){

                $return_update = array(
                    'point_status' => false,
                    'content_status' => false,
                    'point_msg' => 'Geo point was not update! Please try again later!',
                    'content_mgs' => 'Content was not update! Please try again later!',
                );

                $model = new PointDataModel();

                $createPoint = $model->updatePoint( $data['id'], $data, $data['token'] );

                if( is_array($createPoint["point"]) ){
                    if( $createPoint["point"]['status'] == true ){
                        $return_update['point_status'] = true;
                        $return_update['point_msg'] = 'Geo point was update successful!';
                    }else{
                        $return_update['point_status'] = false;
                        $return_update['point_msg'] = $createPoint["point"]['error'] ;
                    }
                }
                if( is_array($createPoint["content"]) ){
                    if( $createPoint["content"]['status'] == true ){
                        $return_update['content_status'] = true;
                        $return_update['content_msg'] = 'Content was update successful!';
                    }else{
                        $return_update['content_status'] = false;
                        $return_update['content_msg'] = $createPoint["content"]['error'] ;
                    }
                }

                return json_encode($return_update);




            }

        }

        return false;
    }


    public function actionView_event_item(){

        $event_data = false;
        $user_phone = false;
        $images = false;


        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            if( isset( $data['event_id'] ) and !empty( $data['event_id'] )  and
                isset( $data['user_id'] ) and !empty( $data['user_id'] )  ){




                $user = Datausers::find()
                    ->asArray()
                    ->where(['profile_id' => $data['user_id'] ])
                    ->one();

                $user_phone = $user['phone'];



                if( $user['token'] ){
                    $UserDataModel = new UserModel();
                    $event_data = $UserDataModel->getEvent( $data['event_id'], $user['token'] );

                    if( $event_data ){
                        $event_data = json_decode($event_data, true);
                       
                        if( isset( $event_data['contentCard'] ) ){
                            if( isset($event_data['contentCard']['images']) and count($event_data['contentCard']['images']) > 0 ){

                                foreach ($event_data['contentCard']['images'] as $image_id ){
                                    $image_data = $UserDataModel->getImage($image_id, $user['token'] );
                                    if( $image_data ){
                                        $images[] = $image_data;
                                    }
                                }


                            }
                        }

                    }

                }


            }

        }

        return $this->renderPartial('modal-event',[
            'user_phone' => $user_phone,
            'full_event_data' => $event_data,
            'images' => $images,
        ]);

    }

    public function actionEvent_list()
    {

        $post_id = Yii::$app->getRequest()->getQueryParam('id');
        if( $post_id ){
            $current_user = $post_id;
        }else{
            $current_user = false;
        }
        $token = Datausers::find()
            ->select('token')
            ->asArray()
            ->where(['not',['token'=>null]])
            ->one();

        $users = Datausers::find()
            ->select('profile_id, phone')
            ->asArray()
            ->all();


        $images = false;
        $all_events = false;

        if( $token["token"] ){
            $UserDataModel = new UserModel();
            $all_events = $UserDataModel->getAllEvents( $token["token"] );

            $image_data = $UserDataModel->getImage(5, $token['token'] );
            if( $image_data ){
                $images[] = $image_data;
            }
           if( $all_events ){
               $all_events = json_decode($all_events, true);
           }
        }

        return $this->render('event_list',[
            'events' => $all_events,
            'images' => $images,
            'users' => $users,
            'current_user_id' => $current_user,
        ]);


    }

    public function actionEvent_add()
    {


        $user_id = Yii::$app->getRequest()->getQueryParam('id');
        $current_user = false;
        if( $user_id ){
            $current_user = Datausers::find()
                ->asArray()
                ->where(['profile_id' => $user_id ])
                ->one();
        }
        $all_users = Datausers::find()
            ->indexBy('id')
            ->asArray()
            ->all();

        return $this->render('add_event',[
            'current_user' => $current_user,
            'all_users' => $all_users,
        ]);



    }




}
