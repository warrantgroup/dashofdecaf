<?php
require_once __DIR__.'/bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


$app->get('/', function() use ($app) {
    return $app->redirect($app["url_generator"]->generate("stories"));
});

$app->get('/stories', function() use ($app) {
	return $app['twig']->render('stories.twig', array(

	));
})->bind('stories');

$app->get('/changelog', function() use ($app) {
    return $app['twig']->render('changelog.twig', array(

    ));
});

$app = new Silex\Application();
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/settings.yml'));
return $app;
