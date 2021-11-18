<?php


namespace CrosierSource\CrosierLibBaseBundle\Doctrine\Listeners;


use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

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
        /** @var EntityId $entity */
        $entity = $args->getObject();

        try {
            $entityManager = $args->getEntityManager();
            $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
            $classes = $cache->get('trackedEntities', function () use ($entityManager) {
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

            if (in_array(get_class($entity), $classes, true)) {
                $strChanges = '';
                /** @var array $entityChangeSet */
                $entityChangeSet = $args->getEntityChangeSet();
                foreach ($entityChangeSet as $field => $changes) {
                    if ($field === 'updated') continue;
                    foreach ($changes as $k => $v) {
                        if ($v instanceof \DateTime) {
                            $changes[$k] = $v->format('d/m/Y H:i:s T');
                        } elseif ($v instanceof EntityId) {
                            $changes[$k] = $v->__toString();
                        } elseif (is_numeric($v)) {
                            $changes[$k] = (float)$v;
                        } elseif (!is_array($v)) {
                            $changes[$k] = (string)$v;
                        } elseif ($field === 'jsonData') { //  && is_array($v)) {
                            continue; // será tratado ali abaixo
                        } else {
                            throw new \InvalidArgumentException('tipo de campo não tratável no PreUpdateListener');
                        }
                    }
                    if ($field === 'jsonData') {
                        $arrDiff = array_diff_assoc($changes[0], $changes[1]);
                        ksort($arrDiff);
                        foreach ($arrDiff as $k => $diff) {
                            $strChanges .= 'jsonData.' . $k . ': de "' . ($changes[0][$k] ?? '[null]') . '" para "' . ($changes[1][$k] ?? '[null]') . '"' . PHP_EOL;
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
                    } catch (\Exception $e) {
                        throw new \RuntimeException('Erro ao salvar na cfg_entity_change para ' . get_class($entity));
                    }
                }
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro no PreUpdateListener para ' . get_class($entity), 0, $e);
        }

    }

}
