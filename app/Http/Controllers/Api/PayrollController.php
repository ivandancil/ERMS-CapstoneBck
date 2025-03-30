<?php

namespace App\Http\Controllers\Api;

use App\Models\Payroll;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage; // Import Storage

class PayrollController extends Controller
{
    public function uploadPayroll(Request $request)
    {
        $request->validate([
            'payrollFile' => 'required|file|mimes:csv,xlsx|max:2048',
        ]);

        if ($request->hasFile('payrollFile')) {
            $file = $request->file('payrollFile');
            $path = $file->store('payrolls', 'public'); // Stores file in storage/app/public/payrolls
            $filename = $file->getClientOriginalName(); // Get original filename

            // Save to database
            $payroll = new Payroll();
            $payroll->file_path = $path;
            $payroll->file_name = $filename; // Save filename
            $payroll->status = 'Pending'; // Default status
            $payroll->save();

            return response()->json([
                'message' => 'File uploaded successfully!',
                'payroll' => $payroll, // Return payroll data
            ], 200);
        }

        return response()->json(['message' => 'File upload failed!'], 400);
    }

    public function getPendingPayrolls()
    {
        $payrolls = Payroll::where('status', 'Pending')->get(); // Fetch pending payrolls

        return response()->json($payrolls);
    }

    public function updatePayrollStatus(Request $request, $id)
    {
        $payroll = Payroll::find($id);
        if (!$payroll) {
            return response()->json(['message' => 'Payroll not found'], 404);
        }

        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $payroll->status = $request->status;
        $payroll->save();

        return response()->json(['message' => 'Payroll status updated successfully!', 'payroll' => $payroll]);
    }

 
public function viewPayroll($id) {
    $payroll = Payroll::find($id);

    if (!$payroll) {
        return response()->json(["error" => "Payroll record not found."], 404);
    }

    // Construct the full file URL
    $fileUrl = asset("storage/" . $payroll->file_path);

    return response()->json([
        "file_name" => $payroll->file_name,
        "file_url" => $fileUrl
    ]);
}

}
