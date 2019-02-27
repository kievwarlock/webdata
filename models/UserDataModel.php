<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Datausers;
use yii\base\ErrorException;


class UserDataModel extends ServerModel
{

    private $path_auth_code = '/auth/activation-code/';
    private $path_auth_login = '/auth/login/';
    private $path_auth_register = '/auth/register/';
    private $path_auth_token = '/auth/token/';
    private $number_format = '380930000001';


    public function getUsersArray()
    {

        $return_data = $this->curlRequest($this->SERVER_PROTECTED_ADRESS, "/person-auth-details");

        if (is_array($return_data) and $return_data['status'] === true) {
            $return_data['data'] = json_decode($return_data['data'], TRUE);
        }

        return $return_data;


    }

    public function generatePhoneNumber($users_count = 0)
    {


        if ($users_count) {
            $return_number = $this->number_format + $users_count;
        } else {
            $return_number = $this->number_format;
        }
        return $return_number;

    }

    public function getSmsCode($number)
    {

        if (!$number) {
            return false;
        }
        $data = array(
            'phoneNumber' => $number,
        );
        $data = json_encode($data);

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            $this->path_auth_code,
            '',
            'POST',
            $data
        );


        if (is_array($return_data) and $return_data['status'] === true) {
            $response_data = json_decode($return_data['data'], true);
            if (isset($response_data["activationCode"]) and !empty($response_data["activationCode"])) {
                return $response_data["activationCode"];
            }
        }
        return false;


    }

    public function addNewUser($oldPhoneNumber = false , $data = false)
    {

        $new_phone_number = false;
        if (!$oldPhoneNumber) {
            $usersArray = $this->getUsersArray();

            if (!is_array($usersArray) and $usersArray['status'] === false) return false;

            $new_phone_number = $this->generatePhoneNumber(count($usersArray['data']));

        }


        if ($oldPhoneNumber) {
            $new_phone_number = $oldPhoneNumber;
        }

        if ($new_phone_number) {
            $sms_code = $this->getSmsCode($new_phone_number);


            if ($sms_code) {

                $token = $this->userRegistration($new_phone_number, $sms_code, $data);

                if ($token) {
                    $token_array = explode(':', $token);
                    $user_data['id'] = $token_array[0];
                    $user_data['token'] = $token;
                    return $user_data;
                }

            }
        }

        return false;
    }


    public function userLogin($number, $code)
    {

        if (!$number and !$code) {
            return false;
        }

        $data = array(
            'phoneNumber' => $number,
            'activationCode' => $code,
        );
        $data = json_encode($data);

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            $this->path_auth_login,
            '',
            'POST',
            $data
        );

        if (is_array($return_data) and $return_data['status'] === true) {
            $response_data = json_decode($return_data['data'], true);
            if (isset($response_data["authToken"]) and !empty($response_data["authToken"])) {
                return $response_data["authToken"];
            }
        }
        return false;

    }

    public function getLocaleList(){
        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            '/locale',
            '',
            'GET'
        );
        if (is_array($return_data) and $return_data['status'] === true) {
            $response_data = json_decode($return_data['data'], true);
            return $response_data;
        }
        return false;
    }

    public function userRegistration($number, $code , $data) {

        if (!$number and !$code) {
            return false;
        }

        $locale = $this->getLocaleList();

        if( !$data ){
            $data = array(
                'phoneNumber' => $number,
                'activationCode' => $code,
                'city' => 'Kiev',
                'fullName' => 'Test',
                'locale' => $locale[0]
            );
        }else{
            $data['phoneNumber'] = $number;
            $data['activationCode'] = $code;
        }


        $data = json_encode($data);


        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            $this->path_auth_register,
            '',
            'POST',
            $data
        );


        if (is_array($return_data) and $return_data['status'] === true) {
            $response_data = json_decode($return_data['data'], true);
            if (isset($response_data["authToken"]) and !empty($response_data["authToken"])) {
                return $response_data["authToken"];
            }
        }
        return false;
    }

    /*public function getUserToken( $number, $code ) {

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
            $response_data = json_decode($return_data['data'], true );
            if( isset( $response_data["authToken"] ) and !empty( $response_data["authToken"] ) ) {
                return $response_data["authToken"];
            }
        }
        return false;

    }
    */

    public function updateProfileCoordinates($token,$lat, $lon )
    {


        if ( !$token and !$lat and !$lon) {
            return false;
        }
        $data = [
            'latitude' => $lat,
            'longitude' => $lon,
        ];
        $profileCoordinates = json_encode($data);

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            "/profile/coordinates/",
            $token,
            'PUT',
            $profileCoordinates
        );

        if (is_array($return_data) and $return_data['status'] === true) {
            $return_data['data'] = json_decode($return_data['data'], TRUE);
            return $return_data;
        }

        return false;


    }

    public function updateProfileVisibility($token, $status )
    {


        if ( !$token and !is_bool($status) ) {
            return false;
        }
        $data = [
            'value' => $status,
        ];
        $statusData = json_encode($data);

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            "/profile/visibility/",
            $token,
            'PUT',
            $statusData
        );

        if (is_array($return_data) and $return_data['status'] === true) {
            $return_data['data'] = json_decode($return_data['data'], TRUE);
            return $return_data;
        }

        return false;


    }

    public function updateUserProfile($token, $data)
    {


        if (!$token and !$data) {
            return false;
        }
        $data['topicIds'] = [];
        $new_data = json_encode($data);

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            "/profile/",
            $token,
            'PUT',
            $new_data
        );

        if (is_array($return_data) and $return_data['status'] === true) {
            $return_data['data'] = json_decode($return_data['data'], TRUE);
            return $return_data;
        }

        return false;


    }

    public function createUserProfile($token)
    {

        // RETURN PROFILE ID
        if (!$token) {
            return false;
        }

        $data = array(
            'fullName' => 'Draft',
            //'nickname' => '',
            'locale' => 'RU',
            'city' => 'Kiev',
        );
        $data = json_encode($data);

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            "/profile/",
            $token,
            'POST',
            $data
        );

        if (is_array($return_data) and $return_data['status'] === true) {
            return json_decode($return_data['data'], TRUE);
        }

        return false;

    }


    public function getUserProfile($id, $token)
    {

        if (!$id and !$token) {
            return false;
        }

        $return_data = $this->curlRequest(
            $this->SERVER_PUBLIC_ADRESS,
            '/profile/' . $id,
            $token
        );

        if (is_array($return_data) and $return_data['status'] === true) {
            $return_data['data'] = json_decode($return_data['data'], TRUE);
        }

        // 404 - not found profile . And if 404 - try created profile
        if ($return_data['http_code'] == 404) {
            $profile_id = $this->createUserProfile($token);
            if ($profile_id) {
                return $this->getUserProfile($profile_id['id'], $token);
            }
        }

        return $return_data;


    }


}
