<?php

namespace App\Services;

class MailCsvService
{
    /**
     * Trouver l'index de la colonne email (insensible à la casse)
     */
    public function findEmailColumn(array $headers)
    {
        $cleanHeaders = array_map(fn($h) => strtolower(trim($h)), $headers);
        return array_search('email', $cleanHeaders);
    }

    /**
     * Remplacer les variables dynamiques dans le texte
     */
    public function replaceVariables(string $text, array $headers, array $row): string
    {
        foreach ($headers as $index => $header) {
            $value = $row[$index] ?? '';
            // Support {{variable}} et @{{variable}}
            $text = str_replace(["{{{$header}}}", "@{{{$header}}}"], $value, $text);
        }
        return $text;
    }
}
