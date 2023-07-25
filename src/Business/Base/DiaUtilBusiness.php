<?php


namespace CrosierSource\CrosierLibBaseBundle\Business\Base;


use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\DBAL\Connection;
use GuzzleHttp\Client;

/**
 * @package CrosierSource\CrosierLibBaseBundle\Business\Base
 */
class DiaUtilBusiness
{

    private Connection $conn;

    private string $bse_dia_util_json = '{ "sabados_comercial": true, "feriados": { "fixos": { "1210": "Nossa Senhora Aparecida", "1511": "Proclamação da República", "2104": "Tiradentes", "2512": "Natal", "0101": "Confraternização Universal", "0105": "Dia do Trabalhador", "0709": "Independência", "0211": "Finados" }, "moveis": { "19800219": "Carvanal", "19800404": "Sexta-feira Santa (Paixão de Cristo)", "19800605": "Corpus Christi", "19810303": "Carvanal", "19810417": "Sexta-feira Santa (Paixão de Cristo)", "19810618": "Corpus Christi", "19820223": "Carvanal", "19820409": "Sexta-feira Santa (Paixão de Cristo)", "19820610": "Corpus Christi", "19830215": "Carvanal", "19830401": "Sexta-feira Santa (Paixão de Cristo)", "19830602": "Corpus Christi", "19840306": "Carvanal", "19840420": "Sexta-feira Santa (Paixão de Cristo)", "19840621": "Corpus Christi", "19850219": "Carvanal", "19850405": "Sexta-feira Santa (Paixão de Cristo)", "19850606": "Corpus Christi", "19860211": "Carvanal", "19860328": "Sexta-feira Santa (Paixão de Cristo)", "19860529": "Corpus Christi", "19870303": "Carvanal", "19870417": "Sexta-feira Santa (Paixão de Cristo)", "19870618": "Corpus Christi", "19880216": "Carvanal", "19880401": "Sexta-feira Santa (Paixão de Cristo)", "19880602": "Corpus Christi", "19890207": "Carvanal", "19890324": "Sexta-feira Santa (Paixão de Cristo)", "19890525": "Corpus Christi", "19900227": "Carvanal", "19900413": "Sexta-feira Santa (Paixão de Cristo)", "19900614": "Corpus Christi", "19910212": "Carvanal", "19910329": "Sexta-feira Santa (Paixão de Cristo)", "19910530": "Corpus Christi", "19920303": "Carvanal", "19920417": "Sexta-feira Santa (Paixão de Cristo)", "19920618": "Corpus Christi", "19930223": "Carvanal", "19930409": "Sexta-feira Santa (Paixão de Cristo)", "19930610": "Corpus Christi", "19940215": "Carvanal", "19940401": "Sexta-feira Santa (Paixão de Cristo)", "19940602": "Corpus Christi", "19950228": "Carvanal", "19950414": "Sexta-feira Santa (Paixão de Cristo)", "19950615": "Corpus Christi", "19960220": "Carvanal", "19960405": "Sexta-feira Santa (Paixão de Cristo)", "19960606": "Corpus Christi", "19970211": "Carvanal", "19970328": "Sexta-feira Santa (Paixão de Cristo)", "19970529": "Corpus Christi", "19980224": "Carvanal", "19980410": "Sexta-feira Santa (Paixão de Cristo)", "19980611": "Corpus Christi", "19990216": "Carvanal", "19990402": "Sexta-feira Santa (Paixão de Cristo)", "19990603": "Corpus Christi", "20000307": "Carvanal", "20000421": "Sexta-feira Santa (Paixão de Cristo)", "20000622": "Corpus Christi", "20010227": "Carvanal", "20010413": "Sexta-feira Santa (Paixão de Cristo)", "20010614": "Corpus Christi", "20020212": "Carvanal", "20020329": "Sexta-feira Santa (Paixão de Cristo)", "20020530": "Corpus Christi", "20030304": "Carvanal", "20030418": "Sexta-feira Santa (Paixão de Cristo)", "20030619": "Corpus Christi", "20040224": "Carvanal", "20040409": "Sexta-feira Santa (Paixão de Cristo)", "20040610": "Corpus Christi", "20050208": "Carvanal", "20050325": "Sexta-feira Santa (Paixão de Cristo)", "20050526": "Corpus Christi", "20060228": "Carvanal", "20060414": "Sexta-feira Santa (Paixão de Cristo)", "20060615": "Corpus Christi", "20070220": "Carvanal", "20070406": "Sexta-feira Santa (Paixão de Cristo)", "20070607": "Corpus Christi", "20080205": "Carvanal", "20080321": "Sexta-feira Santa (Paixão de Cristo)", "20080522": "Corpus Christi", "20090224": "Carvanal", "20090410": "Sexta-feira Santa (Paixão de Cristo)", "20090611": "Corpus Christi", "20100216": "Carvanal", "20100402": "Sexta-feira Santa (Paixão de Cristo)", "20100603": "Corpus Christi", "20110308": "Carvanal", "20110422": "Sexta-feira Santa (Paixão de Cristo)", "20110623": "Corpus Christi", "20120221": "Carvanal", "20120406": "Sexta-feira Santa (Paixão de Cristo)", "20120607": "Corpus Christi", "20130212": "Carvanal", "20130329": "Sexta-feira Santa (Paixão de Cristo)", "20130530": "Corpus Christi", "20140304": "Carvanal", "20140418": "Sexta-feira Santa (Paixão de Cristo)", "20140619": "Corpus Christi", "20150217": "Carvanal", "20150403": "Sexta-feira Santa (Paixão de Cristo)", "20150604": "Corpus Christi", "20160209": "Carvanal", "20160325": "Sexta-feira Santa (Paixão de Cristo)", "20160526": "Corpus Christi", "20170228": "Carvanal", "20170414": "Sexta-feira Santa (Paixão de Cristo)", "20170615": "Corpus Christi", "20180213": "Carvanal", "20180330": "Sexta-feira Santa (Paixão de Cristo)", "20180531": "Corpus Christi", "20190305": "Carvanal", "20190419": "Sexta-feira Santa (Paixão de Cristo)", "20190620": "Corpus Christi", "20200225": "Carvanal", "20200410": "Sexta-feira Santa (Paixão de Cristo)", "20200611": "Corpus Christi", "20210216": "Carvanal", "20210402": "Sexta-feira Santa (Paixão de Cristo)", "20210603": "Corpus Christi", "20220301": "Carvanal", "20220415": "Sexta-feira Santa (Paixão de Cristo)", "20220616": "Corpus Christi", "20230221": "Carvanal", "20230407": "Sexta-feira Santa (Paixão de Cristo)", "20230608": "Corpus Christi", "20240213": "Carvanal", "20240329": "Sexta-feira Santa (Paixão de Cristo)", "20240530": "Corpus Christi", "20250304": "Carvanal", "20250418": "Sexta-feira Santa (Paixão de Cristo)", "20250619": "Corpus Christi", "20260217": "Carvanal", "20260403": "Sexta-feira Santa (Paixão de Cristo)", "20260604": "Corpus Christi", "20270209": "Carvanal", "20270326": "Sexta-feira Santa (Paixão de Cristo)", "20270527": "Corpus Christi", "20280229": "Carvanal", "20280414": "Sexta-feira Santa (Paixão de Cristo)", "20280615": "Corpus Christi", "20290213": "Carvanal", "20290330": "Sexta-feira Santa (Paixão de Cristo)", "20290531": "Corpus Christi", "20300305": "Carvanal", "20300419": "Sexta-feira Santa (Paixão de Cristo)", "20300620": "Corpus Christi", "20310225": "Carvanal", "20310411": "Sexta-feira Santa (Paixão de Cristo)", "20310612": "Corpus Christi", "20320210": "Carvanal", "20320326": "Sexta-feira Santa (Paixão de Cristo)", "20320527": "Corpus Christi", "20330301": "Carvanal", "20330415": "Sexta-feira Santa (Paixão de Cristo)", "20330616": "Corpus Christi", "20340221": "Carvanal", "20340407": "Sexta-feira Santa (Paixão de Cristo)", "20340608": "Corpus Christi", "20350206": "Carvanal", "20350323": "Sexta-feira Santa (Paixão de Cristo)", "20350524": "Corpus Christi", "20360226": "Carvanal", "20360411": "Sexta-feira Santa (Paixão de Cristo)", "20360612": "Corpus Christi", "20370217": "Carvanal", "20370403": "Sexta-feira Santa (Paixão de Cristo)", "20370604": "Corpus Christi" } } }';

