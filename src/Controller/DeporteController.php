<?php

namespace App\Controller;

use App\Entity\Deporte;
use App\Entity\User;
use App\Form\DeporteType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/deporte")
 */
class DeporteController extends BaseController
{
    /**
     * @Route("/nuevo", name="deporte_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        /** @var User $user */
        $user = $this->getUser();
        if (!$user->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        $deporte = new Deporte();
        $form = $this->createForm(DeporteType::class, $deporte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dir = $this->getParameter('deporte_header');
            /** @var UploadedFile $imagen */
            $imagen = $form['imagen']->getData();
            $filename = $form->get('nombre')->getData() . '_header.jpg';
            $imagen->move($dir, $filename);

            $this->em->persist($deporte);
            $this->em->flush();

            return $this->redirectToRoute('user_settings', ['d' => true]);
        }

        return $this->render('deporte/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/editar", name="deporte_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Deporte $deporte): Response
    {
        if (!$this->getUser() || !$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        /** @var User $user */
        $user = $this->getUser();
        if (!$user->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(DeporteType::class, $deporte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dir = $this->getParameter('deporte_header');

            // Elimino la antigua imagen
            $filename = $dir . '/' . $request->get('oldName') . '_header.jpg';
            $filesystem = new Filesystem();
            $filesystem->remove($filename);

            // Subo la nueva imagen
            /** @var UploadedFile $imagen */
            $imagen = $form['imagen']->getData();
            $filename = $form->get('nombre')->getData() . '_header.jpg';
            $imagen->move($dir, $filename);

            $this->em->flush();

            return $this->redirectToRoute('user_settings', ['d' => true]);
        }

        return $this->render('deporte/edit.html.twig', [
            'deporte' => $deporte,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="deporte_delete", methods={"POST"})
     */
    public function delete(Request $request, Deporte $deporte): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        /** @var User $user */
        $user = $this->getUser();
        if (!$user->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        if ($this->isCsrfTokenValid('delete' . $deporte->getId(), $request->request->get('_token'))) {
            $dir = $this->getParameter('deporte_header');
            $filename = $dir . '/' . $deporte->getNombre() . '_header.jpg';
            $filesystem = new Filesystem();
            $filesystem->remove($filename);

            $this->em->remove($deporte);
            $this->em->flush();
        }

        return $this->redirectToRoute('user_settings', ['d' => true]);
    }
}
