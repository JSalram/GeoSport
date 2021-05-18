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

class ValoracionController extends BaseController
{
    /**
     * @Route("/valoracion/{id}/eliminar", name="valoracion_eliminar")
     */
    public function eliminarComentario(Request $request, int $id): RedirectResponse
    {
        $spot = $this->spotRepo->find($request->get('spotId'));
        $valoracion = $this->valoracionRepo->find($id);

        if ($valoracion->getUser() !== $this->getUser()) {
            $this->addFlash('danger', 'No puedes eliminar una valoración que no es tuya.');
            return $this->redirectToRoute('index');
        }
        $em = $this->em;
        $spot->removeValoracion($valoracion);
        $em->flush();

        return $this->redirectToRoute('spot_view', [
            'deporte' => $request->get('deporte'),
            'id' => $request->get('spotId'),
        ]);
    }

    /**
     * @Route("/comentar/{id}", name="comentar_spot", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function comentar(Request $request, int $id): RedirectResponse
    {
        $spot = $this->spotRepo->find($id);
        $nota = $request->get('nota') ?: 0;
        $comentario = $request->get('comentario') ?: null;

        $valoracion = new Valoracion($nota, $comentario, $this->getUser(), $spot);

        $em = $this->em;
        $spot->addValoracion($valoracion);
        $em->persist($valoracion);
        $em->flush();

        return $this->redirectToRoute('spot_view', [
            'deporte' => $request->get('deporte'),
            'id' => $id,
        ]);
    }
}
