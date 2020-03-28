<?php

namespace CrosierSource\CrosierLibBaseBundle\Form\Transformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class Select2TagsTransformer
 * @package CrosierSource\CrosierLibBaseBundle\Form\Transformer
 */
class Select2TagsTransformer implements DataTransformerInterface
{

    /**
     * Transforms an object (issue) to a string (number).
     *
     *
     * @return string
     */
    public function transform($issue)
    {
        if (null === $issue) {
            return [];
        }

        return $issue->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     */
    public function reverseTransform($issueNumber)
    {
        // no issue number? It's optional, so that's ok
        if (!$issueNumber) {
            return;
        }

        $issue = $this->entityManager
            ->getRepository(Issue::class)
            // query for the issue with this id
            ->find($issueNumber);

        if (null === $issue) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $issueNumber
            ));
        }

        return $issue;
    }
}