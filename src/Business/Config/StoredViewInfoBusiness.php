<?php


namespace CrosierSource\CrosierLibBaseBundle\Business\Config;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\StoredViewInfo;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\StoredViewInfoEntityHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\User;

/**
 * Class StoredViewInfoBusiness
 *
 * @package CrosierSource\CrosierLibBaseBundle\Business\Config
 * @author Carlos Eduardo Paulk
 */
class StoredViewInfoBusiness
{

    /** @var RegistryInterface */
    private $doctrine;

    /** @var Security */
    private $security;

    /** @var StoredViewInfoEntityHandler */
    private $entityHandler;

    /**
     * StoredViewInfoBusiness constructor.
     * @param RegistryInterface $doctrine
     * @param Security $security
     * @param StoredViewInfoEntityHandler $entityHandler
     */
    public function __construct(Security $security, RegistryInterface $doctrine, StoredViewInfoEntityHandler $entityHandler)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->entityHandler = $entityHandler;
    }

    /**
     * @param $viewName
     * @param $viewInfo
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function store($viewName, $viewInfo): void
    {
        $serialized = json_encode($viewInfo);

        $params['viewName'] = $viewName;
        /** @var User $user */
        $user = $this->security->getUser();
        $params['user'] = $user->getId();
        $storedViewInfo = $this->doctrine->getRepository(StoredViewInfo::class)->findOneBy($params);
        if (!$storedViewInfo) {
            $storedViewInfo = new StoredViewInfo();
            $storedViewInfo->setViewName($viewName);
            $storedViewInfo->setUser($params['user']);

        }
        $storedViewInfo->setViewInfo($serialized);

        $this->entityHandler->save($storedViewInfo);
    }

    /**
     * @param $viewRoute
     * @return null|object
     */
    public function retrieve($viewRoute)
    {
        $params['viewName'] = $viewRoute;
        $params['user'] = $this->security->getUser();
        if ($r = $this->doctrine->getRepository(StoredViewInfo::class)->findOneBy($params)) {
            $viewInfo = json_decode($r->getViewInfo(), true);
            return $viewInfo;
        }
        return null;

    }

    /**
     * @param $viewRoute
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function clear($viewRoute): void
    {
        $params['viewName'] = $viewRoute;
        $params['user'] = $this->security->getUser();

        $storedViewInfo = $this->doctrine->getRepository(StoredViewInfo::class)->findOneBy($params);
        if ($storedViewInfo) {
            $this->entityHandler->delete($storedViewInfo);
        }
    }


}