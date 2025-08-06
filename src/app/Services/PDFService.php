<?php

namespace App\Services;

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class PDFService
{
    public function generateBookingPDF($view, $data)
    {
        // Configure mPDF with emoji support
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        // Create mPDF instance with compact layout and emoji support
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 11,
            'default_font' => 'dejavusans',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 5,
            'orientation' => 'P',
            'fontDir' => array_merge($fontDirs, [
                __DIR__ . '/../../resources/fonts',
            ]),
            'fontdata' => $fontData + [
                'dejavusanswithemoji' => [
                    'R' => 'DejaVuSans.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ]
            ],
            'default_font' => 'dejavusans',
            'autoScriptToLang' => true,
            'autoLangToFont' => true
        ]);

        // Skip external CSS loading as it may cause issues
        // CSS is now embedded in the templates

        // Use final clean templates
        if ($view === 'customer.bookings.pdf') {
            $view = 'customer.bookings.pdf-final';
        } elseif ($view === 'admin.bookings.pdf') {
            $view = 'admin.bookings.pdf-cards';
        }
        
        // Render the view with data
        $html = view($view, $data)->render();
        
        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        return $mpdf;
    }
}