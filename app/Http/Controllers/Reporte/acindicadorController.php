<?php

namespace matriz\Http\Controllers\Reporte;

use matriz\Models\AcSegto\tab_indicadores;
use matriz\Models\Mantenimiento\tab_lapso;
use matriz\Models\Mantenimiento\tab_ejecutores;
use matriz\Models\Mantenimiento\tab_tipo_indicador;
use matriz\Models\Mantenimiento\tab_sub_tipo_indicador;
use matriz\Models\Mantenimiento\tab_municipio_detalle;
use matriz\Models\Mantenimiento\tab_parroquia_detalle;
use matriz\Models\Mantenimiento\tab_comuna;
use View;
use Input;
use Response;
use DB;
use Session;
use PHPExcel;
use PHPExcel_Settings;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Cell_DataType;
use TCPDF;
use matriz\Http\Controllers\Controller;

class acindicadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Formulario de consulta y vista previa de indicadores de gestión.
     *
     * @param int|null $id_lapso
     * @return \Illuminate\Http\Response
     */
    public function index($id_lapso = null)
    {
        $rol_planificador = array(3, 8);
        $es_planificador = in_array(Session::get('rol'), $rol_planificador);

        $data = json_encode(array(
            'id_ejecutor'       => $es_planificador ? Session::get('ejecutor') : '',
            'id_tab_ejecutores' => $es_planificador ? Session::get('id_tab_ejecutores') : '',
            'id_tab_lapso'      => ($id_lapso !== null && $id_lapso !== '') ? (int) $id_lapso : '',
            'ejercicio'         => Session::get('ejercicio')
        ));

        return View::make('reporte.seguimiento.indicadores')
            ->with('data', $data)
            ->with('ejercicio', Session::get('ejercicio'));
    }

    /**
     * Consulta paginada para vista previa en el GridPanel de ExtJS.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeLista()
    {
        try {
            $start = (int) Input::get('start', 0);
            $limit = (int) Input::get('limit', 25);
            if ($limit <= 0 || $limit > 200) {
                $limit = 25;
            }

            $filtros = $this->extraerFiltros();
            $orden   = Input::get('orden', 'linea');

            $query = $this->construirConsulta($filtros);

            $total = $query->count();

            $this->aplicarOrden($query, $orden);

            $rows = $query->skip($start)->take($limit)->get()->toArray();

            return Response::json(array(
                'success' => 'true',
                'total'   => $total,
                'data'    => $rows
            ), 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return Response::json(array(
                'success' => false,
                'message' => utf8_encode($e->getMessage())
            ), 500);
        }
    }

    /**
     * Exporta Excel: REPORTE DE GESTIÓN POR LÍNEA DE ACCIÓN.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function exportarLineaAccion()
    {
        return $this->generarDescargaExcel('linea');
    }

    /**
     * Exporta Excel: REPORTE DE GESTIÓN POR EJECUTOR.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function exportarEjecutor()
    {
        return $this->generarDescargaExcel('ejecutor');
    }

    /**
     * Exporta PDF infográfico: REPORTE DE GESTIÓN POR LÍNEA DE ACCIÓN.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function exportarPdfLineaAccion()
    {
        try {
            $filtros = $this->extraerFiltros();
            $query   = $this->construirConsulta($filtros);
            $this->aplicarOrden($query, 'linea');
            $registros = $query->get();

            $pdfBinary = $this->construirPdfLineaAccion($registros, $filtros);
            $filename  = 'reporte_linea_accion_' . date('Ymd_His') . '.pdf';

            return Response::make($pdfBinary, 200, array(
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control'       => 'max-age=0',
                'Pragma'              => 'public'
            ));
        } catch (\Illuminate\Database\QueryException $e) {
            return Response::json(array(
                'success' => false,
                'msg'     => array('ERROR (' . $e->getCode() . '):' => $e->getMessage())
            ), 500);
        }
    }

    /**
     * Exporta PDF infográfico: REPORTE DE GESTIÓN POR EJECUTOR.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function exportarPdfEjecutor()
    {
        try {
            $filtros = $this->extraerFiltros();
            $query   = $this->construirConsulta($filtros);
            $this->aplicarOrden($query, 'ejecutor');
            $registros = $query->get();

            $pdfBinary = $this->construirPdfEjecutor($registros, $filtros);
            $filename  = 'reporte_por_ejecutor_' . date('Ymd_His') . '.pdf';

            return Response::make($pdfBinary, 200, array(
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control'       => 'max-age=0',
                'Pragma'              => 'public'
            ));
        } catch (\Illuminate\Database\QueryException $e) {
            return Response::json(array(
                'success' => false,
                'msg'     => array('ERROR (' . $e->getCode() . '):' => $e->getMessage())
            ), 500);
        }
    }

    /**
     * Extrae y sanitiza los filtros enviados desde el formulario.
     * Los roles planificadores locales (3, 8) siempre se restringen a su ejecutor.
     *
     * @return array
     */
    protected function extraerFiltros()
    {
        $rol_planificador = array(3, 8);
        $es_planificador  = in_array(Session::get('rol'), $rol_planificador);

        $id_tab_ejecutores = Input::get('id_tab_ejecutores');
        $id_ejecutor       = Input::get('id_ejecutor');

        if ($es_planificador) {
            $id_tab_ejecutores = Session::get('id_tab_ejecutores');
            $id_ejecutor       = Session::get('ejecutor');
        }

        return array(
            'ejercicio'                => Session::get('ejercicio'),
            'id_tab_lapso'             => Input::get('id_tab_lapso'),
            'id_tab_ejecutores'        => $id_tab_ejecutores,
            'id_ejecutor'              => $id_ejecutor,
            'id_tab_tipo_indicador'    => Input::get('id_tab_tipo_indicador'),
            'id_tab_sub_tipo_indicador'=> Input::get('id_tab_sub_tipo_indicador'),
            'id_tab_municipio_detalle' => Input::get('id_tab_municipio_detalle'),
            'id_tab_parroquia_detalle' => Input::get('id_tab_parroquia_detalle'),
            'id_tab_comuna'            => Input::get('id_tab_comuna'),
            'texto'                    => trim((string) Input::get('texto', ''))
        );
    }

    /**
     * Construye la consulta base sobre ac_seguimiento.tab_indicadores y sus catálogos.
     *
     * Ruta relacional:
     *   ac_seguimiento.tab_indicadores (id_tab_meta_fisica)
     *     -> ac_seguimiento.tab_meta_fisica (id_tab_ac_ae)
     *     -> ac_seguimiento.tab_ac_ae (id_tab_ejecutores, id_tab_ac)
     *     -> ac_seguimiento.tab_ac (id_tab_lapso, id_tab_ejercicio_fiscal)
     *
     * @param array $filtros
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function construirConsulta(array $filtros)
    {
        $query = tab_indicadores::select(
            'ac_seguimiento.tab_indicadores.id',
            'ac_seguimiento.tab_indicadores.id_tab_tipo_indicador',
            'ac_seguimiento.tab_indicadores.id_tab_sub_tipo_indicador',
            'ac.id_tab_lapso',
            'ae.id_tab_ejecutores',
            DB::raw("trim(coalesce(ti.de_tipo_indicador, '')) as linea_accion"),
            DB::raw("trim(coalesce(sti.de_sub_tipo_indicador, '')) as tipo_indicador"),
            DB::raw("trim(l.de_lapso) || ' ' || ac.id_tab_ejercicio_fiscal::text as periodo"),
            DB::raw("trim(coalesce(um.de_unidad_medida, '')) as unidad_medida"),
            'ac_seguimiento.tab_indicadores.nu_cantidad as cantidad',
            DB::raw("trim(coalesce(ac_seguimiento.tab_indicadores.de_indicador, '')) as descripcion"),
            DB::raw("trim(coalesce(mun.de_municipio, '')) as municipio"),
            DB::raw("trim(coalesce(parr.de_parroquia, '')) as parroquia"),
            DB::raw("trim(coalesce(com.de_comuna, '')) as comuna"),
            DB::raw("trim(ej.id_ejecutor) as codigo_ejecutor"),
            DB::raw("trim(coalesce(ej.tx_ejecutor, '')) as ejecutor")
        )
            ->join('ac_seguimiento.tab_meta_fisica as mf', 'mf.id', '=', 'ac_seguimiento.tab_indicadores.id_tab_meta_fisica')
            ->join('ac_seguimiento.tab_ac_ae as ae', 'ae.id', '=', 'mf.id_tab_ac_ae')
            ->join('ac_seguimiento.tab_ac as ac', 'ac.id', '=', 'ae.id_tab_ac')
            ->join('mantenimiento.tab_lapso as l', 'l.id', '=', 'ac.id_tab_lapso')
            ->join('mantenimiento.tab_ejecutores as ej', 'ej.id', '=', 'ae.id_tab_ejecutores')
            ->leftJoin('mantenimiento.tab_tipo_indicador as ti', 'ti.id', '=', 'ac_seguimiento.tab_indicadores.id_tab_tipo_indicador')
            ->leftJoin('mantenimiento.tab_sub_tipo_indicador as sti', 'sti.id', '=', 'ac_seguimiento.tab_indicadores.id_tab_sub_tipo_indicador')
            ->leftJoin('mantenimiento.tab_unidad_medida as um', 'um.id', '=', 'ac_seguimiento.tab_indicadores.id_tab_unidad_medida')
            ->leftJoin('mantenimiento.tab_municipio_detalle as mun', 'mun.id', '=', 'ac_seguimiento.tab_indicadores.id_tab_municipio_detalle')
            ->leftJoin('mantenimiento.tab_parroquia_detalle as parr', 'parr.id', '=', 'ac_seguimiento.tab_indicadores.id_tab_parroquia_detalle')
            ->leftJoin('mantenimiento.tab_comuna as com', 'com.id', '=', 'ac_seguimiento.tab_indicadores.id_tab_comuna')
            ->where('ac.in_activo', '=', true)
            ->where('ae.in_activo', '=', true)
            ->where('mf.in_activo', '=', true);

        if (!empty($filtros['ejercicio'])) {
            $query->where('ac.id_tab_ejercicio_fiscal', '=', $filtros['ejercicio']);
        }
        if (!empty($filtros['id_tab_lapso'])) {
            $query->where('ac.id_tab_lapso', '=', (int) $filtros['id_tab_lapso']);
        }
        if (!empty($filtros['id_tab_ejecutores'])) {
            $query->where('ae.id_tab_ejecutores', '=', (int) $filtros['id_tab_ejecutores']);
        } elseif (!empty($filtros['id_ejecutor'])) {
            $query->where('ej.id_ejecutor', '=', trim($filtros['id_ejecutor']));
        }
        if (!empty($filtros['id_tab_tipo_indicador'])) {
            $query->where('ac_seguimiento.tab_indicadores.id_tab_tipo_indicador', '=', (int) $filtros['id_tab_tipo_indicador']);
        }
        if (!empty($filtros['id_tab_sub_tipo_indicador'])) {
            $query->where('ac_seguimiento.tab_indicadores.id_tab_sub_tipo_indicador', '=', (int) $filtros['id_tab_sub_tipo_indicador']);
        }
        if (!empty($filtros['id_tab_municipio_detalle'])) {
            $query->where('ac_seguimiento.tab_indicadores.id_tab_municipio_detalle', '=', (int) $filtros['id_tab_municipio_detalle']);
        }
        if (!empty($filtros['id_tab_parroquia_detalle'])) {
            $query->where('ac_seguimiento.tab_indicadores.id_tab_parroquia_detalle', '=', (int) $filtros['id_tab_parroquia_detalle']);
        }
        if (!empty($filtros['id_tab_comuna'])) {
            $query->where('ac_seguimiento.tab_indicadores.id_tab_comuna', '=', (int) $filtros['id_tab_comuna']);
        }
        if ($filtros['texto'] !== '') {
            $texto = '%' . $filtros['texto'] . '%';
            $query->where(function ($q) use ($texto) {
                $q->where('ac_seguimiento.tab_indicadores.de_indicador', 'ILIKE', $texto)
                    ->orWhere('ti.de_tipo_indicador', 'ILIKE', $texto)
                    ->orWhere('sti.de_sub_tipo_indicador', 'ILIKE', $texto)
                    ->orWhere('ej.tx_ejecutor', 'ILIKE', $texto)
                    ->orWhere('ej.id_ejecutor', 'ILIKE', $texto);
            });
        }

        return $query;
    }

    /**
     * Aplica el ordenamiento según el tipo de reporte.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipo
     */
    protected function aplicarOrden($query, $tipo)
    {
        if ($tipo === 'ejecutor') {
            $query->orderBy('ej.id_ejecutor', 'ASC')
                ->orderBy('ti.id', 'ASC')
                ->orderBy('sti.id', 'ASC')
                ->orderBy('ac.id_tab_ejercicio_fiscal', 'ASC')
                ->orderBy('l.id', 'ASC')
                ->orderBy('ac_seguimiento.tab_indicadores.id', 'ASC');
        } else {
            $query->orderBy('ti.id', 'ASC')
                ->orderBy('sti.id', 'ASC')
                ->orderBy('ac.id_tab_ejercicio_fiscal', 'ASC')
                ->orderBy('l.id', 'ASC')
                ->orderBy('ej.id_ejecutor', 'ASC')
                ->orderBy('ac_seguimiento.tab_indicadores.id', 'ASC');
        }
    }

    /**
     * Construye el resumen legible de los filtros aplicados para la cabecera del Excel.
     *
     * @param array $filtros
     * @return string
     */
    protected function describirFiltros(array $filtros)
    {
        $partes = array();

        if (!empty($filtros['ejercicio'])) {
            $partes[] = 'Ejercicio: ' . $filtros['ejercicio'];
        }
        if (!empty($filtros['id_tab_lapso'])) {
            $lapso = tab_lapso::find((int) $filtros['id_tab_lapso']);
            if ($lapso) {
                $partes[] = 'Periodo: ' . trim($lapso->de_lapso);
            }
        } else {
            $partes[] = 'Periodo: Todos';
        }
        if (!empty($filtros['id_tab_ejecutores'])) {
            $ej = tab_ejecutores::find((int) $filtros['id_tab_ejecutores']);
            if ($ej) {
                $partes[] = 'Ejecutor: ' . trim($ej->id_ejecutor) . ' - ' . trim($ej->tx_ejecutor);
            }
        } elseif (!empty($filtros['id_ejecutor'])) {
            $ej = tab_ejecutores::where('id_ejecutor', '=', trim($filtros['id_ejecutor']))->first();
            if ($ej) {
                $partes[] = 'Ejecutor: ' . trim($ej->id_ejecutor) . ' - ' . trim($ej->tx_ejecutor);
            }
        }
        if (!empty($filtros['id_tab_tipo_indicador'])) {
            $ti = tab_tipo_indicador::find((int) $filtros['id_tab_tipo_indicador']);
            if ($ti) {
                $partes[] = 'Línea de Acción: ' . trim($ti->de_tipo_indicador);
            }
        }
        if (!empty($filtros['id_tab_sub_tipo_indicador'])) {
            $sti = tab_sub_tipo_indicador::find((int) $filtros['id_tab_sub_tipo_indicador']);
            if ($sti) {
                $partes[] = 'Tipo Indicador: ' . trim($sti->de_sub_tipo_indicador);
            }
        }
        if (!empty($filtros['id_tab_municipio_detalle'])) {
            $mun = tab_municipio_detalle::find((int) $filtros['id_tab_municipio_detalle']);
            if ($mun) {
                $partes[] = 'Municipio: ' . trim($mun->de_municipio);
            }
        }
        if (!empty($filtros['id_tab_parroquia_detalle'])) {
            $parr = tab_parroquia_detalle::find((int) $filtros['id_tab_parroquia_detalle']);
            if ($parr) {
                $partes[] = 'Parroquia: ' . trim($parr->de_parroquia);
            }
        }
        if (!empty($filtros['id_tab_comuna'])) {
            $com = tab_comuna::find((int) $filtros['id_tab_comuna']);
            if ($com) {
                $partes[] = 'Comuna: ' . trim($com->de_comuna);
            }
        }
        if ($filtros['texto'] !== '') {
            $partes[] = 'Búsqueda: "' . $filtros['texto'] . '"';
        }

        $partes[] = 'Generado: ' . date('d/m/Y H:i');

        return implode('  |  ', $partes);
    }

    /**
     * Construye el objeto PHPExcel con formato profesional para cualquiera de los dos reportes.
     *
     * @param string $tipo  'linea' | 'ejecutor'
     * @param \Illuminate\Support\Collection|array $registros
     * @param string $resumenFiltros
     * @return \PHPExcel
     */
    public function construirExcel($tipo, $registros, $resumenFiltros)
    {
        $esEjecutor = ($tipo === 'ejecutor');
        $titulo     = $esEjecutor
            ? 'REPORTE DE GESTIÓN POR EJECUTOR'
            : 'REPORTE DE GESTIÓN POR LÍNEA DE ACCIÓN';
        $nombreHoja = $esEjecutor ? 'POR_EJECUTOR' : 'POR_LINEA_ACCION';

        if ($esEjecutor) {
            $encabezados = array(
                'A' => array('titulo' => 'Código Ejecutor',       'ancho' => 16),
                'B' => array('titulo' => 'Ejecutor',              'ancho' => 38),
                'C' => array('titulo' => 'Línea de Acción',       'ancho' => 34),
                'D' => array('titulo' => 'Tipo Indicador',        'ancho' => 42),
                'E' => array('titulo' => 'Periodo',               'ancho' => 22),
                'F' => array('titulo' => 'Unidad de Medida',      'ancho' => 22),
                'G' => array('titulo' => 'Cantidad',              'ancho' => 14),
                'H' => array('titulo' => 'Descripción / Detalle', 'ancho' => 48),
                'I' => array('titulo' => 'Municipios',            'ancho' => 20),
                'J' => array('titulo' => 'Parroquias',            'ancho' => 24),
                'K' => array('titulo' => 'Comunas',               'ancho' => 30)
            );
            $colCantidad = 'G';
            $colCodigo   = 'A';
        } else {
            $encabezados = array(
                'A' => array('titulo' => 'Línea de Acción',       'ancho' => 34),
                'B' => array('titulo' => 'Tipo Indicador',        'ancho' => 42),
                'C' => array('titulo' => 'Periodo',               'ancho' => 22),
                'D' => array('titulo' => 'Unidad de Medida',      'ancho' => 22),
                'E' => array('titulo' => 'Cantidad',              'ancho' => 14),
                'F' => array('titulo' => 'Descripción / Detalle', 'ancho' => 48),
                'G' => array('titulo' => 'Municipios',            'ancho' => 20),
                'H' => array('titulo' => 'Parroquias',            'ancho' => 24),
                'I' => array('titulo' => 'Comunas',               'ancho' => 30),
                'J' => array('titulo' => 'Código Ejecutor',       'ancho' => 16),
                'K' => array('titulo' => 'Ejecutor',              'ancho' => 38)
            );
            $colCantidad = 'E';
            $colCodigo   = 'J';
        }

        $prevReporting = error_reporting(error_reporting() & ~E_DEPRECATED & ~E_STRICT);
        try {
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()
                ->setCreator('Secretaría de Planificación y Estadística')
                ->setLastModifiedBy('SPE')
                ->setTitle($titulo)
                ->setSubject('Indicadores de Gestión')
                ->setDescription('Reporte de Gestión de Indicadores POA-SAIF.');

            $sheet = $objPHPExcel->setActiveSheetIndex(0);
            $sheet->setTitle($nombreHoja);

            // Anchos de columna
            foreach ($encabezados as $col => $cfg) {
                $sheet->getColumnDimension($col)->setWidth($cfg['ancho']);
            }

            // Fila 1: Título principal
            $sheet->mergeCells('A1:K1');
            $sheet->setCellValue('A1', $titulo);
            $sheet->getRowDimension(1)->setRowHeight(28);
            $sheet->getStyle('A1:K1')->applyFromArray(array(
                'font' => array(
                    'bold'  => true,
                    'size'  => 13,
                    'color' => array('argb' => 'FFFFFFFF')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('argb' => 'FF1F4E78')
                )
            ));

            // Fila 2: Resumen de filtros aplicados
            $sheet->mergeCells('A2:K2');
            $sheet->setCellValue('A2', $resumenFiltros);
            $sheet->getRowDimension(2)->setRowHeight(20);
            $sheet->getStyle('A2:K2')->applyFromArray(array(
                'font' => array(
                    'italic' => true,
                    'size'   => 9,
                    'color'  => array('argb' => 'FF333333')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('argb' => 'FFE9EDF4')
                )
            ));

            // Fila 3: Encabezados de columnas
            $sheet->getRowDimension(3)->setRowHeight(26);
            foreach ($encabezados as $col => $cfg) {
                $sheet->setCellValue($col . '3', $cfg['titulo']);
            }
            $sheet->getStyle('A3:K3')->applyFromArray(array(
                'font' => array(
                    'bold'  => true,
                    'size'  => 10,
                    'color' => array('argb' => 'FFFFFFFF')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'wrap'       => true
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => 'FF000000')
                    )
                ),
                'fill' => array(
                    'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('argb' => 'FF2F5597')
                )
            ));

            // Filas de datos
            $rowCount = 4;
            $totalCantidad = 0;
            foreach ($registros as $item) {
                $linea      = isset($item->linea_accion) ? (string) $item->linea_accion : '';
                $subtipo    = isset($item->tipo_indicador) ? (string) $item->tipo_indicador : '';
                $periodo    = isset($item->periodo) ? (string) $item->periodo : '';
                $um         = isset($item->unidad_medida) ? (string) $item->unidad_medida : '';
                $cantidad   = (isset($item->cantidad) && $item->cantidad !== null && $item->cantidad !== '') ? (float) $item->cantidad : 0;
                $detalle    = isset($item->descripcion) ? (string) $item->descripcion : '';
                $municipio  = isset($item->municipio) ? (string) $item->municipio : '';
                $parroquia  = isset($item->parroquia) ? (string) $item->parroquia : '';
                $comuna     = isset($item->comuna) ? (string) $item->comuna : '';
                $codEj      = isset($item->codigo_ejecutor) ? (string) $item->codigo_ejecutor : '';
                $ejecutor   = isset($item->ejecutor) ? (string) $item->ejecutor : '';
                $totalCantidad += $cantidad;

                if ($esEjecutor) {
                    $sheet->setCellValueExplicit('A' . $rowCount, $codEj, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('B' . $rowCount, $ejecutor, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('C' . $rowCount, $linea, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('D' . $rowCount, $subtipo, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('E' . $rowCount, $periodo, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('F' . $rowCount, $um, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('G' . $rowCount, $cantidad, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit('H' . $rowCount, $detalle, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('I' . $rowCount, $municipio, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('J' . $rowCount, $parroquia, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('K' . $rowCount, $comuna, PHPExcel_Cell_DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValueExplicit('A' . $rowCount, $linea, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('B' . $rowCount, $subtipo, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('C' . $rowCount, $periodo, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('D' . $rowCount, $um, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('E' . $rowCount, $cantidad, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit('F' . $rowCount, $detalle, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('G' . $rowCount, $municipio, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('H' . $rowCount, $parroquia, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('I' . $rowCount, $comuna, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('J' . $rowCount, $codEj, PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('K' . $rowCount, $ejecutor, PHPExcel_Cell_DataType::TYPE_STRING);
                }

                if ($rowCount % 2 === 1) {
                    $sheet->getStyle('A' . $rowCount . ':K' . $rowCount)->applyFromArray(array(
                        'fill' => array(
                            'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array('argb' => 'FFF7F9FC')
                        )
                    ));
                }

                $rowCount++;
            }

            $lastDataRow = $rowCount - 1;
            if ($lastDataRow >= 4) {
                $sheet->getStyle('A4:K' . $lastDataRow)->applyFromArray(array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FFBFBFBF')
                        )
                    ),
                    'alignment' => array(
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                        'wrap'     => true
                    )
                ));

                $sheet->getStyle($colCantidad . '4:' . $colCantidad . $lastDataRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.##');
                $sheet->getStyle($colCantidad . '4:' . $colCantidad . $lastDataRow)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle($colCodigo . '4:' . $colCodigo . $lastDataRow)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                // Fila de total de registros
                $sheet->setCellValueExplicit('A' . $rowCount, 'TOTAL REGISTROS: ' . ($lastDataRow - 3), PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValueExplicit($colCantidad . $rowCount, $totalCantidad, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->getStyle($colCantidad . $rowCount)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.##');
                $sheet->getStyle($colCantidad . $rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('A' . $rowCount . ':K' . $rowCount)->applyFromArray(array(
                    'font' => array('bold' => true),
                    'borders' => array(
                        'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                            'color' => array('argb' => 'FF1F4E78')
                        )
                    ),
                    'fill' => array(
                        'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array('argb' => 'FFD9E1F2')
                    )
                ));
            } else {
                $sheet->mergeCells('A4:K4');
                $sheet->setCellValue('A4', 'No se encontraron registros de indicadores para los filtros seleccionados.');
                $sheet->getStyle('A4:K4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }

            // Panel inmovilizado en encabezados
            $sheet->freezePane('A4');

            return $objPHPExcel;
        } finally {
            error_reporting($prevReporting);
        }
    }

    /**
     * Ejecuta la consulta filtrada y transmite el archivo .xlsx al navegador.
     *
     * @param string $tipo 'linea' | 'ejecutor'
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
     */
    protected function generarDescargaExcel($tipo)
    {
        try {
            $filtros = $this->extraerFiltros();
            $query   = $this->construirConsulta($filtros);
            $this->aplicarOrden($query, $tipo);

            $registros      = $query->get();
            $resumenFiltros = $this->describirFiltros($filtros);
            $objPHPExcel    = $this->construirExcel($tipo, $registros, $resumenFiltros);

            $prefijo  = ($tipo === 'ejecutor') ? 'reporte_gestion_por_ejecutor_' : 'reporte_gestion_por_linea_accion_';
            $filename = $prefijo . date('Ymd_His') . '.xlsx';

            if (!class_exists('ZipArchive')) {
                PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
            }
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

            return Response::stream(function () use ($objWriter) {
                $prevReporting = error_reporting(error_reporting() & ~E_DEPRECATED & ~E_STRICT);
                try {
                    if (ob_get_length()) {
                        ob_end_clean();
                    }
                    $objWriter->save('php://output');
                } finally {
                    error_reporting($prevReporting);
                }
            }, 200, array(
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control'       => 'max-age=0',
                'Pragma'              => 'public'
            ));
        } catch (\Illuminate\Database\QueryException $e) {
            return Response::json(array(
                'success' => false,
                'msg'     => array('ERROR (' . $e->getCode() . '):' => $e->getMessage())
            ), 500);
        }
    }

    /**
     * Genera el binario PDF infográfico agrupado por Línea de Acción, Tipo Indicador y Periodo.
     *
     * @param \Illuminate\Support\Collection|array $registros
     * @param array $filtros
     * @return string
     */
    public function construirPdfLineaAccion($registros, array $filtros)
    {
        $prevReporting = error_reporting(error_reporting() & ~E_DEPRECATED & ~E_STRICT);
        try {
            $pdf = new TCPDF('L', 'mm', 'LETTER', true, 'UTF-8', false);
            $pdf->SetCreator('Secretaría de Planificación y Estadística');
            $pdf->SetAuthor('Gobernación Bolivariana del Zulia');
            $pdf->SetTitle('Reporte de Gestión por Línea de Acción');
            $pdf->SetSubject('Indicadores de Gestión por Línea de Acción');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // Agrupar registros por (Línea de Acción, Tipo Indicador, Periodo)
            $grupos = array();
            foreach ($registros as $item) {
                $clave = (isset($item->id_tab_tipo_indicador) ? $item->id_tab_tipo_indicador : '')
                    . '|' . (isset($item->id_tab_sub_tipo_indicador) ? $item->id_tab_sub_tipo_indicador : '')
                    . '|' . (isset($item->id_tab_lapso) ? $item->id_tab_lapso : '');

                if (!isset($grupos[$clave])) {
                    $grupos[$clave] = array(
                        'linea_accion'   => $this->limpiarTexto(isset($item->linea_accion) ? $item->linea_accion : ''),
                        'tipo_indicador' => $this->limpiarTexto(isset($item->tipo_indicador) ? $item->tipo_indicador : ''),
                        'periodo'        => $this->limpiarTexto(isset($item->periodo) ? $item->periodo : ''),
                        'total_cantidad' => 0.0,
                        'ejecutores'     => array(),
                        'items'          => array()
                    );
                }

                $cantidad = (isset($item->cantidad) && $item->cantidad !== null && $item->cantidad !== '')
                    ? (float) $item->cantidad
                    : 0.0;
                $grupos[$clave]['total_cantidad'] += $cantidad;

                $ejNombre = mb_strtoupper($this->limpiarTexto(isset($item->ejecutor) ? $item->ejecutor : ''), 'UTF-8');
                if ($ejNombre !== '' && !in_array($ejNombre, $grupos[$clave]['ejecutores'], true)) {
                    $grupos[$clave]['ejecutores'][] = $ejNombre;
                }

                $grupos[$clave]['items'][] = array(
                    'unidad_medida' => $this->limpiarTexto(isset($item->unidad_medida) ? $item->unidad_medida : ''),
                    'cantidad'      => $cantidad,
                    'descripcion'   => $this->limpiarTexto(isset($item->descripcion) ? $item->descripcion : ''),
                    'municipio'     => $this->limpiarTexto(isset($item->municipio) ? $item->municipio : ''),
                    'parroquia'     => $this->limpiarTexto(isset($item->parroquia) ? $item->parroquia : ''),
                    'comuna'        => $this->limpiarTexto(isset($item->comuna) ? $item->comuna : '')
                );
            }

            if (empty($grupos)) {
                $pdf->AddPage('L', 'LETTER');
                $this->dibujarEncabezadoPdf(
                    $pdf,
                    'LÍNEA DE ACCIÓN: SIN REGISTROS',
                    'No se encontraron indicadores para los filtros seleccionados',
                    'Ejercicio ' . (isset($filtros['ejercicio']) ? $filtros['ejercicio'] : ''),
                    'linea'
                );
                $pdf->SetFont('helvetica', '', 12);
                $pdf->SetTextColor(90, 90, 90);
                $pdf->SetXY(15, 95);
                $pdf->Cell(249, 12, 'No existen indicadores de gestión registrados con los parámetros de consulta actuales.', 0, 1, 'C');
                return $pdf->Output('', 'S');
            }

            foreach ($grupos as $grupo) {
                $paginasGrupo = array_chunk($grupo['items'], 3);
                $totalPaginasGrupo = count($paginasGrupo);

                foreach ($paginasGrupo as $indicePagina => $tarjetas) {
                    $pdf->AddPage('L', 'LETTER');

                    // 1) Encabezado superior + Logo Zulia + Sub-encabezado (Tipo Indicador + Periodo)
                    $this->dibujarEncabezadoPdf(
                        $pdf,
                        'LÍNEA DE ACCIÓN: ' . $grupo['linea_accion'],
                        $grupo['tipo_indicador'],
                        $grupo['periodo'],
                        'linea'
                    );

                    // 2) Línea divisoria vertical azul entre tarjetas (izq) y resumen (der)
                    $pdf->SetDrawColor(74, 140, 199);
                    $pdf->SetLineWidth(0.55);
                    $pdf->Line(201, 52, 201, 207);

                    // 3) Tarjetas de indicadores (hasta 3 por página)
                    $posY = array(47, 101, 155);
                    foreach ($tarjetas as $idx => $tarjeta) {
                        $this->dibujarTarjetaIndicador($pdf, $tarjeta, $posY[$idx]);
                    }

                    // 4) Panel derecho: TOTAL REALIZADO + ORGANOS Y ENTES
                    $this->dibujarPanelDerechoPdf(
                        $pdf,
                        $grupo['total_cantidad'],
                        'ORGANOS Y ENTES',
                        $grupo['ejecutores'],
                        'organos'
                    );

                    // 5) Pie sutil cuando un grupo tiene más de 3 indicadores
                    if ($totalPaginasGrupo > 1) {
                        $inicio = ($indicePagina * 3) + 1;
                        $fin    = min($inicio + count($tarjetas) - 1, count($grupo['items']));
                        $pdf->SetFont('helvetica', 'I', 7.5);
                        $pdf->SetTextColor(115, 115, 115);
                        $pdf->SetXY(8, 208.5);
                        $pdf->Cell(
                            190,
                            5,
                            'Mostrando indicadores ' . $inicio . '–' . $fin . ' de ' . count($grupo['items'])
                                . '   |   Lámina ' . ($indicePagina + 1) . ' de ' . $totalPaginasGrupo,
                            0,
                            0,
                            'L'
                        );
                    }
                }
            }

            return $pdf->Output('', 'S');
        } finally {
            error_reporting($prevReporting);
        }
    }

    /**
     * Genera el binario PDF infográfico agrupado por Ejecutor, Línea de Acción, Tipo Indicador y Periodo.
     *
     * @param \Illuminate\Support\Collection|array $registros
     * @param array $filtros
     * @return string
     */
    public function construirPdfEjecutor($registros, array $filtros)
    {
        $prevReporting = error_reporting(error_reporting() & ~E_DEPRECATED & ~E_STRICT);
        try {
            $pdf = new TCPDF('L', 'mm', 'LETTER', true, 'UTF-8', false);
            $pdf->SetCreator('Secretaría de Planificación y Estadística');
            $pdf->SetAuthor('Gobernación Bolivariana del Zulia');
            $pdf->SetTitle('Reporte de Gestión por Ejecutor');
            $pdf->SetSubject('Indicadores de Gestión por Ejecutor');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // Agrupar registros por (Ejecutor, Línea de Acción, Tipo Indicador, Periodo)
            $grupos = array();
            foreach ($registros as $item) {
                $clave = (isset($item->id_tab_ejecutores) ? $item->id_tab_ejecutores : '')
                    . '|' . (isset($item->id_tab_tipo_indicador) ? $item->id_tab_tipo_indicador : '')
                    . '|' . (isset($item->id_tab_sub_tipo_indicador) ? $item->id_tab_sub_tipo_indicador : '')
                    . '|' . (isset($item->id_tab_lapso) ? $item->id_tab_lapso : '');

                if (!isset($grupos[$clave])) {
                    $codEj = $this->limpiarTexto(isset($item->codigo_ejecutor) ? $item->codigo_ejecutor : '');
                    $nomEj = $this->limpiarTexto(isset($item->ejecutor) ? $item->ejecutor : '');
                    $grupos[$clave] = array(
                        'ejecutor_titulo' => ($codEj !== '' ? $codEj . ' - ' : '') . $nomEj,
                        'tipo_indicador'  => $this->limpiarTexto(isset($item->tipo_indicador) ? $item->tipo_indicador : ''),
                        'periodo'         => $this->limpiarTexto(isset($item->periodo) ? $item->periodo : ''),
                        'total_cantidad'  => 0.0,
                        'lineas_accion'   => array(),
                        'items'           => array()
                    );
                }

                $cantidad = (isset($item->cantidad) && $item->cantidad !== null && $item->cantidad !== '')
                    ? (float) $item->cantidad
                    : 0.0;
                $grupos[$clave]['total_cantidad'] += $cantidad;

                $lineaNombre = mb_strtoupper($this->limpiarTexto(isset($item->linea_accion) ? $item->linea_accion : ''), 'UTF-8');
                if ($lineaNombre !== '' && !in_array($lineaNombre, $grupos[$clave]['lineas_accion'], true)) {
                    $grupos[$clave]['lineas_accion'][] = $lineaNombre;
                }

                $grupos[$clave]['items'][] = array(
                    'unidad_medida' => $this->limpiarTexto(isset($item->unidad_medida) ? $item->unidad_medida : ''),
                    'cantidad'      => $cantidad,
                    'descripcion'   => $this->limpiarTexto(isset($item->descripcion) ? $item->descripcion : ''),
                    'municipio'     => $this->limpiarTexto(isset($item->municipio) ? $item->municipio : ''),
                    'parroquia'     => $this->limpiarTexto(isset($item->parroquia) ? $item->parroquia : ''),
                    'comuna'        => $this->limpiarTexto(isset($item->comuna) ? $item->comuna : '')
                );
            }

            if (empty($grupos)) {
                $pdf->AddPage('L', 'LETTER');
                $this->dibujarEncabezadoPdf(
                    $pdf,
                    'EJECUTOR: SIN REGISTROS',
                    'No se encontraron indicadores para los filtros seleccionados',
                    'Ejercicio ' . (isset($filtros['ejercicio']) ? $filtros['ejercicio'] : ''),
                    'ejecutor'
                );
                $pdf->SetFont('helvetica', '', 12);
                $pdf->SetTextColor(90, 90, 90);
                $pdf->SetXY(15, 95);
                $pdf->Cell(249, 12, 'No existen indicadores de gestión registrados con los parámetros de consulta actuales.', 0, 1, 'C');
                return $pdf->Output('', 'S');
            }

            foreach ($grupos as $grupo) {
                $paginasGrupo      = array_chunk($grupo['items'], 3);
                $totalPaginasGrupo = count($paginasGrupo);

                foreach ($paginasGrupo as $indicePagina => $tarjetas) {
                    $pdf->AddPage('L', 'LETTER');

                    // 1) Encabezado superior (EJECUTOR) + Logo Zulia + Sub-encabezado (Tipo Indicador + Periodo)
                    $this->dibujarEncabezadoPdf(
                        $pdf,
                        'EJECUTOR: ' . $grupo['ejecutor_titulo'],
                        $grupo['tipo_indicador'],
                        $grupo['periodo'],
                        'ejecutor'
                    );

                    // 2) Línea divisoria vertical azul entre tarjetas (izq) y resumen (der)
                    $pdf->SetDrawColor(74, 140, 199);
                    $pdf->SetLineWidth(0.55);
                    $pdf->Line(201, 52, 201, 207);

                    // 3) Tarjetas de indicadores (hasta 3 por página)
                    $posY = array(47, 101, 155);
                    foreach ($tarjetas as $idx => $tarjeta) {
                        $this->dibujarTarjetaIndicador($pdf, $tarjeta, $posY[$idx]);
                    }

                    // 4) Panel derecho: TOTAL REALIZADO + LÍNEA DE ACCIÓN
                    $this->dibujarPanelDerechoPdf(
                        $pdf,
                        $grupo['total_cantidad'],
                        'LÍNEA DE ACCIÓN',
                        $grupo['lineas_accion'],
                        'linea'
                    );

                    // 5) Pie sutil cuando un grupo tiene más de 3 indicadores
                    if ($totalPaginasGrupo > 1) {
                        $inicio = ($indicePagina * 3) + 1;
                        $fin    = min($inicio + count($tarjetas) - 1, count($grupo['items']));
                        $pdf->SetFont('helvetica', 'I', 7.5);
                        $pdf->SetTextColor(115, 115, 115);
                        $pdf->SetXY(8, 208.5);
                        $pdf->Cell(
                            190,
                            5,
                            'Mostrando indicadores ' . $inicio . '–' . $fin . ' de ' . count($grupo['items'])
                                . '   |   Lámina ' . ($indicePagina + 1) . ' de ' . $totalPaginasGrupo,
                            0,
                            0,
                            'L'
                        );
                    }
                }
            }

            return $pdf->Output('', 'S');
        } finally {
            error_reporting($prevReporting);
        }
    }

    /**
     * Dibuja el cintillo azul superior, Logo Zulia, Tipo Indicador y Periodo.
     */
    protected function dibujarEncabezadoPdf($pdf, $tituloSuperior, $tipoIndicador, $periodo, $modo = 'linea')
    {
        // Cintillo superior azul claro con esquina derecha redondeada (desde borde izquierdo)
        $pdf->SetFillColor(165, 209, 248);
        $pdf->RoundedRect(-6, 6, 232, 16.5, 5, '0110', 'F');

        // Ícono en el extremo izquierdo del cintillo
        if ($modo === 'ejecutor') {
            $this->dibujarIconoOrganos($pdf, 18, 14.3);
        } else {
            $this->dibujarIconoGrafico($pdf, 12, 8.2);
        }

        // Título principal con escalado automático para nombres extensos
        $lenTitulo = mb_strlen($tituloSuperior, 'UTF-8');
        $fontTitulo = ($lenTitulo > 95) ? 9.5 : (($lenTitulo > 75) ? 11 : (($lenTitulo > 56) ? 13 : 15.5));
        $pdf->SetFont('helvetica', 'B', $fontTitulo);
        $pdf->SetTextColor(15, 15, 15);
        $pdf->MultiCell(193, 14.5, $tituloSuperior, 0, 'L', false, 1, 29, 7.2, true, 0, false, true, 14.5, 'M', true);

        // Logo Gobernación Bolivariana del Zulia (esquina superior derecha)
        $logoZulia = public_path('images/cintillo_sistema_derecha.png');
        if (file_exists($logoZulia)) {
            // La imagen tiene margen blanco incorporado, la ubicamos con compensación para que destaque limpia
            $pdf->Image($logoZulia, 224, -4, 58, 58, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        }

        // Píldora gris izquierda: Tipo de Indicador (Sub-tipo)
        $pdf->SetFillColor(236, 236, 236);
        $pdf->RoundedRect(-5, 26.5, 171, 14.5, 5, '0110', 'F');

        $lenSub = mb_strlen($tipoIndicador, 'UTF-8');
        $fontSub = ($lenSub > 130) ? 7.5 : (($lenSub > 80) ? 9 : 11);
        $pdf->SetFont('helvetica', 'B', $fontSub);
        $pdf->SetTextColor(20, 20, 20);
        $pdf->MultiCell(158, 13.5, $tipoIndicador, 0, 'C', false, 1, 5, 27, true, 0, false, true, 13.5, 'M', true);

        // Píldora gris derecha: Periodo
        $pdf->SetFillColor(236, 236, 236);
        $pdf->RoundedRect(170, 26.5, 56, 14.5, 5, '1111', 'F');

        $txtPeriodo = 'Periodo: ' . $periodo;
        $fontPer = (mb_strlen($txtPeriodo, 'UTF-8') > 28) ? 8.5 : 9.5;
        $pdf->SetFont('helvetica', 'B', $fontPer);
        $pdf->SetTextColor(20, 20, 20);
        $pdf->MultiCell(52, 13.5, $txtPeriodo, 0, 'C', false, 1, 172, 27, true, 0, false, true, 13.5, 'M', true);
    }

    /**
     * Dibuja una tarjeta de indicador (3 columnas con íconos + franja inferior de descripción).
     */
    protected function dibujarTarjetaIndicador($pdf, array $tarjeta, $y)
    {
        $xLeft = 8;
        $wTotal = 186;

        // Coordenadas de las 3 columnas
        $c1X = $xLeft;       $c1W = 60;
        $c2X = $c1X + 63;    $c2W = 57;
        $c3X = $c2X + 60;    $c3W = 63;

        // 1) Íconos vectoriales encima de cada píldora
        $this->dibujarIconoVelocimetro($pdf, $c1X + ($c1W / 2), $y + 11.2);
        $this->dibujarIconoCheck($pdf, $c2X + ($c2W / 2), $y + 6.2);
        $this->dibujarIconoPin($pdf, $c3X + ($c3W / 2), $y + 5.2);

        // 2) Píldoras de encabezados de columna
        $pillY = $y + 12.2;
        $pillH = 7.6;

        // Verde: UNIDAD MEDIDA
        $pdf->SetFillColor(7, 208, 2);
        $pdf->RoundedRect($c1X, $pillY, $c1W, $pillH, 3.8, '1111', 'F');
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(15, 15, 15);
        $pdf->SetXY($c1X, $pillY);
        $pdf->Cell($c1W, $pillH, 'UNIDAD MEDIDA', 0, 0, 'C');

        // Amarillo: CANTIDAD
        $pdf->SetFillColor(252, 203, 58);
        $pdf->RoundedRect($c2X, $pillY, $c2W, $pillH, 3.8, '1111', 'F');
        $pdf->SetXY($c2X, $pillY);
        $pdf->Cell($c2W, $pillH, 'CANTIDAD', 0, 0, 'C');

        // Azul claro: TERRITORIALIDAD Y COBERTURA
        $pdf->SetFillColor(165, 209, 248);
        $pdf->RoundedRect($c3X, $pillY, $c3W, $pillH, 3.8, '1111', 'F');
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetXY($c3X, $pillY);
        $pdf->Cell($c3W, $pillH, 'TERRITORIALIDAD Y COBERTURA', 0, 0, 'C');

        // 3) Valores de cada columna
        $valY = $pillY + $pillH + 1.2;
        $valH = 16.5;

        // Valor: Unidad de Medida
        $um = $tarjeta['unidad_medida'];
        $lenUm = mb_strlen($um, 'UTF-8');
        $fontUm = ($lenUm > 42) ? 8 : (($lenUm > 22) ? 9.5 : 11.5);
        $pdf->SetFont('helvetica', 'B', $fontUm);
        $pdf->SetTextColor(15, 15, 15);
        $pdf->MultiCell($c1W - 2, $valH, $um, 0, 'C', false, 1, $c1X + 1, $valY, true, 0, false, true, $valH, 'M', true);

        // Valor: Cantidad
        $cantTxt = $this->formatearCantidad($tarjeta['cantidad']);
        $lenCant = strlen($cantTxt);
        $fontCant = ($lenCant > 10) ? 11 : (($lenCant > 7) ? 13 : 15);
        $pdf->SetFont('helvetica', 'B', $fontCant);
        $pdf->SetTextColor(15, 15, 15);
        $pdf->MultiCell($c2W - 2, $valH, $cantTxt, 0, 'C', false, 1, $c2X + 1, $valY, true, 0, false, true, $valH, 'M', true);

        // Valor: Territorialidad y Cobertura (Municipios / Parroquias / Comunas)
        $mun = $this->recortarTexto($tarjeta['municipio'], 38);
        $par = $this->recortarTexto($tarjeta['parroquia'], 38);
        $com = $this->recortarTexto($tarjeta['comuna'], 38);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(17, 17, 17);
        $htmlTerr = '<div style="font-size:7pt; line-height:1.35; color:#111111;">'
            . '<b>Municipios:</b> ' . htmlspecialchars($mun, ENT_QUOTES, 'UTF-8') . '<br/>'
            . '<b>Parroquias:</b> ' . htmlspecialchars($par, ENT_QUOTES, 'UTF-8') . '<br/>'
            . '<b>Comunas:</b> ' . htmlspecialchars($com, ENT_QUOTES, 'UTF-8')
            . '</div>';
        $pdf->writeHTMLCell($c3W - 2, $valH, $c3X + 1.5, $valY + 0.5, $htmlTerr, 0, 1, false, true, 'L', true);

        // 4) Franja inferior celeste muy claro: Descripción / Detalle
        $descY = $valY + $valH + 0.8;
        $descH = 11.2;
        $pdf->SetFillColor(234, 245, 253);
        $pdf->RoundedRect($xLeft, $descY, $wTotal, $descH, 3.8, '1111', 'F');

        $desc = mb_strtoupper($this->recortarTexto($tarjeta['descripcion'], 230), 'UTF-8');
        $lenDesc = mb_strlen($desc, 'UTF-8');
        $fontDesc = ($lenDesc > 130) ? 7.0 : (($lenDesc > 78) ? 8.0 : 9.2);
        $pdf->SetFont('helvetica', 'B', $fontDesc);
        $pdf->SetTextColor(15, 15, 15);
        $pdf->MultiCell($wTotal - 8, $descH - 0.6, $desc, 0, 'C', false, 1, $xLeft + 4, $descY + 0.3, true, 0, false, true, $descH - 0.6, 'M', true);
    }

    /**
     * Dibuja el panel lateral derecho: TOTAL REALIZADO (círculo concéntrico) y listado de referencia
     * (ORGANOS Y ENTES en reporte por Línea de Acción, o LÍNEA DE ACCIÓN en reporte por Ejecutor).
     */
    protected function dibujarPanelDerechoPdf($pdf, $totalCantidad, $tituloLista, array $elementos, $icono = 'organos')
    {
        $cx = 240.5;

        // Título TOTAL REALIZADO
        $pdf->SetFont('helvetica', 'B', 13.5);
        $pdf->SetTextColor(15, 15, 15);
        $pdf->SetXY(205, 54);
        $pdf->Cell(71, 8, 'TOTAL REALIZADO', 0, 1, 'C');

        // Círculos concéntricos amarillo / dorado / turquesa
        $cy = 91;
        // Anillo exterior amarillo suave
        $pdf->SetFillColor(253, 224, 114);
        $pdf->Circle($cx, $cy, 22, 0, 360, 'F');
        // Anillo intermedio dorado cálido (ligeramente desplazado como en la referencia)
        $pdf->SetFillColor(248, 200, 62);
        $pdf->Circle($cx - 1.5, $cy + 1.8, 18.2, 0, 360, 'F');
        // Círculo central turquesa
        $pdf->SetFillColor(89, 195, 201);
        $pdf->Circle($cx - 2.0, $cy + 2.0, 14.2, 0, 360, 'F');

        // Número total dentro del círculo (centrado en cx-2, cy+2)
        $totalTxt = $this->formatearCantidad($totalCantidad);
        $lenTotal = strlen($totalTxt);
        $fontTotal = ($lenTotal <= 3) ? 24 : (($lenTotal <= 6) ? 17 : (($lenTotal <= 9) ? 12.5 : (($lenTotal <= 11) ? 10 : 8.2)));
        $pdf->SetFont('helvetica', 'B', $fontTotal);
        $pdf->SetTextColor(10, 10, 10);
        $pdf->MultiCell(27, 14, $totalTxt, 0, 'C', false, 1, $cx - 15.5, $cy - 5.0, true, 0, false, true, 14, 'M', true);

        // Cintillo turquesa claro: ORGANOS Y ENTES / LÍNEA DE ACCIÓN (extendido al borde derecho)
        $bannerY = 126;
        $bannerH = 13.5;
        $pdf->SetFillColor(157, 219, 214);
        $pdf->RoundedRect(206.5, $bannerY, 78, $bannerH, 5.5, '1001', 'F');

        // Ícono a la izquierda de la etiqueta
        if ($icono === 'linea') {
            $this->dibujarIconoGraficoPequeno($pdf, 212.5, $bannerY + 2.8);
        } else {
            $this->dibujarIconoOrganos($pdf, 217, $bannerY + 6.8);
        }

        $pdf->SetFont('helvetica', 'B', 10.5);
        $pdf->SetTextColor(15, 15, 15);
        $pdf->SetXY(225, $bannerY);
        $pdf->Cell(52, $bannerH, $tituloLista, 0, 1, 'L');

        // Lista con viñeta circular negra
        $listY  = 144.5;
        $maxY   = 206;
        $nItems = count($elementos);
        $fontIt = ($nItems > 6) ? 6.8 : (($nItems > 4) ? 7.5 : 8.5);
        $lineH  = ($nItems > 6) ? 3.2 : (($nItems > 4) ? 3.7 : 4.2);

        $pdf->SetFont('helvetica', 'B', $fontIt);
        $pdf->SetTextColor(15, 15, 15);

        foreach ($elementos as $elem) {
            if ($listY > $maxY - 6) {
                $pdf->SetFont('helvetica', 'I', 7.5);
                $pdf->SetTextColor(90, 90, 90);
                $pdf->SetXY(217, $listY);
                $pdf->Cell(58, 4, '... y otros registros', 0, 1, 'L');
                break;
            }

            // Viñeta circular negra
            $pdf->SetFillColor(10, 10, 10);
            $pdf->Circle(213.5, $listY + 2.2, 2.3, 0, 360, 'F');

            $elemMostrar = $this->recortarTexto($elem, 110);
            $numLines    = $pdf->getNumLines($elemMostrar, 57);
            $blockH      = max(5.5, $numLines * $lineH + 1.0);

            $pdf->MultiCell(57, $blockH, $elemMostrar, 0, 'L', false, 1, 218.5, $listY, true, 0, false, true, $blockH, 'T', true);
            $listY += $blockH + 2.2;
        }
    }

    /**
     * Versión compacta del ícono de gráfico de barras para el cintillo turquesa LÍNEA DE ACCIÓN.
     */
    protected function dibujarIconoGraficoPequeno($pdf, $x, $y)
    {
        $pdf->SetDrawColor(20, 35, 45);
        $pdf->SetLineWidth(0.35);

        $barras = array(
            array(0,   5.2, 1.7, 2.8),
            array(2.2, 4.0, 1.7, 4.0),
            array(4.4, 2.8, 1.7, 5.2),
            array(6.6, 1.6, 1.7, 6.4)
        );
        $pdf->SetFillColor(45, 130, 138);
        foreach ($barras as $b) {
            $pdf->Rect($x + $b[0], $y + $b[1], $b[2], $b[3], 'DF');
        }
        $pdf->SetLineWidth(0.45);
        $pdf->Line($x + 0.4, $y + 4.0, $x + 3.2, $y + 2.6);
        $pdf->Line($x + 3.2, $y + 2.6, $x + 7.8, $y + 0.2);
        $pdf->Line($x + 6.3, $y + 0.2, $x + 7.8, $y + 0.2);
        $pdf->Line($x + 7.8, $y + 0.2, $x + 7.6, $y + 1.7);
    }

    /**
     * Ícono vectorial de gráfico de barras con flecha ascendente para el cintillo principal.
     */
    protected function dibujarIconoGrafico($pdf, $x, $y)
    {
        $pdf->SetDrawColor(25, 45, 65);
        $pdf->SetLineWidth(0.4);

        // 4 barras crecientes en tonos teal
        $barras = array(
            array(0,   7.5, 2.4, 4.3, array(120, 195, 200)),
            array(3.2, 5.8, 2.4, 6.0, array(92,  178, 185)),
            array(6.4, 4.0, 2.4, 7.8, array(72,  160, 168)),
            array(9.6, 2.2, 2.4, 9.6, array(52,  140, 150))
        );
        foreach ($barras as $b) {
            $pdf->SetFillColor($b[4][0], $b[4][1], $b[4][2]);
            $pdf->Rect($x + $b[0], $y + $b[1], $b[2], $b[3], 'DF');
        }

        // Línea de tendencia ascendente con nodos
        $pdf->SetDrawColor(20, 35, 50);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($x + 0.5, $y + 5.8, $x + 4.4, $y + 3.8);
        $pdf->Line($x + 4.4, $y + 3.8, $x + 7.6, $y + 2.2);
        $pdf->Line($x + 7.6, $y + 2.2, $x + 11.4, $y + 0.2);

        // Punta de flecha
        $pdf->Line($x + 9.4, $y + 0.2, $x + 11.4, $y + 0.2);
        $pdf->Line($x + 11.4, $y + 0.2, $x + 11.2, $y + 2.2);

        // Puntos en los vértices
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Circle($x + 0.5, $y + 5.8, 0.65, 0, 360, 'DF');
        $pdf->Circle($x + 4.4, $y + 3.8, 0.65, 0, 360, 'DF');
        $pdf->Circle($x + 7.6, $y + 2.2, 0.65, 0, 360, 'DF');
    }

    /**
     * Ícono vectorial de velocímetro semicircular superior para UNIDAD MEDIDA.
     */
    protected function dibujarIconoVelocimetro($pdf, $cx, $cy)
    {
        // Semicírculo superior exterior azul claro (de 0° derecha antihorario a 180° izquierda)
        $pdf->SetFillColor(186, 214, 245);
        $pdf->PieSector($cx, $cy, 10.2, 0, 180, 'F', false, 0);
        // Semicírculo superior interior blanco (formando arco limpio)
        $pdf->SetFillColor(255, 255, 255);
        $pdf->PieSector($cx, $cy + 0.1, 6.0, 0, 180, 'F', false, 0);
        // Aguja azul apuntando hacia arriba-derecha
        $pdf->SetDrawColor(46, 102, 204);
        $pdf->SetLineWidth(1.0);
        $pdf->Line($cx, $cy - 0.6, $cx + 3.6, $cy - 7.4);
        $pdf->SetFillColor(46, 102, 204);
        $pdf->Circle($cx, $cy - 0.6, 1.35, 0, 360, 'F');
    }

    /**
     * Ícono vectorial de círculo verde con marca de verificación blanca para CANTIDAD.
     */
    protected function dibujarIconoCheck($pdf, $cx, $cy)
    {
        $pdf->SetFillColor(7, 200, 2);
        $pdf->Circle($cx, $cy, 5.2, 0, 360, 'F');

        $pdf->SetDrawColor(255, 255, 255);
        $pdf->SetLineWidth(1.2);
        $pdf->Line($cx - 2.3, $cy + 0.1, $cx - 0.5, $cy + 2.0);
        $pdf->Line($cx - 0.5, $cy + 2.0, $cx + 2.6, $cy - 1.9);
    }

    /**
     * Ícono vectorial de marcador de ubicación rojo para TERRITORIALIDAD Y COBERTURA.
     */
    protected function dibujarIconoPin($pdf, $cx, $cy)
    {
        // Punta inferior del pin (triángulo)
        $pdf->SetFillColor(235, 42, 30);
        $pdf->Polygon(array(
            $cx - 3.6, $cy + 1.2,
            $cx + 3.6, $cy + 1.2,
            $cx,       $cy + 6.4
        ), 'F');
        // Cabeza circular roja
        $pdf->Circle($cx, $cy, 4.0, 0, 360, 'F');
        // Punto central blanco
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Circle($cx, $cy, 1.5, 0, 360, 'F');
    }

    /**
     * Ícono vectorial institucional (engranaje + nodos de articulación) para ORGANOS Y ENTES.
     */
    protected function dibujarIconoOrganos($pdf, $cx, $cy)
    {
        // Engranaje superior estilizado
        $pdf->SetDrawColor(20, 35, 35);
        $pdf->SetFillColor(20, 35, 35);
        $pdf->SetLineWidth(0.45);
        $pdf->Circle($cx, $cy - 2.0, 2.2, 0, 360, 'D');
        $pdf->Circle($cx, $cy - 2.0, 0.8, 0, 360, 'F');
        for ($ang = 0; $ang < 360; $ang += 45) {
            $rad = deg2rad($ang);
            $x1 = $cx + cos($rad) * 2.1;
            $y1 = ($cy - 2.0) + sin($rad) * 2.1;
            $x2 = $cx + cos($rad) * 3.0;
            $y2 = ($cy - 2.0) + sin($rad) * 3.0;
            $pdf->Line($x1, $y1, $x2, $y2);
        }
        // Arco inferior representando colaboración entre órganos y entes
        $pdf->SetLineWidth(0.6);
        $pdf->Line($cx - 3.4, $cy + 1.6, $cx - 1.2, $cy + 3.4);
        $pdf->Line($cx - 1.2, $cy + 3.4, $cx + 1.2, $cy + 3.4);
        $pdf->Line($cx + 1.2, $cy + 3.4, $cx + 3.4, $cy + 1.6);
        $pdf->Line($cx - 1.8, $cy + 2.2, $cx + 1.8, $cy + 2.2);
    }

    /**
     * Normaliza espacios y saltos de línea en textos provenientes de BD.
     */
    protected function limpiarTexto($texto)
    {
        $texto = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', (string) $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);
        return trim($texto);
    }

    /**
     * Recorta texto largo preservando UTF-8 sin romper caracteres.
     */
    protected function recortarTexto($texto, $maxLen)
    {
        if (mb_strlen($texto, 'UTF-8') <= $maxLen) {
            return $texto;
        }
        return rtrim(mb_substr($texto, 0, $maxLen - 3, 'UTF-8')) . '...';
    }

    /**
     * Formatea cantidades numéricas (sin decimales si es entero, hasta 2 si tiene fracción).
     */
    protected function formatearCantidad($numero)
    {
        $valor = (float) $numero;
        if (abs($valor - round($valor)) < 0.0001) {
            return number_format($valor, 0, ',', '.');
        }
        return number_format($valor, 2, ',', '.');
    }
}
