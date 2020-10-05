<?php
namespace application\verification\tokens;

class UserToken{

    public function create(){
        // random password generator!
        $_SESSION['PASS'] = 'Buf6klDCo0Fjktr32Wdxcik0dwfj976'; 
        $timestamp   = time() + (7 * 24 * 60);
        $passphrase  = $_SESSION['PASS'];
        $passphrase .= 'Middelmatig87Kringloopwinkel'; 
        $passphrase .= $timestamp;
        $secret      = 'chefsspecialtaugemayoburger';
        $algo        = 'sha512';

        return bin2hex(hash_hmac($algo, $passphrase, $secret, true)) . '|' .  $timestamp;
    }

    public function check($key){
        $parts = explode('|', $key);
        $passphrase  = $_SESSION['PASS'];
        $passphrase .= 'Middelmatig87Kringloopwinkel';
        $passphrase .= $parts[1];
        $secret      = 'chefsspecialtaugemayoburger';
        $algo        = 'sha512';

        try{
            if (!hash_equals(hex2bin($parts[0]), hash_hmac($algo, $passphrase, $secret, true))) {
                return "valid";
            } else {
                throw new \Exception('AdminToken could not be validated');
            }

        } catch (\Exception $e){
            // maybe not show errors to client?
            echo json_encode($e->getMessage);
        }
    }
}