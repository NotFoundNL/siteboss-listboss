<?php

declare(strict_types=1);

namespace NotFound\ListBoss\Helpers;

use GuzzleHttp\Client;

trait Api
{
    private function call(string $method = 'GET', string $endPoint = '', array $params = [], bool $updateSelf = false): ?object
    {
        if (! in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
            return null;
        }

        $client = new Client();
        $endPoint = 'job/'.$endPoint;
        $newJob = $client->request($method, env('LISTBOSS_ENDPOINT', 'https://listboss.nl/v2/').$endPoint, [
            'json' => $params,
            'headers' => [
                'Authorization' => 'Bearer '.env('LISTBOSS_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'allow_redirects' => false,
        ]);
        $result = json_decode($newJob->getBody()->getContents());
        if ($result === null) {
            return null;
        }
        if ($updateSelf && $this->id === null) {
            $this->id = $result->id;
        }

        return $result;
    }
}
