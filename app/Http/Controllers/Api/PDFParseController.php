<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Http\Controllers\Controller;


class PDFParseController extends Controller
{
    public function parse(Request $request)
    {
        // Check if the file is uploaded
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    
        // Get the uploaded file
        $file = $request->file('file');
    
        // Parse the PDF file
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getRealPath());
        $text = $pdf->getText(); // Extract text from the PDF
    
        // Extract data using regex (customize as per your PDF format)
        $name = $this->matchRegex('/Name\s*[:\s]+([A-Za-z\s]+)/', $text);
        $email = $this->matchRegex('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text);
        $position = $this->matchRegex('/Position\s*[:\s]+([A-Za-z\s]+)/', $text);
        $dob = $this->matchRegex('/Date of Birth\s*[:\s]+(\d{4}-\d{2}-\d{2})/', $text);
        $address = $this->matchRegex('/Address\s*[:\s]+([^\n]+)/', $text);
    
        // If any of the fields are empty, return a "Record not found" message
        if (empty($name) || empty($email) || empty($position) || empty($dob) || empty($address)) {
            return response()->json(['message' => 'Record not found.'], 404);
        }
    
        // Return extracted data
        return response()->json([
            'name' => $name,
            'email' => $email,
            'position' => $position,
            'dob' => $dob,
            'address' => $address,
        ]);
    }
    

    // Helper function to match text using regex
    private function matchRegex($pattern, $text)
    {
        preg_match($pattern, $text, $matches);
        return $matches[1] ?? ''; // Return matched data or empty string if not found
    }
}
