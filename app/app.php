<?php

require_once __DIR__ .'/../vendor/autoload.php';

$app = new Silex\Application();

$app['guzzle'] = $app->share(function() {
    return new \GuzzleHttp\Client();
});

$app['my_client'] = $app->share(function() use($app) {
    return new \Devhelp\Piwik\Api\Guzzle\Client\PiwikGuzzleClient($app['guzzle']);
});

$app['my_piwik_method'] = $app->share(function() use($app) {
    return $app['devhelp_piwik.api']->getMethod('Actions.get');
});

$app->register(new \Devhelp\Silex\Piwik\PiwikApiServiceProvider([
    'client' => 'my_client',
    'api' => [
        'reader' => [
            'url' => 'http://demo.piwik.org',
            'default_params' => [
                'idSite' => 7,
                'period' => 'day',
                'date' => 'yesterday',
                'format' => 'json',
                'token_auth' => 'anonymous'
            ]
        ]
    ]
]));


$app->get('/demo/method-call', function() use($app) {
    return $app['my_piwik_method']->call([])->getBody()->getContents();
});
