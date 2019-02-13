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
        return $this->get('/api/bse/diaUtil/incPeriodo/', $params);
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
        $r = json_decode($this->post('/api/bse/diaUtil/findDiaUtil/', $params), true);

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
        $r = json_decode($this->get('/api/bse/diaUtil/findDiasUteisFinanceirosByMesAno/', $params));
        return $r;
    }

}