<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;

/**
 * Cliente para consumir os serviços REST da DiaUtilAPI.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient\Base
 * @author Carlos Eduardo Pauluk
 */
class DiaUtilAPIClient extends CrosierAPIClient
{

    public static function getBaseUri(): string
    {
        return $_SERVER['CROSIERCORE_URL'] . '/api/bse/diaUtil';
    }

    /**
     * @param int $id
     * @return string
     */
    public function incPeriodo(\DateTime $ini, \DateTime $fim, bool $futuro)
    {
        $params = [
            'ini' => $ini->format('Y-m-d'),
            'fim' => $fim->format('Y-m-d'),
            'futuro' => $futuro
        ];
        $contents = $this->get('/incPeriodo/', $params, true);
        return json_decode($contents, true);
    }

    /**
     * @param \DateTime $dt
     * @param bool $prox
     * @param bool $financeiro
     * @param bool $comercial
     * @return \DateTime|mixed
     * @throws ViewException
     */
    public function findDiaUtil(\DateTime $dt, bool $prox = true, ?bool $financeiro = null, ?bool $comercial = null): ?\DateTime
    {
        $params = [
            [
                'dt' => $dt->format('Y-m-d'),
                'prox' => $prox,
                'financeiro' => $financeiro,
                'comercial' => $comercial,
            ]
        ];
        $r = json_decode($this->post('/findDiaUtil/', $params), true);

        if (!isset($r['diaUtil'])) {
            $this->handleErrorResponse($r);
        }
        $diaUtil = $r['diaUtil'];
        return \DateTime::createFromFormat('Y-m-d H:i:s.u', $diaUtil['date']);
    }

    /**
     * @param \DateTime $mesano
     * @return mixed
     */
    public function findDiasUteisFinanceirosByMesAno(\DateTime $mesano)
    {
        $params = [
            'mesano' => $mesano->format('Y-m'),
        ];
        $r = json_decode($this->get('/findDiasUteisFinanceirosByMesAno/', $params));
        return $r;
    }


}