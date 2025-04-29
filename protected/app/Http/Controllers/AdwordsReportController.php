<?php

namespace App\Http\Controllers;

use App\Models\AdwordsReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdwordsReportController extends Controller
{
    public function index(Request $request)
    {
        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['customer']);
        })->get();

        $start = $request->start_date
            ? Carbon::createFromFormat('d/m/Y', $request->start_date)->startOfDay()->format('Y-m-d')
            : now()->startOfMonth()->format('Y-m-d');
        $end = $request->end_date
            ? Carbon::createFromFormat('d/m/Y', $request->end_date)->endOfDay()->format('Y-m-d')
            : now()->endOfMonth()->format('Y-m-d');

        $data = AdwordsReport::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy([
                'user_id',
                function ($item) {
                    return $item->date;
                }
            ]);

        return view('ads-report.index', compact('data', 'users'));
    }

    public function create()
    {
        return view('ads-report.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv',
            'user_id' => 'required|exists:users,id',
        ]);

        $file = $request->file('file');
        $userId = $request->user_id;

        // Open the file
        $handle = fopen($file, 'r');

        // Skip metadata lines
        for ($i = 0; $i < 5; $i++) {
            fgetcsv($handle);
        }

        // Read header row and process the data rows
        $header = fgetcsv($handle); // Should be: Urutan,Waktu,Deskripsi,Jumlah,Catatan

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 5) {
                continue; // skip incomplete rows
            }

            [$order, $dateStr, $description, $amount, $note] = $row;

            // Convert date from dd/mm/yyyy to yyyy-mm-dd
            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $dateStr)->format('Y-m-d');

            // Generate a checksum to prevent duplicates
            $checksum = md5($userId . $date . $description . $amount . $note);

            // Check if it already exists
            if (!AdwordsReport::where('checksum', $checksum)->exists()) {
                AdwordsReport::create([
                    'user_id' => $userId,
                    'checksum' => $checksum,
                    'date' => $date,
                    'description' => $description,
                    'total' => (float) str_replace(',', '', $amount),
                    'note' => $note === '-' ? null : $note,
                ]);
            }
        }

        fclose($handle);

        return redirect()->route('ads-report.index')
            ->with('success', 'AdWords report imported successfully.');
    }
}
