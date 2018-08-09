<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Datausers;
use app\models\UserModel;


class SiteController extends MainController
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




    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }




    public function actionUsers()
    {

      /*  $UserDataModel = new UserModel();
        var_dump( $UserDataModel->generatePhoneNumber() );*/

     //   $customers = Customer::findAll(


        $datausers = Datausers::find()
            ->indexBy('id')
            ->asArray()
            ->all();

        return $this->render('users',[
            'datausers' => $datausers,
        ]);
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

    public function actionAdd()
    {


        $UserDataModel = new UserModel();
        $newUserId = $UserDataModel->addNewUser();

        $datausers = Datausers::find()
            ->indexBy('id')
            ->asArray()
            ->all();
        return $this->renderPartial('users',[
            'datausers' => $datausers,
            'new_user' => $newUserId
        ]);


    }

    public function actionUpdate_profile() {



        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            if( isset($data['phone']) and !empty($data['phone']) ){
                $datausers = Datausers::find()
                    ->asArray()
                    ->where(['phone' => $data['phone'] ])
                    ->one();
                if( $datausers['token'] ){
                    $UserDataModel = new UserModel();
                    return $UserDataModel->updateUserProfile( $datausers['token'], $data );
                }

            }

        }
        return false;
    }


    public function actionEdit_user_profile()
    {
        if (Yii::$app->request->isAjax) {


            $data = Yii::$app->request->post();
            if( isset( $data['user_phone'] ) and !empty( $data['user_phone'] )    ){
                $datausers = Datausers::find()
                    ->indexBy('id')
                    ->asArray()
                    ->where(['phone' => $data['user_phone'] ])
                    ->one();


                if( $datausers['token'] and $datausers['profile_id']  ){
                    $UserDataModel = new UserModel();
                    $user_data = $UserDataModel->getUserProfile($datausers['profile_id'], $datausers['token'] );
                }

                return $this->renderPartial('modal-edit-user',[
                     'full_user_data' => $user_data,
                ]);

            }

        }

        return false;


    }


    public function actionImages()
    {



        /*$param = Yii::$app->getRequest()->getQueryParam('id');
        var_dump($param);*/
        return $this->render('image_upload',[
            //'datausers' => $datausers,
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
    public function actionAdd_event_to_server()
    {
        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();

            if( isset($data['type']) and !empty($data['type']) and isset($data['profile_id']) and !empty($data['profile_id'])  ){

                $current_user = Datausers::find()
                    ->asArray()
                    ->where(['profile_id' => $data['profile_id'] ])
                    ->one();
                if( $current_user['token']) {

                    $UserDataModel = new UserModel();
                    $add_event = $UserDataModel->addEvent( $data, $current_user['token'] );
                    return $add_event;

                }

            }

        }

        return false;
    }


    public function actionAdd_image()
    {

        if (Yii::$app->request->isAjax) {


            if( isset( $_FILES['image']['tmp_name'] ) and !empty( $_FILES['image']['tmp_name'] )
                and
                isset( $_POST['profile_id'] ) and !empty( $_POST['profile_id'] ) ) {

                $current_user = Datausers::find()
                    ->asArray()
                    ->where(['profile_id' => $_POST['profile_id'] ])
                    ->one();

                if( $current_user['token'] ){
                    $UserDataModel = new UserModel();
                    $image_id = $UserDataModel->addImageToServer($_FILES['image']['tmp_name'] , $current_user['token'] );
                    if( $image_id ){
                        return $image_id;
                    }
                }


            }

        }

        return false;


    }

}
