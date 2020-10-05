<?php
namespace application\verification\tokens;

class LoginToken{

    public function create(){
        // random password generator!
        $_SESSION['PASS'] = 'nordicWalkingOn02-12-19!!'; 
        $timestamp   = time() + (7 * 24 * 60);
        $passphrase  = $_SESSION['PASS'];
        $passphrase .= 'NoRushToCheckTheCatAtHomeForSheIsQueenOfOurCastle'; 
        $passphrase .= $timestamp;
        $secret      = 'IWouldLieIfIWasASpy';
        $algo        = 'sha512';

        return bin2hex(hash_hmac($algo, $passphrase, $secret, true)) . '|' .  $timestamp;
    }

    public function check($key){

        $parts = explode('|', $key);
        $passphrase  = $_SESSION['PASS'];
        $passphrase .= 'NoRushToCheckTheCatAtHomeForSheIsQueenOfOurCastle';
        $passphrase .= $parts[1];
        $secret      = 'IWouldLieIfIWasASpy';
        $algo        = 'sha512';

        try{

            if (hash_equals(hex2bin($parts[0]), hash_hmac($algo, $passphrase, $secret, true))) {
                return "valid";
            } else {
                throw new \PDOException("LoginToken could not be validated");
            }

        } catch (\PDOException $e){
            // nmaybe not show errors to client?
            echo json_encode("problem : ".$e);
            exit;
        }
    }

}


