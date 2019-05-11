<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient;


/**
 * Class CrosierEntityIdAPIClient.
 * Classe padrão para interagir com classes BaseAPIEntityIdController.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient
 * @author Carlos Eduardo Pauluk
 */
class CrosierEntityIdAPIClient extends CrosierAPIClient
{

    /**
     * @param string $baseURI
     * @return CrosierEntityIdAPIClient
     */
    public function setBaseURI(string $baseURI)
    {
        $this->baseURI = $baseURI;
        return $this;
    }

    /**
     * @param int $id
     * @return null|array
     */
    public function findById(int $id): ?array
    {
        try {
            $r = $this->get('/findById/' . $id);
            $r = json_decode($r, true)['result'];
            return $r;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param array $filters
     * @param int $start
     * @param int $limit
     * @return null|array
     */
    public function findByFilters(array $filters, int $start = 0, int $limit = 100): ?array
    {
        try {
            $r = $this->get('/findByFilters/', ['filters' => urlencode(json_encode($filters)), 'start' => $start, 'limit' => $limit]);
            return json_decode($r, true);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     *
     * @param array $filters
     * @return null|array
     */
    public function findOneByFilters(array $filters): ?array
    {
        try {
            // Pesquisa pelo menos 2 para poder executar a lógica.
            $r = $this->get('/findByFilters/', ['filters' => urlencode(json_encode($filters)), 'start' => 0, 'limit' => 2]);
            $d = json_decode($r, true);
            if ($d && $d['results']) {
                if (count($d['results']) !== 1) {
                    throw new \LogicException('Mais de 1 resultado para findOneByFilters');
                }
                return $d['results'][0];
            }
            return null;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erro em findOneByFilters', 0, $e->getMessage());
        }
    }


    /**
     * @return mixed
     */
    public function getNew()
    {
        $r = $this->get('/getNew');
        return json_decode($r, true);
    }


    /**
     * @param array $objArray
     * @return mixed
     */
    public function save(array $objArray) {
        $r = $this->post('/save', ['entity' => $objArray]);
        return json_decode($r);
    }


}