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
}
