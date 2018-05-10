<?php

namespace AppBundle\Twig;

class TwigExtension extends \Twig_Extension
{

    public function getName()
    {
        return 'twig_extension';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('basename', [$this, 'basenameFilter'])
        ];
    }

    /**
     * @var string $value
     * @return string
     */
    public function basenameFilter($value, $suffix = '')
    {
        return basename($value, $suffix);
    }
}