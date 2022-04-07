<?php

namespace App\Http\Controllers;

use Codedge\Fpdf\Fpdf\Fpdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class StudentController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {         
        return view('student.index')
        ->with('students', $this->getStudents());
    }
    
    public function getStudents(){
        
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => env('students_url'),
        ));
        
        $students = json_decode(curl_exec($ch));
        curl_close($ch); 
        
        return $students;
    }
    
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $students = $this->getStudents();
        $fpdf = new Fpdf;
        $fpdf->AddPage("L");
        $fpdf->SetAutoPageBreak(false);
        $fpdf->Image(env('template_image'), 0, 0, $fpdf->GetPageWidth(), $fpdf->GetPageHeight());
        $fpdf->SetFont('Arial', 'B', 12);
        $w = $fpdf->GetPageWidth()/2+150;
        $h = $fpdf->GetPageHeight()/2-10;

        $qr_location = $this->makeQr($id);
        foreach ( $students as $element ) {
            if ( $id == $element->numero ) {  
                $fpdf->Cell($w, $h, "Se hace constar que:", 0, 0, 'C');
                $fpdf->Ln(10);
                $genre = $element->genero=='f' ? 'DÑA.' : 'DON.';
                $fpdf->Cell($w, $h, utf8_decode("{$genre} {$element->nombre}"), 0, 0, 'C');
                $fpdf->Ln(20);
                $fpdf->Cell($w, $h, utf8_decode("Ha sido distinguida por la Comisión Académica del Titulo Propio"), 0, 0, 'C');
                $fpdf->Ln(5);
                $fpdf->Cell($w, $h, utf8_decode($element->master), 0, 0, 'C');
                $fpdf->Ln(5);
                $fpdf->Cell($w, $h, utf8_decode("con el presente:"), 0, 0, 'C');
                $fpdf->Ln(50);
                $fpdf->Cell($w, $h, utf8_decode("Correspondiente a la edición {$element->edicion}"), 0, 0, 'C');
                $fpdf->Ln(10);
                $fpdf->Cell($w, $h, utf8_decode("Salamanca, {$element->fechamencion}"), 0, 0, 'C');
                $fpdf->Ln(15);
                $fpdf->Cell($w, $h, utf8_decode($element->nombredirector), 0, 0, 'C');
                $fpdf->Ln(5);
                $fpdf->Cell($w, $h, utf8_decode("Director del Master"), 0, 0, 'C');
                break;
            }
        }
        $fpdf->Image($qr_location, $fpdf->GetPageWidth()-50, $fpdf->GetPageHeight()-50 , 40, 40);
        $fpdf->Output();
        exit;   
    }

    public function makeQr($id){

            $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data(env("qr_url") . "?id={$id}")
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->build();

            $location = public_path() ."/qrs/qrcode_{$id}.png";
            $result->saveToFile($location);

            return $location;
    }
}
