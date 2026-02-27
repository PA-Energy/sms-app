<?php

namespace App\Services;

use App\Traits\GoIpTrait;

class GoIpService
{
    use GoIpTrait;

    public function __construct()
    {
        $this->goipAddr = GOIP_ADDR;
        $this->goipUser = GOIP_USER;
        $this->goipPassword = GOIP_PASSWORD;
        $this->goipLine = GOIP_LINE;
    }

    public function fetchInbox()
    {
        return $this->inbox();
    }

    public function sendSms($phoneNumber, $message, $line = null)
    {
        $this->goipTelNum = $phoneNumber;
        $this->goipSmsContent = $message;
        if ($line !== null) {
            $this->goipLine = $line;
        }
        return $this->send();
    }
}
