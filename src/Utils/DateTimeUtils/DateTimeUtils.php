<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils;



use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;

/**
 * Class DateTimeUtils.
 *
 * @package App\Utils
 * @author Carlos Eduardo Pauluk
 */
class DateTimeUtils
{

    public static function parseDateStr($dateStr)
    {
        if (strlen($dateStr) === 5) { // dd/mm
            $dt = \DateTime::createFromFormat('d/m', $dateStr);
            $dt->setTime(12, 0, 0, 0);
            return $dt;
        } else if (strlen($dateStr) === 7) { // YYYY-mm (mesAno)
            $dt = \DateTime::createFromFormat('Y-m-d', $dateStr . '-01');
            $dt->setTime(12, 0, 0, 0);
            return $dt;
        } else if (strlen($dateStr) === 8) { // dd/mm/yy
            $dt = \DateTime::createFromFormat('d/m/y', $dateStr);
            $dt->setTime(12, 0, 0, 0);
            return $dt;
        } else if (strlen($dateStr) === 10) { // dd/mm/YYYY
            if (preg_match('/\d{4}-\d{2}-\d{2}/',$dateStr)) {
                $dt = \DateTime::createFromFormat('Y-m-d', $dateStr);
            } else {
                $dt = \DateTime::createFromFormat('d/m/Y', $dateStr);
            }
            $dt->setTime(12, 0, 0, 0);
            return $dt;
        } else if (strlen($dateStr) === 16) { // dd/mm/YYYY 12:34
            return \DateTime::createFromFormat('d/m/Y H:i', $dateStr);
        } else if (strlen($dateStr) === 19) { // dd/mm/YYYY 12:34:00
            return \DateTime::createFromFormat('d/m/Y H:i:s', $dateStr);
        } else {
            throw new ViewException('Impossível parse na data [' . $dateStr . ']');
        }
    }

    public static function monthDiff(\DateTime $dtIni, \DateTime $dtFim)
    {
        if ($dtIni->getTimestamp() > $dtFim->getTimestamp()) {
            throw new \Exception("dtIni > dtFim");
        }
        $difAnos = intval($dtFim->format('Y')) - intval($dtIni->format('Y'));
        $difMeses = intval($dtFim->format('m')) - intval($dtIni->format('m'));
        return ($difAnos * 12) + $difMeses;
    }

