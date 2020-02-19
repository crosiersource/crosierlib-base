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
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormType para ser utilizada com campos do tipo JSON.
 *
 * Class JsonType
 * @package CrosierSource\CrosierLibBaseBundle\Form
 */
class CompoType extends AbstractType implements DataMapperInterface
{

    private array $metadata;

    private string $nomeDoCampo;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->metadata = $options['metadata'];
        $this->nomeDoCampo = $options['nomeDoCampo'];

        $campos = explode('|', $this->metadata['formato']);
        foreach ($campos as $campo) {
            list($prefixoCampo, $tipo, $sufixo) = explode(',', $campo);
            $nomeDoSubCampo = $this->nomeDoCampo . '_' . $prefixoCampo;
            $metadata = ['tipo' => $tipo];
            switch ($tipo) {
                case "string":
                    $this->buildTextType($builder, $nomeDoSubCampo, $metadata);
                    break;
                case "int":
                    $this->buildIntegerType($builder, $nomeDoSubCampo, $metadata);
                    break;
                case "decimal1":
                case "decimal2":
                case "decimal3":
                case "decimal4":
                case "decimal5":
                    $this->buildDecimalType($builder, $nomeDoSubCampo, $metadata);
                    break;
                default:
                    throw new \LogicException('tipo N/D para campo ' . $nomeDoSubCampo . ': ' . $tipo);
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
            'grouping' => 'true',
            'attr' => [
                'class' => 'crsr-dec' . $metadata['tipo'][7]
            ]
        ]);
    }

    // ...

    /**
     * Do atributo da entidade para os campos.
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

        $viewData = explode('|', $viewData);

        // invalid data type
        if (!is_array($viewData)) {
            throw new UnexpectedTypeException($viewData, 'array');
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);
        $campos = explode('|', $this->metadata['formato']);

        foreach ($campos as $i => $campo) {
            $d = explode(',', $campo);
            $nomeDoSubCampo = $this->nomeDoCampo . '_' . $d[0];
            $tipo = $d[1];
            if (isset($forms[$nomeDoSubCampo]) && isset($viewData[$i]) && $viewData[$i] !== null) {
                $this->setFormData($forms[$nomeDoSubCampo], $nomeDoSubCampo, $tipo, $viewData[$i]);
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

        $campos = explode('|', $this->metadata['formato']);
        foreach ($campos as $campo) {
            $d = explode(',', $campo);
            $nomeDoSubCampo = $this->nomeDoCampo . '_' . $d[0];
            $tipo = $d[1];
            $this->setViewData($viewData, $nomeDoSubCampo, $tipo, $forms[$nomeDoSubCampo]->getData());
        }
    }

    /**
     * @param array $viewData
     * @param string $nomeDoCampo
     * @param string $tipo
     * @param null $val
     */
    private function setViewData(array &$viewData, string $nomeDoCampo, string $tipo, $val = null)
    {
        if (!$val) return;
        switch ($tipo) {
            case "string":
            case "int":
            case "decimal1":
            case "decimal2":
            case "decimal3":
            case "decimal4":
            case "decimal5":
            case "preco":
                $viewData[$nomeDoCampo] = $val;
                break;
            default:
                throw new \LogicException('tipo N/D para campo ' . $nomeDoCampo . ': ' . $tipo);
        }
    }

    /**
     * @param FormInterface $form
     * @param string $nomeDoCampo
     * @param string $tipo
     * @param $val
     */
    private function setFormData(FormInterface $form, string $nomeDoCampo, string $tipo, $val)
    {
        switch ($tipo) {
            case "string":
                $form->setData($val);
                break;
            case "int":
                $form->setData((int)$val);
            case "decimal1":
            case "decimal2":
            case "decimal3":
            case "decimal4":
            case "decimal5":
            case "preco":
                if (!is_numeric($val)) {
                    $fmt = new \NumberFormatter('pt_BR', \NumberFormatter::DECIMAL);
                    $number = $fmt->parse($val);
                    $form->setData($number);
                } else {
                    $form->setData($val);
                }
                break;
            default:
                throw new \LogicException('tipo N/D para campo ' . $nomeDoCampo . ': ' . $tipo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $campos = explode('|', $options['metadata']['formato']);
        $prefixos = [];
        $tipos = [];
        $sufixos = [];
        foreach ($campos as $campo) {
            $d = explode(',', $campo);
            $prefixos[] = $d[0];
            $tipos[] = $d[1];
            $sufixos[] = $d[2];
        }
        $view->vars['prefixos'] = $prefixos;
        $view->vars['tipos'] = $tipos;
        $view->vars['sufixos'] = $sufixos;

        // para renderizar a div do widget com "col-sm-10 form-inline d-flex flex-nowrap"
        $view->vars['compo'] = true;
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // hidden fields cannot have a required attribute
            'metadata' => [],
            'required' => false,
            // Pass errors to the parent
            'error_bubbling' => true,
            'compound' => true,
            'nomeDoCampo' => ''
        ]);
    }


}