<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use RuntimeException;
use TCPDF;

class PdfController extends Controller
{
    public function generate(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'hps' => 'required|date_format:H:i',
            'data' => 'required|array',
            'data.*.ramassage' => 'required|array',
            'data.*.ramassage.*.HDR' => 'required|string',
            'data.*.ramassage.*.trajet' => 'required|string',
            'data.*.emps' => 'required|array',
            'data.*.emps.*.matricule' => 'required|string',
            'data.*.emps.*.nom' => 'required|string',
            'data.*.emps.*.telephone' => 'nullable|string',
            'data.*.emps.*.lieu_ramassage' => 'nullable|string',
            'data.*.emps.*.pnt_ramassage_adm' => 'nullable|string',
            'data.*.emps.*.pnt_ramassage_dec' => 'nullable|string',
        ]);

        $date = Carbon::parse($data['date'])->format('d/m/Y');
        $hps = Carbon::createFromFormat('H:i', $data['hps'])->format('H:i');
        $logoPath = public_path('images/logosof.png');

        if (! is_file($logoPath)) {
            throw new RuntimeException('Le logo requis pour générer le PDF est introuvable.');
        }

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('LogiCar');
        $pdf->SetAuthor('SOFITRANS S.A');
        $pdf->SetTitle("Planning du {$date} - {$hps}");
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(12, 12, 12);
        $pdf->SetAutoPageBreak(true, 16);
        $pdf->AddPage();

        $pdf->Image($logoPath, 12, 12, 52, 0, 'PNG');
        $pdf->SetXY(76, 13);
        $pdf->SetFont('helvetica', 'B', 19);
        $pdf->SetTextColor(16, 24, 40);
        $pdf->Cell(0, 10, 'Planning des ramassages', 0, 1, 'L');
        $pdf->SetX(76);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->SetTextColor(71, 84, 103);
        $pdf->Cell(90, 7, 'Date : '.$date, 0, 0, 'L');
        $pdf->Cell(90, 7, 'Prise de service : '.$hps, 0, 1, 'L');

        $pdf->SetY(36);
        $pdf->SetDrawColor(21, 94, 239);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(12, 36, 285, 36);
        $pdf->Ln(7);

        foreach ($data['data'] as $itemIndex => $item) {
            if ($itemIndex > 0) {
                $pdf->Ln(4);
            }

            foreach ($item['ramassage'] as $ramassage) {
                $pdf->SetFillColor(239, 244, 255);
                $pdf->SetDrawColor(208, 221, 255);
                $pdf->SetTextColor(21, 54, 111);
                $pdf->SetFont('helvetica', 'B', 11);
                $pdf->Cell(
                    273,
                    10,
                    '  Trajet : '.self::escape($ramassage['trajet'])
                        .'     |     Début du ramassage : '.self::escape(self::formatTime($ramassage['HDR'])),
                    1,
                    1,
                    'L',
                    true,
                );
                $pdf->Ln(2);
            }

            $table = '
                <table border="1" cellpadding="5" cellspacing="0" width="100%">
                    <thead>
                        <tr style="background-color:#155eef;color:#ffffff;font-weight:bold;">
                            <th width="8%" align="center">Matricule</th>
                            <th width="20%" align="left">Nom et prénom</th>
                            <th width="13%" align="center">Téléphone</th>
                            <th width="17%" align="left">Lieu de ramassage</th>
                            <th width="21%" align="left">Point ADM</th>
                            <th width="21%" align="left">Point décalé</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($item['emps'] as $index => $employee) {
                $background = $index % 2 === 0 ? '#ffffff' : '#f5f8ff';
                $table .= '<tr style="background-color:'.$background.';">
                    <td width="8%" align="center">'.self::escape($employee['matricule']).'</td>
                    <td width="20%" align="left">'.self::escape($employee['nom']).'</td>
                    <td width="13%" align="center">'.self::escape($employee['telephone'] ?? '').'</td>
                    <td width="17%" align="left">'.self::escape($employee['lieu_ramassage'] ?? '').'</td>
                    <td width="21%" align="left">'.self::escape($employee['pnt_ramassage_adm'] ?? '').'</td>
                    <td width="21%" align="left">'.self::escape($employee['pnt_ramassage_dec'] ?? '').'</td>
                </tr>';
            }

            $table .= '</tbody></table>';

            $pdf->SetTextColor(16, 24, 40);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->writeHTML($table, true, false, true, false, '');
        }

        $pageCount = $pdf->getNumPages();
        $pdf->SetAutoPageBreak(false);
        for ($page = 1; $page <= $pageCount; $page++) {
            $pdf->setPage($page);
            $pdf->SetY(-12);
            $pdf->SetDrawColor(228, 231, 236);
            $pdf->Line(12, $pdf->GetY(), 285, $pdf->GetY());
            $pdf->SetY(-10);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetTextColor(102, 112, 133);
            $pdf->Cell(0, 6, "SOFITRANS S.A  |  Planning du {$date}  |  Page {$page}/{$pageCount}", 0, 0, 'R');
        }

        $filename = 'planning_'.$data['date'].'_'.$hps.'.pdf';

        return response($pdf->Output($filename, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function formatTime(string $value): string
    {
        try {
            return Carbon::parse($value)->format('H:i');
        } catch (\Carbon\Exceptions\InvalidFormatException) {
            return $value;
        }
    }
}
