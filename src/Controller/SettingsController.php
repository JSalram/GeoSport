<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function userSettings(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        $mySpots = $this->spotRepo->findBy(['user' => $this->getUser()]);

        return $this->render('settings/user.html.twig', [
            'mySpots' => $mySpots,
        ]);
    }
}
