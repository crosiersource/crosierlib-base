<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\PdfUtils;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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

    
    public static function genDownloadPdf(string $filename, string $html): ?Response
    {
        $pdfContent = self::genPdf($html);
        return self::downloadPdf($filename, $pdfContent);
    }
    
    
    public static function downloadPdf(string $filename, $pdfContent): ?Response
    {
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

}
