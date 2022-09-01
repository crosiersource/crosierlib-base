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
                        } elseif (is_bool($v)) {
                            $changes[$k] = $v ? 'SIM' : 'NÃO';
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
                        $arrDiff0_1 = self::array_diff_assoc_recursive($changes[0], $changes[1]);
                        $arrDiff1_0 = self::array_diff_assoc_recursive($changes[1], $changes[0]);
                        $arrDiff = array_merge_recursive($arrDiff0_1, $arrDiff1_0);
                        ksort($arrDiff);
                        foreach ($arrDiff as $k => $diff) {
                            $from = is_array($diff) ? json_encode($changes[0][$k] ?? []) : ($changes[0][$k] ?? '_NULL_');
                            $to = is_array($diff) ? json_encode($changes[1][$k] ?? []) : ($changes[1][$k] ?? '_NULL_');
                            $strChanges .= 'jsonData.' . $k . ': de "' . $from . '" para "' . $to . '"' . PHP_EOL;
                        }
                    } else if ((string)$changes[0] !== (string)$changes[1]) {
                        $strChanges .= $field . ': de "' . $changes[0] . '" para "' . $changes[1] . '"' . PHP_EOL;
                    }

                }
                if ($strChanges) {
                    $entityChange = [
                        'entity_class' => get_class($entity),
                        'entity_id' => $entity->getId(),
                        'changing_user_id' => $entity->getUserUpdatedId(),
                        'changed_at' => $entity->getUpdated()->format('Y-m-d H:i:s'),
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

    private static function array_diff_assoc_recursive($array1, $array2)
    {
        $array1 = is_array($array1) ? $array1 : [$array1];
        $array2 = is_array($array2) ? $array2 : [$array2];
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key])) {
                    $difference[$key] = $value;
                } elseif (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = self::array_diff_assoc_recursive($value, $array2[$key]);
                    if ($new_diff != FALSE) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!array_key_exists($key, $array2) || $array2[$key] != $value) {
                $difference[$key] = $value;
            }
        }
        return !isset($difference) ? [] : $difference;
    }

}
