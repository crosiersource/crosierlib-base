<?php


namespace CrosierSource\CrosierLibBaseBundle\Twig;


/**
 * Class FilterInput
 * @package CrosierSource\CrosierLibBaseBundle\Twig
 * @author Carlos Eduardo Pauluk
 */
class FilterInput
{
    /** @var string */
    private $label;

    /** @var string */
    private $inputName;

    /** @var string */
    private $type;

    /** @var null|string */
    private $val;

    /** @var null|array */
    private $config;

    /**
     * FilterInput constructor.
     * @param string $label
     * @param string $inputName
     * @param string|null $type
     * @param string|null $val
     * @param array|null $config
     */
    public function __construct(string $label, string $inputName, ?string $type = 'STRING', ?string $val = null, ?array $config = null)
    {
        $this->label = $label;
        $this->inputName = $inputName;
        $this->type = $type;
        $this->val = $val;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return FilterInput
     */
    public function setLabel(string $label): FilterInput
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getInputName(): string
    {
        return $this->inputName;
    }

    /**
     * @param string $inputName
     * @return FilterInput
     */
    public function setInputName(string $inputName): FilterInput
    {
        $this->inputName = $inputName;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return FilterInput
     */
    public function setType(string $type): FilterInput
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVal(): ?string
    {
        return $this->val;
    }

    /**
     * @param string|null $val
     * @return FilterInput
     */
    public function setVal(?string $val): FilterInput
    {
        $this->val = $val;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getConfig(): ?array
    {
        return $this->config;
    }

    /**
     * @param array|null $config
     * @return FilterInput
     */
    public function setConfig(?array $config): FilterInput
    {
        $this->config = $config;
        return $this;
    }


}

