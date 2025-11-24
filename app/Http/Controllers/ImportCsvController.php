<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

// class ImportCsvController extends Controller
// {
   
//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         // Récupérer les données CSV de la session si elles existent
//         $headers = session('csv_headers', []);
//         $rowCount = session('csv_row_count', 0);
//         $filename = session('csv_filename', '');
        
//         return view('sendmail.import-csv', compact('headers', 'rowCount', 'filename'));
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         // Validation du fichier CSV
//         $request->validate([
//             'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240', // max 10MB
//         ]);

//         try {
//             $file = $request->file('csv_file');
//             $path = $file->getRealPath();
            
//             // Lecture du fichier CSV
//             $csvData = array_map('str_getcsv', file($path));
            
//             if (empty($csvData)) {
//                 return back()->with('error', 'Le fichier CSV est vide.');
//             }

//             // Récupération des headers (première ligne)
//             $headers = array_shift($csvData);
            
//             // Nettoyer les headers (supprimer BOM UTF-8 si présent)
//             $headers = array_map(function($header) {
//                 return trim(str_replace("\xEF\xBB\xBF", '', $header));
//             }, $headers);

//             // Stocker les données en session pour utilisation ultérieure
//             session([
//                 'csv_headers'   => $headers,
//                 'csv_data'      => $csvData,
//                 'csv_filename'  => $file->getClientOriginalName(),
//                 'csv_row_count' => count($csvData)
//             ]);

//             return redirect()->route('import.create')->with([
//                 'success' => 'Fichier CSV importé avec succès!',
//                 'headers' => $headers,
//                 'row_count' => count($csvData)
//             ]);

//         } catch (\Exception $e) {
//             return back()->with('error', 'Erreur lors de l\'import du fichier: ' . $e->getMessage());
//         }
//     }


// }

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MailCsvService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class ImportCsvController extends Controller
{
    protected $service;

    public function __construct(MailCsvService $service)
    {
        $this->service = $service;
    }

    /**
     * Afficher le formulaire d'import CSV
     */
    public function create()
    {
        $csvData = Cache::get('csv_data', []);
        $headers = Cache::get('csv_headers', []);
        $filename = Cache::get('csv_filename', '');
        $rowCount = count($csvData);

        return view('sendmail.import-csv', compact('headers', 'rowCount', 'filename'));
    }

    /**
     * Importer le fichier CSV/XLS/XLSX
     */
    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('csv_file');
            $extension = $file->getClientOriginalExtension();

            if (in_array(strtolower($extension), ['xls', 'xlsx'])) {
                $csvData = Excel::toArray([], $file)[0] ?? [];
            } else {
                $csvData = array_map('str_getcsv', file($file->getRealPath()));
            }

            if (empty($csvData)) {
                return back()->with('error', 'Le fichier est vide ou mal formaté.');
            }

            // Récupération des headers
            $headers = array_shift($csvData);
            $headers = array_map(fn($h) => trim(str_replace("\xEF\xBB\xBF", '', $h)), $headers);

            // Nettoyer les lignes vides
            $csvData = array_filter($csvData, fn($row) => array_filter($row));

            // Sauvegarde dans cache (1h)
            Cache::put('csv_data', $csvData, 3600);
            Cache::put('csv_headers', $headers, 3600);
            Cache::put('csv_filename', $file->getClientOriginalName(), 3600);

            return redirect()->route('import.create')->with([
                'success' => 'Fichier importé avec succès!',
                'headers' => $headers,
                'row_count' => count($csvData)
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }
}
