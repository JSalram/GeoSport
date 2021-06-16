<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ValoracionExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('nota', [$this, 'printNota']),
        ];
    }

    public function printNota($nota): string
    {
        $nota = intval($nota);
        $valoracion = '';

        for ($i = 0; $i <= 10; $i++) {
            if ($i <= $nota) {
                if ($i > 0) {
                    if ($i % 2 === 0) {
                        $valoracion .= '<i class="fa fa-star"></i>';
                    } else if ($i === $nota) {
                        $valoracion .= '<i class="fa fa-star-half-alt"></i>';
                    }
                }
            } else if ($i % 2 !== 0) {
                $valoracion .= '<i class="far fa-star"></i>';
            }
        }

        return $valoracion;
    }
}
