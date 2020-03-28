<?php

namespace CrosierSource\CrosierLibBaseBundle\Form;


use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Factory\CachingFactoryDecorator;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 *
 * @package CrosierSource\CrosierLibBaseBundle\Form
 */
class Select2TagsType extends ChoiceType
{

    private $choiceListFactory;

    public function __construct(ChoiceListFactoryInterface $choiceListFactory = null)
    {
        $this->choiceListFactory = $choiceListFactory ?: new CachingFactoryDecorator(
            new PropertyAccessDecorator(
                new DefaultChoiceListFactory()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = null !== $options['choices'] ? $options['choices'] : [];
        $choiceList = $this->choiceListFactory->createListFromChoices($choices, $options['choice_value']);
        $builder->setAttribute('choice_list', $choiceList);

        $builder->resetModelTransformers();
        $builder->resetViewTransformers();
        $builder->addViewTransformer(new CallbackTransformer(
            function ($tagsAsArray) {
                if ($tagsAsArray) {
                    $tags = explode(', ', $tagsAsArray);
                    return array_combine($tags, $tags);
                } else {
                    return [];
                }
            },
            function ($tagsAsString) {
                // transform the string back to an array
                return $tagsAsString ? implode(', ', $tagsAsString) : '';
            }));


    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $choiceTranslationDomain = $options['choice_translation_domain'];
        if ($view->parent && null === $choiceTranslationDomain) {
            $choiceTranslationDomain = $view->vars['translation_domain'];
        }

        /** @var ChoiceListInterface $choiceList */
        $choiceList = $form->getConfig()->getAttribute('choice_list');

        /** @var ChoiceListView $choiceListView */
        $choiceListView = $form->getConfig()->hasAttribute('choice_list_view')
            ? $form->getConfig()->getAttribute('choice_list_view')
            : $this->createChoiceListView($choiceList, $options);

        $view->vars = array_replace($view->vars, [
            'multiple' => $options['multiple'],
            'expanded' => $options['expanded'],
            'preferred_choices' => $choiceListView->preferredChoices,
            'choices' => $choiceListView->choices,
            'separator' => '-------------------',
            'placeholder' => null,
            'choice_translation_domain' => $choiceTranslationDomain,
        ]);

        // The decision, whether a choice is selected, is potentially done
        // thousand of times during the rendering of a template. Provide a
        // closure here that is optimized for the value of the form, to
        // avoid making the type check inside the closure.
        if ($options['multiple']) {
            $view->vars['is_selected'] = function ($choice, array $values) {
                return \in_array($choice, $values, true);
            };
        } else {
            $view->vars['is_selected'] = function ($choice, $value) {
                return $choice === $value;
            };
        }

        // Check if the choices already contain the empty value
        $view->vars['placeholder_in_choices'] = $choiceListView->hasPlaceholder();

        // Only add the empty value option if this is not the case
        if (null !== $options['placeholder'] && !$view->vars['placeholder_in_choices']) {
            $view->vars['placeholder'] = $options['placeholder'];
        }

        if ($options['multiple'] && !$options['expanded']) {
            // Add "[]" to the name in case a select tag with multiple options is
            // displayed. Otherwise only one of the selected options is sent in the
            // POST request.
            $view->vars['full_name'] .= '[]';
        }
    }


    private function createChoiceListView(ChoiceListInterface $choiceList, array $options)
    {
        return $this->choiceListFactory->createView(
            $choiceList,
            $options['preferred_choices'],
            $options['choice_label'],
            $options['choice_name'],
            $options['group_by'],
            $options['choice_attr']
        );
    }


}