    /**
     *
     * Retorna se o período especificado é um período relatorial.
     * 01 - 15
     * 01 - ultimoDia
     * 16 - ultimoDia
     * 16 - 15
     *
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @return bool
     * @throws \Exception
     */
    public static function isPeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim)
    {
        if ($dtIni->getTimestamp() > $dtFim->getTimestamp()) {
            throw new \Exception("dtIni > dtFim");
        }

        $dtFimEhUltimoDiaDoMes = $dtFim->format('Y-m-d') === $dtFim->format('Y-m-t');
        $dtIniDia = intval($dtIni->format('d'));
        $dtFimDia = intval($dtFim->format('d'));

        // 01 - 15
        // 01 - ultimoDia
        // 16 - ultimoDia
        // 16 - 15
        return ($dtIniDia == 1 and $dtFimDia == 15) OR
            ($dtIniDia == 1 and $dtFimEhUltimoDiaDoMes) OR
            ($dtIniDia == 16 and $dtFimEhUltimoDiaDoMes) OR
            ($dtIniDia == 16 and $dtFimDia == 15);

    }


    /**
     * Incrementa o período relatorial.
     *
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @return false|string
     * @throws \Exception
     */
    public static function incPeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim)
    {
        $dtIni = clone $dtIni;
        $dtFim = clone $dtFim;
        // Seto para meio-dia para evitar problemas com as funções diff caso bata com horário de verão
        $dtIni->setTime(12, 0, 0, 0);
        $dtFim->setTime(12, 0, 0, 0);

        if (!DateTimeUtils::isPeriodoRelatorial($dtIni, $dtFim)) {
            throw new \Exception("O período informado não é relatorial.");
        }

        $dtFimEhUltimoDiaDoMes = $dtFim->format('Y-m-d') === $dtFim->format('Y-m-t');
        $dtIniDia = $dtIni->format('d');
        $dtFimDia = $dtFim->format('d');

        $difMeses = DateTimeUtils::monthDiff($dtIni, $dtFim);
        $difDias = $dtFim->diff($dtIni)->days;

        // A próxima dtIni vai ser sempre um dia depois da dtFim
        $proxDtIni = clone $dtFim;
        $proxDtIni = $proxDtIni->add(new \DateInterval('P1D'));
        $proxDtIniF = $proxDtIni->format('Y-m-d');

        $proxDtFim = null;

        // dtFim vai ser sempre dia 16 ou o último dia do mês
        if ($difMeses == 0) {
            if ($difDias > 16) {
                // Não é quinzena, é o mês inteiro
                $proxDtFim = $dtFim->setDate($dtFim->format('Y'), $dtFim->format('m') + 1, 28)->format('Y-m-t');
            } else {
                // é uma quinzena
                if ($dtFimDia == 15) {
                    $proxDtFim = $dtFim->format('Y-m-t');
                } else { // fimDia == ultimo dia do mês
                    $proxDtFim = $dtFim->setDate($dtFim->format('Y'), $dtFim->format('m') + 1, 15)->format('Y-m-d');
                }
            }
        } else {
            // não estão no mesmo mês...

            // É um período de 45 dias ou mais
            if (($dtIniDia == 1) and ($dtFimDia == 15 or $dtFimEhUltimoDiaDoMes)) {
                // iniDia = 16 (fimDia + 1)
                // fimDia = ultimo dia do mês
                $proxDtFim = $proxDtIni->setDate($proxDtIni->format('Y'), $proxDtIni->format('m') + ($difMeses), 28)->format('Y-m-t');
            } else if (($dtIniDia == 16) and $dtFimEhUltimoDiaDoMes or $dtFimDia == 15) {
                $proxDtFim = $proxDtIni->setDate($proxDtIni->format('Y'), $proxDtIni->format('m') + ($difMeses), 15)->format('Y-m-d');
            }
        }

        $periodo['dtIni'] = $proxDtIniF;
        $periodo['dtFim'] = $proxDtFim;

        return $periodo;
    }

    public static function decPeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim)
    {
        // Seto para meio-dia para evitar problemas com as funções diff caso bata com horário de verão
        $dtIni = clone $dtIni;
        $dtFim = clone $dtFim;
        $dtIni->setTime(12, 0, 0, 0);
        $dtFim->setTime(12, 0, 0, 0);

        if (!DateTimeUtils::isPeriodoRelatorial($dtIni, $dtFim)) {
            throw new \Exception("O período informado não é relatorial.");
        }

        $dtFimEhUltimoDiaDoMes = $dtFim->format('Y-m-d') === $dtFim->format('Y-m-t');
        $dtIniDia = $dtIni->format('d');
        $dtFimDia = $dtFim->format('d');

        $difMeses = DateTimeUtils::monthDiff($dtIni, $dtFim);
        $difDias = $dtFim->diff($dtIni)->days;

        // A próxima dtIni vai ser sempre um dia depois da dtFim
        $proxDtFim = clone $dtIni;

        $proxDtFim = $proxDtFim->sub(new \DateInterval('P1D'));
        $proxDtFimF = $proxDtFim->format('Y-m-d');

        // dtFim vai ser sempre dia 16 ou o último dia do mês
        $proxDtIni = null;
        if ($difMeses == 0) {
            if ($difDias > 16) {
                // Não é quinzena
//                $proxDtIni = $dtFim->sub(new \DateInterval('P1M'))->format('Y-m-') . '01';
                $proxDtIni = $dtFim->setDate($dtFim->format('Y'), $dtFim->format('m') - 1, 1)->format('Y-m-d');
            } else {
                // é quinzena
                if ($dtIniDia == 16) {
                    $proxDtIni = $dtFim->setDate($dtIni->format('Y'), $dtIni->format('m'), 1)->format('Y-m-d');
                } else { // iniDia == 01
                    $proxDtIni = $dtFim->setDate($dtFim->format('Y'), $dtFim->format('m') - 1, 16)->format('Y-m-d');
                }
            }
        } else {
            // não estão no mesmo mês...

            // É um período de 45 dias ou mais
            if (($dtIniDia == 01 or $dtIniDia == 16) and ($dtFimDia == 15)) {
                $proxDtIni = $proxDtFim->setDate($proxDtFim->format('Y'), $proxDtFim->format('m') - ($difMeses), 16)->format('Y-m-d');
            } else if (($dtIniDia == 01 or $dtIniDia == 16) and $dtFimEhUltimoDiaDoMes) {
                $proxDtIni = $proxDtFim->setDate($proxDtFim->format('Y'), $proxDtFim->format('m') - ($difMeses), 01)->format('Y-m-d');
            }
        }

        $periodo['dtIni'] = $proxDtIni;
        $periodo['dtFim'] = $proxDtFimF;

        return $periodo;

    }

    /**
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @param $proFuturo
     * @return false|string
     * @throws \Exception
     */
    public static function iteratePeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim, $proFuturo)
    {
        if ($proFuturo) {
            return DateTimeUtils::incPeriodoRelatorial($dtIni, $dtFim);
        } else {
            return DateTimeUtils::decPeriodoRelatorial($dtIni, $dtFim);
        }
    }


    /**
     * Incrementa ou decrementa em 1 mês levando em consideração a possibilidade do dia ser o último do mês (e nesse caso, seta como último dia do mês ajustado).
     *
     * @param \DateTime $dt
     * @param int $inc
     * @return bool|\DateTime
     */
    public static function incMes(\DateTime $dt, $inc = 1)
    {
        $ehUltimoDiaDoMes = $dt->format('Y-m-d') === $dt->format('Y-m-t');
        $dtProx = clone $dt;
        if ($ehUltimoDiaDoMes) {
            $dtProx = $dtProx->setDate($dt->format('Y'), intval($dt->format('m')) + $inc, 1);
            $dtProx = \DateTime::createFromFormat('Y-m-d', $dtProx->format('Y-m-t'));
        } else {
            $dtProx = $dt->setDate($dt->format('Y'), intval($dt->format('m')) + $inc, $dt->format('d'));
        }
        $dtProx->setTime(0,0,0,0);
        return $dtProx;
    }

    /**
     * Retorna o primeiro dia do mês a partir de uma data.
     *
     * @param \DateTime $dt
     * @return bool|\DateTime
     */
    public static function getPrimeiroDiaMes(\DateTime $dt) {
        $primeiroDiaMes = \DateTime::createFromFormat('Ymd', $dt->format('Y') . $dt->format('m') . '01');
        $primeiroDiaMes->setTime(0,0,0,0);
        return $primeiroDiaMes;
    }

    /**
     * Retorna o último dia do mês a partir de uma data.
     *
     * @param \DateTime $dt
     * @return bool|\DateTime
     */
    public static function getUltimoDiaMes(\DateTime $dt) {
        $ultimoDiaMes = \DateTime::createFromFormat('Ymd', $dt->format('Y') . $dt->format('m') . $dt->format('t'));
        $ultimoDiaMes->setTime(0,0,0,0);
        return $ultimoDiaMes;
    }

}