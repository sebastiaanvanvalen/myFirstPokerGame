<?php

namespace application\verification\tokens;

use application\verification\tokens\loginToken;
use application\verification\tokens\UserToken;
use application\verification\tokens\ChatToken;

class TokenHandler
{
    private $tokenType;
    private $csrf_token;
    private $inlogToken;
    private $userToken;
    private $chatToken;

    public function __construct($tokenType, $csrf_token)
    {
        $this->tokenType = $this->validateType($tokenType);
        $this->csrf_token = $this->validateToken($csrf_token);
    }

    public function handle()
    {
        try {
            switch ($this->tokenType) {
                case "loginToken":
                    $this->inlogToken = new LoginToken();
                    if ($this->inlogToken->check($this->csrf_token) === "valid") {
                        return "valid";
                    };
                    break;
                case "userToken":
                    $this->userToken = new UserToken();
                    if ($this->userToken->check($this->csrf_token) === "valid") {
                        return "valid";
                    };
                    break;
                case "chatToken":
                    $this->chatToken = new ChatToken();
                    if ($this->chatToken->check($this->csrf_token) === "valid") {
                        return "valid";
                    };
                    break;
                default:
                    throw new \Exception("TokenHandler could not validate token");
            }
        } catch (\Exception $e) {
            echo json_encode("\n" . $e->getMessage() . "\n\n");
        }
    }

    // PRIVATE

    private function validateType($tokenType)
    {
        return $tokenType;
    }

    private function validateToken($csrf_token)
    {
        return $csrf_token;
    }
}
