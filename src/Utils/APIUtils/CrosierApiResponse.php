<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\APIUtils;

use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Classe para padronizar as respostas às chamadas customizadas às APIs do Crosier.
 *
 * @author Carlos Eduardo Pauluk
 */
class CrosierApiResponse
{

    public static function success($data = null, ?string $msg = 'Executado com sucesso'): JsonResponse
    {
        return new JsonResponse(
            [
                'RESULT' => 'OK',
                'MSG' => $msg,
                'DATA' => $data
            ]
        );
    }


    public static function error(?\Throwable $t = null, ?bool $sendExceptionMessage = true, ?string $msg = "Ocorreu um erro", $data = null): JsonResponse
    {
        $r = [
            'RESULT' => 'ERRO',
            'MSG' => $msg,
            'EXCEPTION_MSG' => ($sendExceptionMessage && $t) ? ExceptionUtils::treatException($t) : null,
            'DATA' => $data,
        ];
        return new JsonResponse($r, 418);
    }
    
    
    public static function viewExceptionError(?\Throwable $t = null, ?string $msg2 = null): JsonResponse
    {
        if ($t instanceof ViewException) {
            $msg = ExceptionUtils::treatException($t);
        } else {
            $msg = 'Ocorreu um erro';
        }
        $r = [
            'RESULT' => 'ERRO',
            'MSG' => $msg2,
            'EXCEPTION_MSG' => $msg,
            'DATA' => null,
        ];
        return new JsonResponse($r, 418);
    }
}