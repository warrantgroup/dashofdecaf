<?php
require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\TwigServiceProvider;
use DerAlex\Silex\YamlConfigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

$app = new Silex\Application();
$app['debug'] = true;

if(!file_exists(__DIR__ . '/config.yml')) {
    echo 'Missing configuration file, please update config.yml including your Pivotal Key API key.';
    die();
}

$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/templates'));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/config.yml'));
$app->register(new FranMoreno\Silex\Provider\PagerfantaServiceProvider());
$app->register(new Moust\Silex\Provider\CacheServiceProvider(), array(
    'cache.options' => array(
        'driver' => 'file',
        'cache_dir' => realpath(__DIR__ . '/../cache')
    )
));

$api = new \PivotalTracker\Api($app['config']['pivotaltracker']);

$app->get('/', function() use ($app) {
    return $app->redirect($app["url_generator"]->generate("stories"));
});

$app->get('/stories', function() use ($app, $api) {
	return $app['twig']->render('stories/index.twig', array(
        'stories' => null,
        'labels' => $app['config']['labels']
	));

})->bind('stories');

$app->get('/changelog', function(Request $request) use ($app, $api) {
    return $app['twig']->render('changelog/index.twig', array(
        'stories' => null,
        'labels' => $app['config']['labels']
    ));

})->bind('changelog');

$app->get('/roadmap', function() use ($app, $api) {
    $story = new PivotalTracker\Story($api);
    $params['filters'] = array('storyType' => array('release'));
    $params['limit'] = 100;
    $params['includeDone'] = false;
    $params['release'] = true;
    return $app['twig']->render('roadmap/index.twig', array(
        'releases' => $story->search($params)
    ));

})->bind('roadmap');

$app->post('/stories', function(Request $request) use ($app, $api) {

    $story = new PivotalTracker\Story($api);
    $story->setLabels($app['config']['labels']);

    $page = $request->request->get('page');

    if(empty($page)) {
        $app['cache']->store('stories', $story->search($request->request->all())->toArray());
    }

    $pagerfanta = new Pagerfanta(new ArrayAdapter($app['cache']->fetch('stories')));
    $pagerfanta->setMaxPerPage($story->getLimit());
    $pagerfanta->setCurrentPage((int) ($page) ? $page : 1);

    if($request->isXmlHttpRequest()) {
        return $app['twig']->render('stories/list.twig', array(
            'stories' => $pagerfanta->getCurrentPageResults(),
            'pager' => $pagerfanta
        ));
    }

});

$app->post('/changelog', function(Request $request) use ($app, $api) {

    $params = $request->request->all();

    $params['changelog'] = true;
    $params['filters']['storyType'] = array('feature', 'bug');
    $params['filters']['storyStatus'] = array('finished');

    $story = new PivotalTracker\Story($api);
    $story->setLabels($app['config']['labels']);
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

