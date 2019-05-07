<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient;


/**
 * Class CrosierEntityIdAPIClient.
 * Classe padrão para interagir com classes BaseAPIEntityIdController.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient
 * @author Carlos Eduardo Pauluk
 */
abstract class CrosierEntityIdAPIClient extends CrosierAPIClient
{

    /**
     * @param int $id
     * @return null|array
     */
    public function findById(int $id): ?array
    {
        try {
            $r = $this->post('/findById/' . $id);
            $r = json_decode($r, true);
            return $r['result'];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param array $filters
     * @return null|array
     */
    public function findByFilters(array $filters): ?array
    {
        try {
            $r = $this->post('/findByFilters/', $filters);
            return json_decode($r, true);
        } catch (\Exception $e) {
            return null;
        }
    }

}