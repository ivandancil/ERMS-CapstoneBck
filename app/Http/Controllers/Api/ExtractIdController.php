<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ExtractIdController extends Controller
{
    // Handle file upload and ID extraction
    public function extract(Request $request)
    {
        // Validate the file input
        $request->validate([
            'file' => 'required|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        // Store the file in the 'uploads' folder
        $file = $request->file('file');
        $filePath = $file->storeAs('uploads', time() . '.' . $file->getClientOriginalExtension());

        // Perform OCR on the uploaded file
        try {
            $ocrText = (new TesseractOCR(storage_path('app/' . $filePath)))->run();

            // You can now process the extracted text and map it to your fields
            // Example simulated extraction (replace with actual parsing logic):
            $data = [
                'last_name' => 'Doe', // Extract from OCR text
                'given_names' => 'John Michael',
                'middle_name' => 'Smith',
                'birthdate' => '1990-01-01',
                'address' => '1234 Elm Street',
            ];

            // Return the extracted data
            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => 'OCR failed: ' . $e->getMessage()], 500);
        }
    }
}
