<?php

use MicroCMS\DAO\ArticleDAO;
use MicroCMS\DAO\CommentDAO;
use MicroCMS\DAO\UserDAO;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Register service providers.
$app->register(new DoctrineServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',));
$app['twig'] = $app->share($app->extend('twig', function(Twig_Environment $twig, $app) {
            $twig->addExtension(new Twig_Extensions_Extension_Text());
            return $twig;
        }));
$app->register(new ValidatorServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secured' => array(
            'pattern'   => '^/',
            'anonymous' => true,
            'logout'    => true,
            'form'      => array('login_path' => '/login', 'check_path' => '/login_check'),
            'users'     => $app->share(function () use ($app) {
                return new UserDAO($app['db']);
            }),
        ),
    ),
    'security.role_hierarchy' => array(
        'ROLE_ADMIN' => array('ROLE_USER'),
    ),
    'security.access_rules'   => array(
        array('^/admin', 'ROLE_ADMIN'),
    ),
));
$app->register(new FormServiceProvider());
$app->register(new TranslationServiceProvider());
$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/microcms.log',
    'monolog.name'    => 'MicroCMS',
    'monolog.level'   => $app['monolog.level'],
));
$app->register(new ServiceControllerServiceProvider());
if (isset($app['debug']) && $app['debug']) {
    $app->register(new HttpFragmentServiceProvider());
    $app->register(new WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => __DIR__.'/../var/cache/profiler'
    ));
}
// Register services.
$app['dao.article'] = $app->share(function ($app) {
    return new ArticleDAO($app['db']);
});
$app['dao.user'] = $app->share(function ($app) {
    return new UserDAO($app['db']);
});
$app['dao.comment'] = $app->share(function ($app) {
    $commentDAO = new CommentDAO($app['db']);
    $commentDAO->setArticleDAO($app['dao.article']);
    $commentDAO->setUserDAO($app['dao.user']);
    return $commentDAO;
});

// Register error handler
$app->error(function (Exception $e, $code) use ($app) {
    switch ($code) {
        case 403:
            $message = 'Access denied.';
            break;
        case 404:
            $message = "The requested resource could not be found.";
            break;
        default:
            $message = "Something went wrong.";
    }
    return $app['twig']->render('error.html.twig', array(
                'message' => $message
    ));
});
