<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Form\FormRenderer;

// use the top priority Accept-Language as default locale.
$lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'])[0];
$locale = explode(';', $lang)[0];

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());

$app->register(new SessionServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new LocaleServiceProvider(), [
//    'locale' => 'en',
    'locale' => $locale,
]);
$app->register(new TranslationServiceProvider());

$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
});

// @see https://github.com/silexphp/Silex/issues/1579#issuecomment-352999675
$app->extend('twig.runtimes', function ($runtimes, $app) {
    return array_merge($runtimes, [
        FormRenderer::class => 'twig.form.renderer',
    ]);
});

$app->extend('translator', function ($translator, $app) {
    /** @var \Symfony\Component\Translation\Translator $translator */
    $translator->addResource('xliff', __DIR__.'/../vendor/symfony/validator/Resources/translations/validators.ja.xlf', 'ja');

    return $translator;
});

return $app;
