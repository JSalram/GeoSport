<?php

namespace App\Controller;

use App\Repository\DeporteRepository;
use App\Repository\ProvinciaRepository;
use App\Repository\SpotRepository;
use App\Repository\ValoracionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var DeporteRepository
     */
    protected $depRepo;

    /**
     * @var ProvinciaRepository
     */
    protected $provRepo;

    /**
     * @var SpotRepository
     */
    protected $spotRepo;

    /**
     * @var ValoracionRepository
     */
    protected $valoracionRepo;

    public function __construct(EntityManagerInterface $em, DeporteRepository $depRepo,
                                ProvinciaRepository $provRepo, SpotRepository $spotRepo,
                                ValoracionRepository $valoracionRepo)
    {
        $this->em = $em;
        $this->depRepo = $depRepo;
        $this->provRepo = $provRepo;
        $this->spotRepo = $spotRepo;
        $this->valoracionRepo = $valoracionRepo;
    }
}
