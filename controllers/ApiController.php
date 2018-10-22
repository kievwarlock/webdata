<?php

namespace app\controllers;


use app\models\UserDataModel;
use yii\base\ErrorException;
use yii\web\Response;


class ApiController extends MainController
{
    /**
     * {@inheritdoc}
     */
    public $enableCsrfValidation = false;
    private $auth_token = '3bad8293-cb72-454f-a52c-9f9aa3d2e3cc';
    private $max_count_users = 20;
    private $min_count_users = 1;


    public function behaviors()
    {
        return [

            // For cross-domain AJAX request
            'corsFilter'  => [
                'class' => \yii\filters\Cors::className(),
                'cors'  => [
                    // restrict access to domains:
                    'Origin'                           => ['*'],
                    'Access-Control-Request-Method'    => ['GET'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age'           => 3600,                 // Cache (seconds)
                ],
            ],

        ];
    }




    public function actionGetUsers( $count = 10 )
    {
        $return_array_data = [
            'status' => false,
            'data' => '',
            'error' => ''
        ];

        if( is_numeric( $count ) and  $count >= $this->min_count_users and $count <= $this->max_count_users ){


            if( \Yii::$app->request->isGet ){

                $headers = apache_request_headers();

                if( isset($headers['Authorization']) and $headers['Authorization'] == $this->auth_token ){
                    $userDataModel = new UserDataModel();
                    $users_data = $userDataModel->getUsersArray();

                    if($users_data['status'] === true ){

                        $users_array = array_slice( $users_data['data'], 0 , $count);

                        $new_user_data_array = array();
                        foreach ($users_array as $user_item) {

                            $user_data = $userDataModel->getUserProfile( $user_item['id'], $user_item['token'] );
                            if( $user_data['status'] === true){
                                $new_user_data_array[] = [
                                    'id'            => $user_item['id'],
                                    'phoneNumber'   => $user_item['phoneNumber'],
                                    'fullName'      => $user_data['data']['fullName'],
                                    'city'          => $user_data['data']['city'],
                                ];
                            }

                        }
                        if( is_array($new_user_data_array) and count($new_user_data_array) > 0 ){
                            $return_array_data['status'] = $users_data['status'];
                            $return_array_data['data'] = $new_user_data_array ;
                        }else{
                            $return_array_data['status'] = false;
                            $return_array_data['error'] = 'Data is empty';
                        }

                    }else{

                        $return_array_data['status'] = $users_data['status'];
                        $return_array_data['error'] =  $users_data['error'];

                    }

                }else{
                    $return_array_data['error'] = 'Authorization token error';
                }



            }

        }else{
            $return_array_data = [
                'status' => false,
                'error' => 'Count of users range from ' . $this->min_count_users . ' to ' . $this->max_count_users,
            ];
        }


        \Yii::$app->response->format = Response::FORMAT_JSON;

        return $return_array_data;


    }




}
