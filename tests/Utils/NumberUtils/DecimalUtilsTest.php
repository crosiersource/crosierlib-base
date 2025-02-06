<?php

namespace Tests\Utils\NumberUtils;

use CrosierSource\CrosierLibBaseBundle\Utils\NumberUtils\DecimalUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeUtilsTest
 *
 * @package Tests\Utils\DateTimeUtils
 * @author Carlos Eduardo Pauluk
 */
class DecimalUtilsTest extends TestCase
{

    public function testRounds()
    {

        $testes = [
            // valor, precisão, UP, HALF_UP, DOWN, HALF_DOWN
            [1.2340, 2, 1.240, 1.23, 1.230, 1.230],
            [1.2340, 3, 1.234, 1.234, 1.234, 1.234],
            [1.2345, 3, 1.235, 1.235, 1.234, 1.234],
            [0.99, 2, 0.99, 0.99, 0.99, 0.99],
            [0.99, 1, 1.00, 1.00, 0.90, 1.00],
            [123.456, 2, 123.46, 123.46, 123.45, 123.46],
            [123.455, 2, 123.46, 123.46, 123.45, 123.45],
            [123.454, 2, 123.46, 123.45, 123.45, 123.45],
            [-123.454, 2, -123.45, -123.45, -123.46, -123.45], // ??
        ];


        foreach ($testes as $t) {
            $this->assertEquals($t[2], DecimalUtils::round($t[0], $t[1], DecimalUtils::ROUND_UP));
            $this->assertEquals($t[3], DecimalUtils::round($t[0], $t[1], DecimalUtils::ROUND_HALF_UP));
            $this->assertEquals($t[4], DecimalUtils::round($t[0], $t[1], DecimalUtils::ROUND_DOWN));
            $this->assertEquals($t[5], DecimalUtils::round($t[0], $t[1], DecimalUtils::ROUND_HALF_DOWN));
        }

    }


    public function testDividirValorProporcionalmente()
    {
        $testes = [
            [100, [200, 200, 200, 200, 200], [20, 20, 20, 20, 20]],
            [100, [10, 10, 10], [33.34, 33.33, 33.33]],
            [12.34, [10, 20, 30], [2.06, 4.11, 6.17]],
            [23.45, [7, 17, 97], [1.36, 3.29, 18.80]],
            [159.76, [13.48, 19.17, 7.3], [53.91, 76.66, 29.19]],
            [0.00, [1, 1, 1], [0.00, 0.00, 0.00]],
            [0.00, [0, 0, 0], [0.00, 0.00, 0.00]],
            [0.01, [1, 1, 1], [0.01, 0.00, 0.00]],
            [0.01, [0, 0, 1], [0.00, 0.00, 0.01]],
            [0.01, [0, 1, 0], [0.00, 0.01, 0.00]],
            [0.02, [0, 1, 0], [0.00, 0.02, 0.00]],
            [0.02, [50, 50, 0], [0.01, 0.01, 0.00]],
            [151.15, [0.0, 990.0], [0.0, 151.15]],
        ];
        foreach ($testes as $t) {
            $this->assertEquals($t[2], DecimalUtils::dividirValorProporcionalmente($t[0], $t[1]));
            $somaPartes = DecimalUtils::somarValoresMonetarios($t[2]);
            $this->assertEquals($t[0], $somaPartes);
        }
    }
    
