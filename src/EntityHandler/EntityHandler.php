<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler;


use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
use ReflectionClass;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Classe abstrata responsável pela lógica ao salvar ou deletar entidades na base de dados.
 *
 * @author Carlos Eduardo Pauluk
 */
abstract class EntityHandler implements EntityHandlerInterface
{

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    public function __construct(RegistryInterface $doctrine, Security $security, ParameterBagInterface $parameterBag)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return RegistryInterface
     */
    public function getDoctrine(): RegistryInterface
    {
        return $this->doctrine;
    }

    /**
     *
     * @return mixed
     */
    abstract public function getEntityClass();

    /**
     * Para ser sobreescrito.
     *
     * @param $entityId
     * @return mixed|void
     */
    public function beforeSave($entityId)
    {

    }

    /**
     * Implementação vazia pois não é obrigatório.
     *
     * @param $entityId
     */
    public function handleSavingEntityId(/** @var EntityId $entityId */
        $entityId)
    {
        $this->handleUppercaseFields($entityId);

        if (!$entityId->getId()) {
            $entityId->setInserted(new \DateTime('now'));
        }
        $entityId->setUpdated(new \DateTime('now'));

        if ($this->security->getUser()) {
            /** @var User $user */
            $user = $this->security->getUser();
            $entityId->setEstabelecimentoId($user->getEstabelecimentoId());
            $entityId->setUserUpdatedId($user->getId());
            if (!$entityId->getId()) {
                $entityId->setUserInsertedId($user->getId());
            }
        } else {
            if (!$entityId->getEstabelecimentoId()) {
                $entityId->setEstabelecimentoId(1);
            }
            if (!$entityId->getUserInsertedId()) {
                $entityId->setUserInsertedId(1);
            }
            if (!$entityId->getUserUpdatedId()) {
                $entityId->setUserUpdatedId(1);
            }
        }
    }

    /**
     * Executa o persist/update e o flush.
     *
     * @param EntityId $entityId
     * @param bool $flush
     * @return EntityId|object
     * @throws ViewException
     */
    public function save(EntityId $entityId, $flush = true)
    {
        try {
            $this->handleSavingEntityId($entityId);
            $this->beforeSave($entityId);
            if ($entityId->getId()) {
                $entityId = $this->doctrine->getEntityManager()->merge($entityId);
            } else {
                $this->doctrine->getEntityManager()->persist($entityId);
            }
            if ($flush) {
                $this->doctrine->getEntityManager()->flush();
            }
            $this->afterSave($entityId);
        } catch (\Exception $e) {
            throw new ViewException('Erro ao salvar (' . $e->getMessage() . ')', 0, $e);
        }
        return $entityId;
    }

    /**
     * Implementação vazia pois não é obrigatório.
     *
     * @param $entityId
     */
    public function afterSave($entityId)
    {
    }

    /**
     * Implementação vazia pois não é obrigatório.
     *
     * @param $entityId
     */
    public function beforeDelete($entityId)
    {
    }

    /**
     * Executa o DELETE e o flush.
     *
     * @param $entityId
     * @throws ViewException
     */
    public function delete($entityId)
    {
        try {
            $this->beforeDelete($entityId);
            $this->doctrine->getEntityManager()->remove($entityId);
            $this->doctrine->getEntityManager()->flush();
            $this->afterDelete($entityId);
        } catch (\Exception $e) {
            $msg = ExceptionUtils::treatException($e);
            throw new ViewException('Erro ao deletar' . ($msg ? ' (' . $msg . ')' : '') , 0, $e);
        }
    }

    /**
     * Implementação vazia pois não é obrigatório.
     *
     * @param $entityId
     */
    public function afterDelete($entityId)
    {
    }

    /**
     * Implementação vazia pois não é obrigatório.
     *
     * @param $entityId
     */
    public function beforeClone($entityId)
    {
    }

    /**
     * Copia o objeto removendo informações específicas.
     *
     * @param $e
     * @return EntityId|object
     * @throws ViewException
     */
    public function doClone($e)
    {
        /** @var EntityId $newE */
        $newE = clone $e;
        $newE->setId(null);
        $newE->setInserted(null);
        $newE->setUpdated(null);
        $newE->setUserInsertedId(null);
        $newE->setUserUpdatedId(null);
        $this->beforeClone($newE);
        $this->save($newE);
        return $newE;
    }


    private function handleUppercaseFields($entityId): void
    {
        $uppercaseFieldsJson = file_get_contents($this->parameterBag->get('kernel.project_dir') . '/src/Entity/uppercaseFields.json');
        $uppercaseFields = json_decode($uppercaseFieldsJson);
        $class = str_replace('\\', '_', get_class($entityId));
        $reflectionClass = new ReflectionClass(get_class($entityId));
        $campos = isset($uppercaseFields->$class) ? $uppercaseFields->$class : array();
        foreach ($campos as $field) {
            $property = $reflectionClass->getProperty($field);
            $property->setAccessible(true);
            $property->setValue($entityId, mb_strtoupper($property->getValue($entityId)));
        }
    }


}