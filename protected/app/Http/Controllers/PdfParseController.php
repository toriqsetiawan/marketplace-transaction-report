<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Spatie\PdfToText\Pdf;

class PdfParseController extends Controller
{
    public function index()
    {
        return view('pdf-parser');
    }

    public function store(Request $request)
    {
        // Validate file
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
        ]);

        // Handle the uploaded file
        $uploadedPdf = $request->file('pdf');
        $fileName = time() . '_' . $uploadedPdf->getClientOriginalName();
        $destinationPath = public_path('pdf');
        $uploadedPdf->move($destinationPath, $fileName);

        // Full file path
        $pdfPath = $destinationPath . DIRECTORY_SEPARATOR . $fileName;

        // Parse the PDF
        $text = Pdf::getText($pdfPath);

        // dd($text);

        // Split the text into individual shipments by 'No. Resi' as the delimiter
        $shipments = preg_split('/\f+/', $text);

        // Initialize an array to store shipment data
        $shipmentData = [];

        foreach ($shipments as $shipment) {
            if (empty(trim($shipment))) continue;

            preg_match('/(?:No\.?\s*Resi|Resi)\s*:?[\s]*([A-Z0-9]+)/i', $shipment, $resiMatch);
            preg_match('/Pengirim\s*:\s*(.+?)(?:\s{2,}|$)/i', $shipment, $pengirimMatch);
            preg_match('/Batas\s*Kirim\s*:\s*(\d{2}-\d{2}-\d{4})/i', $shipment, $batasKirimMatch);
            preg_match('/No\.?\s*Pesanan\.?\s*:\s*([\w\-]+)/i', $shipment, $pesananMatch);
            preg_match('/Berat\s*:\s*([0-9.,]+\s*\w*)/i', $shipment, $beratMatch);
            preg_match('/Penerima\s*:\s*(.*?)(?:\n|HOME|No\.?\s*Resi|$)/is', $shipment, $penerimaMatch);
            // preg_match('/HOME\s*(.*?)\s*(?:Berat|Batas Kirim|No\.? Resi)/is', $shipment, $addressMatch);
            preg_match('/(?:HOME|OFFICE)\s*(.*?)\s*(?:Berat|Batas Kirim|No\.? Resi)/is', $shipment, $addressMatch);
            preg_match('/Qty\s*(\d+)/i', $shipment, $qtyMatch);


            $shipmentData[] = [
                'no_resi' => $resiMatch[1] ?? 'No Resi not found',
                'pengirim' => $pengirimMatch[1] ?? 'Pengirim not found',
                'penerima' => $penerimaMatch[1] ?? 'Penerima not found',
                'batas_kirim' => $batasKirimMatch[1] ?? 'Batas Kirim not found',
                'no_pesanan' => $pesananMatch[1] ?? 'No Pesanan not found',
                'berat' => $beratMatch[1] ?? 'Berat not found',
                'alamat' => trim(preg_replace('/\s+/', ' ', $addressMatch[1] ?? 'Alamat not found')),
                'qty' => isset($qtyMatch[1]) ? (int)$qtyMatch[1] : 0,
            ];
        }

        // Return the result as JSON
        return response()->json($shipmentData, 200, [], JSON_PRETTY_PRINT);
    }
}
