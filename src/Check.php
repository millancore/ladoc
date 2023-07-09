<?php

namespace Ladoc;

use Throwable;

class Check
{
    private const API_URL_TAGS = 'https://api.github.com/repos/millancore/ladoc/tags';

    public function isLastVersion(string $currentVersion): bool
    {
        try {
            $lastVersion = $this->getLastVersion();
        } catch (Throwable) {
            // Try next time
            return true;
        }

        if (!$lastVersion) {
            return true;
        }

        return $currentVersion === $lastVersion;
    }


    public function getLastVersion(): ?string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::API_URL_TAGS . '?per_page=1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Ladoc');
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        $tags = json_decode((string)$response, true);

        return $tags[0]['name'];
    }
}
