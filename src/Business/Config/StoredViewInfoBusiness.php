<?php


namespace CrosierSource\CrosierLibBaseBundle\Business\Config;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\StoredViewInfo;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\StoredViewInfoEntityHandler;
use Doctrine\ORM\EntityManagerInterface;
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

    /** @var EntityManagerInterface */
    private $doctrine;

    /** @var Security */
    private $security;

    /** @var StoredViewInfoEntityHandler */
    private $entityHandler;

    /**
     * StoredViewInfoBusiness constructor.
     * @param EntityManagerInterface $doctrine
     * @param Security $security
     * @param StoredViewInfoEntityHandler $entityHandler
     */
    public function __construct(Security $security, EntityManagerInterface $doctrine, StoredViewInfoEntityHandler $entityHandler)
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
    public function store(string $viewName, array $viewInfo): void
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
     * Adiciona ou altera apenas um valor no array de viewInfo.
     *
     * @param $viewName
     * @param $val
     */
    public function set(string $viewName, array $val): void
    {
        $viewInfo = $this->retrieve($viewName) ?? [];
        $viewInfo = array_merge($viewInfo, $val);
        $this->store($viewName, $viewInfo);
    }

    /**
     * Remove um valor no array de viewInfo.
     *
     * @param $viewName
     * @param $val
     */
    public function remove(string $viewName, array $key): void
    {
        $viewInfo = $this->retrieve($viewName);
        if ($viewInfo && isset($viewInfo[$key])) {
            unset($viewInfo[$key]);
            $this->store($viewName, $viewInfo);
        }
    }

    /**
     * @param $viewRoute
     * @return null|array
     */
    public function retrieve(string $viewName): ?array
    {
        $params['viewName'] = $viewName;
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
    public function clear(string $viewName): void
    {
        $params['viewName'] = $viewName;
        $params['user'] = $this->security->getUser();

        $storedViewInfo = $this->doctrine->getRepository(StoredViewInfo::class)->findOneBy($params);
        if ($storedViewInfo) {
            $this->entityHandler->delete($storedViewInfo);
        }
    }


}