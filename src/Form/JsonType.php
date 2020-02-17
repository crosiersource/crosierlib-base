<?php


namespace CrosierSource\CrosierLibBaseBundle\Form;


use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormType para ser utilizada com campos do tipo JSON.
 *
 * Class JsonType
 * @package CrosierSource\CrosierLibBaseBundle\Form
 */
class JsonType extends AbstractType implements DataMapperInterface
{

    private array $defsJson;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $this->defsJson = $options['defsJson'];

        foreach ($this->defsJson as $defsCampo) {
            switch ($defsCampo['tipo']) {
                case "string":
                    $this->buildTextType($builder, $defsCampo);
                    break;
                case "date":
                    $this->buildDateType($builder, $defsCampo);
                    break;
                default:
                    throw new \LogicException('tipo N/D');
            }
        }
        $builder->setDataMapper($this);

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $defsCampo
     */
    private function buildTextType(FormBuilderInterface $builder, array $defsCampo)
    {
        $builder->add($defsCampo['nome'], TextType::class, [
            'mapped' => false,
            'label' => $defsCampo['label'],
            'required' => $defsCampo['required'] ?? false,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $defsCampo
     */
    private function buildDateType(FormBuilderInterface $builder, array $defsCampo)
    {
        $builder->add($defsCampo['nome'], DateType::class, [
            'mapped' => false,
            'label' => $defsCampo['label'],
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'class' => 'crsr-date'
            ],
            'required' => $defsCampo['required'] ?? false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // hidden fields cannot have a required attribute
            'required' => false,
            // Pass errors to the parent
            'error_bubbling' => true,
            'compound' => true,
            'defsJson' => []
        ]);
    }

    // ...

    /**
     * Do atributo da entidade para os campos.
     *
     * @param array|null $viewData
     */
    public function mapDataToForms($viewData, $forms)
    {
        // there is no data yet, so nothing to prepopulate
        if (null === $viewData) {
            return;
        }

        // invalid data type
        if (!is_array($viewData)) {
            throw new UnexpectedTypeException($viewData, 'array');
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        foreach ($this->defsJson as $campo) {
            $this->setFormData($forms[$campo['nome']], $campo, $viewData[$campo['nome']]);
        }
    }

    /**
     * Dos campos para o atributo da entidade.
     *
     * @param iterable|FormInterface[] $forms
     * @param mixed $viewData
     */
    public function mapFormsToData($forms, &$viewData)
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);
        $viewData = [];
        foreach ($this->defsJson as $defCampo) {
            $this->setViewData($viewData, $defCampo, $forms[$defCampo['nome']]->getData());
        }
    }

    /**
     * @param $viewData
     * @param $defCampo
     * @param $val
     */
    private function setViewData(array &$viewData, array $defCampo, $val)
    {
        switch ($defCampo['tipo']) {
            case 'date':
                if (! $val instanceof \DateTime) throw new \LogicException($defCampo['nome'] . ' is not DateTime');
                $viewData[$defCampo['nome']] = $val->format('Y-m-d');
                break;
            case 'datetime':
                if (! $val instanceof \DateTime) throw new \LogicException($defCampo['nome'] . ' is not DateTime');
                $viewData[$defCampo['nome']] = $val->format('Y-m-d H:m:i');
                break;
            default:
                $viewData[$defCampo['nome']] = $val;
        }
    }

    /**
     * @param FormInterface $form
     * @param $defCampo
     * @param $val
     */
    private function setFormData(FormInterface $form, $defCampo, $val)
    {
        switch ($defCampo['tipo']) {
            case 'date':
            case 'datetime':
                $form->setData(DateTimeUtils::parseDateStr($val));
                break;
            default:
                $form->setData($val);
        }
    }


}