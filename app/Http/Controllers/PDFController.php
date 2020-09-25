<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Artista;

class PDFController extends Controller
{
    public function index(){
        //C rear el objeto pdf
        $pdf = new Fpdf();
        //Añadir pagina
        $pdf->AddPage();
        //Establecer una cordenada a pintar
        $pdf->SetXY(10,10);
        //Establecer tipo de letra
        $pdf->SetFont('Arial', 'B', 14);
        //Establecer color de las celdas
        $pdf->SetDrawColor(213, 108, 86);
        //Establecer color de letra
        $pdf->SetTextColor(213, 108, 86);
        
        //Establecer un contenido
        $pdf->Cell(100, 10, "Nombre artista", 1, 0, 'C');
        $pdf->Cell(50, 10, "Numero Albumes", 1, 1, 'C');

        //Recorrer el arrgelo de artistas para mostrar
        //Artista y numero de discos por artista
        $artistas = Artista::all();
        $pdf->SetFont('Arial', 'I', 11);
        foreach($artistas as $a){
            
            $pdf->Cell(100, 10, substr(utf8_decode($a->Name), 0, 50), 1, 0, 'L');
            $pdf->Cell(50, 10, $a->albumes()->count(), 1, 1, 'C');

        }
        //Sacar el pdf al navegador


        //Utilizar objeto response
        $response = response($pdf->Output());
        //Definir el tipo mime
        $response->header("Content-Type" , 'application/pdf');
        //Retornar respuesta al navegador
        return $response;
    }
}
