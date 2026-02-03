<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DocumentValidationService
{
    /**
     * The OCR Space API Key.
     * Use 'helloworld' for simple testing or get a free key from https://ocr.space/ocrapi
     */
    protected $apiKey = 'helloworld';

    /**
     * Validate a document based on its type and content keywords.
     *
     * @param string $filePath Absolute path to the image
     * @param string $type cr, or, license, id
     * @return array ['success' => bool, 'message' => string]
     */
    public function validate($filePath, $type)
    {
        try {
            // 1. Perform OCR
            $response = Http::attach(
                'file', file_get_contents($filePath), basename($filePath)
            )->post('https://api.ocr.space/parse/image', [
                'apikey' => $this->apiKey,
                'language' => 'eng',
                'isOverlayRequired' => false,
            ]);

            if (!$response->successful()) {
                Log::error('OCR Service Failed', ['status' => $response->status()]);
                return ['success' => false, 'message' => 'Validation service currently unavailable.'];
            }

            $data = $response->json();
            $text = strtoupper($data['ParsedResults'][0]['ParsedText'] ?? '');

            if (empty($text)) {
                return ['success' => false, 'message' => 'No text detected in the image. Please ensure the document is clear and well-lit.'];
            }

            // 2. Keyword Matching based on document type
            $keywords = $this->getKeywordsForType($type);
            $matchCount = 0;
            $matchedKeywords = [];

            foreach ($keywords as $keyword) {
                if (str_contains($text, strtoupper($keyword))) {
                    $matchCount++;
                    $matchedKeywords[] = $keyword;
                }
            }

            // 3. Logic: If no keywords match, it's likely the wrong document
            if ($matchCount === 0) {
                return [
                    'success' => false,
                    'message' => "The uploaded file does not appear to be a valid " . strtoupper($type) . ". Please upload a clear image of the required document."
                ];
            }

            return [
                'success' => true,
                'message' => 'Document validated successfully.',
                'detected_text_preview' => substr($text, 0, 100)
            ];

        } catch (\Exception $e) {
            Log::error('Document Validation Error: ' . $e->getMessage());
            return ['success' => true, 'message' => 'Bypassing validation due to connection error.']; // Graceful fail
        }
    }

    protected function getKeywordsForType($type)
    {
        return match ($type) {
            'cr_file' => ['CERTIFICATE', 'REGISTRATION', 'CHASSIS', 'ENGINE', 'PHILIPPINES'],
            'or_file' => ['OFFICIAL', 'RECEIPT', 'PAYMENT', 'TOTAL', 'LTO'],
            'license_file' => ['DRIVER', 'LICENSE', 'RESTRICTION', 'PHILIPPINES', 'CARD'],
            'com_file', 'student_id_file', 'employee_id_file' => ['UNIVERSITY', 'EVSU', 'STUDENT', 'EASTERN', 'VISAYAS'],
            default => ['PHILIPPINES'],
        };
    }
}
