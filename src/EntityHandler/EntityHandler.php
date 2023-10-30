<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler;


use CrosierSource\CrosierLibBaseBundle\Business\Config\SyslogBusiness;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
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

    protected ManagerRegistry $managerRegistry;

    protected EntityManagerInterface $doctrine;

    protected Security $security;

    protected ParameterBagInterface $parameterBag;

    protected SyslogBusiness $syslog;

    protected bool $isTransacionalSave = false;

    protected bool $willFlush = true;

    protected bool $salvouLogInsert = false;


    public function __construct(ManagerRegistry       $managerRegistry,
                                Security              $security,
                                ParameterBagInterface $parameterBag,
                                SyslogBusiness        $syslog)
    {
        $this->managerRegistry = $managerRegistry;
        $this->doctrine = $managerRegistry->getManager();
        $this->security = $security;
        $this->parameterBag = $parameterBag;
        $this->syslog = $syslog;
    }

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
            if ($this->isTransacionalSave) {
                $this->doctrine->beginTransaction();
            }
            $this->beforeDelete($entityId);
            $this->doctrine->remove($entityId);
            $this->doctrine->flush();
            $this->afterDelete($entityId);
            if ($this->isTransacionalSave) {
                $this->doctrine->commit();
            }
        } catch (\Exception $e) {
            if ($this->isTransacionalSave) {
                $this->doctrine->rollback();
            }
            $msg = ExceptionUtils::treatException($e);
            throw new ViewException('Erro ao deletar' . ($msg ? ' (' . $msg . ')' : ''), 0, $e, $this->syslog);
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
     * @param EntityId $e
     * @return EntityId
     */
    public function cloneEntityId(EntityId $e)
    {
        $newE = clone $e;
        $this->beforeClone($newE);
        $newE->setId(null);
        $newE->setInserted(null);
        $newE->setUpdated(null);
        $newE->setUserInsertedId(null);
        $newE->setUserUpdatedId(null);
        $newE->setEstabelecimentoId(null);
        return $newE;
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
        $newE = $this->cloneEntityId($e);
        $this->afterClone($newE, $e);
        $this->save($newE);
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
            if ($this->isTransacionalSave) {
                $this->doctrine->beginTransaction();
            }
            $this->handleSavingEntityId($entityId);
            $this->willFlush = $flush;
            $this->beforeSave($entityId);
            $inserting = false;
            if ($entityId->getId()) {
                $entityId = $this->doctrine->merge($entityId);
            } else {
                $inserting = true;
                $this->doctrine->persist($entityId);
            }

            if ($flush) {
                $this->doctrine->flush();
            }

            try {
                if (!$this->salvouLogInsert && $inserting && $entityId->getId()) {

                    $class = str_replace("\\", ":", get_class($entityId));
                    
                    $entityChange = [
                        'entity_class' => $class,
                        'entity_id' => $entityId->getId(),
                        'changed_at' => $entityId->getUpdated()->format('Y-m-d H:i:s'),
                        'changes' => 'INSERINDO',
                    ];

                    /** @var User $user */
                    $user = $this->security->getUser();
                    
                    if (!$user) {
                        $entityChange['changing_user_id'] = 0;
                        $entityChange['changing_user_username'] = 'n/d';
                        $entityChange['changing_user_nome'] = 'n/d';
                    } else {
                        $entityChange['changing_user_id'] = $user->getUserInsertedId();
                        $entityChange['changing_user_username'] = $user->username;
                        $entityChange['changing_user_nome'] = $user->nome;
                    }

                    $this->managerRegistry->getManager('logs')->getConnection()->insert('cfg_entity_change', $entityChange);
                    $this->salvouLogInsert = true;
                }
            } catch (\Throwable $e) {
                $this->syslog->err('Erro ao inserir em cfg_entity_change', $e->getMessage());
            }

            $this->afterSave($entityId);

            $this->handleJsonMetadata();
            if ($this->isTransacionalSave) {
                $this->doctrine->commit();
            }
            $this->posAfterSave($entityId);
        } catch (\Throwable $e) {
            if ($this->isTransacionalSave) {
                $this->doctrine->rollback();
            }
            $msg = ExceptionUtils::treatException($e);
            $msg = $msg ? 'Erro ao salvar (' . $msg . ')' : 'Erro ao salvar';
            throw new ViewException($msg, 0, $e, $this->syslog);
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
                if (!$entityId->getEstabelecimentoId()) {
                    $entityId->setEstabelecimentoId($user->getEstabelecimentoId());
                }
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
            $msg = 'Erro ao handleSavingEntityId (' . ExceptionUtils::treatException($e) . ')';
            $this->syslog->err($msg);
            throw new \RuntimeException($msg);
        }
    }


    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function handleJsonMetadata()
    {
        $tableName = $this->doctrine->getClassMetadata($this->getEntityClass())->getTableName();

        $conn = $this->getDoctrine()->getConnection();
        $rConfig = $conn->fetchAllAssociative('SELECT * FROM cfg_app_config WHERE app_uuid = :appUUID AND chave = :chave', ['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => $tableName . '_json_metadata']);

        if ($rConfig) {
            $cfgAppConfig = $rConfig[0];
            $jsonMetadata = json_decode($cfgAppConfig['valor'], true);
            $mudou = null;
            foreach ($jsonMetadata['campos'] as $campo => $metadata) {
                if ((($metadata['tipo'] ?? '') === 'tags') &&
                    (isset($metadata['sugestoes']) or (strpos(($metadata['class'] ?? ''), 's2allownew') !== FALSE))) {
                    $valoresNaBase = $conn->fetchAllAssociative('SELECT distinct(json_data->>"$.' . $campo . '") as val FROM ' . $tableName . ' WHERE json_data->>"$.' . $campo . '" NOT IN (\'\',\'null\') ORDER BY json_data->>"$.' . $campo . '"');
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
                $property->setValue($entityId, trim(mb_strtoupper($property->getValue($entityId))));
            }
        } catch (\ReflectionException $e) {
            $msg = ExceptionUtils::treatException($e);
            $msg = 'Erro em handleUppercaseFields (' . $msg . ')';
            $this->syslog->err($msg);
            throw new \RuntimeException($msg, 0, $e);
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


    /**
     * Implementação vazia pois não é obrigatório.
     *
     * @param $entityId
     */
    public function posAfterSave($entityId)
    {
    }


    public function updateUpdated(string $tableName, int $id)
    {
        if (!$id) {
            throw new ViewException('Impossível realizar updated sem id da entidade');
        }
        $userId = null;
        if ($this->security->getUser()) {
            /** @var User $user */
            $user = $this->security->getUser();
            $userId = $user->getId();
        }
        $params = [
            'updated' => DateTimeUtils::getSQLFormatted(),
        ];
        if ($userId) {
            $params['user_updated_id'] = $userId;
        }
        $this->getDoctrine()->getConnection()->update($tableName, $params, ['id' => $id]);
    }


    protected function getRegistroDaTabela(EntityId $entityId): ?array
    {
        $tableName = $this->doctrine->getClassMetadata($this->getEntityClass())->getTableName();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id = :id';
        $params = ['id' => $entityId->getId()];
        return $this->getDoctrine()->getConnection()->fetchAssociative($sql, $params);
    }

}
