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

    /**
     * @param $dateStr
     * @return null|\DateTime
     */
    public static function parseDateStr($dateStr): ?\DateTime
    {
        if (strlen($dateStr) === 5) { // dd/mm
            $dt = \DateTime::createFromFormat('d/m', $dateStr);
            $dt->setTime(12, 0);
            return $dt;
        }

        if (strlen($dateStr) === 7) { // YYYY-mm (mesAno)
            $dt = \DateTime::createFromFormat('Y-m-d', $dateStr . '-01');
            $dt->setTime(12, 0);
            return $dt;
        }

        if (strlen($dateStr) === 8) { // dd/mm/yy
            $dt = \DateTime::createFromFormat('d/m/y', $dateStr);
            $dt->setTime(12, 0);
            return $dt;
        }

        if (strlen($dateStr) === 10) { // dd/mm/YYYY
            if (preg_match('/\d{4}-\d{2}-\d{2}/', $dateStr)) {
                $dt = \DateTime::createFromFormat('Y-m-d', $dateStr);
            } else {
                $dt = \DateTime::createFromFormat('d/m/Y', $dateStr);
            }
            $dt->setTime(12, 0);
            return $dt;
        }

        if (strlen($dateStr) === 16) { // dd/mm/YYYY 12:34
            return \DateTime::createFromFormat('d/m/Y H:i', $dateStr);
        }

        if (strlen($dateStr) === 19) { // dd/mm/YYYY 12:34:00
            return \DateTime::createFromFormat('d/m/Y H:i:s', $dateStr);
        }

        return null;
    }

    /**
     * Calcula a diferença em meses.
     *
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @return float|int
     * @throws \Exception
     */
    public static function monthDiff(\DateTime $dtIni, \DateTime $dtFim)
    {
        if ($dtIni->getTimestamp() > $dtFim->getTimestamp()) {
            throw new \RuntimeException('dtIni deve ser menor ou igual a dtFim');
        }
        $difAnos = (int)$dtFim->format('Y') - (int)$dtIni->format('Y');
        $difMeses = (int)$dtFim->format('m') - (int)$dtIni->format('m');
        return ($difAnos * 12) + $difMeses;
    }

    /**
     * Retorna se o período especificado é um período relatorial.
     *
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
    public static function isPeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim): bool
    {
        if ($dtIni->getTimestamp() > $dtFim->getTimestamp()) {
            throw new \RuntimeException('dtIni deve ser menor ou igual a dtFim');
        }

        $dtFimEhUltimoDiaDoMes = $dtFim->format('Y-m-d') === $dtFim->format('Y-m-t');
        $dtIniDia = (int)$dtIni->format('d');
        $dtFimDia = (int)$dtFim->format('d');

        // 01 - 15
        // 01 - ultimoDia
        // 16 - ultimoDia
        // 16 - 15
        return ($dtIniDia === $dtFimDia) OR
            ($dtIniDia === 1 && $dtFimDia === 15) OR
            ($dtIniDia === 1 && $dtFimEhUltimoDiaDoMes) OR
            ($dtIniDia === 16 && $dtFimEhUltimoDiaDoMes) OR
            ($dtIniDia === 16 && $dtFimDia === 15);

    }


    /**
     * Incrementa o período relatorial.
     *
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @param bool $strict Se deve verificar se o período realmente é um período relatorial.
     * @return false|string
     * @throws \Exception
     */
    public static function incPeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim, $strict = false)
    {
        $dtIni = clone $dtIni;
        $dtFim = clone $dtFim;
        // Seto para meio-dia para evitar problemas com as funções diff caso bata com horário de verão
        $dtIni->setTime(12, 0);
        $dtFim->setTime(12, 0);

        if ((!($ehPeriodoRelatorial = self::isPeriodoRelatorial($dtIni, $dtFim))) && $strict) {
            throw new \RuntimeException('O período informado não é relatorial.');
        }


        $difMeses = self::monthDiff($dtIni, $dtFim);
        $difDias = $dtFim->diff($dtIni)->days;

        // A próxima dtIni vai ser sempre um dia depois da dtFim
        $proxDtIni = (clone $dtFim)->add(new \DateInterval('P1D'));
        $proxDtIniF = $proxDtIni->format('Y-m-d');

        // Se não é um período relatorial, apenas acrescenta o número de dias
        if (!$ehPeriodoRelatorial) {
            $proxDtFim = (clone $proxDtIni)->add(new \DateInterval('P' . $difDias . 'D'));
            $proxDtFimF = $proxDtFim->format('Y-m-d');
            $periodo['dtIni'] = $proxDtIniF;
            $periodo['dtFim'] = $proxDtFimF;
            return $periodo;
        }
        // else

        $dtFimEhUltimoDiaDoMes = $dtFim->format('Y-m-d') === $dtFim->format('Y-m-t');
        $dtIniDia = (int)$dtIni->format('d');
        $dtFimDia = (int)$dtFim->format('d');

        $proxDtFimF = null;

        // Se é o mesmo dia
        if ($dtIni->format('Y-m-d') === $dtFim->format('Y-m-d')) {
            $proxDtFimF = $proxDtIniF;
        } else if ($difMeses === 0) {
            if ($difDias > 16) {
                // Não é quinzena, é o mês inteiro
                $proxDtFimF = $dtFim->setDate($dtFim->format('Y'), (int)$dtFim->format('m') + 1, 28)->format('Y-m-t');
            } else if ($dtFimDia === 15) {
                $proxDtFimF = $dtFim->format('Y-m-t');
            } else { // fimDia === ultimo dia do mês
                $proxDtFimF = $dtFim->setDate($dtFim->format('Y'), (int)$dtFim->format('m') + 1, 15)->format('Y-m-d');
            }
        } else {
            if (($dtIniDia === 1) && ($dtFimDia === 15 || $dtFimEhUltimoDiaDoMes)) {
                // iniDia = 16 (fimDia + 1)
                // fimDia = ultimo dia do mês
                $proxDtFimF = $proxDtIni->setDate($proxDtIni->format('Y'), (int)$proxDtIni->format('m') + $difMeses, 28)->format('Y-m-t');
            } else if (($dtIniDia === 16 && $dtFimEhUltimoDiaDoMes) || $dtFimDia === 15) {
                $proxDtFimF = $proxDtIni->setDate($proxDtIni->format('Y'), (int)$proxDtIni->format('m') + $difMeses, 15)->format('Y-m-d');
            }
        }
        $periodo['dtIni'] = $proxDtIniF;
        $periodo['dtFim'] = $proxDtFimF;

        return $periodo;
    }

    /**
     * Decrementa um período relatorial.
     *
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @param bool $strict Se deve verificar se o período realmente é um período relatorial.
     * @return mixed
     * @throws \Exception
     */
    public static function decPeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim, $strict = false)
    {
        $dtIni = clone $dtIni;
        $dtFim = clone $dtFim;
        // Seto para meio-dia para evitar problemas com as funções diff caso bata com horário de verão
        $dtIni->setTime(12, 0);
        $dtFim->setTime(12, 0);

        if ((!($ehPeriodoRelatorial = self::isPeriodoRelatorial($dtIni, $dtFim))) && $strict) {
            throw new \RuntimeException('O período informado não é relatorial.');
        }


        $difMeses = self::monthDiff($dtIni, $dtFim);
        $difDias = $dtFim->diff($dtIni)->days;

        // A próxima dtFim vai ser sempre um dia antes da dtIni
        $proxDtFim = (clone $dtIni)->sub(new \DateInterval('P1D'));
        $proxDtFimF = $proxDtFim->format('Y-m-d');

        // Se não é um período relatorial, apenas decrementa o número de dias
        if (!$ehPeriodoRelatorial) {
            $proxDtIni = (clone $proxDtFim)->sub(new \DateInterval('P' . $difDias . 'D'));
            $proxDtIniF = $proxDtIni->format('Y-m-d');
            $periodo['dtIni'] = $proxDtIniF;
            $periodo['dtFim'] = $proxDtFimF;
            return $periodo;
        }
        // else

        $dtFimEhUltimoDiaDoMes = $dtFim->format('Y-m-d') === $dtFim->format('Y-m-t');
        $dtIniDia = (int)$dtIni->format('d');
        $dtFimDia = (int)$dtFim->format('d');

        $proxDtIniF = null;

        // Se é o mesmo dia
        if ($dtIni->format('Y-m-d') === $dtFim->format('Y-m-d')) {
            $proxDtIniF = $proxDtFimF;
        } else if ($difMeses === 0) {
            if ($difDias > 16) {
                // Não é quinzena
                $proxDtIniF = $dtFim->setDate($dtFim->format('Y'), $dtFim->format('m') - 1, 1)->format('Y-m-d');
            } else if ($dtIniDia === 16) {
                $proxDtIniF = $dtFim->setDate($dtIni->format('Y'), $dtIni->format('m'), 1)->format('Y-m-d');
            } else { // iniDia === 01
                $proxDtIniF = $dtFim->setDate($dtFim->format('Y'), $dtFim->format('m') - 1, 16)->format('Y-m-d');
            }
        } else {
            if ($dtIniDia === 01 || $dtIniDia === 16) {
                // É um período de 45 dias ou mais
                if ($dtFimDia === 15) {
                    $proxDtIniF = $proxDtFim->setDate($proxDtFim->format('Y'), $proxDtFim->format('m') - $difMeses, 16)->format('Y-m-d');
                } else if ($dtFimEhUltimoDiaDoMes) {
                    $proxDtIniF = $proxDtFim->setDate($proxDtFim->format('Y'), $proxDtFim->format('m') - $difMeses, 01)->format('Y-m-d');
                }
            }
        }
        $periodo['dtIni'] = $proxDtIniF;
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
    public static function iteratePeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim, $proFuturo = true)
    {
        if ($proFuturo) {
            return self::incPeriodoRelatorial($dtIni, $dtFim);
        }
        // else
        return self::decPeriodoRelatorial($dtIni, $dtFim);
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
            $dtProx = $dtProx->setDate($dt->format('Y'), (int)$dt->format('m') + $inc, 1);
            $dtProx = \DateTime::createFromFormat('Y-m-d', $dtProx->format('Y-m-t'));
        } else {
            $dtProx = $dt->setDate($dt->format('Y'), (int)$dt->format('m') + $inc, $dt->format('d'));
        }
        $dtProx->setTime(0, 0);
        return $dtProx;
    }

    /**
     * Retorna o primeiro dia do mês a partir de uma data.
     *
     * @param \DateTime $dt
     * @return bool|\DateTime
     */
    public static function getPrimeiroDiaMes(\DateTime $dt)
    {
        $primeiroDiaMes = \DateTime::createFromFormat('Ymd', $dt->format('Y') . $dt->format('m') . '01');
        $primeiroDiaMes->setTime(0, 0);
        return $primeiroDiaMes;
    }

    /**
     * Retorna o último dia do mês a partir de uma data.
     *
     * @param \DateTime $dt
     * @return bool|\DateTime
     */
    public static function getUltimoDiaMes(\DateTime $dt)
    {
        $ultimoDiaMes = \DateTime::createFromFormat('Ymd', $dt->format('Y') . $dt->format('m') . $dt->format('t'));
        $ultimoDiaMes->setTime(0, 0);
        return $ultimoDiaMes;
    }

}