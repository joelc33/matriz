<?php

namespace matriz\Http\Controllers\Reporte;
//*******agregar esta linea******//
use matriz\Models\Proyecto\tab_meta_financiera;
use View;
use Validator;
use Input;
use Response;
use DB;
use Session;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Cell_DataType;
//*******************************//
use Illuminate\Http\Request;

use matriz\Http\Requests;
use matriz\Http\Controllers\Controller;

class proyectoController extends Controller
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
  public function lista()
  {
    $data = json_encode(array("id_ejecutor" => Session::get('ejecutor')));
    return View::make('reporte.poa.proyecto')->with('data',$data);
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function ubica()
  {
    $data = json_encode(array("id_ejecutor" => Session::get('ejecutor')));
    return View::make('reporte.poa.ubicaproyecto')->with('data',$data);
  }

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function todoExportar()
  {

    DB::beginTransaction();

    try {

    //Query
    $consulta1 = tab_meta_financiera::join('mantenimiento.tab_fuente_financiamiento as t01','t01.id','=','public.t68_metas_detalle.co_fuente')
    ->join('public.t67_metas as t02','t02.co_metas','=','public.t68_metas_detalle.co_metas')
    ->join('public.t39_proyecto_acc_espec as t03','t03.co_proyecto_acc_espec','=','t02.co_proyecto_acc_espec')
    ->join('mantenimiento.tab_ejecutores as t04','t04.id','=','t03.co_ejecutores')
    ->join('public.t26_proyectos as t05','t03.id_proyecto','=','t05.id_proyecto')
    ->join('mantenimiento.tab_ejecutores as t06','t06.id_ejecutor','=','t05.id_ejecutor')
    ->select( 'mo_presupuesto', 'co_partida', 'de_fuente_financiamiento', 'nb_meta',
    't04.id_ejecutor as ej_ae', 't03.descripcion as de_ae', 't05.nombre as nb_proyecto',
    'clase_sector', 't05.id_ejecutor', 't06.tx_ejecutor as nb_ejecutor', 't05.id_ejercicio',
    DB::raw('tx_categoria_proyecto( t05.id_proyecto, t03.tx_codigo, t05.id_ejercicio) as categoria'))
    ->where('public.t68_metas_detalle.edo_reg', '=', true )
    ->where('t02.edo_reg', '=', true )
    ->where('t05.id_ejercicio', '=', Session::get('ejercicio') )
    ->where('t05.edo_reg', '=', true )
    ->orderBy('t05.id_ejecutor','ASC')
    ->get();

    $i = 0;
    $acumulado = 0;

    // Instantiate a new PHPExcel object
    $objPHPExcel = new PHPExcel();
    // Set properties
    $objPHPExcel->getProperties()->setCreator("Yoser Perez");
    $objPHPExcel->getProperties()->setLastModifiedBy("SPE");
    $objPHPExcel->getProperties()->setTitle("Listado POA Proyectos");
    $objPHPExcel->getProperties()->setSubject("Reporte");
    $objPHPExcel->getProperties()->setDescription("Reporte para documento de Office 2007 XLSX.");
    // Set the active Excel worksheet to sheet 0
    $objPHPExcel->setActiveSheetIndex(0);
    // Rename sheet
    //$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(15);
    //$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(15);
    //$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(30);
    $objPHPExcel->getActiveSheet()->setTitle('POA_PROYECTO');
    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray(
        array(
          'font'    => array(
            'bold'      => true
          ),
          'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          ),
          'borders' => array(
            'top'     => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
            )
          ),
          'fill' => array(
            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
              'rotation'   => 90,
            'startcolor' => array(
              'argb' => 'FFA0A0A0'
            ),
            'endcolor'   => array(
              'argb' => 'FFFFFFFF'
            )
          )
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray(
        array(
          'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
          ),
          'borders' => array(
            'left'     => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
            )
          )
        )
    );

    $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray(
        array(
          'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
          )
        )
    );

    $objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray(
        array(
          'borders' => array(
            'right'     => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
            )
          )
        )
    );
    // Initialise the Excel row number
    $rowCount = 2;
    // Iterate through each result from the SQL query in turn
    // We fetch each database result row into $row in turn

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Ejercicio')
    ->setCellValue('B1', 'Unidad Ejecutora')
    ->setCellValue('C1', 'Sector')
    ->setCellValue('D1', 'Proyecto')
    ->setCellValue('E1', 'Acción específica')
    ->setCellValue('F1', 'Código del ejecutor de la acción específica')
    ->setCellValue('G1', 'Actividad')
    ->setCellValue('H1', 'Presupuesto')
    ->setCellValue('I1', 'Categoría')
    ->setCellValue('J1', 'Partida')
    ->setCellValue('K1', 'Fuente de Financiamiento');

    // Make bold cells
    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold(true);


    foreach ($consulta1 as $key => $value) {
        // Set cell An to the "name" column from the database (assuming you have a column called name)
        $i++;
        $acumulado = $acumulado+$value->mo_ingreso;

        // Set thin black border outline around column
        $styleThinBlackBorderOutline = array(
          'borders' => array(
            'outline' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN,
              'color' => array('argb' => 'FF000000'),
            ),
          ),
        );
        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleThinBlackBorderOutline);

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $value->id_ejercicio);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $value->id_ejecutor.'-'.$value->nb_ejecutor);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $value->clase_sector);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $value->nb_proyecto);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $value->de_ae);
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $value->ej_ae);
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $value->nb_meta);
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $value->mo_presupuesto);
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $value->categoria);
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $value->co_partida);
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $value->de_fuente_financiamiento);

        $rowCount++;

    }


    // Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    // We'll be outputting an excel file
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // It will be called file.xls
    header('Content-Disposition: attachment; filename="poa_proyecto_'.date("H:i:s").'.xlsx"');
    $objWriter->save('php://output');

    DB::commit();

  }catch (\Illuminate\Database\QueryException $e)
  {
    DB::rollback();
    return Response::json(array(
      'success' => false,
      'msg' => array('ERROR ('.$e->getCode().'):'=> $e->getMessage())
    ));
  }

  }

}
