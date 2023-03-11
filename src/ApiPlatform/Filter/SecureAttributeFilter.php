<?php

namespace CrosierSource\CrosierLibBaseBundle\ApiPlatform\Filter;

use ApiPlatform\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SecureAttributeFilter implements FilterInterface
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        foreach ($this->security->getUser()->getRoles() as $role) {
            $context['groups'][] = $role;
        }
    }

}