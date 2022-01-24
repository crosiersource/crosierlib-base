<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\Base\DiaUtil;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;

/**
 * Repository para a entidade DiaUtil.
 *
 * @author Carlos Eduardo Pauluk
 */
class DiaUtilRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return DiaUtil::class;
    }

    /**
     * @param \DateTime $dia
     * @return DiaUtil|null
     */
    public function doFindBy(\DateTime $dia): ?DiaUtil
    {
        $dql = 'SELECT d FROM DiaUtil d WHERE d.dia = :dia';

        $em = $this->getEntityManager();
        $query = $em->createQuery($dql);
        $query->setParameters(array(
            $dia
        ));

        return $query->getResult();
    }

    /**
     * Retorna o próximo dia útil financeiro (incluindo o dia passado).
     * @param \DateTime $dia
     * @param bool $prox
     * @param bool|null $financeiro
     * @param bool|null $comercial
     * @return \DateTime|null
     */
    public function findDiaUtil(\DateTime $dia, bool $prox = null, ?bool $financeiro = null, ?bool $comercial = null): ?\DateTime
    {

        try {
            $ini = clone $dia;
            $fim = clone $dia;
            if ($prox === null) {
                // Pode ser o mesmo passado
                $ini->add(new \DateInterval('P0D'));
                $fim->add(new \DateInterval('P20D'));
            } else if ($prox === true) {
                // Deve necessariamente ser o próximo dia útil
                $ini->add(new \DateInterval('P1D'));
                $fim->add(new \DateInterval('P20D'));
            } else if ($prox === false) {
                // Deve necessatiamente ser o dia útil anterior
                $ini->sub(new \DateInterval('P20D'));
                $fim->sub(new \DateInterval('P1D'));
            }
            $lista = $this->findDiasUteisBy($ini, $fim, $comercial, $financeiro);
            if ($prox === null || $prox === true) {
                if (isset($lista[0])) {
                    /** @var DiaUtil $proxDia */
                    $proxDia = $lista[0];
                    return $proxDia->dia;
                }
            } else {
                if (isset($lista[count($lista) - 1])) {
                    /** @var DiaUtil $diaAnt */
                    $diaAnt = $lista[count($lista) - 1];
                    return $diaAnt->dia;
                }
            }
            return null;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param \DateTime $ini
     * @param \DateTime $fim
     * @param null $comercial
     * @param null $financeiro
     * @return mixed
     */
    public function findDiasUteisBy(\DateTime $ini, \DateTime $fim, $comercial = null, $financeiro = null)
    {
        $ini->setTime(0, 0, 0, 0);
        $fim->setTime(23, 59, 59, 999999);
        $params = array();

        $dql = "SELECT d FROM CrosierSource\CrosierLibBaseBundle\Entity\Base\DiaUtil d WHERE d.dia BETWEEN :ini AND :fim ";

        $params['ini'] = $ini;
        $params['fim'] = $fim;

        if ($comercial !== null) {
            $dql .= ' AND d.comercial = :comercial';
            $params['comercial'] = $comercial ? true : false;
        }
        if ($financeiro !== null) {
            $dql .= ' AND d.financeiro = :financeiro';
            $params['financeiro'] = $financeiro ? true : false;
        }
        $dql .= ' ORDER BY d.dia';

        $em = $this->getEntityManager();

        $query = $em->createQuery($dql);
        $query->setParameters($params);

        return $query->getResult();
    }

    /**
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @param bool $futuro
     * @param bool|null $comercial
     * @param bool|null $financeiro
     */
    public function incPeriodo(\DateTime $dtIni, \DateTime $dtFim, bool $futuro = true, ?bool $comercial = null, ?bool $financeiro = null): array
    {
        $dtIni->setTime(0,0,0,0);
        $dtFim->setTime(0,0,0,0);
        if ($dtIni === $dtFim) {
            $diaUtil = $this->findEnesimoDiaUtil($dtIni, $futuro ? 2 : -2, $financeiro, $comercial);
            $periodo = [
                'dtIni' => $diaUtil->format('Y-m-d'),
                'dtFim' => $diaUtil->format('Y-m-d'),
            ];
        } else {
            $periodo = DateTimeUtils::iteratePeriodoRelatorial($dtIni, $dtFim, $futuro);
        }
        return $periodo;
    }


    /**
     * Encontra o enésimo dia útil financeiro do mês.
     *
     * @param \DateTime $dtIni
     * @param int $ordinal
     * @param bool|null $financeiro
     * @param bool|null $comercial
     *
     * @return DiaUtil|NULL
     */
    public function findEnesimoDiaUtil(\DateTime $dtIni, int $ordinal, ?bool $financeiro = null, ?bool $comercial = null): ?\DateTime
    {
        try {
            $ordinal = $ordinal ?: 1; // o correto é passar 1, mas se passar 0 considera o mesmo.
            // Considera-se que existam no mínimo 3x mais dias úteis que dias não úteis
            if ($ordinal >= 0) {
                $dtFim = (clone $dtIni)->add(new \DateInterval('P' . ($ordinal * 3) . 'D'));
                $diasUteis = $this->findDiasUteisBy($dtIni, $dtFim, $comercial, $financeiro);
                return $diasUteis[$ordinal - 1]->dia ?? null;
            } else {
                $dtFim = (clone $dtIni)->sub(new \DateInterval('P' . ((abs($ordinal) * 3) . 'D')));
                $diasUteis = $this->findDiasUteisBy($dtFim, $dtIni, $comercial, $financeiro);
                return $diasUteis[count($diasUteis) - abs($ordinal)]->dia ?? null;
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage(), 0, $e);
        }
    }
}
