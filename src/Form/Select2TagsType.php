<?php

namespace CrosierSource\CrosierLibBaseBundle\Form;


use CrosierSource\CrosierLibBaseBundle\Form\Transformer\Select2TagsTransformer;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @package CrosierSource\CrosierLibBaseBundle\Form
 */
class Select2TagsType extends AbstractType
{

    private Select2TagsTransformer $transformer;

    public function __construct(Select2TagsTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'The selected issue does not exist',
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * @inheritDoc
     */
    public function mapDataToForms($viewData, iterable $forms)
    {
        // TODO: Implement mapDataToForms() method.
    }

    /**
     * @inheritDoc
     */
    public function mapFormsToData(iterable $forms, &$viewData)
    {
        // TODO: Implement mapFormsToData() method.
    }
}