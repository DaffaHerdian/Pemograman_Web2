<?php

namespace App\Http\Controllers;

use App\Services\Documents;
use App\Services\Finance;
use App\Services\ReportData;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\AutoFilter;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

class ReportController extends Controller
{
    public function index(Request $r, ReportData $reports)
    {
        return view('finance.reports', $reports->data($reports->filters($r)));
    }

    public function print(Request $r, ReportData $reports)
    {
        return view('finance.report-print', $reports->data($reports->filters($r)));
    }

    public function pdf(Request $r, ReportData $reports, Documents $docs, Finance $finance)
    {
        $d = $reports->data($reports->filters($r));
        $finance->audit($r->user()->id, 'report_pdf', $d['type'], ['rows' => $d['rows']->count()]);

        return $docs->pdf(view('finance.report-print', $d + ['isPdf' => true])->render(), 'laporan-'.$d['type'].'.pdf', count($d['headers']) >= 8 ? 'landscape' : 'portrait');
    }

    public function excel(Request $r, ReportData $reports, Finance $finance)
    {
        $d = $reports->data($reports->filters($r));
        $path = tempnam(storage_path('app/private'), 'xlsx-');
        try {
            $safe = static function ($value): string {
                $text = (string) $value;

                return preg_match('/^[\s]*[=+\-@]/u', $text) ? "'".$text : $text;
            };
            $options = new Options;
            $lastColumn = count($d['headers']) - 1;
            foreach ([1, 2, 3, 4] as $rowNumber) {
                $options->mergeCells(0, $rowNumber, $lastColumn, $rowNumber);
            }
            $writer = new Writer($options);
            $writer->openToFile($path);
            $sheet = $writer->getCurrentSheet();
            $sheet->setName('Laporan '.ucfirst($d['type']));
            $sheet->setSheetView((new SheetView)->setFreezeRow(6)->setShowGridLines(false));
            $sheet->setPrintTitleRows('$1:$6');
            foreach ($d['headers'] as $index => $header) {
                $sheet->setColumnWidth(str_contains($header, '(Rp)') ? 23 : (in_array($header, ['Anggota', 'Nama', 'Proses']) ? 29 : 20), $index + 1);
            }
            // Column-width API uses one-based indexes; merging uses zero-based indexes.
            $writer->addRow(Row::fromValues([$safe($d['identity']->name)], (new Style)->setFontBold()->setFontSize(18)->setFontColor('0F766E'))->setHeight(32));
            $writer->addRow(Row::fromValues(['Laporan '.ucfirst($d['type'])], (new Style)->setFontBold()->setFontSize(13))->setHeight(25));
            $period = 'Periode: '.($d['filters']['tanggal_dari'] ?? 'Awal pencatatan').' sampai '.($d['filters']['tanggal_sampai'] ?? 'Saat ini');
            if (! empty($d['filters']['anggota'])) {
                $period .= ' | ID anggota: '.$d['filters']['anggota'];
            }
            if (! empty($d['filters']['status'])) {
                $period .= ' | Status: '.$d['filters']['status'];
            }
            if (! empty($d['filters']['jenis_simpanan'])) {
                $period .= ' | Simpanan: '.$d['filters']['jenis_simpanan'];
            }
            $writer->addRow(Row::fromValues([$safe($period)], (new Style)->setFontColor('64748B')->setFontSize(10))->setHeight(24));
            $writer->addRow(Row::fromValues(['Dicetak '.now('Asia/Jakarta')->format('d/m/Y H:i').' WIB | '.$d['rows']->count().' catatan'], (new Style)->setFontColor('64748B')->setFontSize(10)));
            $writer->addRow(Row::fromValues([]));
            $headerStyle = (new Style)->setFontBold()->setFontColor('FFFFFF')->setBackgroundColor('0F766E')->setShouldWrapText();
            $writer->addRow(Row::fromValues($d['headers'], $headerStyle)->setHeight(32));
            $sheet->setAutoFilter(new AutoFilter(0, 6, $lastColumn, max(6, 6 + $d['rows']->count())));
            $moneyKeys = ['jumlah', 'jumlah_pinjaman', 'sisa_pinjaman', 'sisa_jasa', 'jumlah_bayar', 'jasa_bayar', 'remaining_principal', 'remaining_interest', 'amount'];
            foreach ($d['rows'] as $index => $row) {
                $background = $index % 2 ? 'F1F5F9' : 'FFFFFF';
                $bodyStyle = (new Style)->setFontSize(10)->setBackgroundColor($background)->setShouldWrapText();
                $moneyStyle = (clone $bodyStyle)->setFormat('#,##0.00;[Red]-#,##0.00')->setCellAlignment('right');
                $cells = [];
                foreach ((array) $row as $key => $value) {
                    $cells[] = in_array($key, $moneyKeys, true)
                        ? Cell::fromValue((float) $value, $moneyStyle)
                        : Cell::fromValue($safe($value), $bodyStyle);
                }
                $writer->addRow((new Row($cells))->setHeight(27));
            }
            if ($d['hasTotal']) {
                $label = match ($d['type']) {
                    'simpanan' => 'Total mutasi bersih', 'pinjaman' => 'Total sisa kewajiban', 'angsuran' => 'Total pembayaran', default => 'Total sesuai filter'
                };
                $writer->addRow(Row::fromValues([]));
                $style = (new Style)->setFontBold()->setBackgroundColor('E6F4F1')->setFontColor('0F766E');
                $writer->addRow((new Row([Cell::fromValue($label, $style), Cell::fromValue((float) $d['total'], (clone $style)->setFormat('#,##0.00')->setCellAlignment('right'))]))->setHeight(30));
            }
            $writer->close();
            $bytes = file_get_contents($path);
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
        $finance->audit($r->user()->id, 'report_excel', $d['type'], ['rows' => $d['rows']->count()]);

        return response($bytes, 200, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'Content-Disposition' => 'attachment; filename="laporan-'.$d['type'].'.xlsx"', 'Cache-Control' => 'private, no-store']);
    }
}
