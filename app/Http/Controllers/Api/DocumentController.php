<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\File; // Add this to import the File model

class DocumentController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,pdf,docx|max:5120', // 5MB max size
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filename = 'pds_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/documents', $filename);

        // Save file metadata to the database
        $fileRecord = File::create([
            'file_name' => $filename,
            'original_name' => $originalName,
            'file_path' => Storage::url('documents/' . $filename), // Generated public URL
            'uploaded_at' => now(),
        ]);
        

        return response()->json([
            'message' => 'Personal Data Sheet uploaded successfully.',
            'file_path' => $fileRecord->file_path,
        ]);
    }

    public function index()
    {
        // Fetch files from the database
        $files = File::all();

        return response()->json($files);
    }

     // Delete function
     public function delete($id)
     {
         // Find the file record in the database
         $fileRecord = File::find($id);
 
         if (!$fileRecord) {
             return response()->json(['message' => 'File not found.'], 404);
         }
 
         // Delete the file from storage
         if (Storage::exists('public/documents/' . $fileRecord->file_name)) {
             Storage::delete('public/documents/' . $fileRecord->file_name);
         }
 
         // Delete the file record from the database
         $fileRecord->delete();
 
         return response()->json(['message' => 'File deleted successfully.']);
     }
}
