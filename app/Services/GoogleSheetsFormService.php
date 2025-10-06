<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;

class GoogleSheetsFormService
{
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/pion-form-49ee802487b7.json'));
        $client->addScope(Sheets::SPREADSHEETS);

        $this->service = new Sheets($client);
        $this->spreadsheetId = env('FORM_GOOGLE_SHEET_ID');
    }

    public function appendRow($values)
    {
        $range = 'Sheet1!A:F';
        $body = new \Google\Service\Sheets\ValueRange([
            'values' => [$values]
        ]);

        return $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            $range,
            $body,
            ['valueInputOption' => 'USER_ENTERED']
        );
    }
}
