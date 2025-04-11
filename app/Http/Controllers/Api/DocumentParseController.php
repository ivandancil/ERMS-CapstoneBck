<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DocumentParseController extends Controller
{
    public function parse(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getPathname());
        $text = $pdf->getText();

        // Map of labels to field keys
        $fields = [
            'cs_id_no' => 'CS ID No',
            'surname' => 'SURNAME',
            'first_name' => 'FIRST NAME',
            'middle_name' => 'MIDDLE NAME',
            'name_ext' => 'NAME EXTENSION',
            'date_of_birth' => 'DATE OF BIRTH',
            'place_of_birth' => 'PLACE OF BIRTH',
            'sex' => 'SEX',
            'civil_status' => 'CIVIL STATUS',
            'height' => 'HEIGHT',
            'weight' => 'WEIGHT',
            'blood_type' => 'BLOOD TYPE',
            'gsis_id' => 'GSIS ID NO',
            'pagibig_id' => 'PAG-IBIG ID NO',
            'philhealth_id' => 'PHILHEALTH NO',
            'sss_id' => 'SSS NO',
            'tin_id' => 'TIN NO',
            'agency_employee_no' => 'AGENCY EMPLOYEE NO',
            'residential_address' => 'RESIDENTIAL ADDRESS',
            'permanent_address' => 'PERMANENT ADDRESS',
            'telephone' => 'TELEPHONE NO',
            'mobile' => 'MOBILE NO',
            'email' => 'E-MAIL ADDRESS',
            'citizenship' => 'CITIZENSHIP',
        ];

        $output = [];

        foreach ($fields as $key => $label) {
            $output[$key] = $this->extractField($text, $label);
        }

        return response()->json($output);
    }

    private function extractField($text, $label)
    {
        // Match the field's label and grab the value after it
        // The pattern now accounts for different separators like ':' or whitespace
        $pattern = '/' . preg_quote($label, '/') . '[\s:]*([^\n]+)/i';

        if (preg_match($pattern, $text, $matches)) {
            // Trim and return the matched value
            return trim($matches[1]);
        }

        // Return an empty string if the field is not found
        Log::warning("Field not found: " . $label);
        return 'Field not found'; // You can change this to something else if needed
    }
}
