<?php

namespace App\Controller;

use App\Entity\Valoracion;
use App\Repository\SpotRepository;
use App\Repository\ValoracionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/valoracion")
 */
class ValoracionController extends BaseController
{
    /**
     * @Route("/eliminar/{id}", name="valoracion_eliminar")
     */
    public function eliminarComentario(int $id): RedirectResponse
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $valoracion = $this->valoracionRepo->find($id);
        $spot = $valoracion->getSpot();

        if ($valoracion->getUser() !== $this->getUser()) {
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
