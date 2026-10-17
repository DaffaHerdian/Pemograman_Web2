<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

final class Documents
{
    public function pdf(string $html, string $filename, string $orientation = 'portrait'): Response
    {
        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('defaultMediaType', 'print');
        $options->set('isPhpEnabled', false);
        $options->set('chroot', storage_path('app/private'));
        $pdf = new Dompdf($options);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4', $orientation);
        $pdf->render();
        $canvas = $pdf->getCanvas();
        $canvas->page_text($canvas->get_width() - 104, $canvas->get_height() - 26, 'Halaman {PAGE_NUM} / {PAGE_COUNT}', $pdf->getFontMetrics()->getFont('DejaVu Sans'), 7, [0.39, 0.45, 0.55]);

        return response($pdf->output(), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="'.$filename.'"', 'Cache-Control' => 'private, no-store']);
    }
}
