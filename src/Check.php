<?php

namespace Ladoc;

use Throwable;

class Check
{
    private const REPO_URL = 'https://api.github.com/repos/millancore/ladoc/tags';

    public function isLastVersion(string $currentVersion): bool
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, self::REPO_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
            $response = curl_exec($ch);
            curl_close($ch);

        } catch (Throwable) {
            // Try next time
            return true;
        }

        if (!$response) {
            return true;
        }

        $tags = json_decode((string) $response, true);

        return $currentVersion === $tags[0]['name'];
    }
}