<?php


namespace CrosierSource\CrosierLibBaseBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer as APIPlatformDateTimeNormalizer;

/**
 * Class DateTimeNormalizer
 * @package CrosierSource\CrosierLibBaseBundle\Normalizer
 */
class DateTimeNormalizer extends APIPlatformDateTimeNormalizer
{
    
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        // substitui o comportamento padrão para poder receber valores null (problema com ApiPlatform)
        if (null === $data) {
            return null;
        }

        return parent::denormalize($data, $type, $format, $context);
    }
}