<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class PDFController extends Controller
{
    public function generatePDF(Request $request)
    {
        // $students = Student::all();
        // foreach ($students as $student) {
        //     $student->saveQr();
        // }
        $request->validate([
            'student_ids' => 'required|array| exists:students,id',
            'student_ids.*' => 'integer'
        ]);
        // Assuming you have an array of student IDs
        $studentIds = $request->input('student_ids');
        

        // Fetch the student data from the database based on the student IDs
        $students = Student:: whereIn('id', $studentIds)->orderBy('code')->get();

        // Generate the PDF
        $pdf = PDF::loadView('pdf.student_qr_codes', compact('students'));
          // Set font configuration for Arabic
          $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
          $pdf->getDomPDF()->getOptions()->set('isPhpEnabled', true);
          $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);
          $pdf->getDomPDF()->getOptions()->set('fontDir', public_path('fonts/'));
          $pdf->getDomPDF()->getOptions()->set('fontCache', public_path('fonts/'));
          $pdf->getDomPDF()->getOptions()->set('defaultFont', 'noto'); // Replace with your font name
        // Set the paper size and orientation (optional)
        $pdf->setPaper('letter', 'p');


        // Generate the PDF file content
        $pdfContent = $pdf->output();
        // Save the PDF file in the public folder
        $pdfFileName = 'student_qr_codes.pdf';
        $pdfFilePath = public_path($pdfFileName);

        file_put_contents($pdfFilePath, $pdfContent);
        // Return the file path so it can be accessed via API
        return  response()->json(['pdf_url' => asset($pdfFileName)]);
    }
}