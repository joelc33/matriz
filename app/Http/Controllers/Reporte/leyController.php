<?php

namespace matriz\Http\Controllers\Reporte;
//*******agregar esta linea******//
use View;
use Input;
use Response;
use DB;
use Auth;
use TCPDF;
use Crypt;
use File;
use Blade;
use Session;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;


class leyController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function libro()
    {
      $pdf = new TCPDF("P", PDF_UNIT, 'Letter', true, 'UTF-8', false);
      $pdf->SetCreator('Sistema Nueva Etapa, Yoser Perez');
      $pdf->SetAuthor('Yoser Perez');
      $pdf->SetTitle('Ley de Presupuesto');
      $pdf->SetSubject('Ley de Presupuesto');
      $pdf->SetKeywords('Ley de Presupuesto, PDF, Zulia, SPE, '.Session::get("ejercicio").'');
      $pdf->SetMargins(10,10,10);
      $pdf->SetTopMargin(32);
      $pdf->SetPrintHeader(true);
      $pdf->SetPrintFooter(true);
      // set auto page breaks
      $pdf->SetAutoPageBreak(TRUE, 15);
      $pdf->AddPage();
      //Cuerpo de la planilla
      $pdf->lastPage();
      $pdf->output('LEY_DE_PRESUPUESTO_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
    }
}
