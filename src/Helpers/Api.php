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

        $queryOrJson = $method === 'GET' ? 'query' : 'json';
        $client = new Client();
        $endPoint = 'job/'.$endPoint;
        $newJob = $client->request($method, config('listboss.endpoint').$endPoint, [
            $queryOrJson => $params,
            'headers' => [
                'Authorization' => 'Bearer '.config('listboss.api_key'),
                'Content-Type' => 'application/json',
            ],
            'allow_redirects' => false,

            'verify' => config('listboss.ssl_verify'),
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
