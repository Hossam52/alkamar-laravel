<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Student QR Codes</title>
    <style>
        /* @font-face {
            font-family: 'noto';
            src: url('{{ public_path('fonts/noto.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        } */

        /* Adjust the styles for the page layout */
        .page {
            page-break-after: always;
            text-align: center;
            padding: 10px;
            width: 100%;
            height: 100vh; /* Full viewport height */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
        }

        .qr-code {
            max-width: 100%;
            max-height: 100%;
            width: 100%;
            height: 80%;
        }

        .noto-sans-arabic {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 18px; /* Adjust font size as needed */
        }

        .student {
            font-size: 40px; /* Adjust font size for the student name */
        }
    </style>
</head>
<body>

@foreach($students as $student)
    <div class="page">
        <!-- Use the 'qrcodes' directory and the student ID to load the SVG image -->
        {{-- <img class="qr-code" src="{{ public_path('qrcodes/' .$student->stage_id .'/'.  $student->id . '.svg') }}" style="display: block; margin: 0 auto;"> --}}
        <img class="qr-code" src="{{ public_path('qrcodes/' .$student->stage_id .'/'.  $student->id . '.svg') }}" >
        <p class="noto-sans-arabic student">Code: {{ $student->code }}</p>
        {{-- <p class="noto-sans-arabic student">Name: {{ $student->name }}</p> --}}
    </div>
@endforeach

</body>
</html>
