<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DocumentParseController extends Controller
{
    public function parseDocument(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048',
        ]);
    
        $file = $request->file('file');
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($file->getPathname());
        $text = $pdf->getText();
    
        // Log the extracted raw text for debugging
        Log::info('Extracted text from PDF: ' . $text);
    
        $data = [
            'name' => $this->extractValue($text, 'Name'),
            'email' => $this->extractValue($text, 'Email'),
            'position' => $this->extractValue($text, 'Position'),
            'dob' => $this->extractValue($text, 'Date of Birth'),
            'address' => $this->extractValue($text, 'Address'),
        ];
    
        if (empty(array_filter($data))) {
            return response()->json(['message' => 'Record not found.'], 404);
        }
    
        return response()->json($data);
    }
    

    private function extractValue($text, $field)
    {
        // This regex should work with varying spaces and punctuation after field name.
        $pattern = "/$field\s*[:\-\.]*\s*(.+)/i"; // Allow for varying space and punctuation
        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
    

}
