<?php

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tch\A2V\Converter;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->match('/', function (Request $request) use ($app) {
    /** @var Form $form */
    $form = require __DIR__.'/forms/convert-form.php';

    $form->handleRequest($request);
    if ($form->isValid()) {
        
        /** @var UploadedFile[] $files */
        $files = $request->files->get('form');
        $audioFile = $files['audio_file'];
        $imageFile = $files['image_file'];

        $data = $request->request->get('form');

        // process.
        $converter = new Converter($audioFile, $data['output_format'], $data['frame_rate'], $imageFile, $data['image_resolution'], $data['image_color']);
        $outputFilePath = $converter();

        if (!file_exists($outputFilePath)) {
            $app->abort(404);
        }

        $downloadFileName = sprintf('%s.%s', pathinfo($audioFile->getClientOriginalName(), PATHINFO_FILENAME), $data['output_format']);
        return $app
            ->sendFile($outputFilePath)
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $downloadFileName)
        ;
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
