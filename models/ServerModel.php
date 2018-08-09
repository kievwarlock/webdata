<?php
namespace app\models;



class ServerModel  {

    protected $SECURITY_USER_NAME = 'admin';
    protected $SECURITY_USER_PASSWORD = '8C87cDm78ps9mTCE';
    protected $SERVER_PROTECTED_ADRESS = 'http://stage.gether.work:8012'; //'http://159.69.82.236:8012';
    protected $SERVER_PUBLIC_ADRESS = 'http://stage.gether.work:8010'; //'http://159.69.82.236:8010';



    private function getAuthorizationBasic(){
        return base64_encode( $this->SECURITY_USER_NAME . ':' . $this->SECURITY_USER_PASSWORD );
    }

    protected function curlRequest(  $SERVER_ADRESS, $END_POINT, $token = '', $CURLOPT_CUSTOMREQUEST = 'GET' , $data = '' ){

        $return= [
            'status' => false,
            'data' => '',
            'error' => '',
            'http_code' => '',
        ];

        if( !$SERVER_ADRESS and  !$END_POINT ){
            return $return= [
                'status' => false,
                'data' => '',
                'error' => 'No found SERVER_ADRESS or END_POINT',
                'http_code' => '0',
            ];
        }
        if( $CURLOPT_CUSTOMREQUEST == 'POST' and !$data ){
            return $return= [
                'status' => false,
                'data' => '',
                'error' => 'No found data',
                'http_code' => '0',
            ];
        }

        $CURLOPT_HTTPHEADER_ARRAY = array(
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        );

        if( $SERVER_ADRESS == $this->SERVER_PROTECTED_ADRESS  ){
            $CURLOPT_HTTPHEADER_ARRAY[] = "Authorization: Basic " . $this->getAuthorizationBasic();
        }elseif ( $SERVER_ADRESS == $this->SERVER_PUBLIC_ADRESS  ){
            $CURLOPT_HTTPHEADER_ARRAY[] = "X-Auth-Token:" . $token;
        }

        $CURLOPT_ARRAY = array(
            CURLOPT_URL => $SERVER_ADRESS . $END_POINT ,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT_MS => 3000,
            CURLOPT_CUSTOMREQUEST => $CURLOPT_CUSTOMREQUEST,
            CURLOPT_HTTPHEADER => $CURLOPT_HTTPHEADER_ARRAY,
        );

        if( $data ){
            $CURLOPT_ARRAY[CURLOPT_POSTFIELDS] = $data;
        }


        $curl = curl_init();
        curl_setopt_array($curl, $CURLOPT_ARRAY);


        $response = curl_exec($curl);
        $return['http_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);


        curl_close($curl);


        if ( $return['http_code'] == 200 ) {
            $return['status'] = true;
            $return['data'] = $response;
        }else{
            $return['status'] = false;
            $return['error'] = $error . ' CODE: ' . $return['http_code'];
        }

        return $return;


    }



}
