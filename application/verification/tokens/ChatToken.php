<?php
namespace application\verification\tokens;

class ChatToken{

    public function create(){
        // random password generator!
        $_SESSION['CHATPASS'] = 'fUnGameSInHolland8475overthinking'; 
        $timestamp   = time() + (7 * 24 * 60);
        $passphrase  = $_SESSION['CHATPASS'];
        $passphrase .= 'pLeaSeBeScilEntOnthEhALlWay9934ItSleepssss'; 
        $passphrase .= $timestamp;
        $secret      = 'IKeepNoSecrets668627676777';
        $algo        = 'sha512';
        
        return bin2hex(hash_hmac($algo, $passphrase, $secret, true)) . '|' .  $timestamp;
    }
    
    public function check($key){
        
        $parts = explode('|', $key);
        $passphrase  = $_SESSION['CHATPASS'];
        $passphrase .= 'pLeaSeBeScilEntOnthEhALlWay9934ItSleepssss';
        $passphrase .= $parts[1];
        $secret      = 'IKeepNoSecrets668627676777';
        $algo        = 'sha512';
        

        try{

            if (hash_equals(hex2bin($parts[0]), hash_hmac($algo, $passphrase, $secret, true))) {
                return "valid";
            } else {
                throw new \PDOException("ChatToken could not be validated");
            }

        } catch (\PDOException $e){
            // nmaybe not show errors to client?
            echo json_encode("problem : ".$e);
            exit;
        }
    }

}