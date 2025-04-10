<?php

namespace App\Http\Controllers\Api;

use TesseractOCR;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OCRController extends Controller
{
    public function processFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $filePath = $request->file('file')->store('uploads');
        $file = storage_path('app/' . $filePath);

        $ocr = new TesseractOCR($file);
        $text = $ocr->run();

        // ✅ Call the parser here
        $parsedData = $this->parseOCRText($text);

        return response()->json([
            'raw_text' => $text,
            'parsed_data' => $parsedData
        ]);
    }

    private function parseOCRText($text)
    {
        $result = [];
        $lines = explode("\n", $text);

        foreach ($lines as $line) {
            if (stripos($line, 'name') !== false) {
                $result['name'] = trim(explode(':', $line)[1] ?? '');
            }
            if (stripos($line, 'id') !== false) {
                $result['id'] = trim(explode(':', $line)[1] ?? '');
            }
            if (stripos($line, 'dob') !== false || stripos($line, 'birth') !== false) {
                $result['dob'] = trim(explode(':', $line)[1] ?? '');
            }
            if (stripos($line, 'address') !== false) {
                $result['address'] = trim(explode(':', $line)[1] ?? '');
            }
            if (stripos($line, 'expiry') !== false) {
                $result['expiry'] = trim(explode(':', $line)[1] ?? '');
            }
        }

        return $result;
    }
}
