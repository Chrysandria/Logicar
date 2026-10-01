<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCPDF;
use Carbon\Carbon;

class PdfController extends Controller
{
    public function generate(Request $request)
    {
        $validate = $request->validate([
            'date' => 'required|date',
            'hps' => 'required|date_format:H:i',
            'data' => 'required|array',
        ]);

        if ($validate) {
            $date = Carbon::parse($request->date)->format('d-m-Y');
            $hps = Carbon::createFromFormat('H:i', $request->hps)->format('H:i');

            $data = $request->data;

            $pdf = new TCPDF();
            $pdf->setCreator('Laravel TCPDF');
            $pdf->setAuthor('Logicar');
            $pdf->setTitle($date . ' - ' . $hps);

            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            $pdf->AddPage();

            $pdf->setFont('helvetica', 'B', 16);
            $pdf->Cell(0, 15, 'SOFITRANS S.A', 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 10, "Planning du : " . $date, 0, 1, 'R');
            $pdf->Cell(0, 10, 'HPS : ' . $hps, 0, 1, 'R');
            $divider = "<hr style='border:1px solid #000;' />";
            $pdf->writeHTML($divider, true, false, true, false, '');


            $pdf->Ln(10);
            $pdf->SetFont('helvetica', '', 12);

            foreach ($data as $item) {
                $table = '<table border="1" cellpadding="4">
                            <thead>
                                <tr style="background-color:#303030; color:#fff;">
                                    <th style="inline-size: 30px;"><strong>#</strong></th>
                                    <th style="inline-size: 150px;"><strong>Nom</strong></th>
                                    <th><strong>Telephone</strong></th>
                                    <th><strong>Lieu de ramassage</strong></th>
                                    <th><strong>Point de ramassage ADM</strong></th>
                                    <th><strong>Point de ramassage décalé</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                        ';
                foreach ($item["ramassage"] as $ramassage) {
                    $pdf->setFont('helvetica', '', 10);
                    $pdf->Ln(0);
                    $pdf->Cell(0,10,"Trajet : ".$ramassage["trajet"],0,0,'L');
                    $pdf->Cell(0,10,"HDR : ".Carbon::parse($ramassage["HDR"])->format('H:i'),0,1,'R');
                    $pdf->Ln(2);
                }

                foreach($item["emps"] as $emp){
                    $pdf->setFont('helvetica', '', 10);
                    $pdf->Ln(0);
                    $table .= '    <tr>
                                        <td align="center" style="inline-size: 30px;">' . $emp['matricule'] . '</td>
                                        <td align="center" style="inline-size: 150px;">' . $emp['nom'] . '</td>
                                        <td align="center">' . $emp['telephone'] . '</td>
                                        <td align="center">' . $emp['lieu_ramassage'] . '</td>
                                        <td align="center">' . $emp['pnt_ramassage_adm'] . '</td>
                                        <td align="center">' . $emp['pnt_ramassage_dec'] . '</td>
                                    </tr>
                              ';
                }
                $table .= '</tbody></table>';
                $pdf->writeHTML($table, true, false, true, false, '');

               $pdf->writeHTML('<hr style="border:1px solid #000;" />', true, false, true, false, '');
               $pdf->Ln(5);
            }

            return response($pdf->Output('planning_' . $date . '_' . $hps . '.pdf', 'S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "inline;filename=planning_$date._$hps.pdf");
        } else {
            return response()-> json(["message" => "Erreur de validation"]);
        }
    }
}
