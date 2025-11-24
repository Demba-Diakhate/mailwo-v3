<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Log;

// class SendMailController extends Controller
// {
//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         $headers = session('csv_headers', []);
//         $csvData = session('csv_data', []);
//         $filename = session('csv_filename', '');
//         $rowCount = session('csv_row_count', 0);
        
//         return view('sendmail.send-mail', compact('headers', 'csvData', 'filename', 'rowCount'));
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         // Validation
//         $validated = $request->validate([
//             'subject' => 'required|string|max:255',
//             'body' => 'required|string',
//             'from_email' => 'required|email',
//             'from_name' => 'required|string|max:255',
//         ]);

//         // Récupérer les données CSV
//         $headers = session('csv_headers', []);
//         $csvData = session('csv_data', []);

//         if (empty($csvData)) {
//             return back()->with('error', 'Aucune donnée CSV disponible. Veuillez d\'abord importer un fichier CSV.');
//         }

//         // Vérifier qu'il y a une colonne email
//         $emailColumnIndex = array_search('email', $headers);
//         if ($emailColumnIndex === false) {
//             return back()->with('error', 'La colonne "email" est introuvable dans votre fichier CSV.');
//         }

//         $sentCount = 0;
//         $failedEmails = [];

//         // Parcourir chaque ligne du CSV
//         foreach ($csvData as $row) {
//             try {
//                 // Récupérer l'email du destinataire
//                 $recipientEmail = $row[$emailColumnIndex] ?? null;
                
//                 if (empty($recipientEmail) || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
//                     continue; // Ignorer les emails invalides
//                 }

//                 // Personnaliser le sujet et le corps
//                 $personalizedSubject = $this->replaceVariables($validated['subject'], $headers, $row);
//                 $personalizedBody = $this->replaceVariables($validated['body'], $headers, $row);

//                 // Envoyer l'email
//                 Mail::raw($personalizedBody, function ($message) use ($recipientEmail, $personalizedSubject, $validated) {
//                     $message->to($recipientEmail)
//                             ->subject($personalizedSubject)
//                             ->from($validated['from_email'], $validated['from_name']);
//                 });

//                 $sentCount++;

//             } catch (\Exception $e) {
//                 Log::error('Erreur envoi email: ' . $e->getMessage());
//                 $failedEmails[] = $recipientEmail ?? 'Email invalide';
//             }
//         }

//         // Retour avec message de succès
//         if ($sentCount > 0) {
//             $message = $sentCount === count($csvData) 
//                 ? 'Tous les emails ont été envoyés avec succès!' 
//                 : "$sentCount email(s) envoyé(s) sur " . count($csvData);
            
//             return redirect()->route('sendMail.create')->with([
//                 'success' => $message,
//                 'sent_count' => $sentCount
//             ]);
//         }

//         return back()->with('error', 'Aucun email n\'a pu être envoyé. Vérifiez vos données CSV.');
//     }

//     /**
//      * Remplacer les variables dynamiques dans le texte
//      */
//     private function replaceVariables(string $text, array $headers, array $row): string
//     {
//         foreach ($headers as $index => $header) {
//             $value = $row[$index] ?? '';
//             // $text = str_replace("{{" . $header . "}}", $value, $text);
//             $text = str_replace("{{{$header}}}", $value, $text);
//         }
        
//         return $text;
//     }
// }

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\MailCsvService;

class SendMailController extends Controller
{
    protected $service;

    public function __construct(MailCsvService $service)
    {
        $this->service = $service;
    }

    /**
     * Afficher le formulaire d'envoi
     */
    public function create()
    {
        $csvData = Cache::get('csv_data', []);
        $headers = Cache::get('csv_headers', []);
        $filename = Cache::get('csv_filename', '');
        $rowCount = count($csvData);

        return view('sendmail.send-mail', compact('headers', 'csvData', 'filename', 'rowCount'));
    }

    /**
     * Envoyer les emails
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'from_email' => 'required|email',
            'from_name' => 'required|string|max:255',
        ]);

        $csvData = Cache::get('csv_data', []);
        $headers = Cache::get('csv_headers', []);

        if (empty($csvData)) {
            return back()->with('error', 'Aucune donnée CSV disponible. Importez un fichier avant.');
        }

        $emailColumnIndex = $this->service->findEmailColumn($headers);
        if ($emailColumnIndex === false) {
            return back()->with('error', 'La colonne "email" est introuvable dans votre fichier CSV.');
        }

        $sentCount = 0;
        $failedEmails = [];

        foreach ($csvData as $row) {
            try {
                $recipientEmail = $row[$emailColumnIndex] ?? null;
                if (empty($recipientEmail) || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                $subject = $this->service->replaceVariables($validated['subject'], $headers, $row);
                $body = $this->service->replaceVariables($validated['body'], $headers, $row);

                Mail::raw($body, function ($message) use ($recipientEmail, $subject, $validated) {
                    $message->to($recipientEmail)
                            ->subject($subject)
                            ->from($validated['from_email'], $validated['from_name']);
                });

                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Erreur envoi email: ' . $e->getMessage());
                $failedEmails[] = $recipientEmail ?? 'Email invalide';
            }
        }

        $message = $sentCount > 0 
            ? "$sentCount email(s) envoyé(s) sur " . count($csvData) 
            : 'Aucun email n\'a pu être envoyé.';

        return redirect()->route('sendMail.create')->with([
            'success' => $message,
            'sent_count' => $sentCount
        ]);
    }
}
