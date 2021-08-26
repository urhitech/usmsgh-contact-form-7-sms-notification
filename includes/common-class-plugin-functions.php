<?php
if ( ! defined( 'WPINC' ) ) { die; }

class Contact_Form_Sms_Integration_abn_Functions {

    public function __construct() {
        add_action( 'wpcf7_before_send_mail', array($this, 'configure_send_sms' ) );
    }

    public function get_cf7_tagS_To_String($value,$form){
        if(function_exists('wpcf7_mail_replace_tags')) {
            $return = wpcf7_mail_replace_tags($value);
        } elseif(method_exists($form, 'replace_mail_tags')) {
            $return = $form->replace_mail_tags($value);
        } else {
            return;
        }
        return $return;
    }

    public function configure_send_sms( $form ) {
        $options = get_option( 'wpcf7_international_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id)) ;
        $sendToAdmin = false;
        $sendToVisitor = false;
        $adminNumber = '';
        $adminMessage = '';
        $visitorNumber = '';
        $visitorMessage = '';

        if(isset($options['phone']) && $options['phone'] != '' && isset($options['message']) && $options['message'] != ''){
            $adminNumber = $this->get_cf7_tagS_To_String($options['phone'],$form);
            $adminMessage = $this->get_cf7_tagS_To_String($options['message'],$form);
            $sendToAdmin = true;
        }


        if(isset($options['visitorNumber']) && $options['visitorNumber'] != '' &&
            isset($options['visitorMessage']) && $options['visitorMessage'] != ''){

            $visitorNumber = $this->get_cf7_tagS_To_String($options['visitorNumber'],$form);
            $visitorMessage = $this->get_cf7_tagS_To_String($options['visitorMessage'],$form);
            $sendToVisitor = true;
        }

        if($sendToAdmin){
            $ADMINSEND = $this->send_sms($adminNumber,$adminMessage);
            if($ADMINSEND){
                $save_db = array();
                $send_res = $ADMINSEND['body'];
                $save_db['response'] = $send_res;
                $save_db['formID'] = method_exists($form, 'id') ? $form->id() : $form->id;
                $save_db['formNAME'] = method_exists($form, 'name') ? $form->name() : $form->name;
                $save_db['datetime'] = date("Y-m-d H:i:s");
                $save_db['message'] = $adminMessage;
                $save_db['to'] = $adminNumber;
                $save_db['type'] = 'admin';
                $save_db['ID'] = time().rand(0,1000);
                $this->save_history($save_db);
            }
        }

        if($sendToVisitor) {
            $visitorSEND = $this->send_sms($visitorNumber, $visitorMessage);
            if($visitorSEND) {
                if(! is_wp_error($response)){
                    $save_db = array();
                    $send_res = $visitorSEND['body'];
                    $save_db['response'] = $send_res;
                    $save_db['formID'] = method_exists($form, 'id') ? $form->id() : $form->id;
                    $save_db['formNAME'] = method_exists($form, 'name') ? $form->name() : $form->name;
                    $save_db['datetime'] = date("Y-m-d H:i:s");
                    $save_db['message'] = $visitorMessage;
                    $save_db['to'] = $visitorNumber;
                    $save_db['type'] = 'visitor';
                    $save_db['ID'] = time().rand(0,1000);
                    $this->save_history($save_db);
                }

                if(is_wp_error($response)){
                    $save_db = array();
                    $save_db['response'] = json_encode($visitorSEND);
                    $save_db['formID'] = method_exists($form, 'id') ? $form->id() : $form->id;
                    $save_db['formNAME'] = method_exists($form, 'name') ? $form->name() : $form->name;
                    $save_db['datetime'] = date("Y-m-d H:i:s");
                    $save_db['message'] = $visitorMessage;
                    $save_db['to'] = $visitorNumber;
                    $save_db['type'] = 'visitor';
                    $save_db['ID'] = time().rand(0,1000);
                    $this->save_history($save_db);
                }
            }
        }
    }

    /** Sending messages
     * @param $phone
     * @param $message
     * @return false
     */
    public function send_sms($phone, $message) {
        $url = 'https://webapp.usmsgh.com/api/sms/send';
        $pattern = '/^0/';
        $api_token = get_option(Contact_FormSI_DB_SLUG.'api_token','');
        $sender_id = get_option(Contact_FormSI_DB_SLUG.'sender_id','');
        $country =	get_option(Contact_FormSI_DB_SLUG.'country','');
        $country_code =	get_option(Contact_FormSI_DB_SLUG.'country_code','');
        $reg_phone =	get_option(Contact_FormSI_DB_SLUG.'reg_phone','');

        $phone_number = preg_replace($pattern, $country_code, $phone);

        if (empty($sender_id)) {
            $sender_id = $reg_phone;
        }

        if (!empty($api_token) && !empty($sender_id)) {
            $phone = explode(',', $phone);;
            if (count($phone) > 1) {
                $phone_numbers = [];
                foreach ($phone as $item) {
                    $phone_numbers[] = $item;
                }
                self::sms_group_config($url, $api_token, $sender_id, $phone_numbers, $message);
            } else
                $this->send_sms_conf($url, $api_token, $sender_id, $phone_number, $message);
        }
        return false;
    }

    public function save_history($data){
        $array = get_option( 'wpcf7is_history');
        if(empty($array)){$array = array(); }
        $array[$data['ID']] = $data;
        update_option('wpcf7is_history',$array);
    }


    /** Single Messages configuration
     * @param $url
     * @param $api_token
     * @param $recipient
     * @param $message
     * @param $sender_id
     * @return mixed
     *
     * Send single SMS
     */
    public function send_sms_conf($url, $api_token, $sender_id, $recipient, $message) {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'recipient' => $recipient,
                'sender_id' => $sender_id,
                'message' =>  $message
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                "accept: application/json",
                "authorization: Bearer ".$api_token,
            ],
        ]);

        $resp = curl_exec($ch);
        if ($e = curl_error($ch)) {
            echo $e;
        } else {}
    }


    /** Bulk / Group Messages configuration
     * @param $url
     * @param $api_token
     * @param $sender_id
     * @param $recipients
     * @param $message
     */
    public static function sms_group_config($url, $api_token, $sender_id, $recipients, $message)
    {
        foreach ($recipients as $key => $recipient) {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL            => $url,
                    CURLOPT_POST           => true,
                    CURLOPT_POSTFIELDS     => json_encode([
                        'recipient' => $recipient,
                        'sender_id' => $sender_id,
                        'message'   => $message
                    ]),
                    CURLOPT_RETURNTRANSFER => true,

                    CURLOPT_HTTPHEADER => [
                        "accept: application/json",
                        "authorization: Bearer ".$api_token
                    ],
                ]);

                $resp = curl_exec($ch);

                if ($e = curl_error($ch)) {
                    echo $e;
                } else {
                    print_r( json_decode($resp, true));
                    curl_close($ch);
                }
                curl_close($ch);
            }
    }
}

/** Helper function for checking bugs or getting useful info.
 * @param $data
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die;
}