<?php

namespace App\Services;

use Google\Service\Sheets;
use App\Services\GoogleClientService;
use Exception;

class GoogleSheetService
{
    protected $sheets;

    public function __construct(GoogleClientService $googleClientService)
    {
        $client = $googleClientService->createClient();
        $this->sheets = new Sheets($client);
    }

    public function appendRow(string $spreadsheetId, string $range, array $values): void
    {
        $body = new Sheets\ValueRange([
            'values' => [$values]
        ]);

        $params = ['valueInputOption' => 'RAW'];

        $this->sheets->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    }

    public function ensureHeader(string $spreadsheetId, string $range): void
    {
        $response = $this->sheets->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            $this->appendRow($spreadsheetId, $range, [
                'Họ tên',
                'Email',
                'Số điện thoại',
                'Nội dung',
                'Thời gian gửi'
            ]);
        }
    }
}
