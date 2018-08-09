<?php
/**
 * Created by PhpStorm.
 * User: gemmi
 * Date: 26.07.2018
 * Time: 11:26
 */

namespace app\components;

use yii\base\Widget;

class ProfileFormWidget extends Widget
{
    public $data;
    public $user_profile_form = [
        'id' => [
            'hidden' => false,
            'type'   => 'hidden',
            'label'  => 'ID'
        ],
        'phoneNumber' => [
            'hidden' => false,
            'type'   => 'hidden',
            'label'  => 'Phone'
        ],
        'avatarId' => [
            'hidden' => false,
            'type'   => 'text',
            'label'  => 'Avatar'
        ],
        'fullName' => [
            'hidden' => false,
            'type'   => 'text',
            'label'  => 'User Name'
        ],
        'locale' => [
            'hidden' => false,
            'type'   => 'select',
            'label'  => 'Locale',
            'value' =>[
                '' => 'Chose locale',
                'de' => 'DE',
                'us' => 'US',
                'fr' => 'FR',
                'es' => 'ES',
                'it' => 'IT',
                'ru' => 'RU',
            ],
        ],
        'city' => [
            'hidden' => false,
            'type'   => 'text',
            'label'  => 'City'
        ],
        'visible' => [
            'hidden' => false,
            'type'   => 'select',
            'label'  => 'Visible status',
            'value' =>[
                'fff' => 'Chose status',
                '1' => 'Visible',
                '0' => 'Hidden',
            ],
        ]
    ];




    private function generateFormTemplate( $data, $user_profile_form ){

        if(!is_array($user_profile_form) or !is_array($data)) return false;

        $return_html = '';



        foreach ($data as $user_item_key => $user_item_value  ) {

            if ($user_profile_form[$user_item_key]) {

                switch ($user_profile_form[$user_item_key]['type']) {


                    case 'hidden':

                        if ($user_profile_form[$user_item_key]['hidden'] === false) {
                            $return_html .=  '
                            <input type="hidden" name="' . $user_item_key . '"   value="' . $user_item_value  . '" id="' .  $user_item_key  . '">
                            ';
                        }

                        break;


                    case 'text':

                        if ($user_profile_form[$user_item_key]['hidden'] === false) {

                          $return_html .=  '
                            <div class="form-group">
                                <label for="locale">' . $user_profile_form[$user_item_key]['label'] . '</label>
                                <input type="text" name="' . $user_item_key . '" class="form-control"
                                       value="' . $user_item_value  . '" id="' .  $user_item_key  . '"
                                       placeholder="' . $user_profile_form[$user_item_key]['label']  . '">
                            </div>';

                        }

                        break;

                    case 'checkbox':

                        if ($user_profile_form[$user_item_key]['hidden'] === false) {
                            $checked = ($user_item_value == 1) ? 'checked' : '';
                            $return_html .='
                            <div class="form-group">
                                <label for="' . $user_item_key . '">' . $user_profile_form[$user_item_key]['label'] . '
                                    <input type="checkbox" name="'. $user_item_key . '" value="true"
                                           id="' .  $user_item_key . '" '. $checked .' >
                                </label>
                            </div>';

                        }
                        break;




                    case 'image':

                        if ($user_profile_form[$user_item_key]['hidden'] === false) {
                            $checked = ($user_item_value == 1) ? 'checked' : '';
                            $return_html .='
                            <div class="form-group">
                                <label for="' . $user_item_key . '">' . $user_profile_form[$user_item_key]['label'] . '
                                    <input type="checkbox" name="'. $user_item_key . '" value="true"
                                           id="' .  $user_item_key . '" '. $checked .' >
                                </label>
                            </div>';

                        }
                        break;


                    case 'select':



                        if ($user_profile_form[$user_item_key]['hidden'] === false) {
                            $return_html .='
                            <div class="form-group custom-select-block">
                                <label for="' .  $user_item_key . '">' .  $user_profile_form[$user_item_key]['label'] . '
                                    <br>                                 
                                    <select name="' .  $user_item_key .'" id="' .  $user_item_key . '">';

                                        //$return_html .='<option value=""  >' . $user_profile_form[$user_item_key]['label']  . '</option>';

                                        foreach ($user_profile_form[$user_item_key]['value'] as $select_key => $select_value) {


                                            if ( $select_key == $user_item_value) {
                                                $selected = 'selected';
                                            } else {
                                                $selected = '';
                                            }

                                            $return_html .=' 
                                            <option value="' .  $select_key . '" '.  $selected .' >
                                                '. $select_value .'
                                            </option>';
                                        }
                                    $return_html .='
                                    </select>

                                </label>
                            </div>';

                        }

                        break;

                    default:

                        break;

                }

            }
        }

        return $return_html;

    }

    public function run(){
        return $this->generateFormTemplate($this->data, $this->user_profile_form);
    }
}