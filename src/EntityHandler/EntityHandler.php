<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler;


use CrosierSource\CrosierLibBaseBundle\Business\Config\SyslogBusiness;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
use Doctrine\DBAL\Connection;
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

    protected EntityManagerInterface $doctrine;

    protected Security $security;

    protected ParameterBagInterface $parameterBag;

    protected SyslogBusiness $syslog;

    /**
     * @param EntityManagerInterface $doctrine
     * @param Security $security
     * @param ParameterBagInterface $parameterBag
     * @param SyslogBusiness $syslog
     */
    public function __construct(EntityManagerInterface $doctrine,
                                Security $security,
                                ParameterBagInterface $parameterBag,
                                SyslogBusiness $syslog)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->parameterBag = $parameterBag;
        $this->syslog = $syslog;
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
        $this->getDoctrine()->beginTransaction();
        /** @var EntityId $newE */
        $newE = clone $e;
        $newE->setId(null);
        $newE->setInserted(null);
        $newE->setUpdated(null);
        $newE->setUserInsertedId(null);
        $newE->setUserUpdatedId(null);
        $this->beforeClone($newE);
        $this->save($newE);
        $this->afterClone($newE, $e);
        $this->getDoctrine()->commit();
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
     * Implementação vazia pois não é obrigatório.
     *
     * @param $newEntityId
     * @param $oldEntityId
     * @throws ViewException
     */
    public function afterClone($newEntityId, $oldEntityId)
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
            if ($entityId->getId()) {
                $entityId = $this->doctrine->merge($entityId);
            } else {
                $this->doctrine->persist($entityId);
            }

            if ($flush) {
                $this->doctrine->flush();
            }
            $this->afterSave($entityId);
            $this->handleJsonMetadata();
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
    public function handleSavingEntityId($entityId): void
    {

        try {
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
        } catch (\Exception $e) {
            throw new \RuntimeException('Erro ao handleSavingEntityId');
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function handleJsonMetadata()
    {
        $tableName = $this->doctrine->getClassMetadata($this->getEntityClass())->getTableName();

        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection();
        $rConfig = $conn->fetchAll('SELECT * FROM cfg_app_config WHERE app_uuid = :appUUID AND chave = :chave', ['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => $tableName . '_json_metadata']);

        if ($rConfig) {
            $cfgAppConfig = $rConfig[0];
            $jsonMetadata = json_decode($cfgAppConfig['valor'], true);
            $mudou = null;
            foreach ($jsonMetadata['campos'] as $campo => $metadata) {
                if (isset($metadata['sugestoes']) &&
                    (isset($metadata['tipo']) && $metadata['tipo'] === 'tags') or
                    (isset($metadata['class']) && strpos($metadata['class'], 's2allownew') !== FALSE)) {
                    $valoresNaBase = $conn->fetchAll('SELECT distinct(json_data->>"$.' . $campo . '") as val FROM ' . $tableName . ' WHERE json_data->>"$.' . $campo . '" NOT IN (\'\',\'null\') ORDER BY json_data->>"$.' . $campo . '"');
                    foreach ($valoresNaBase as $v) {
                        $valExploded = explode(',', $v['val']);
                        foreach ($valExploded as $val) {
                            if ($val && !in_array($val, $metadata['sugestoes'])) {
                                $metadata['sugestoes'][] = $val;
                                $mudou = true;
                            }
                        }
                    }
                    if ($mudou) {
                        sort($metadata['sugestoes']);
                        $jsonMetadata['campos'][$campo]['sugestoes'] = $metadata['sugestoes'];
                    }
                }
            }
            if ($mudou) {
                $cfgAppConfig['valor'] = json_encode($jsonMetadata);
                $cfgAppConfig['is_json'] = (bool)$cfgAppConfig['is_json'] ? 1 : 0;
                $conn->update('cfg_app_config', $cfgAppConfig, ['id' => $cfgAppConfig['id']]);
            }
        }
    }

    /**
     * @param $entityId
     */
    private function handleUppercaseFields($entityId): void
    {
        try {
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
        } catch (\ReflectionException $e) {
            throw new \RuntimeException('Erro em handleUppercaseFields', 0, $e);
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
