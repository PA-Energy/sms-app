<?php

namespace App;
use Illuminate\Support\Facades\Http;

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
            $response = Http::withHeaders([
                'Authorization' => "Basic {$authString}",
            ])->timeout(10)->get($url);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch SMS inbox: ' . $response->body());
            }

            $html = $response->body();

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
        $password = 'admin'; // GoIP password

        $response = Http::withBasicAuth($this->goipUser, $this->goipPassword)
            ->asForm()
            ->post("{$this->goipAdddr}/default/en_US/sms_info.html", [
                'line' => $this->goipLine,
                'smskey' => $this->goipSmsKey,
                'action' => 'sms',
                'telnum' => $this->goipTelNum,
                'smscontent' => $this->goipSmsContent,
                'send' => 'send',
            ]);

        if ($response->successful()) {
            $this->info('SMS sent successfully!');
        } else {
            $this->error('Failed to send SMS. Response: ' . $response->body());
        }
    }

}
