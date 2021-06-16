<?php

namespace App\Controller;

use App\Repository\DeporteRepository;
use App\Repository\ProvinciaRepository;
use App\Repository\SpotRepository;
use App\Repository\UserRepository;
use App\Repository\ValoracionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BaseController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var DeporteRepository */
    protected $depRepo;

    /** @var ProvinciaRepository */
    protected $provRepo;

    /** @var SpotRepository */
    protected $spotRepo;

    /** @var ValoracionRepository */
    protected $valoracionRepo;

    /** @var UserRepository */
    protected $userRepo;

    /** @var UserPasswordEncoderInterface */
    protected $passwordEncoder;

    public function __construct(EntityManagerInterface $em, DeporteRepository $depRepo,
                                ProvinciaRepository $provRepo, SpotRepository $spotRepo,
                                ValoracionRepository $valoracionRepo, UserRepository $userRepo,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->depRepo = $depRepo;
        $this->provRepo = $provRepo;
        $this->spotRepo = $spotRepo;
        $this->valoracionRepo = $valoracionRepo;
        $this->userRepo = $userRepo;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        // Marca si hay revisiones pendientes o no
        $spotsPendientes = $this->spotRepo->findBy(['aprobado' => null]);
        $parameters['revision'] = !empty($spotsPendientes);

        return parent::render($view, $parameters, $response);
    }
}
