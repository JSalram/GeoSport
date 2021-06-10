<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Valoracion;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/valoracion")
 */
class ValoracionController extends BaseController
{
    /**
     * @Route("/eliminar/{id}", name="valoracion_eliminar")
     */
    public function eliminarValoracion(int $id): RedirectResponse
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        /** @var User $user */
        $user = $this->getUser();
        $valoracion = $this->valoracionRepo->find($id);
        $spot = $valoracion->getSpot();

        if (!$user->isAdmin() && $valoracion->getUser() !== $user) {
            $this->addFlash('danger', 'No puedes eliminar una valoración que no es tuya.');
            return $this->redirectToRoute('index');
        }

        $spot->removeValoracion($valoracion);
        $this->em->remove($valoracion);
        $this->em->flush();

        return $this->redirectToRoute('spot_view', ['id' => $spot->getId()]);
    }

    /**
     * @Route("/comentar/spot/{id}", name="comentar_spot", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function comentar(Request $request, int $id): RedirectResponse
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $spot = $this->spotRepo->find($id);
        $nota = $request->get('nota') ?: 0;
        $comentario = $request->get('comentario') ?: null;

        $valoracion = new Valoracion($nota, $comentario, $this->getUser(), $spot);

        $spot->addValoracion($valoracion);
        $this->em->persist($valoracion);
        $this->em->flush();

        return $this->redirectToRoute('spot_view', ['id' => $id]);
    }
}