    private array $feriadosPorAno = [];

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @throws ViewException
     */
    public function gerarOuCorrigirDiasUteis(\DateTime $dtIni, \DateTime $dtFim, ?bool $corrigir = true)
    {
        try {
            $rsDiasUteisConfig = $this->conn
                ->fetchAssociative('SELECT valor FROM cfg_app_config WHERE chave = \'bse_dia_util.json\' AND app_uuid = :coreAppUUID',
                    ['coreAppUUID' => '175bd6d3-6c29-438a-9520-47fcee653cc5']);

            if (!$rsDiasUteisConfig) {
                $this->conn->insert('cfg_app_config', [
                    'chave' => 'bse_dia_util.json',
                    'app_uuid' => '175bd6d3-6c29-438a-9520-47fcee653cc5',
                    'valor' => $this->bse_dia_util_json,
                    'inserted' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'updated' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'estabelecimento_id' => 1,
                    'user_inserted_id' => 1,
                    'user_updated_id' => 1,
                ]);
                $valorJson = $this->bse_dia_util_json;
            } else {
                $valorJson = $rsDiasUteisConfig['valor'];
            }

            $diasUteisConfig = json_decode($valorJson, true);

            $agora = (new \DateTime())->format('Y-m-d H:i:s');
            $rsDiasUteisNaBase = $this->conn->fetchAllAssociative('SELECT * FROM bse_diautil WHERE date(dia) BETWEEN :dtIni AND :dtFim',
                [
                    'dtIni' => $dtIni->format('Y-m-d'),
                    'dtFim' => $dtFim->format('Y-m-d')
                ]);
            $diasUteisNaBase = [];
            foreach ($rsDiasUteisNaBase as $diaUtilNaBase) {
                $dti = DateTimeUtils::parseDateStr($diaUtilNaBase['dia'])->format('Ymd');
                $diasUteisNaBase[$dti] = $diaUtilNaBase;
            }

            $datesList = DateTimeUtils::getDatesList($dtIni, $dtFim);
            foreach ($datesList as $date) {
                $dti = $date->format('Ymd');
                if ($diasUteisNaBase[$dti] ?? false) {
                    if (!$corrigir) {
                        continue;
                    }
                }

                $comercial = 1;
                $financeiro = 1;

                if (isset($diasUteisConfig['feriados']['fixos'][$date->format('dm')])) {
                    $descricao = $diasUteisConfig['feriados']['fixos'][$date->format('dm')];
                    $comercial = 0;
                    $financeiro = 0;
                } elseif (isset($diasUteisConfig['feriados']['moveis'][$date->format('Ymd')])) {
                    $descricao = $diasUteisConfig['feriados']['moveis'][$date->format('Ymd')];
                    $comercial = 0;
                    $financeiro = 0;
                } else {
                    $feriadoFromBrasilApi = null;
                    if ($feriadoFromBrasilApi = $this->getFromBrasilApi($date)) {
                        $descricao = $feriadoFromBrasilApi['name'];
                        $comercial = 0;
                        $financeiro = 0;
                    } else {

                        $descricao = mb_strtoupper(DateTimeUtils::getDiaDaSemana($date));
                        $diaSemana = (int)$date->format('w');

                        // domingo
                        if ($diaSemana === 0) {
                            $comercial = 0;
                            $financeiro = 0;
                        }
                        // sábado
                        if ($diaSemana === 6) {
                            $financeiro = 0;
                            if (!($diasUteisConfig['considera_sabados_como_comercial'] ?? false)) {
                                $comercial = 0;
                            }
                        }
                    }
                }

                $diaUtil = [
                    'dia' => $date->format('Y-m-d'),
                    'descricao' => mb_strtoupper($descricao),
                    'comercial' => $comercial,
                    'financeiro' => $financeiro,
                    'municipio_id' => null,
                    'inserted' => $agora,
                    'updated' => $agora,
                    'estabelecimento_id' => 1,
                    'user_inserted_id' => 1,
                    'user_updated_id' => 1,
                    'version' => 0
                ];

                if (isset($diasUteisNaBase[$dti])) {
                    $this->conn->update('bse_diautil', $diaUtil, ['id' => $diasUteisNaBase[$dti]['id']]);
                } else {
                    $this->conn->insert('bse_diautil', $diaUtil);
                }
            }

        } catch (\Throwable $e) {
            throw new ViewException('Ocorreu um erro ao gerar os dias úteis');
        }

    }


    public function getFromBrasilApi(\DateTime $dia): array
    {
        $ano = (int)$dia->format('Y');
        if (!($this->feriadosPorAno[$ano] ?? false)) {
            $client = new Client();
            $response = $client->request('GET', 'https://brasilapi.com.br/api/feriados/v1/' . $ano);
            $body = $response->getBody();
            $json = json_decode($body, true);
            foreach ($json as $feriado) {
                $this->feriadosPorAno[$ano][$feriado['date']] = $feriado;
            }
        }
        return $this->feriadosPorAno[$ano][$dia->format('Ymd')] ?? [];
    }

}