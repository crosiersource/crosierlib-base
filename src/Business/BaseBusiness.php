<?php

namespace CrosierSource\CrosierLibBaseBundle\Business;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * Class BaseBusiness.
 *
 * Classe base para fornecer recursos padrão a todos os Business comuns.
 *
 * @package App\Business
 * @author Carlos Eduardo Pauluk
 */
class BaseBusiness
{

    private $doctrine;

    private $logger;



    /**
     * @return RegistryInterface
     */
    public function getDoctrine(): RegistryInterface
    {
        return $this->doctrine;
    }

    /**
     * @required
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return mixed
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @required
     * @param mixed $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }



}

