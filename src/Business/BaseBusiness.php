<?php

namespace CrosierSource\CrosierLibBaseBundle\Business;

use Psr\Log\LoggerInterface;
use Doctrine\Common\Persistence\ManagerRegistry;


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
     * @return ManagerRegistry
     */
    public function getDoctrine(): ManagerRegistry
    {
        return $this->doctrine;
    }

    /**
     * @required
     * @param ManagerRegistry $doctrine
     */
    public function setDoctrine(ManagerRegistry $doctrine): void
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

