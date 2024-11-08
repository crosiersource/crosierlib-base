<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Config;

use CrosierSource\CrosierLibBaseBundle\Business\Config\EntityChangeVo;
use InfluxDB2\Client;

class InfluxDbEntityChangesRepository
{

    private Client $client;

    private function getClient(): Client
    {
        if (!isset($this->client)) {
            $this->client = new Client([
                "url" => $_SERVER['INFLUXDB_URL'],
                "token" => $_SERVER['INFLUXDB_APITOKEN'],
                "bucket" => $_SERVER['INFLUXDB_BUCKET'],
                "org" => "crosiersource",
                "verifySSL" => false,
            ]);
        }
        return $this->client;
    }

    public function findCreatedByEntityClassAndEntityId(mixed $entityClass, mixed $entityId)
    {
    }

    public function findChangesByEntityClassAndEntityId(mixed $entityClass, mixed $entityId): array
    {
        $client = $this->getClient();
        $queryApi = $client->createQueryApi();


        $query = sprintf('
                            from(bucket: "%s")
                              |> range(start: %s)
                              |> filter(fn: (r) => r._measurement == "%s")
                              |> filter(fn: (r) => r._field == "alteracoes")
                              |> filter(fn: (r) => r.entity == "\"%s\"")
                              |> filter(fn: (r) => r.entityId == "%s")
                              |> group()
                              |> sort(columns: ["_time"], desc: true)
                            ',
            $_SERVER['INFLUXDB_BUCKET'],
            "-90d",
            "entity_change",
            $entityClass,
            $entityId);


        $result = $queryApi->query($query);

        $rs = [];

        if (($result[0] ?? false) && $result[0]?->records) {
            foreach ($result[0]->records as $record) {
                $vo = new EntityChangeVo(
                    trim($record->values['entity'], '"'),
                    trim($record->values['entityId'], '"'),
                    trim($record->values['ip'], '"'),
                    trim($record->values['userId'], '"'),
                    trim($record->values['username'], '"'),
                    trim($record->values['_time'], '"'),
                    trim($record->values['_value'], '"')
                );

                $rs[] = $vo;
            }
        }

        return $rs;
    }
}