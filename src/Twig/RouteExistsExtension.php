<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RouteExistsExtension
 *
 * @package CrosierSource\CrosierLibBaseBundle\Twig
 * @author Carlos Eduardo Pauluk
 */
class RouteExistsExtension extends AbstractExtension
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function getFunctions()
    {
        return array(
            new TwigFunction('routeExists', array($this, 'routeExists')),
        );
    }


    function routeExists($name)
    {

        $router = $this->container->get('router');
        return (null === $router->getRouteCollection()->get($name)) ? false : true;
    }
}