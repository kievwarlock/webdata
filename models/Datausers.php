<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "datausers".
 *
 * @property int $id
 * @property int $profile_id
 * @property string $phone
 * @property string $token
 * @property string $fullname
 * @property string $nickname
 * @property string $locale
 * @property string $city
 */
class Datausers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'datausers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['phone', 'token', 'fullname', 'nickname', 'locale', 'city'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'profile_id' => 'Profile id',
            'phone' => 'Phone',
            'token' => 'Token',
            'fullname' => 'Fullname',
            'nickname' => 'Nickname',
            'locale' => 'Locale',
            'city' => 'City',
        ];
    }

    public function getUsers(){
        return $this->authKey;
    }
}
