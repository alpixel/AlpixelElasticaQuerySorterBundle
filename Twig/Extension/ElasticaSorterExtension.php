<?php

namespace Alpixel\Bundle\ElasticaQuerySorterBundle\Twig\Extension;

use Alpixel\Bundle\ElasticaQuerySorterBundle\Services\ElasticaQuerySorter;

class ElasticaSorterExtension extends \Twig_Extension
{
    protected $sorter;
    protected $configuration;

    public function __construct(ElasticaQuerySorter $sorter, $configuration)
    {
        $this->sorter = $sorter;
        $this->configuration = $configuration;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('elastica_sort', [$this, 'displaySort'], [
                'is_safe'           => ['html'],
                'needs_environment' => true,
            ]),
            new \Twig_SimpleFunction('elastica_clear_sort', [$this, 'clearSort'], [
                'is_safe'           => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function clearSort(\Twig_Environment $twig)
    {
        return $twig->render($this->configuration['clear_sort']);
    }

    public function displaySort(\Twig_Environment $twig, $label, $sortKey)
    {
        $isCurrentSort = ($this->sorter->fetchData('sortBy') == $sortKey);

        return $twig->render($this->configuration['sort_link'], [
            'label'     => $label,
            'isCurrent' => $isCurrentSort,
            'sortKey'   => $sortKey,
            'sortOrder' => $this->sorter->fetchData('sortOrder'),
        ]);
    }

    public function getName()
    {
        return 'alpixel_elastica_sorter_extension';
    }
}
