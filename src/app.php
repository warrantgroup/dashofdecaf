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

    $changelog = new PivotalTracker\ChangeLog();

    return $app['twig']->render('changelog.twig', array(
         'stories' => $changelog->build()
    ));
});

return $app;