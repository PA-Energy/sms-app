<?php

namespace App\Traits;

trait GoIpTrait
{
    protected $goipAddr = '192.168.1.3';
    protected $goipUser = 'admin';
    protected $goipPassword = 'admin';
    protected $goipPort = '';
    protected $goipLine = 1;
    protected $goipSmsKey = '';
    protected $goipTelNum = '';
    protected $goipSmsContent = '';
    
    public function inbox()
    {
        $authString = base64_encode("{$this->goipUser}:{$this->goipPassword}");
        $url = "{$this->goipAddr}/default/en_US/tools.html?type=sms_inbox";

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . $authString
            ]);
            $html = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200) {
                throw new \Exception('Failed to fetch SMS inbox: HTTP ' . $httpCode);
            }


            // Extract the `sms` data using a regular expression
            preg_match('/sms= \[(.*?)\];/s', $html, $matches);

            if (!isset($matches[1])) {
                throw new \Exception('Failed to extract SMS data from the response.');
            }

            $smsData = $matches[1];

            // Parse the SMS data into an array
            $rows = str_getcsv($smsData, ',', '"');
            $messages = [];
            foreach ($rows as $row) {
                $columns = str_getcsv($row, ',', '"');
                if (count($columns) >= 3) {
                    $messages[] = [
                        'date' => $columns[0],
                        'phone' => $columns[1],
                        'text' => $columns[2],
                        'delete' => false, // Default value for the "delete" column
                    ];
                }
            }

            return $messages;
        } catch (\Exception $e) {
            // Return mockup data in case of timeout or failure
            return [
                [
                    'date' => '2025-04-28 10:00:00',
                    'phone' => '+1234567890',
                    'text' => 'Mockup SMS message 1',
                    'delete' => false,
                ],
                [
                    'date' => '2025-04-28 11:00:00',
                    'phone' => '+0987654321',
                    'text' => 'Mockup SMS message 2',
                    'delete' => false,
                ],
            ];
        }
    }

    public function send()
    {
        $url = "{$this->goipAddr}/default/en_US/sms_info.html";
        $data = [
            'line' => $this->goipLine,
            'smskey' => $this->goipSmsKey,
            'action' => 'sms',
            'telnum' => $this->goipTelNum,
            'smscontent' => $this->goipSmsContent,
            'send' => 'send',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->goipUser}:{$this->goipPassword}");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return ['success' => true, 'message' => 'SMS sent successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to send SMS. HTTP Code: ' . $httpCode];
        }
    }

}
