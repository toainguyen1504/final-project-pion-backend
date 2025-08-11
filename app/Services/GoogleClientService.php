<?php

namespace App\Services;

use Google\Client;

class GoogleClientService
{
    public function createClient(): Client
    {
        $client = new Client();

        $client->setApplicationName('Laravel Google API');
        $client->setScopes([
            'https://www.googleapis.com/auth/spreadsheets',
            'https://www.googleapis.com/auth/drive',
        ]);
        $client->setAuthConfig(storage_path('app/google/google-credentials.json'));
        $client->setAccessType('offline');

        return $client;
    }
}
