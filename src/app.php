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

$app->get('/stories', function() use ($app, $api) {

	return $app['twig']->render('stories/index.twig', array(
        'stories' => null,
        'labels' => $app['config']['labels'],
        'page' => 0
	));

})->bind('stories');

$app->get('/changelog', function(Request $request) use ($app, $api) {

    return $app['twig']->render('changelog/index.twig', array(
        'stories' => null,
        'labels' => $app['config']['labels']
    ));

})->bind('changelog');

$app->post('/stories', function(Request $request) use ($app, $api) {

    $params = $request->request->all();
    $story = new PivotalTracker\Story($api);
    $collection = $story->search($params);

    if(!isset($params['offset'])) {
        $params['offset'] = 0;
    }

    if($request->isXmlHttpRequest()) {
        return $app['twig']->render('stories/list.twig', array(
            'stories' => $collection,
            'page' => $params['offset']
        ));
    }

});

$app->post('/changelog', function(Request $request) use ($app, $api) {

    $params = $request->request->all();

    $params['changelog'] = true;

    $params['filters']['storyType'] = array('feature', 'bug');
    $params['filters']['storyStatus'] = array('finished');

    $story = new PivotalTracker\Story($api);
    $collection = $story->search($params);

    if(!isset($params['offset'])) {
        $params['offset'] = 0;
    }

    if($request->isXmlHttpRequest()) {
        return $app['twig']->render('changelog/list.twig', array(
            'features' => $collection->get('feature'),
            'bugs' =>$collection->get('bug'),
            'page' => $params['offset']
        ));
    }

});

return $app;

