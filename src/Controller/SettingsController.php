<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ajustes")
 */
class SettingsController extends BaseController
{
    /**
     * @Route("/usuario", name="user_settings")
     */
    public function userSettings(Request $request): Response
    {
        $spotsPagina = 10;
        $pagina = intval($request->get('p', 1));

        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        $mySpots = $this->spotRepo->findByUser($this->getUser(), $pagina, $spotsPagina);
        $maxPaginas = ceil(count($mySpots) / $spotsPagina);

        return $this->render('settings/user.html.twig', [
            'mySpots' => $mySpots,
            'pagina' => $pagina,
            'maxPaginas' => $maxPaginas
        ]);
    }
}
