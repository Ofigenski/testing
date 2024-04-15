<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('shuffle', [$this, 'shuffle']),
        ];
    }

    public function shuffle(array $array): array
    {
        $result = $array;
        shuffle($result);

        return $result;
    }
}
