<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\PdfUtils;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @author Carlos E Pauluk
 */
class PdfUtils
{

    public static function genPdf(string $html): ?string
    {
        gc_collect_cycles();
        gc_disable();

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('enable_remote', true);

        $dompdf = new Dompdf($pdfOptions);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        gc_collect_cycles();
        gc_enable();

        return $dompdf->output();
    }

}