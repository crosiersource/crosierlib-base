<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler;


use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
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
     * @var EntityManagerInterface
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

    /**
     * EntityHandler constructor.
     * @param EntityManagerInterface $doctrine
     * @param Security $security
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(EntityManagerInterface $doctrine, Security $security, ParameterBagInterface $parameterBag)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getDoctrine(): EntityManagerInterface
    {
        return $this->doctrine;
    }

    /**
     *
     * @return mixed
     */
    abstract public function getEntityClass();

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
            $this->doctrine->remove($entityId);
            $this->doctrine->flush();
            $this->afterDelete($entityId);
        } catch (\Exception $e) {
            $msg = ExceptionUtils::treatException($e);
            throw new ViewException('Erro ao deletar' . ($msg ? ' (' . $msg . ')' : ''), 0, $e);
        }
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
     * Implementação vazia pois não é obrigatório.
     *
     * @param $entityId
     */
    public function afterDelete($entityId)
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

    /**
     * Implementação vazia pois não é obrigatório.
     *
     * @param $entityId
     */
    public function beforeClone($entityId)
    {
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
            if (!$entityId->getId()) {
                $this->doctrine->persist($entityId);
            }
            if ($flush) {
                $this->doctrine->flush();
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
     * @throws \Exception
     */
    public function handleSavingEntityId($entityId): void
    {
        /** @var EntityId $entityId */
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

    private function handleUppercaseFields($entityId): void
    {
        $uppercaseFieldsJson = file_get_contents($this->parameterBag->get('kernel.project_dir') . '/src/Entity/uppercaseFields.json');
        $uppercaseFields = json_decode($uppercaseFieldsJson);
        $class = str_replace('\\', '_', get_class($entityId));
        $reflectionClass = new ReflectionClass(get_class($entityId));
        $campos = $uppercaseFields->$class ?? [];
        foreach ($campos as $field) {
            $property = $reflectionClass->getProperty($field);
            $property->setAccessible(true);
            $property->setValue($entityId, mb_strtoupper($property->getValue($entityId)));
        }
    }

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
    public function afterSave($entityId)
    {
    }


}