<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ValoracionExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('nota', [$this, 'printNota']),
        ];
    }

//    public function getFunctions(): array
//    {
//        return [
//            new TwigFunction('nota', [$this, 'printNota']),
//        ];
//    }

    public function printNota($nota)
    {
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
