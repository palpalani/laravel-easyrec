<?php

namespace Antoineaugusti\Tests\LaravelEasyrec;

use Antoineaugusti\LaravelEasyrec\Easyrec;
use GuzzleHttp\Adapter\MockAdapter;
use GuzzleHttp\Adapter\TransactionInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use PHPUnit\Framework\TestCase;

abstract class LaravelEasyrecTestCase extends TestCase
{
    public const ITEM_ID = 1337;

    public const USER_ID = 69;

    public const ITEM_DESCRIPTION = 'mock-description';

    public const ITEM_URL = 'mock-url';

    public const RATING_NOTE = 5;

    public const SESSION_ID = 'mock-session';

    public const CUSTOM_ACTION = 'mock-action';

    public Easyrec $easyrec;

    public function setUp(): void
    {
        $this->easyrec = new Easyrec([
            'baseURL' => 'mock-url',
            'apiVersion' => '1.0',
            'apiKey' => 'mock-key',
            'tenantID' => 'mock-tenant',
        ]);

        // Always return a 200 OK response
        $mockAdapter = new MockAdapter(function (TransactionInterface $trans) {
            $request = $trans->getRequest();

            return new Response(200);
        });

        // Replace the HTTP client
        $client = new Client(['adapter' => $mockAdapter, 'base_url' => $this->easyrec->getBaseURL()]);
        $this->easyrec->setHttpClient($client);
    }
}
