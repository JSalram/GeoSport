<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SettingsSaver;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/ajustes")
 */
class SettingsController extends BaseController
{
    /**
     * @Route("/usuario", name="user_settings")
     */
    public function userSettings(Request $request, SluggerInterface $slugger): Response
    {
        $spotsPagina = 10;
        $pagina = intval($request->get('p', 1));
        $paneSpots = $request->get('s', false);
        $paneDeporte = $request->get('d', false);

        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        /** @var User $usuario */
        $usuario = $this->getUser();

        if (!$usuario->isVerified()) {
            return $this->redirectToRoute('index');
        }

        $saver = new SettingsSaver($this->userRepo, $usuario, $request, $this->passwordEncoder);
        if ($request->isMethod('POST')) {
            // GENERAL //
            if ($saver->validar('foto')) {
                $saver->actualizaFoto($slugger, $this->getParameter('foto_perfil'));
            }
            if ($saver->validar('username'))
                $usuario->setUsername($request->get('username'));
            if ($saver->validar('email'))
                $usuario->setEmail($request->get('email'));

            // CONTRASEÑA //
            if (!empty($request->get('oldPassword')) && !empty($request->get('newPassword'))
                && !empty($request->get('repeatPassword')) && $saver->validar('password')) {
                $usuario->setPassword($this->passwordEncoder->encodePassword($usuario, $request->get('newPassword')));
            }

            if ($saver->cambios) {
                $this->em->flush();
                $this->addFlash('ajustesGuardados', 'success');
                return $this->redirectToRoute('user_settings');
            } else {
                $this->addFlash('ajustesGuardados', 'danger');
            }
        }

        $deportes = $this->depRepo->findAll();
        $mySpots = $this->spotRepo->findByUser($usuario, $pagina, $spotsPagina);
        $maxPaginas = ceil(count($mySpots) / $spotsPagina);

        return $this->render('settings/user.html.twig', [
            'mySpots' => $mySpots,
            'pagina' => $pagina,
            'maxPaginas' => $maxPaginas,
            'paneSpots' => $paneSpots,
            'paneDeporte' => $paneDeporte,
            'deportes' => $deportes,
        ]);
    }

    /**
     * @Route("/foto/eliminar", name="eliminaFoto")
     * @return RedirectResponse
     */
    public function eliminaFoto(): RedirectResponse
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        /** @var User $usuario */
        $usuario = $this->getUser();
        $filesystem = new Filesystem();
        $filesystem->remove($usuario->getFoto());

        $usuario->removeFoto();
        $this->em->flush();

        return $this->redirectToRoute('user_settings');
    }
}
