<?php

namespace CrosierSource\CrosierLibBaseBundle\Business\Config;

interface StoredViewInfoBusinessInterface
{

    private $doctrine;

    private $security;

    private $entityHandler;


    public function getDoctrine():RegistryInterface;

    public function getSecurity(): Security;

    public function getEntityHandler():


    public function store(string $viewName, string $viewInfo);

    public function retrieve(string $viewRoute);

    public function clear(string $viewRoute);

}