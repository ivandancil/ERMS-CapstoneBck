<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OCRController extends Controller
{
    // public function processFile(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
    //     ]);

    //     $filePath = $request->file('file')->store('uploads');
    //     $file = storage_path('app/' . $filePath);

    //     // Use Tesseract OCR to extract text
    //     $ocr = new TesseractOCR($file);
    //     $text = $ocr->run();

    //     // Parse the extracted text with regex
    //     $parsedData = $this->parseOCRText($text);

    //     return response()->json([
    //         'parsed_data' => $parsedData
    //     ]);
    // }

    // private function parseOCRText($text)
    // {
    //     $result = [];
    //     // Regex parsing for PDS fields
    //     preg_match('/Name:\s*(.*)/i', $text, $matches);
    //     if (isset($matches[1])) {
    //         $result['name'] = trim($matches[1]);
    //     }

    //     preg_match('/ID:\s*(.*)/i', $text, $matches);
    //     if (isset($matches[1])) {
    //         $result['id'] = trim($matches[1]);
    //     }

    //     preg_match('/Date of Birth:\s*(.*)/i', $text, $matches);
    //     if (isset($matches[1])) {
    //         $result['dob'] = trim($matches[1]);
    //     }

    //     preg_match('/Address:\s*(.*)/i', $text, $matches);
    //     if (isset($matches[1])) {
    //         $result['address'] = trim($matches[1]);
    //     }

    //     // Add more fields as necessary

    //     return $result;
    // }
}
