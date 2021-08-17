<?php

namespace CrosierSource\CrosierLibBaseBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package CrosierSource\CrosierLibBaseBundle\DependencyInjection
 * @author Carlos Eduardo Pauluk
 */
class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        return new TreeBuilder('CrosierLibBaseBundle');
    }
}