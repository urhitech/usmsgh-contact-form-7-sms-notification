<?php
if (!defined('WPINC')) {
    die;
}

include( plugin_dir_path( __FILE__ ) . '..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');
use Urhitech\Usms;

class USMSGH_Contact_Form_Sms_Notification_abn_Functions
{
    static $curl_handle = NULL;
    const API_URL = "https://webapp.usmsgh.com/api/sms/send/";
    const API_BAL_URI = 'https://webapp.usmsgh.com/api/balance';


    public function __construct()
    {
        add_action('wpcf7_before_send_mail', array($this, 'configure_send_sms'));
    }

    public function get_cf7_tagS_To_String($value, $form)
    {
        if (function_exists('wpcf7_mail_replace_tags')) {
            $return = wpcf7_mail_replace_tags($value);
        } elseif (method_exists($form, 'replace_mail_tags')) {
            $return = $form->replace_mail_tags($value);
        } else {
            return;
        }
        return $return;
    }

    public function configure_send_sms($form)
    {
        $options = get_option('wpcf7_international_sms_' . (method_exists($form, 'id') ? $form->id() : $form->id));
        $sendToAdmin = false;
        $sendToVisitor = false;
        $adminNumber = '';
        $adminMessage = '';
        $visitorNumber = '';
        $visitorMessage = '';

        if (isset($options['phone']) && $options['phone'] != '' && isset($options['message']) && $options['message'] != '') {
            $adminNumber = $this->get_cf7_tagS_To_String($options['phone'], $form);
            $adminMessage = $this->get_cf7_tagS_To_String($options['message'], $form);
            $sendToAdmin = true;
        }


        if (
            isset($options['visitorNumber']) && $options['visitorNumber'] != '' &&
            isset($options['visitorMessage']) && $options['visitorMessage'] != ''
        ) {

            $visitorNumber = $this->get_cf7_tagS_To_String($options['visitorNumber'], $form);
            $visitorMessage = $this->get_cf7_tagS_To_String($options['visitorMessage'], $form);
            $sendToVisitor = true;
        }

        if ($sendToAdmin) {
            $ADMINSEND = $this->send_sms($adminNumber, $adminMessage);
            if ($ADMINSEND) {
                $save_db = array();
                $send_res = $ADMINSEND['body'];
                $save_db['response'] = $send_res;
                $save_db['formID'] = method_exists($form, 'id') ? $form->id() : $form->id;
                $save_db['formNAME'] = method_exists($form, 'name') ? $form->name() : $form->name;
                $save_db['datetime'] = date("Y-m-d H:i:s");
                $save_db['message'] = $adminMessage;
                $save_db['to'] = $adminNumber;
                $save_db['type'] = 'admin';
                $save_db['ID'] = time() . rand(0, 1000);
                $this->save_history($save_db);
            }
        }

        if ($sendToVisitor) {
            $visitorSEND = $this->send_sms($visitorNumber, $visitorMessage);
            if ($visitorSEND) {
                if (!is_wp_error($response)) {
                    $save_db = array();
                    $send_res = $visitorSEND['body'];
                    $save_db['response'] = $send_res;
                    $save_db['formID'] = method_exists($form, 'id') ? $form->id() : $form->id;
                    $save_db['formNAME'] = method_exists($form, 'name') ? $form->name() : $form->name;
                    $save_db['datetime'] = date("Y-m-d H:i:s");
                    $save_db['message'] = $visitorMessage;
                    $save_db['to'] = $visitorNumber;
                    $save_db['type'] = 'visitor';
                    $save_db['ID'] = time() . rand(0, 1000);
                    $this->save_history($save_db);
                }

                if (is_wp_error($response)) {
                    $save_db = array();
                    $save_db['response'] = json_encode($visitorSEND);
                    $save_db['formID'] = method_exists($form, 'id') ? $form->id() : $form->id;
                    $save_db['formNAME'] = method_exists($form, 'name') ? $form->name() : $form->name;
                    $save_db['datetime'] = date("Y-m-d H:i:s");
                    $save_db['message'] = $visitorMessage;
                    $save_db['to'] = $visitorNumber;
                    $save_db['type'] = 'visitor';
                    $save_db['ID'] = time() . rand(0, 1000);
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
    public function send_sms($phone, $message)
    {
        $endpoint = self::API_URL;
        $pattern = '/^0/';
        $api_token = get_option(Contact_FormSI_DB_SLUG . 'api_token', '');
        $sender_id = get_option(Contact_FormSI_DB_SLUG . 'sender_id', '');
        $country =    get_option(Contact_FormSI_DB_SLUG . 'country', '');
        $country_code =    get_option(Contact_FormSI_DB_SLUG . 'country_code', '');
        $reg_phone =    get_option(Contact_FormSI_DB_SLUG . 'reg_phone', '');

        if (empty($country_code)) $country_code = '233';

        $phone_number = preg_replace($pattern, $country_code, $phone);

        if (empty($sender_id)) $sender_id = $reg_phone;

        if (!empty($api_token) && !empty($sender_id)) {
            $sms = new Urhitech\Usms();

            $phone = explode(',', $phone_number);

//            $sms->send_sms($endpoint, $api_token, $sender_id, $phone_number, $message);

            if (count($phone) > 1) {
                $sms->send_sms($endpoint, $api_token, $sender_id, $phone, $message);
            } else {
                $sms->send_sms($endpoint, $api_token, $sender_id, $phone_number, $message);
            }
        }
        return false;
    }

    public function save_history($data)
    {
        $array = get_option('wpcf7is_history');
        if (empty($array)) {
            $array = array();
        }
        $array[$data['ID']] = $data;
        update_option('wpcf7is_history', $array);
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
     *
     * Error [body] => {"status":"error","message":"CSRF token mismatch."}
     */

    public function sms_group_config($endpoint, $api_token, $sender_id, $recipients, $message)
    {
        foreach ($recipients as $recipient) {
            $body = [
                'recipient' => $recipient,
                'sender_id' => $sender_id,
                'message' =>  $message
            ];

            $body = wp_json_encode( $body );

            $options = [
                'body'        => $body,
                'headers'     => [
                    'Authorization' => 'Bearer ' . $api_token,
                    'Accept' => 'application/json',
                ],
                'timeout'     => 60,
                'redirection' => 5,
                'blocking'    => true,
                'httpversion' => '1.0',
                'sslverify'   => false,
                'data_format' => 'body',
            ];

            //dd($options);

            $response = wp_remote_post( $endpoint, $options );

            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                echo "Something went wrong: $error_message";
            } else {
                echo 'Response:<pre>';
                print_r( $response );
                echo '</pre>';
            }
        }
        return false;
    }

    public function send_sms_conf($endpoint, $api_token, $sender_id, $recipient, $message) {
        $body = [
            'recipient' => $recipient,
            'sender_id' => $sender_id,
            'message' =>  $message
        ];

        $body = wp_json_encode( $body );

        $options = [
            'body'        => $body,
            'headers'     => [
                'Authorization' => 'Bearer ' . $api_token,
                'Accept' => 'application/json',
            ],
            'timeout'     => 60,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.0',
            'sslverify'   => false,
            'data_format' => 'body',
        ];

        $response = wp_remote_post( $endpoint, $options );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
        } else {
            echo 'Response:<pre>';
            print_r( $response );
            echo '</pre>';
        }
    }

    /** Bulk / Group Messages configuration
     * @param $url
     * @param $api_token
     * @param $sender_id
     * @param $recipients
     * @param $message
     */
    public function sms_group_config1($url, $api_token, $sender_id, $recipients, $message)
    {
        foreach ($recipients as $key => $recipient) {
            if ($this::curl_installed()) {
                //curl is installed and we can use it

                //Initialize the curl handle if it is not initialized yet
                if (!isset($this::$curl_handle)) {
                    $this::$curl_handle = curl_init();
                }

                // Copy it and fill it with your parameters
                $ch = curl_copy_handle($this::$curl_handle);
                curl_setopt($ch, CURLOPT_URL, $url);

                // Encode payload and set post body
                $data_string = json_encode([
                    'recipient' => $recipient,
                    'sender_id' => $sender_id,
                    'message' =>  $message
                ]);

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

                // Set the user agent which tells you the WordPress and php version and that curl is used
                // This will help you when debugging problems
                curl_setopt($ch, CURLOPT_USERAGENT, 'Curl/WordPress/'.$wp_version.'/PHP'
                    .phpversion().'; ' . home_url());

                // Do not echo result
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // Set header
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "accept: application/json",
                    "authorization: Bearer " . $api_token,
                ]);

                // Set HTTP version to 1.1 to allow keepalive connections
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

                $response = curl_exec($ch);
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($http_status != 200) {
                    //TODO: Handle error
                }
                curl_clone($ch);
                return json_decode($response, true);
            } else {
                //Curl is not installed. fallback to WP HTTP API


                // Set the content type to application/json and add a body
                $response = wp_remote_post($url, [
                    'headers' => [
                        'accept' => 'application/json'
                    ],
                    'body' => json_encode($data_string)
                ]);

                return json_decode(wp_remote_retrieve_body($response), true);
            }
        }
    }

    private static function curl_installed(){
        return function_exists('curl_version');
    }
}

/** Helper function for checking bugs or getting useful info.
 * @param $data
 */
function dd($data)
{
    _e('<pre>');
    print_r($data);
    _e('</pre>');
    die;
}
