<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class YoutubeService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.youtube.api_key');
    }

    /**
     * Convert youtube url -> embed url
     */
    public function convertYoutubeUrl(string $url): string
    {
        $videoId = $this->extractVideoId($url);

        if (!$videoId) {
            return $url;
        }

        return "https://www.youtube.com/embed/" . $videoId;
    }

    /**
     * Extract video id from url
     */
    public function extractVideoId(string $url): ?string
    {
        // youtu.be/xxxxx
        if (preg_match('/youtu\.be\/([^\?]+)/', $url, $matches)) {
            return $matches[1];
        }

        // youtube.com/watch?v=xxxxx
        if (preg_match('/v=([^\&]+)/', $url, $matches)) {
            return $matches[1];
        }

        // youtube.com/embed/xxxxx
        if (preg_match('/embed\/([^\?]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get duration (seconds) from youtube url
     */
    public function getDurationFromUrl(string $url): ?int
    {
        $videoId = $this->extractVideoId($url);

        if (!$videoId) {
            return null;
        }

        return $this->getDurationFromVideoId($videoId);
    }

    /**
     * Get duration from youtube API
     */
    public function getDurationFromVideoId(string $videoId): ?int
    {
        try {

            $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                'part' => 'contentDetails',
                'id' => $videoId,
                'key' => $this->apiKey
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if (empty($data['items'][0]['contentDetails']['duration'])) {
                return null;
            }

            $isoDuration = $data['items'][0]['contentDetails']['duration'];

            return $this->convertIso8601ToSeconds($isoDuration);
        } catch (\Exception $e) {

            return null;
        }
    }

    /**
     * Convert ISO8601 duration (PT5M30S) -> seconds
     */
    protected function convertIso8601ToSeconds(string $duration): int
    {
        $interval = new \DateInterval($duration);

        return ($interval->h * 3600)
            + ($interval->i * 60)
            + $interval->s;
    }
}
