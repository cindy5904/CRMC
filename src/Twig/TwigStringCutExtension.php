<?php 

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigStringCutExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [new TwigFilter('cut', [$this, 'cutFilter'])];
    }

    public function cutFilter($content): string
    {
        $newContent = substr($content, 0, 200);
        $limit = strrpos($newContent, " ");
        $content = substr($content, 1, $limit);

        return $content;
    }
}

