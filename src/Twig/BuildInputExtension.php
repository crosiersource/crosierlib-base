<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class CrosierCoreAssetExtension
 *
 * @package CrosierSource\CrosierLibBaseBundle\Twig
 * @author Carlos Eduardo Pauluk
 */
class BuildInputExtension extends AbstractExtension
{

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(RegistryInterface $doctrine, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('buildRowInput', [$this, 'buildRowInput'], ['is_safe' => ['html']]),
            new TwigFunction('buildInput', [$this, 'buildInput'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string $label
     * @param string $inputName
     * @param string $tipo
     * @param mixed|null $val
     * @param array|null $config
     * @return string
     */
    function buildRowInput(string $label, string $inputName, string $tipo, $val = null, ?array $config = null)
    {
        $for = isset($config['arrayField']) ? $config['arrayField'] . '[' . $inputName . ']' : $inputName;

        $r = '<div class="form-group">';
        $r .= '<label for="' . $for . '">' . $label . '</label>';
        $r .= $this->buildInput($inputName, $tipo, $val, $config);
        $r .= '</div>';
        return $r;

    }

    /**
     * @param string $inputName
     * @param string $tipo
     * @param mixed|null $val
     * @param array|null $config
     * @return string
     */
    function buildInput(string $inputName, string $tipo, $val = null, ?array $config = null)
    {
        $prefixo = $config['prefixo'] ?? null;
        $sufixo = $config['sufixo'] ?? null;
        if (isset($config['arrayField'])) {
            $inputName = $config['arrayField'] . '[' . $inputName . ']';
        }

        if ($val === []) {
            $val = null;
        }

        try {
            switch ($tipo) {
                case 'HIDDEN':
                    $r = '<input type="hidden" id="' . $inputName . '" name="' . $inputName . '" class="form-control" value="' . $val . '">';
                    return $r;
                case 'STRING':
                    $r = '<div class="input-group">';
                    if ($prefixo) {
                        $r .= '<div class="input-group-prepend"><span class="input-group-text">' . $prefixo . '</span></div>';
                    }
                    $r .= '<input type="text" id="' . $inputName . '" name="' . $inputName . '" class="form-control" value="' . $val . '">';
                    if ($sufixo) {
                        $r .= '<div class="input-group-append"><span class="input-group-text">' . $sufixo . '</span></div>';
                    }
                    $r .= '</div>';
                    return $r;
                case 'INTEGER':
                    $r = '<div class="input-group">';
                    if ($prefixo) {
                        $r .= '<div class="input-group-prepend"><span class="input-group-text">' . $prefixo . '</span></div>';
                    }
                    $r .= '<input type="number" min="0" step="1" id="' . $inputName . '" name="' . $inputName . '" class="form-control int" value="' . $val . '">';
                    if ($sufixo) {
                        $r .= '<div class="input-group-append"><span class="input-group-text">' . $sufixo . '</span></div>';
                    }
                    $r .= '</div>';
                    return $r;
                case 'DECIMAL1':
                case 'DECIMAL2':
                case 'DECIMAL3':
                case 'DECIMAL4':
                case 'DECIMAL5':
                    $valFormatado = number_format((float)$val, $tipo[7], ',', '.');
                    $r = '<div class="input-group">';
                    if ($prefixo) {
                        $r .= '<div class="input-group-prepend"><span class="input-group-text">' . $prefixo . '</span></div>';
                    }
                    $r .= '<input type="text" class="form-control ' . strtolower($tipo) . '" id="' . $inputName . '_' . '" name="' . $inputName . '" value="' . $valFormatado . '">';
                    if ($sufixo) {
                        $r .= '<div class="input-group-append"><span class="input-group-text">' . $sufixo . '</span></div>';
                    }
                    $r .= '</div>';
                    return $r;
                case 'BETWEEN_INTEGER':
                    $r = '<div class="input-group">Entre ';

                    if ($prefixo) {
                        $r .= '<div class="input-group-prepend"><span class="input-group-text">' . $prefixo . '</span></div>';
                    }
                    $r .= '<input type="number" min="0" step="1" id="' . $inputName . '[i]" name="' . $inputName . '[i]" class="form-control int" value="' . $val['i'] . '">';
                    if ($sufixo) {
                        $r .= '<div class="input-group-append"><span class="input-group-text">' . $sufixo . '</span></div>';
                    }
                    $r .= ' e ';

                    if ($prefixo) {
                        $r .= '<div class="input-group-prepend"><span class="input-group-text">' . $prefixo . '</span></div>';
                    }
                    $r .= '<input type="number" min="0" step="1" id="' . $inputName . '[f]" name="' . $inputName . '[f]" class="form-control int" value="' . $val['f'] . '">';
                    if ($sufixo) {
                        $r .= '<div class="input-group-append"><span class="input-group-text">' . $sufixo . '</span></div>';
                    }

                    $r .= '</div>';
                    return $r;
                case 'BETWEEN_DATE':
                    $dtIni = '';
                    $dtFim = '';
                    if (is_array($val)) {
                        $dtIni = isset($val['i']) ? DateTimeUtils::parseDateStr($val['i'])->format('d/m/Y') : '';
                        $dtFim = isset($val['f']) ? DateTimeUtils::parseDateStr($val['f'])->format('d/m/Y') : '';
                    }
                    $r = '<div class="input-group">';
                    $r .= '<input type="text" id="' . $inputName . '[i]" name="' . $inputName . '[i]" class="form-control crsr-date" value="' . $dtIni . '"> - ';
                    $r .= '<input type="text" id="' . $inputName . '[f]" name="' . $inputName . '[f]" class="form-control crsr-date" value="' . $dtFim . '">';
                    $r .= '</div>';
                    return $r;
                case 'EQ_DIAMES':
                    $r = '<div class="input-group">';
                    $r .= '<input type="text" id="' . $inputName . '" name="' . $inputName . '" class="form-control crsr-date-diames" value="' . $val . '">';
                    $r .= '</div>';
                    return $r;
                case 'DATE':
                    if ($val) {
                        $dt = DateTimeUtils::parseDateStr($val);
                        $val = $dt ? $dt->format('d/m/Y') : null;
                    }
                    $r = '<div class="input-group">';
                    if ($prefixo) {
                        $r .= '<div class="input-group-prepend"><span class="input-group-text">' . $prefixo . '</span></div>';
                    }
                    $r .= '<input type="text" id="' . $inputName . '" name="' . $inputName . '" class="form-control crsr-' . strtolower($tipo) . '" value="' . $val . '">';
                    if ($sufixo) {
                        $r .= '<div class="input-group-append"><span class="input-group-text">' . $sufixo . '</span></div>';
                    }
                    $r .= '</div>';
                    return $r;
                case 'DATETIME':
                    if ($val) {
                        $dt = DateTimeUtils::parseDateStr($val);
                        $val = $dt ? $dt->format('d/m/Y H:i:s') : null;
                    }

                    $r = '<div class="input-group">';
                    if ($prefixo) {
                        $r .= '<div class="input-group-prepend"><span class="input-group-text">' . $prefixo . '</span></div>';
                    }
                    $r .= '<input type="text" id="' . $inputName . '" name="' . $inputName . '" class="form-control crsr-' . strtolower($tipo) . '" value="' . $val . '">';
                    if ($sufixo) {
                        $r .= '<div class="input-group-append"><span class="input-group-text">' . $sufixo . '</span></div>';
                    }
                    $r .= '</div>';
                    return $r;
                case 'LISTA':
                    $options = [];

                    return '<select id="' . $inputName . '" name="' . $inputName . '"
                                            data-options="' . htmlentities($options) . '"
                                            class="form-control autoSelect2"></select>';
                case 'TAGS':

                    $tagsoptions = null;
                    $options = [];
                    if ($val) {
                        $selecteds = explode(',', $val);

                        foreach ($selecteds as $v) {
                            $options[] = ['id' => $v, 'text' => $v, 'selected' => true];
                        }
                    }
                    $tagsoptions = htmlentities(json_encode($options));

                    return '<select multiple id="' . $inputName . '" name="' . $inputName . '[]"
                                data-tagsoptions="' . $tagsoptions . '"
                                class="form-control autoSelect2 notuppercase"></select>';

                default:
                    return 'tipo não definido';
            }
        } catch (\Exception $e) {
            $this->logger->error('Erro ao construir campo');
            return '<< erro ao construir campo >>';
        }

    }

}