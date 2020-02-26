<?php

namespace CrosierSource\CrosierLibBaseBundle\Form;


use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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

    private array $jsonMetadata;

    private array $jsonData;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $this->jsonMetadata = $options['jsonMetadata'];
        $this->jsonData = $options['jsonData'];

        foreach ($this->jsonMetadata['campos'] as $nome => $metadata) {
            switch ($metadata['tipo']) {
                case "string":
                    $this->buildTextType($builder, $nome, $metadata);
                    break;
                case "html":
                    $this->buildHtmlType($builder, $nome, $metadata);
                    break;
                case "int":
                    $this->buildIntegerType($builder, $nome, $metadata);
                    break;
                case "decimal1":
                case "decimal2":
                case "decimal3":
                case "decimal4":
                case "decimal5":
                    $this->buildDecimalType($builder, $nome, $metadata);
                    break;
                case "preco":
                    $this->buildMoneyType($builder, $nome, $metadata);
                    break;
                case "date":
                    $this->buildDateType($builder, $nome, $metadata);
                    break;
                case "datetime":
                    $this->buildDatetimeType($builder, $nome, $metadata);
                    break;
                case "bool":
                    $this->buildBoolType($builder, $nome, $metadata);
                    break;
                case "tags":
                    $this->buildTagsType($builder, $nome, $metadata);
                    break;
                case "compo":
                    $this->buildCompoType($builder, $nome, $metadata);
                    break;
                case "select":
                    $this->buildSelectType($builder, $nome, $metadata);
                    break;
                default:
                    throw new \LogicException('tipo N/D para campo ' . $nome . ': ' . $metadata['tipo']);
            }
        }
        $builder->setDataMapper($this);

    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $nome
     * @param array $metadata
     */
    private function buildTextType(FormBuilderInterface $builder, string $nome, array $metadata)
    {
        $builder->add($nome, TextType::class, [
            'mapped' => false,
            'label' => $metadata['label'] ?? $nome,
            'required' => $metadata['required'] ?? false,
            'disabled' => $metadata['disabled'] ?? false,
            'attr' => [
                'class' => isset($metadata['notuppercase']) && $metadata['notuppercase'] === true ? 'notuppercase' : ''
            ]
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $nome
     * @param array $metadata
     */
    private function buildHtmlType(FormBuilderInterface $builder, string $nome, array $metadata)
    {
        $builder->add($nome, TextareaType::class, [
            'mapped' => false,
            'label' => $metadata['label'] ?? $nome,
            'required' => $metadata['required'] ?? false,
            'attr' => [
                'class' => 'summernote'
            ],
            'disabled' => $metadata['disabled'] ?? false
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $nome
     * @param array $metadata
     */
    private function buildIntegerType(FormBuilderInterface $builder, string $nome, array $metadata)
    {
        $builder->add($nome, IntegerType::class, [
            'mapped' => false,
            'label' => $metadata['label'] ?? $nome,
            'required' => $metadata['required'] ?? false,
            'disabled' => $metadata['disabled'] ?? false
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $nome
     * @param array $metadata
     */
    private function buildDecimalType(FormBuilderInterface $builder, string $nome, array $metadata)
    {
        $builder->add($nome, NumberType::class, [
            'mapped' => false,
            'label' => $metadata['label'] ?? $nome,
            'required' => $metadata['required'] ?? false,
            'scale' => (int)$metadata['tipo'][7],
            'grouping' => true,
            'attr' => [
                'class' => 'crsr-dec' . $metadata['tipo'][7]
            ],
            'disabled' => $metadata['disabled'] ?? false
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $nome
     * @param array $metadata
     */
    private function buildMoneyType(FormBuilderInterface $builder, string $nome, array $metadata)
    {
        $builder->add($nome, MoneyType::class, [
            'mapped' => false,
            'label' => $metadata['label'] ?? $nome,
            'required' => $metadata['required'] ?? false,
            'currency' => 'BRL',
            'grouping' => 'true',
            'attr' => [
                'class' => 'crsr-money'
            ],
            'disabled' => $metadata['disabled'] ?? false
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $nome
     * @param array $metadata
     */
    private function buildDateType(FormBuilderInterface $builder, string $nome, array $metadata)
    {
        $builder->add($nome, DateType::class, [
            'mapped' => false,
            'label' => $metadata['label'] ?? $nome,
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'class' => 'crsr-date'
            ],
            'required' => $metadata['required'] ?? false,
            'disabled' => $metadata['disabled'] ?? false
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $nome
     * @param array $metadata
     */
    private function buildDatetimeType(FormBuilderInterface $builder, string $nome, array $metadata)
    {
        $builder->add($nome, DateTimeType::class, [
            'mapped' => false,
            'label' => $metadata['label'] ?? $nome,
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'dd/MM/yyyy HH:mm:ss',
            'attr' => ['class' => 'crsr-datetime'],
            'required' => $metadata['required'] ?? false,
            'disabled' => $metadata['disabled'] ?? false
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $nome
     * @param array $metadata
     */
    private function buildBoolType(FormBuilderInterface $builder, string $nome, array $metadata)
    {
        $builder->add($nome, ChoiceType::class, [
            'mapped' => false,
            'label' => $metadata['label'] ?? $nome,
            'choices' => [
                'Sim' => 'S',
                'Não' => 'N'
            ],
            'attr' => [
                'class' => 'autoSelect2'
            ],
            'required' => $metadata['required'] ?? false,
            'disabled' => $metadata['disabled'] ?? false
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $nome
     * @param array $metadata
     */
    private function buildTagsType(FormBuilderInterface $builder, string $nome, array $metadata)
    {
        $choices = null;
        if (isset($this->jsonData[$nome])) {
            $choices = is_array($this->jsonData[$nome]) ? $this->jsonData[$nome] : explode(',', $this->jsonData[$nome]);
            $choices = array_combine($choices, $choices); // pois o ChoiceType precisa que os valores sejam chaves
        }
        $builder->add($nome, ChoiceType::class, [
            'mapped' => false,
            'multiple' => true,
            'choices' => $choices,
            'label' => $metadata['label'] ?? $nome,
            'attr' => [
                'class' => 'autoSelect2',
                'data-tags' => 'true',
                'data-token-separator' => ',',
            ],
            'required' => $metadata['required'] ?? false,
            'disabled' => $metadata['disabled'] ?? false
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $nome
     * @param array $metadata
     */
    private function buildCompoType(FormBuilderInterface $builder, string $nome, array $metadata)
    {
        $builder->add($nome, CompoType::class, [
            'mapped' => false,
            'metadata' => $metadata,
            'label' => $metadata['label'] ?? $nome,
            'nomeDoCampo' => $nome,
            'attr' => [
            ],
            'required' => $metadata['required'] ?? false,
            'disabled' => $metadata['disabled'] ?? false
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param string $nome
     * @param array $metadata
     */
    private function buildSelectType(FormBuilderInterface $builder, string $nome, array $metadata)
    {
        $choices = array_combine($metadata['opcoes'], $metadata['opcoes']); // pois o ChoiceType precisa que os valores sejam chaves
        $builder->add($nome, ChoiceType::class, [
            'mapped' => false,
            'multiple' => false,
            'choices' => $choices,
            'label' => $metadata['label'] ?? $nome,
            'attr' => [
                'class' => 'autoSelect2',
            ],
            'required' => $metadata['required'] ?? false,
        ]);
    }

    /**
     * Do BD para os campos no html.
     *
     * @param array|null $viewData
     * @param $forms
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

        foreach ($this->jsonMetadata['campos'] as $nomeDoCampo => $metadata) {
            if (isset($forms[$nomeDoCampo]) && isset($viewData[$nomeDoCampo]) && $viewData[$nomeDoCampo] !== NUll) {
                $this->setFormData($forms[$nomeDoCampo], $nomeDoCampo, $metadata, $viewData[$nomeDoCampo]);
            }
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
        foreach ($this->jsonMetadata['campos'] as $nomeDoCampo => $metadata) {
            $this->setViewData($viewData, $nomeDoCampo, $metadata, $forms[$nomeDoCampo]->getData());
        }
    }

    /**
     * @param array $viewData
     * @param string $nomeDoCampo
     * @param array $metadata
     * @param null $val
     */
    private function setViewData(array &$viewData, string $nomeDoCampo, array $metadata, $val = null)
    {
        if (!$val) return;
        switch ($metadata['tipo']) {
            case "string":
            case "html":
            case "int":
            case "bool":
            case "select":
                $viewData[$nomeDoCampo] = $val;
                break;
            case "tags":
                $viewData[$nomeDoCampo] = implode(',', $val);
                break;
            case "decimal1":
            case "decimal2":
            case "decimal3":
            case "decimal4":
            case "decimal5":
            case "preco":
                if (!is_numeric($val)) {
                    $fmt = new \NumberFormatter('pt_BR', \NumberFormatter::DECIMAL);
                    $number = $fmt->parse($val);
                    $viewData[$nomeDoCampo] = $number;
                } else {
                    $viewData[$nomeDoCampo] = $val;
                }
                break;
            case 'date':
                if (!$val instanceof \DateTime) throw new \LogicException($nomeDoCampo . ' is not DateTime');
                $viewData[$nomeDoCampo] = $val->format('Y-m-d');
                break;
            case 'datetime':
                if (!$val instanceof \DateTime) throw new \LogicException($nomeDoCampo . ' is not DateTime');
                $viewData[$nomeDoCampo] = $val->format('Y-m-d H:m:i');
                break;
            case "compo":
                $viewData[$nomeDoCampo] = implode('|', $val);
                break;
            default:
                throw new \LogicException('tipo N/D para campo ' . $nomeDoCampo . ': ' . $metadata['tipo']);
        }
    }

    /**
     * @param FormInterface $form
     * @param string $nomeDoCampo
     * @param array $metadata
     * @param mixed $val
     */
    private function setFormData(FormInterface $form, string $nomeDoCampo, array $metadata, $val)
    {
        switch ($metadata['tipo']) {
            case "string":
            case "html":
            case "int":
            case "bool":
            case "decimal1":
            case "decimal2":
            case "decimal3":
            case "decimal4":
            case "decimal5":
            case "preco":
            case "compo":
            case "select":
                $form->setData($val !== '' ? $val : null);
                break;
            case "tags":
                if (!is_array($val)) {
                    $form->setData(explode(',', $val));
                } else {
                    $form->setData($val);
                }
                break;
            case 'date':
            case 'datetime':
                $form->setData(DateTimeUtils::parseDateStr($val));
                break;
            default:
                throw new \LogicException('tipo N/D para campo ' . $nomeDoCampo . ': ' . $metadata['tipo']);
        }
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
            'jsonMetadata' => [],
            'jsonData' => []
        ]);
    }

}
