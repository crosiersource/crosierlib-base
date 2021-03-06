<?php

namespace CrosierSource\CrosierLibBaseBundle\DependencyInjection;

use CrosierSource\CrosierLibBaseBundle\Repository\Security\UserRepository;
use CrosierSource\CrosierLibBaseBundle\Twig\RouteExistsExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class CrosierLibBaseExtension
 *
 * @package CrosierSource\CrosierLibBaseBundle\DependencyInjection
 * @author Carlos Eduardo Pauluk
 */
class CrosierLibBaseExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->registerForAutoconfiguration(RouteExistsExtension::class)
            ->addTag('twig.extension');


        $container->registerForAutoconfiguration(UserRepository::class)
            ->addTag('doctrine.repository_service');
    }


}