<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils;


/**
 * Class DateTimeUtils.
 *
 * @author Carlos Eduardo Pauluk
 */
class DateTimeUtils
{

    const DATAHORACOMPLETA_PATTERN = "@\A(?<d>\d{1,2})/(?<m>\d{1,2})/(?<Y>\d{1,4})(\s(?<H>\d{1,2}):(?<i>\d{2})(:(?<s>\d{2}))?)?\z@";
    // 01/02/2021 14:59:59

    const DATAHORACOMPLETACOMFUSO_PATTERN1 = "@\A(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})\.(\d{3,7})([+-]\d{2}:\d{2}){0,1}Z\z@";
    // 2023-07-03T14:38:40.0775003Z

    const DATAHORACOMPLETACOMFUSO_PATTERN2 = "@\A(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})\.\d{3}([+-]\d{2}:\d{2}){0,1}(?!.*Z$).*\z@";
    // 2020-05-02T10:54:44.000-04:00

    const DATA_PATTERN1 = "@\A(\d{1,2}/\d{1,2}/\d{4})\z@";

    const DATASQL_PATTERN2 = "@\A(\d{4}-\d{1,2}-\d{1,2})\z@";


    /**
     * @param $dateStr
     * @return null|\DateTime
     */
    public static function parseDateStr($dateStr): ?\DateTime
    {
        $dateStr = str_replace('  ', ' ', trim($dateStr));
        if (!$dateStr) {
            return null;
        }

        if (preg_match(self::DATAHORACOMPLETACOMFUSO_PATTERN1, $dateStr, $matches)) {
            $dateStr = $matches[1];
        }

        if (preg_match(self::DATAHORACOMPLETACOMFUSO_PATTERN2, $dateStr)) {
            return \DateTime::createFromFormat('Y-m-d\TH:i:s\.uP', $dateStr);
        }

        if (preg_match(self::DATA_PATTERN1, $dateStr, $matches)) {
            $dateStr = $matches[1];
            $dt = \DateTime::createFromFormat('d/m/Y', $dateStr);
            $dt->setTime(12, 0);
            return $dt;
        }

        if (preg_match(self::DATASQL_PATTERN2, $dateStr, $matches)) {
            $dateStr = $matches[1];
            $dt = \DateTime::createFromFormat('Y-m-d', $dateStr);
            $dt->setTime(12, 0);
            return $dt;
        }

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

        if (preg_match(self::DATAHORACOMPLETA_PATTERN, $dateStr, $matches)) {
            $pattern = 'd/m/';
            $pattern .= strlen($matches['Y']) === 2 ? 'y' : 'Y';
            $pattern .= isset($matches['H']) ? ' H:i' : '';
            $pattern .= isset($matches['s']) ? ':s' : '';
            $dt = \DateTime::createFromFormat($pattern, $dateStr);
            if ($pattern === 'd/m/Y') {
                $dt->setTime(12, 0);
            }
            return $dt;
        }

        if (strlen($dateStr) === 16) { // dd/mm/YYYY 12:34
            return \DateTime::createFromFormat('d/m/Y H:i', $dateStr);
        }

        if (strlen($dateStr) === 19) { // dd/mm/YYYY 12:34:00
            if (preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $dateStr)) {
                $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $dateStr);
            } else if (preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $dateStr)) {
                $dt = \DateTime::createFromFormat('Y-m-d\TH:i:s', $dateStr);
            } else {
                $dt = \DateTime::createFromFormat('d/m/Y H:i:s', $dateStr);
            }
            return $dt;
        }
        // 2019-04-30T18:15:02-03:00
        if (strlen($dateStr) === 25) { // dd/mm/YYYY 12:34
            return \DateTime::createFromFormat('Y-m-d\TH:i:sP', $dateStr);
        }
        if (strlen($dateStr) === 26) { // dd/mm/YYYY 12:34
            return \DateTime::createFromFormat('Y-m-d H:i:s.u', $dateStr);
        }
        if (strlen($dateStr) > 33) {
            // Sun Aug 01 2021 00:00:00 GMT-0300 (Horário Padrão de Brasília)
            return \DateTime::createFromFormat('D M d Y H:i:s \G\M\TO', substr($dateStr, 0, 33));
        }

        throw new \RuntimeException('Impossível parse na data (' . $dateStr . ')');
    }


    /**
     * Faz o parse em uma string no formato "01/01/2001 - 02/02/2002", retornando um array.
     * @param string $concatDates
     * @return null|array
     */
    public static function parseConcatDates(string $concatDates): ?array
    {
        if (strlen($concatDates) === 23) {
            $ini = substr($concatDates, 0, 10);
            $val['i'] = DateTimeUtils::parseDateStr($ini);
            $fim = substr($concatDates, 13, 10);
            $val['f'] = DateTimeUtils::parseDateStr($fim);
            return $val;
        }
        if (strlen($concatDates) > 23 && str_contains($concatDates, ',')) {
            $split = explode(',', $concatDates);
            $val['i'] = DateTimeUtils::parseDateStr($split[0]);
            $val['f'] = DateTimeUtils::parseDateStr($split[1]);
            return $val;
        }
        return null;
    }

    /**
     * Calcula a diferença em meses.
     * @throws \RuntimeException
     */
    public static function monthDiff(\DateTime $dtIni, \DateTime $dtFim): int
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
        return
            ($dtIniDia === 1 && $dtFimDia === 15) or
            ($dtIniDia === 1 && $dtFimEhUltimoDiaDoMes) or
            ($dtIniDia === 16 && $dtFimEhUltimoDiaDoMes) or
            ($dtIniDia === 16 && $dtFimDia === 15);

    }


    /**
     * Incrementa o período relatorial.
     * @throws \Exception
     */
    public static function incPeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim, $strict = false): array
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
     * @throws \Exception
     */
    public static function decPeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim, $strict = false): array
    {
        $dtIni = clone $dtIni;
        $dtFim = clone $dtFim;
        // Seto para meio-dia para evitar problemas com as funções diff caso bata com horário de verão
        $dtIni->setTime(12, 0);
        $dtFim->setTime(12, 0);

        $ehPeriodoRelatorial = self::isPeriodoRelatorial($dtIni, $dtFim);
        if (!$ehPeriodoRelatorial && $strict) {
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
     * @throws \Exception
     */
    public static function iteratePeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim, $proFuturo = true): array
    {
        if ($proFuturo) {
            return self::incPeriodoRelatorial($dtIni, $dtFim);
        }
        // else
        return self::decPeriodoRelatorial($dtIni, $dtFim);
    }


    /**
     * Incrementa ou decrementa em 1 mês levando em consideração a possibilidade do dia ser
     * o último do mês (e nesse caso, seta como último dia do mês ajustado).
     */
    public static function incMes(\DateTime $dt, $inc = 1): \DateTime
    {
        $dt = clone $dt;
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
     */
    public static function getPrimeiroDiaMes(\DateTime $dt = null): \DateTime
    {
        $dt = $dt ?: new \DateTime();
        $primeiroDiaMes = \DateTime::createFromFormat('Ymd', $dt->format('Y') . $dt->format('m') . '01');
        $primeiroDiaMes->setTime(0, 0);
        $primeiroDiaMes->setTimezone(new \DateTimeZone("America/Fortaleza"));
        return $primeiroDiaMes;
    }

    /**
     * Retorna o último dia do mês a partir de uma data.
     */
    public static function getUltimoDiaMes(\DateTime $dt = null): \DateTime
    {
        $dt = $dt ?: new \DateTime();
        $ultimoDiaMes = \DateTime::createFromFormat('Ymd', $dt->format('Y') . $dt->format('m') . $dt->format('t'));
        $ultimoDiaMes->setTime(0, 0);
        $ultimoDiaMes->setTimezone(new \DateTimeZone("America/Fortaleza"));
        return $ultimoDiaMes;
    }


    /**
     * Retorna um array com todos os Datetime para o mês/ano passado.
     */
    public static function getDiasMesAno(string $mesano): array
    {
        try {
            $dt = \DateTime::createFromFormat('Ymd', $mesano . '01');
            $dt->setTime(0, 0);
            $ultimoDia = self::getUltimoDiaMes($dt)->format('d/m/Y');
            $dts = [];
            $aux = clone $dt;
            while (true) {
                $dts[] = clone $aux;
                $aux->add(new \DateInterval('P1D'));
                if ($aux->format('d/m/Y') === $ultimoDia) {
                    $dts[] = clone $aux;
                    break;
                }
            }
            return $dts;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erro ao gerar dias para o mesano "' . $mesano . '"', 0, $e);
        }
    }

    /**
     * Retorna um array com todos os Datetime para o mês/ano passado.
     */
    public static function getDatasMesAno(string $mesano): array
    {
        $dtIni = \DateTime::createFromFormat('Ymd', $mesano . '01');
        $dtIni->setTime(0, 0);

        $dtFim = \DateTime::createFromFormat('Ymd', $dtIni->format('Ymt'));
        $dtFim->setTime(23, 59, 59, 99999);

        return ['i' => $dtIni, 'f' => $dtFim];
    }

    public static function getDatesList(\DateTime $dtIni, \DateTime $dtFim): array
    {
        $list = [];
        $invert = false;
        if ($dtIni->getTimestamp() > $dtFim->getTimestamp()) {
            $invert = true;
        }

        $dtAux = clone($dtIni);
        $dtAux->setTime(12, 0);
        $dtFim->setTime(12, 0);
        $list[] = clone($dtAux);

        if ($invert) {
            while (true) {
                $dtAux->setDate($dtAux->format('Y'), $dtAux->format('m'), ((int)$dtAux->format('d') - 1));
                if ($dtAux->getTimestamp() < $dtFim->getTimestamp()) {
                    break;
                }
                $list[] = clone($dtAux);
            }
        } else {
            while (true) {
                $dtAux->setDate($dtAux->format('Y'), $dtAux->format('m'), ((int)$dtAux->format('d') + 1));
                if ($dtAux->getTimestamp() > $dtFim->getTimestamp()) {
                    break;
                }
                $list[] = clone($dtAux);
            }
        }

        return $list;
    }

    /**
     * Retorna uma lista de "meses" (sendo representados sempre pelo primeiro dia de cada mês).
     */
    public static function getMonthsList(\DateTime $dtIni, \DateTime $dtFim): array
    {
        $dtIni->setDate($dtIni->format('Y'), $dtIni->format('m'), 1);
        $dtFim->setDate($dtFim->format('Y'), $dtFim->format('m'), 1);

        $list = [];
        $invert = false;
        if ($dtIni->getTimestamp() > $dtFim->getTimestamp()) {
            $invert = true;
        }

        $dtAux = clone($dtIni);
        $dtAux->setTime(12, 0);
        $dtFim->setTime(12, 0);

        $list[] = clone($dtAux);

        if ($invert) {
            while (true) {
                $dtAux->setDate($dtAux->format('Y'), ((int)$dtAux->format('m')) - 1, 1);
                if ($dtAux->getTimestamp() < $dtFim->getTimestamp()) {
                    break;
                }
                $list[] = clone($dtAux);
            }
        } else {
            while (true) {
                $dtAux->setDate($dtAux->format('Y'), ((int)$dtAux->format('m')) + 1, 1);
                if ($dtAux->getTimestamp() > $dtFim->getTimestamp()) {
                    break;
                }
                $list[] = clone($dtAux);
            }
        }

        return $list;
    }


    /**
     * Retorna uma lista de "semanas" (sendo que o domingo é considerado o primeiro dia).
     */
    public static function getWeeksList(\DateTime $dtIni, \DateTime $dtFim): array
    {
        // Mudo a dtIni para o domingo da mesma semana
        $dtIni_diaDaSemana = $dtIni->format('w');
        $dtIni->setDate($dtIni->format('Y'), $dtIni->format('m'), (int)$dtIni->format('d') - $dtIni_diaDaSemana);

        // Mudo a dtFim para o sábado da mesma semana
        $dtFim_diaDaSemana = $dtFim->format('w');
        $dtFim->setDate($dtFim->format('Y'), $dtFim->format('m'), (int)$dtFim->format('d') + (6 - $dtFim_diaDaSemana));

        $invert = false;
        if ($dtIni->getTimestamp() > $dtFim->getTimestamp()) {
            $invert = true;
        }

        $dtAuxIni = clone($dtIni);
        $dtAuxIni->setTime(12, 0);
        $dtAuxFim = clone($dtAuxIni);

        $dtFim->setTime(12, 0);

        $list = [];

        while (true) {
            $dtAuxFim->setDate($dtAuxFim->format('Y'), $dtAuxFim->format('m'), (int)$dtAuxFim->format('d') + 6);
            $list[] = [clone($dtAuxIni), clone($dtAuxFim)];
            if ($dtAuxFim->getTimestamp() >= $dtFim->getTimestamp()) {
                break;
            }
            $dtAuxIni->setDate($dtAuxIni->format('Y'), $dtAuxIni->format('m'), (int)$dtAuxIni->format('d') + 7);
            $dtAuxFim = clone($dtAuxIni);
        }

        return $invert ? array_reverse($list) : $list;
    }

    public static function getDiaDaSemana(\DateTime $dt): string
    {
        $d = $dt->format('w');
        $dias = [
            '0' => 'Domingo',
            '1' => 'Segunda',
            '2' => 'Terça',
            '3' => 'Quarta',
            '4' => 'Quinta',
            '5' => 'Sexta',
            '6' => 'Sábado'
        ];
        return $dias[(int)$d];
    }

    public static function diffInMinutes(\DateTime $dtFim, \DateTime $dtIni): int
    {
        $diff = $dtFim->diff($dtIni);
        $minTotal = ($diff->days * 1440) + ($diff->h * 60) + $diff->i;
        return ($diff->invert) ? $minTotal : ($minTotal * -1);
    }

    public static function diffInDias(\DateTime $dtFim, \DateTime $dtIni): int
    {
        $diff = $dtFim->diff($dtIni);
        $total = $diff->days;
        return ($diff->invert) ? $total : ($total * -1);
    }

    public static function dataMaiorQue(\DateTime $esta, \DateTime $aquela): bool
    {
        return ($esta->diff($aquela)->invert === 1);
    }

    /**
     * Adiciona $days em $dt.
     */
    public static function addDays(\DateTime $dt, int $days): \DateTime
    {
        $novaDt = clone $dt;
        $novaDt->setDate($novaDt->format('Y'), $novaDt->format('m'), (int)$novaDt->format('d') + $days);
        return $novaDt;
    }


    public static function getSQLFormatted(?\DateTime $dt = null, bool $datetime = true): string
    {
        $dt = $dt ? clone $dt : new \DateTime();
        return $dt->format('Y-m-d' . ($datetime ? ' H:i:s' : ''));
    }

    /**
     * @throws \Exception
     */
    public static function addMinutes(?\DateTime $dt = null, ?int $minutes = 0): \DateTime
    {
        $dt = $dt ? clone $dt : new \DateTime();
        return $dt->add(new \DateInterval('PT' . $minutes . 'M'));
    }

    public static function ehMesmoDia(?\DateTime $dt1, ?\DateTime $dt2): bool
    {
        $dtUm = (clone $dt1);
        $dtUm->setTimezone($dt2->getTimezone());
        return ($dtUm->format('dmY') === $dt2->format('dmY'));
    }


    /**
     * Altera apenas o dia de uma data.
     */
    public static function setDay(?\DateTime $dt, int $dia): \DateTime
    {
        $dt = clone $dt;
        return $dt->setDate($dt->format('Y'), $dt->format('m'), $dia);
    }


    public static function ehAntesOuIgual(
        \DateTime $dt1,
        \DateTime $dt2,
        ?bool     $ignoraHorario = true): bool
    {
        $dt1 = clone($dt1);
        $dt2 = clone($dt2);
        if ($ignoraHorario) {
            $dt1->setTime(0, 0);
            $dt2->setTime(0, 0);
        }
        return ($dt1->getTimestamp() <= $dt2->getTimestamp());
    }

    public static function ehAntes(
        \DateTime $dt1,
        \DateTime $dt2,
        ?bool     $ignoraHorario = true): bool
    {
        $dt1 = clone($dt1);
        $dt2 = clone($dt2);
        if ($ignoraHorario) {
            $dt1->setTime(0, 0);
            $dt2->setTime(0, 0);
        }
        return ($dt1->getTimestamp() < $dt2->getTimestamp());
    }

    public static function ehDepoisOuIgual(
        \DateTime $dt1,
        \DateTime $dt2,
        ?bool     $ignoraHorario = true): bool
    {
        $dt1 = clone($dt1);
        $dt2 = clone($dt2);
        if ($ignoraHorario) {
            $dt1->setTime(0, 0);
            $dt2->setTime(0, 0);
        }
        return ($dt1->getTimestamp() >= $dt2->getTimestamp());
    }

    public static function ehDepois(
        \DateTime $dt1,
        \DateTime $dt2,
        ?bool     $ignoraHorario = true): bool
    {
        $dt1 = clone($dt1);
        $dt2 = clone($dt2);
        if ($ignoraHorario) {
            $dt1->setTime(0, 0);
            $dt2->setTime(0, 0);
        }
        return ($dt1->getTimestamp() > $dt2->getTimestamp());
    }


    public static function formatPtBr(\DateTime $dt, string $format): string
    {
        return \IntlDateFormatter::formatObject($dt, $format, 'pt_BR');
    }


}
