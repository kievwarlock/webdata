<?php

namespace app\controllers;

use app\models\AvatarDataModel;
use app\models\UserDataModel;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Datausers;
use app\models\UserModel;
use yii\base\ErrorException;

class UserController extends MainController
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



    public function actionIndex()
    {


        $userDataModel = new UserDataModel();

        $users_data = $userDataModel->getUsersArray();

        if($users_data['status'] === true ){
            return $this->render('index',[
                'users' => $users_data['data'] ,
            ]);
        }else{
            return $this->render('index',[
                'users' => '' ,
                'error' => $users_data['error']
            ]);
        }

    }

    public function actionCreate()
    {
        if( Yii::$app->request->isPjax ){
            $userDataModel = new UserDataModel();

            $newUser = $userDataModel->addNewUser();

            $usersArray = $userDataModel->getUsersArray();


            if( $usersArray['status'] === true ){
                return $this->renderAjax('index',[
                    'users' => $usersArray['data'],
                    'new_user' => $newUser
                ]);
            }else{
                return $this->renderAjax('index',[
                    'users' => '',
                    'new_user' => $newUser,
                    'error' => $usersArray['error'],
                ]);
            }

        }
        throw new NotFoundHttpException('Hey! You cant do that!');


    }

    public function actionView()
    {
        if (Yii::$app->request->isAjax) {


            $data = Yii::$app->request->post();


            if( isset( $data['user_id'] ) and !empty( $data['user_id'] )   and
                isset( $data['user_token'] ) and !empty( $data['user_token'] ) ){

                $UserDataModel = new UserDataModel();


                $user_data = $UserDataModel->getUserProfile( $data['user_id'], $data['user_token'] );




                if( $user_data['status'] === true){

                    $avatar_base64 = false;

                    if( isset($user_data['data']["avatarId"]) and !empty($user_data['data']["avatarId"]) ) {
                        $model = new AvatarDataModel();

                        $avatar_base64 = $model->getAvatar($user_data['data']["avatarId"], $data['user_token'], 'original' );

                    }

                    return $this->renderAjax('modal-edit-user',[
                        'full_user_data' => $user_data['data'],
                        'token' => $data['user_token'],
                        'avatar' => $avatar_base64
                    ]);
                }else{
                    return 'ERROR!' . $user_data['error'];
                }


            }

        }

        return false;


    }

    public function actionUpdate()
    {

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();


            $return_response = false;

            if( isset($data['id']) and !empty($data['id']) and
                isset($data['token']) and !empty($data['token']) ){

                $token = $data['token'];
                $UserDataModel = new UserDataModel();

                //unset($data['id']);
                //unset($data['token']);
               /* if( isset($data['phoneNumber']) ){
                    unset($data['phoneNumber']);
                }*/
                if( isset($data['visible']) ){
                    if( $data['visible'] == 1){
                        $data['visible'] = true;
                    }else{
                        $data['visible'] = false;
                    }
                }

                $update_return = $UserDataModel->updateUserProfile( $token, $data );


                if( $update_return['status'] === true ){
                    $return_response['status_fields'] = true;
                    $return_response['data_fields'] =  $update_return['data'];
                }else{
                    $return_response['status_fields'] = false;
                    $return_response['error_fields'] =  $update_return['error'] . ' CODE: ' . $update_return['http_code'] . ' RESPONSE:' . $update_return['data'] ;
                }



            }

            if( isset( $_FILES['image']['tmp_name'] ) and !empty( $_FILES['image']['tmp_name'] )
                and
                isset( $data['token'] ) and !empty( $data['token'] ) ) {


                $model = new AvatarDataModel();
                $avatar = $model->setAvatar($_FILES['image']['tmp_name'] ,  $data['token'] );


                if( $avatar['status'] === true ){
                    $return_response['status_avatar'] = true;
                    $return_response['data_avatar'] =  $avatar['data'];
                }else{
                    $return_response['status_avatar'] = false;
                    $return_response['error_avatar'] =  $avatar['error'];
                }

            }



            if(is_array($return_response)){
                $return_response = json_encode($return_response);
            }

            return $return_response;
        }

        return  false;

    }



}
