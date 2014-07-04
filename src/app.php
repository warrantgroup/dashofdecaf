<?php
require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\TwigServiceProvider;
use DerAlex\Silex\YamlConfigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

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

$app->get('/changelog', function() use ($app, $api) {

    $changelog = new PivotalTracker\ChangeLog($api);

    return $app['twig']->render('changelog.twig', array(
         'stories' => $changelog->build(array())
    ));
});

return $app;
