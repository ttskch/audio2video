<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->match('/', function (Request $request) use ($app) {
    /** @var \Symfony\Component\Form\Form $form */
    $form = require __DIR__.'/forms/convert-form.php';

    $form->handleRequest($request);
    if ($form->isValid()) {
        $app['session']->getFlashBag()->add('success', $app['translator']->trans('Form submitted.'));

        return $app->redirect($app['url_generator']->generate('homepage'));
    }

    return $app['twig']->render('index.html.twig', [
        'form' => $form->createView(),
    ]);
})
->bind('homepage')
;

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
