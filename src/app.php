<?php
require_once __DIR__.'/bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* main */
$app->get('/', function() use($app) {

	$foo = new \Library\Foo("Foobar");

	
	$test = $foo->getBar();
	
	return $app['twig']->render('index.twig', array(
			'test' => $test,
	));

});

return $app;