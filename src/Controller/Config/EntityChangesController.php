<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller\Config;

use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\InfluxDbEntityChangesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EntityChangesController extends BaseController
{

    /**
     * @Route("/api/core/config/entityChanges/", name="api_core_config_entityChanges")
     */
    public function getEntityChanges(
        Request                         $request,
        InfluxDbEntityChangesRepository $influxRepo,
    ): JsonResponse
    {
        $entityClass = $request->get('entityClass');
        $entityId = $request->get('entityId');
        // $created = $influxRepo->findCreatedByEntityClassAndEntityId($entityClass, $entityId);
        $created = [];
        $changes = $influxRepo->findChangesByEntityClassAndEntityId($entityClass, $entityId);

        $hydra = [
            '@context' => '/api/contexts/EntityChange',
            '@id' => '/api/core/config/entityChanges/',
            '@type' => 'hydra:Collection',
            'hydra:member' => $changes,
            'hydra:totalItems' => count($changes),
        ];

        return new JsonResponse($hydra);
    }

}