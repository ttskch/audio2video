<?php

namespace App\Controller;

use App\Entity\ConvertCriteria;
use App\Form\ConvertType;
use App\Service\Converter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}", name="home_")
 */
class HomeController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, Converter $converter, SessionInterface $session, TranslatorInterface $translator)
    {
        $form = $this->createForm(ConvertType::class, $criteria = new ConvertCriteria());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $outputFilePath = $converter->setCriteria($criteria)->convert();
                $downloadFileName = sprintf('%s.%s', pathinfo($criteria->audioFile->getClientOriginalName(), PATHINFO_FILENAME), $criteria->outputFormat);
                $session->set('download', [$outputFilePath, $downloadFileName]);
            } catch (\Exception $e) {
                $this->addFlash('danger', $translator->trans($e->getMessage()));
            }

            // don't redirect to 'home_download' so that can inform frontend that conversion is complete
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'download_ready' => boolval($session->get('download')),
        ]);
    }

    /**
     * @Route("/download/", name="download")
     */
    public function download(SessionInterface $session)
    {
        if (list($outputFilePath, $downloadFileName) = $session->get('download')) {
            $session->remove('download');

            return (new BinaryFileResponse($outputFilePath))
                ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $downloadFileName)
            ;
        }

        return $this->redirectToRoute('home_index');
    }
}
