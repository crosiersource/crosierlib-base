<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @package CrosierSource\CrosierLibBaseBundle\Twig
 * @author Carlos Eduardo Pauluk
 */
class TransliterateFilter extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('transliterate', [$this, 'transliterate']),
        ];
    }

    public function transliterate(string $string)
    {
        $transliterator = \Transliterator::createFromRules(':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;', \Transliterator::FORWARD);
        return $transliterator->transliterate($string);
    }
}