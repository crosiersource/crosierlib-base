<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\APIUtils;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class APIProblemException
 *
 * @package CrosierSource\CrosierLibBaseBundle\Utils\APIUtils
 * @author Carlos Eduardo Pauluk
 */
class APIProblemException extends HttpException
{
    private $apiProblem;

    public function __construct(ApiProblem $apiProblem, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $this->apiProblem = $apiProblem;
        $statusCode = $apiProblem->getStatusCode();
        $message = $apiProblem->getTitle();

        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    public function getApiProblem()
    {
        return $this->apiProblem;
    }
}
