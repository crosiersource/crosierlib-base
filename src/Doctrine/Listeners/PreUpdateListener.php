<?php


namespace CrosierSource\CrosierLibBaseBundle\Doctrine\Listeners;


use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class PreUpdateListener
 *
 * @package CrosierSource\CrosierLibBaseBundle\Doctrine\Listeners
 * @author Carlos Eduardo Pauluk
 */
class PreUpdateListener
{

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
        $classes = $cache->get('trackedEntities', function (ItemInterface $item) use ($entityManager) {
            $all = $entityManager->getMetadataFactory()->getAllMetadata();
            $annotationReader = new AnnotationReader();

            $classes = [];
            foreach ($all as $classMeta) {
                $reflectionClass = $classMeta->getReflectionClass();
                if ($annotationReader->getClassAnnotation($reflectionClass, 'CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\TrackedEntity')) {
                    $classes[] = $classMeta->getName();
                }
            }
            return $classes;
        });

        /** @var EntityId $entity */
        $entity = $args->getObject();

        if (in_array(get_class($entity), $classes, true)) {
            $strChanges = '';
            /** @var array $entityChangeSet */
            $entityChangeSet = $args->getEntityChangeSet();
            foreach ($entityChangeSet as $field => $changes) {
                if ($field === 'updated') continue;
                foreach ($changes as $k => $v) {
                    if ($changes[$k] instanceof \DateTime) {
                        $changes[$k] = $changes[$k]->format('d/m/Y H:i:s T');
                    } elseif ($changes[$k] instanceof EntityId) {
                        $changes[$k] = $changes[0]->__toString() ?: $changes[0]->getId();
                    } elseif (is_numeric($changes[$k])) {
                        $changes[$k] = (float)$changes[$k];
                    } elseif (!is_array($changes[$k])) {
                        $changes[$k] = (string)$changes[$k];
                    }
                }
                if ($field === 'jsonData') {
                    $arrDiff = array_diff($changes[0], $changes[1]);
                    foreach ($arrDiff as $k => $diff) {
                        $strChanges .= 'jsonData.' . $k . ': de "' . $changes[0][$k] . '" para "' . $changes[1][$k] . '"' . PHP_EOL;
                    }
                } else if ((string)$changes[0] !== (string)$changes[1]) {
                    $strChanges .= $field . ': de "' . $changes[0] . '" para "' . $changes[1] . '"' . PHP_EOL;
                }

            }
            if ($strChanges) {
                $entityChange = [
                    'entity_class' => get_class($entity),
                    'entity_id' => $entity->getId(),
                    'changing_user_id' => $entity->getUserInsertedId(),
                    'changed_at' => $entity->getUpdated()->format('Y-m-d'),
                    'changes' => $strChanges,
                ];

                try {
                    $conn = $entityManager->getConnection();
                    $conn->insert('cfg_entity_change', $entityChange);
                } catch (DBALException $e) {
                    throw new \RuntimeException('Erro ao salvar na cfg_entity_change para ' . get_class($entity));
                }
            }


        }

    }

}
