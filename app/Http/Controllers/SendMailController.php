<?php

namespace App\Http\Controllers;

use App\Jobs\SendFileEmail;
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
        $csvData  = Cache::get('csv_data', []);
        $headers  = Cache::get('csv_headers', []);
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
            'subject'    => 'required|string|max:255',
            'body'       => 'required|string',
            'from_email' => 'required|email',
            'from_name'  => 'required|string|max:255',
        ]);

        // Récupération des données importées
        $csvData = Cache::get('csv_data', []);
        $headers = Cache::get('csv_headers', []);

        Cache::put('mail_progress', [
    'total' => count($csvData),
    'sent'  => 0
]);


        if (empty($csvData)) {
            return back()->with('error', 'Aucune donnée CSV disponible. Veuillez importer un fichier avant.');
        }

        // Vérifier la colonne email
        $emailColumnIndex = $this->service->findEmailColumn($headers);
        if ($emailColumnIndex === false) {
            return back()->with('error', 'La colonne "email" est introuvable dans votre fichier.');
        }

        $sentCount = 0;
        $failedEmails = [];

        foreach ($csvData as $row) {
            try {
                $recipientEmail = $row[$emailColumnIndex] ?? null;

                if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                    $failedEmails[] = $recipientEmail;
                    continue;
                }

                // Remplacement des variables dans le sujet et le corps
                $subject = $this->service->replaceVariables($validated['subject'], $headers, $row);
                $body    = $this->service->replaceVariables($validated['body'], $headers, $row);

                // Envoi email Gmail par queue(job)
                // SendFileEmail::dispatch(
                //     $recipientEmail,
                //     $subject,
                //     $body,
                //     $validated['from_email'],
                //     $validated['from_name']
                // );
                
                // Envoi email synchrones
                Mail::html(
                    view('emails.template', [
                        'subject' => $subject,
                        'body' => $body
                    ])->render(),
                    function ($message) use ($recipientEmail, $validated, $subject) {
                        $message->to($recipientEmail)
                                ->from($validated['from_email'], $validated['from_name'])
                                ->subject($subject);
                    }
                );

                $sentCount++;

                // Mise à jour progression
$progress = Cache::get('mail_progress');
$progress['sent']++;
Cache::put('mail_progress', $progress);


            } catch (\Exception $e) {
                Log::error('Erreur envoi email vers ' . $recipientEmail . ' : ' . $e->getMessage());
                $failedEmails[] = $recipientEmail;
            }
        }

        $message = $sentCount > 0 
            ? "$sentCount email(s) envoyé(s) sur " . count($csvData)
            : "Aucun email n'a pu être envoyé.";

        return redirect()->route('sendMail.create')->with([
            'success' => $message,
            'sent_count' => $sentCount,
            'failed_emails' => $failedEmails,
        ]);
    }
}

