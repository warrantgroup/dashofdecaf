<?php
require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\TwigServiceProvider;
use DerAlex\Silex\YamlConfigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

if(!file_exists(__DIR__ . '/config.yml')) {
    echo 'Missing configuration file, please update config.yml including your Pivotal Key API key.';
    die();
}

$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/templates'));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/config.yml'));

$api = new \PivotalTracker\Api($app['config']['pivotaltracker']);

$app->get('/', function() use ($app) {
    return $app->redirect($app["url_generator"]->generate("stories"));
});

$app->get('/stories', function() use ($app) {
	return $app['twig']->render('stories.twig', array(

	));
})->bind('stories');

$app->get('/changelog', function(Request $request) use ($app, $api) {

    $params = $request->query->all();

    $client = new PivotalTracker\ChangeLog($api);
    $changelog = $client->build($params);

    return $app['twig']->render('changelog.twig', array(
         'features' => $changelog['feature'],
         'bugs' => $changelog['bug']
    ));
});

return $app;
