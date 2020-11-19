<?php
/**
 * Created by PhpStorm.
 * User: chaos
 * Date: 2019-05-22
 * Time: 15:59
 */

namespace App\Http\Utils;


use Illuminate\Support\Facades\Log;

class QuickbloxHelper
{
    public $appId, $authKey, $authSecret;

    public function __construct()
    {
        $this->appId = env('QUICKBLOX_APP_ID', '');
        $this->authKey = env('QUICKBLOX_AUTH_KEY', '');
        $this->authSecret = env('QUICKBLOX_AUTH_SECRET', '');

    }

    function createSession() {

        // Generate signature
        $nonce = rand();
        $timestamp = time(); // time() method must return current timestamp in UTC but seems like hi is return timestamp in current time zone
        $signature_string = "application_id=" . $this->appId . "&auth_key=" . $this->authKey . "&nonce=" . $nonce . "&timestamp=" . $timestamp;

        $signature = hash_hmac('sha1', $signature_string , $this->authSecret);

        // Build post body
        $post_body = http_build_query( array(
            'application_id' => $this->appId,
            'auth_key' => $this->authKey,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'signature' => $signature,
        ));

        // Configure cURL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.quickblox.com/session.json');
        curl_setopt($curl, CURLOPT_POST, true); // Use POST
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_body); // Setup post body
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Receive server response

        // Execute request and read response
        $response = curl_exec($curl);
        $responseJSON = json_decode($response)->session;

        // Check errors
        if ($responseJSON) {
            return $responseJSON;
        } else {
            $error = curl_error($curl). '(' .curl_errno($curl). ')';
            return $error;
        }

        // Close connection
        curl_close($curl);

    }

    function createUser($token, $username, $password, $email) {

        $post_body = http_build_query(array(
            'user[login]' => $username,
            'user[password]' => $password,
            'user[email]' => $email
        ));
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.quickblox.com/users.json');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'QuickBlox-REST-API-Version: 0.1.0',
            'QB-Token: ' . $token
        ));
        $response = curl_exec($curl);
        $responseJSON = json_decode($response);


        if ($responseJSON) {
            return $responseJSON;
        } else {
            return false;
        }
        curl_close($curl);



    }

}