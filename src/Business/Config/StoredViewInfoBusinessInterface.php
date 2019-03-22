<?php

namespace CrosierSource\CrosierLibBaseBundle\Business\Config;

use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandlerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Interface StoredViewInfoBusinessInterface
 * @package CrosierSource\CrosierLibBaseBundle\Business\Config
 * @author Carlos Eduardo Pauluk
 */
interface StoredViewInfoBusinessInterface
{

    public function getDoctrine(): RegistryInterface;

    public function getSecurity(): Security;

    public function getEntityHandler(): EntityHandlerInterface;

    public function store(string $viewName, string $viewInfo);

    public function retrieve(string $viewRoute);

    public function clear(string $viewRoute);

}