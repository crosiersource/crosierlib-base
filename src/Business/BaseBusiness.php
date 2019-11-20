<?php

namespace CrosierSource\CrosierLibBaseBundle\Business;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Class BaseBusiness.
 *
 * Classe base para fornecer recursos padrão a todos os Business comuns.
 *
 * @author Carlos Eduardo Pauluk
 */
class BaseBusiness
{

    private $doctrine;

    private $logger;



    /**
     * @return EntityManagerInterface
     */
    public function getDoctrine(): EntityManagerInterface
    {
        return $this->doctrine;
    }

    /**
     * @required
     * @param EntityManagerInterface $doctrine
     */
    public function setDoctrine(EntityManagerInterface $doctrine): void
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

