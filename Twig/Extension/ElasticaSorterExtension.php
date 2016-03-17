<?php

namespace Alpixel\Bundle\ElasticaUtilsBundle\Twig\Extension;

use Alpixel\Bundle\ElasticaUtilsBundle\Services\ElasticaQuerySorter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class ElasticaSorterExtension extends \Twig_Extension
{
    protected $sorter;

    public function __construct(ElasticaQuerySorter $sorter)
    {
        $this->sorter = $sorter;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('elastica_sort', array($this, 'displaySort'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
            new \Twig_SimpleFunction('elastica_clear_sort', array($this, 'clearSort'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
        );
    }

    public function clearSort(\Twig_Environment $twig)
    {
        return $twig->render('AlpixelElasticaUtilsBundle:blocks:clear_sort.html.twig');
    }

    public function displaySort(\Twig_Environment $twig, $label, $sortKey)
    {
        $isCurrentSort = ($this->sorter->fetchData('sortBy') == $sortKey);

        return $twig->render('AlpixelElasticaUtilsBundle:blocks:sort_link.html.twig', array(
            'label'     => $label,
            'isCurrent' => $isCurrentSort,
            'sortKey'   => $sortKey,
            'sortOrder' => $this->sorter->fetchData('sortOrder'),
        ));
    }

    public function getName()
    {
        return 'alpixel_elastica_sorter_extension';
    }
}
