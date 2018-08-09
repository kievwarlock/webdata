<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Datausers;
use yii\base\ErrorException;


class UserDataModel extends  ServerModel {

    private $path_auth_sms = '/auth/activation-code/';
    private $path_auth_token = '/auth/token/';
    private $number_format =  '380930000002';


    public function getUsersArray(){

        $return_data = $this->curlRequest( $this->SERVER_PROTECTED_ADRESS,"/person-auth-details" );

        if( is_array($return_data) and $return_data['status'] === true ){
            $return_data['data'] = json_decode($return_data['data'], TRUE);
        }

        return $return_data;


    }

    public function generatePhoneNumber ($users_count  = 0) {


        if( $users_count ){
            $return_number = $this->number_format + $users_count;
        }else{
            $return_number = $this->number_format;
        }
        return $return_number;

    }

    public function getSmsCode( $number ) {

        if( !$number){
            return false;
        }


        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            $this->path_auth_sms,
            '',
            'POST',
            $number
        );

        if( is_array($return_data) and $return_data['status'] === true ){
            return $return_data['data'];
        }
        return false;



    }

    public function addNewUser( $oldPhoneNumber = false ){

        $usersArray = $this->getUsersArray();

        if( !is_array($usersArray) and $usersArray['status'] === false ) return false;

        $new_phone_number =  $this->generatePhoneNumber( count($usersArray['data']) );


        if( $oldPhoneNumber ){
            $new_phone_number = $oldPhoneNumber;
        }
        if( $new_phone_number ){
            $sms_code = $this->getSmsCode($new_phone_number);


            if( $sms_code ){

                $token = $this->getUserToken($new_phone_number, $sms_code );

                if( $token ){

                    $profile_id =  $this->createUserProfile($token);

                    if( isset( $profile_id ) and !empty($profile_id) ){
                        return $profile_id;
                    }

                }

            }
        }

        return false;
    }
    
    public function getUserToken( $number, $code ) {

        if( !$number and !$code){
            return false;
        }

        $data = array(
            'phoneNumber' => $number,
            'activationCode' => $code,
        );
        $data = json_encode($data);

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            $this->path_auth_token,
            '',
            'POST',
            $data
        );

        if( is_array($return_data) and $return_data['status'] === true ){
            return $return_data['data'];
        }
        return false;


    }
    
    public function updateUserProfile( $token, $data ){


        if( !$token and !$data ){
            return false;
        }

        $new_data = json_encode($data);

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            "/profile/",
            $token,
            'POST',
            $new_data
        );

        if( is_array($return_data) and $return_data['status'] === true ){
            $return_data['data'] = json_decode( $return_data['data'], TRUE );
            return $return_data;
        }

        return false;


    }

    public function createUserProfile( $token ){

        // RETURN PROFILE ID
        if( !$token ){
            return false;
        }

        $data = array(
            'fullName' => '',
            'nickname' => '',
            'locale' => '',
            'city' => '',
        );
        $data = json_encode($data);

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            "/profile/",
            $token,
            'POST',
            $data
        );

        if( is_array($return_data) and $return_data['status'] === true ){
            return  json_decode( $return_data['data'], TRUE );
        }

        return false;

    }


    public function getUserProfile($id, $token) {

        if( !$id and !$token){
            return false;
        }

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            '/profile/' . $id,
            $token
        );

        if( is_array($return_data) and $return_data['status'] === true ){
             $return_data['data'] = json_decode( $return_data['data'], TRUE );
        }

        // 404 - not found profile . And if 404 - try created profile
        if( $return_data['http_code'] == 404  ){
            $profile_id =  $this->createUserProfile($token);
            if( $profile_id ){
                return $this->getUserProfile($profile_id['id'],$token );
            }
        }

        return $return_data;



    }


}