    public function testDividirValorProporcionalmenteComRestoNaUltima()
    {
        $testes = [
            [100, [200, 200, 200, 200, 200], [20, 20, 20, 20, 20]],
            [100, [10, 10, 10], [33.33, 33.33, 33.34]],
            [12.34, [10, 20, 30], [2.06, 4.11, 6.17]],
            [23.45, [7, 17, 97], [1.36, 3.29, 18.80]],
            [159.76, [13.48, 19.17, 7.3], [53.91, 76.66, 29.19]],
            [0.00, [1, 1, 1], [0.00, 0.00, 0.00]],
            [0.00, [0, 0, 0], [0.00, 0.00, 0.00]],
            [0.01, [1, 1, 1], [0.01, 0.00, 0.00]],
            [0.01, [0, 0, 1], [0.00, 0.00, 0.01]],
            [0.01, [0, 1, 0], [0.00, 0.01, 0.00]],
            [0.02, [0, 1, 0], [0.00, 0.02, 0.00]],
            [0.02, [50, 50, 0], [0.01, 0.01, 0.00]],
            [151.15, [0.0, 990.0], [0.0, 151.15]],

            // Novos casos de teste
            // 1. Valor a ser dividido proporcionalmente entre parcelas com valores iguais
            [100, [50, 50, 50, 50], [25, 25, 25, 25]],

            // 2. Valor a ser dividido proporcionalmente entre parcelas com valores diferentes
            [100, [10, 20, 30, 40], [10, 20, 30, 40]],

            // 3. Valor a ser dividido proporcionalmente com a diferença na última parcela (resto pequeno)
            [99.99, [33, 33, 33], [33.33, 33.33, 33.33]],

            // 4. Valor a ser dividido proporcionalmente com a diferença na primeira parcela (resto pequeno)
            [99.99, [10, 20, 30, 40], [10, 20, 30, 39.99]],

            // 5. Valor a ser dividido entre parcelas, incluindo zero
            [100, [0, 50, 50], [0.00, 50.00, 50.00]],

            // 6. Valor a ser dividido proporcionalmente entre parcelas com valores negativos e positivos
            [100, [-50, 150, 100], [-25.00, 75.00, 50.00]],

            // 7. Valor a ser dividido proporcionalmente quando o valor total é zero
            [0, [10, 20, 30], [0.00, 0.00, 0.00]],

            // 8. Valor a ser dividido proporcionalmente entre parcelas com um valor muito maior que os outros
            [100, [1, 1, 998], [0.10, 0.10, 99.80]],

            // 9. Valor a ser dividido proporcionalmente entre parcelas muito pequenas
            [0.03, [0.01, 0.01, 0.01], [0.01, 0.01, 0.01]],

            // 10. Valor a ser dividido proporcionalmente com diferença decentes entre valores de parcelas
            [100, [5, 15, 80], [5.00, 15.00, 80.00]],

            // 11. Valor a ser dividido proporcionalmente com parcelas iguais a zero
            [0.02, [0, 0, 1], [0.00, 0.00, 0.02]],

            // 12. Valor a ser dividido proporcionalmente com valores e diferença na primeira parcela
            [250, [40, 60, 100], [50, 75, 125]],

            // 13. Valor a ser dividido proporcionalmente com uma parcela significativamente maior
            [100, [1, 1, 98], [1, 1, 98]],

            // 14. Valor a ser dividido proporcionalmente com grandes valores nas parcelas
            [1000, [100, 200, 700], [100, 200, 700]],

            // 15. Valor a ser dividido proporcionalmente com valor muito pequeno
            [0.04, [1, 2, 1], [0.01, 0.02, 0.01]],

            // 16. Valor a ser dividido proporcionalmente com um único valor grande na parcela
            [500, [1, 999], [0.5, 499.5]],

            // 17. Valor a ser dividido proporcionalmente com uma parcela zero e o resto positivo
            [100, [0, 100], [0, 100]],

            // 18. Valor a ser dividido proporcionalmente com valor zero a ser distribuído
            [0, [50, 100], [0.00, 0.00]],

            // 19. Valor a ser dividido proporcionalmente com valores grandes em múltiplas parcelas
            [1000, [100, 200, 300, 400], [100, 200, 300, 400]],

            // 20. Valor a ser dividido proporcionalmente com valores muito pequenos e diferença na última parcela
            [0.05, [0.01, 0.02, 0.02], [0.01, 0.02, 0.02]],
        ];
                
        
        foreach ($testes as $t) {
            $this->assertEquals($t[2], DecimalUtils::dividirValorProporcionalmente($t[0], $t[1], false));
            $somaPartes = DecimalUtils::somarValoresMonetarios($t[2]);
            $this->assertEquals($t[0], $somaPartes);
        }
    }
    
    
    


}
