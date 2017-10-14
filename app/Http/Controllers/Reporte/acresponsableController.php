<?php

namespace matriz\Http\Controllers\Reporte;
//*******agregar esta linea******//
use matriz\Models\Ac\tab_ac_responsable;
use View;
use Validator;
use Input;
use Response;
use DB;
use Session;
use TCPDF;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class acresponsableController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function responsable()
  {
    $pdf = new TCPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
    $pdf->SetCreator('Sistema Nueva Etapa, Yoser Perez');
    $pdf->SetAuthor('Yoser Perez');
    $pdf->SetTitle('Ley de Presupuesto');
    $pdf->SetSubject('Ley de Presupuesto');
    $pdf->SetKeywords('Ley de Presupuesto, PDF, Zulia, SPE, '.Session::get("ejercicio").'');
    $pdf->SetMargins(10,10,10);
    $pdf->SetTopMargin(10);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->AddPage();

    //Cierre de Reporte
    $pdf->lastPage();
    $pdf->output('LISTADO_RESPONSABLES_'.Input::get('id_ejecutor').'_'.Session::get("ejercicio").'_'.date("H:i:s").'.pdf', 'D');
  }
}
