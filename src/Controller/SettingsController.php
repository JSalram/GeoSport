<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SettingsSaver;
use Couchbase\Document;
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

        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        /** @var User $usuario */
        $usuario = $this->getUser();

        if (!$usuario->isVerified()) {
            return $this->redirectToRoute('index');
        }

        $v = new SettingsSaver($this->userRepo, $usuario, $request, $this->passwordEncoder);
        if ($request->isMethod('POST')) {
            // GENERAL //
            if ($v->validar('foto')) {
                if ($usuario->hasFoto()) {
                    $filesystem = new Filesystem();
                    $filesystem->remove($usuario->getFoto());
                }
                $v->actualizaFoto($slugger, $this->getParameter('foto_perfil'));
            }
            if ($v->validar('username'))
                $usuario->setUsername($request->get('username'));
            if ($v->validar('email'))
                $usuario->setEmail($request->get('email'));

            // CONTRASEÑA //
            if (!empty($request->get('oldPassword')) && !empty($request->get('newPassword'))
                && !empty($request->get('repeatPassword')) && $v->validar('password')) {
                $usuario->setPassword($this->passwordEncoder->encodePassword($usuario, $request->get('newPassword')));
            }

            $this->em->flush();
            $this->addFlash('ajustesGuardados', 'OK');
            return $this->redirectToRoute('user_settings');
        }

        $mySpots = $this->spotRepo->findByUser($usuario, $pagina, $spotsPagina);
        $maxPaginas = ceil(count($mySpots) / $spotsPagina);

        return $this->render('settings/user.html.twig', [
            'mySpots' => $mySpots,
            'pagina' => $pagina,
            'maxPaginas' => $maxPaginas,
            'paneSpots' => $paneSpots,
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
