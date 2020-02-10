<?php

namespace CrosierSource\CrosierLibBaseBundle\Business;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;


/**
 * Class BaseBusiness.
 *
 * Classe base para fornecer recursos padrão a todos os Business comuns.
 *
 * @author Carlos Eduardo Pauluk
 */
class BaseBusiness
{

    /** @var EntityManagerInterface */
    private $doctrine;

    /** @var LoggerInterface */
    protected $logger;


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

