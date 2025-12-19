<?php

namespace matriz\Http\Controllers\Reporte;

//*******agregar esta linea******//
use matriz\Models\Mantenimiento\tab_presupuesto_ingreso;
use matriz\Models\Mantenimiento\tab_partidas;
use matriz\Models\Mantenimiento\tab_objetivo_sectorial;
use matriz\Models\Mantenimiento\tab_clasificador_tipo;
use matriz\Models\Mantenimiento\tab_tipo_personal;
use matriz\Models\Ac\tab_ac;
use matriz\Models\Ac\tab_ac_ae_partida;
use matriz\Models\Proyecto\tab_proyecto;
use matriz\Models\Proyecto\tab_proyecto_ae_partida;
use matriz\Models\Proyecto\vista_relacion_transferencia;
use matriz\Models\Proyecto\vista_distribucion_presupuesto;
use matriz\Models\Mantenimiento\tab_escala_salarial;
use matriz\Models\Mantenimiento\tab_distribucion_municipio;
use matriz\Models\Mantenimiento\tab_ejecutores;
use matriz\Models\Ac\tab_ac_es_partida_desagregado;
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
use Helper;
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

        $ejercicio = Session::get("ejercicio");

        $pdf = new TCPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
        $pdf->SetCreator('Sistema POA, Yoser Perez');
        $pdf->SetAuthor('Yoser Perez');
        $pdf->SetTitle('Ley de Presupuesto');
        $pdf->SetSubject('Ley de Presupuesto');
        $pdf->SetKeywords('Ley de Presupuesto, PDF, Zulia, SPE, '.$ejercicio.'');
        $pdf->SetMargins(15, 10, 10);
        $pdf->SetTopMargin(10);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        // set auto page breaks
        $pdf->SetAutoPageBreak(true, 9);
        //$pdf->AddPage();

        $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        /******Portada Titulo I*********/
        $pdf->SetAlpha(0.3);
        $pdf->Image(public_path().'/images/escudo_zulia.png', 15, 40, 190, 190, 'PNG', '', '', false, 170, '', false, false, 0);
        $pdf->ln(30);
        $pdf->setAlpha(1);
        $pdf->SetFont('', '', 8);

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(1);
        //
        $pdf->SetY(15);
        $pdf->SetFont('', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(190, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(230);
        $pdf->SetFont('', 'B', 12);
        //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
        $pdf->writeHTML('<b><u>TITULO I<u/></b>', true, false, true, false, 'R');
        $pdf->ln(1);
        $pdf->MultiCell(195, 5, 'DISPOSICIONES GENERALES', 0, 'R', 0, 0, '', '', true);
        $pdf->ln(10);
        // set border width
        $pdf->SetLineWidth(0.508);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->setCellHeightRatio(0);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        $pdf->ln(2);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);
        
        
        
/******Inicio INFORMACION GENERAL DE LA ENTIDAD******/ 
        
        
         $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(1.2);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(95, 5, 'INFORMACIÓN GENERAL DE LA ENTIDAD FEDERAL', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(196, 6, '', 1, 'C', 0, 0, '', '', true);
        $pdf->SetFont('', 'B', 9);
        $pdf->SetY(29);
        $pdf->MultiCell(25, 5, 'BASE LEGAL:', 0, '', 0, 0, '', '', true);
        $pdf->SetFont('', '', 8);
        $pdf->SetX(36);
        $pdf->MultiCell(171, 5,'EL ESTADO ZULIA FUE CREADO OFICIALMENTE EL 22 DE ABRIL DE 1864, GACETA OFICIAL N° 324 CAPITAL MARACAIBO', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(4);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(196, 6, 'IDENTIFICACIÓN DE LOS ÓRGANOS DEL PODER PÚBLICO ESTADAL:', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(196, 6, '', 1, 'C', 0, 0, '', '', true);
        $pdf->SetFont('', 'B', 9);
        $pdf->SetY(41);
        $pdf->MultiCell(35, 5, 'GOBERNACIÓN:', 0, '', 0, 0, '', '', true);
        $pdf->SetFont('', '', 8);
        $pdf->SetX(43);
        $pdf->MultiCell(171, 5,'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'L', 0, 0, '', '', true); 
        $pdf->ln(4);
        $pdf->MultiCell(196, 6, 'DOMICILIO LEGAL:  CALLE 95 ENTRE AV. 4 Y 5 EDIFICIO PALACIO DE GOBIERNO PISO 1 CASCO CENTRAL FRENTE A LA PLAZA BOLIVAR, MARACAIBO ESTADO ZULIA.', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(7);
        $pdf->MultiCell(49, 6, 'TELÉFONO (S)', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'PAGINA WEB', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'FAX (S)', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'CODIGO POSTAL', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '4001', 1, 'R', 0, 0, '', '', true);      
        $pdf->ln(6);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(196, 6, 'NOMBRES Y APELLIDOS DEL GOBERNADOR (A)', 0, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetY(64);
        $pdf->MultiCell(196, 12, '', 1, 'C', 0, 0, '', '', true);
        $pdf->SetFont('', 'B', 9);
        $pdf->SetY(71);
        $pdf->MultiCell(196, 5, 'LUIS GERARDO CALDERA MORALES', 0, '', 0, 0, '', '', true);
        $pdf->ln(5);    
        $pdf->MultiCell(196, 6, 'PERSONAL DIRECTIVO DE LA GOBERNACIÓN Y ÓRGANOS AUXILIARES:', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(49, 6, 'DIRECCIÓN ADMINISTRATIVA', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'NOMBRES Y APELLIDOS', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'CORREO ELECTRÓNICO', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'TELÉFONO (S)', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(49, 6, 'Planificación y/o Presupuesto', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'HELÍMENAS JOSE ESPINA MORALES', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'espihgob7@gmail.com', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '04146629031', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(49, 6, 'Administracion y/o Finanzas', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'ALICIA CHIQUINQUIRA VILLALOBOS HERNÁNDEZ', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'aliciavillaloboszulia@gmail.com', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '04261221703', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(49, 6, 'Recursos Humanos y/o Personal', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'MARIA ALEJANDRA VILLALOBOS ', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'drrhh2025@gmail.com', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '04126859350', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(49, 6, 'Sindico (a) Procurador (a)', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'ANGEL FRANCISCO PAZ CASTILLO', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'procurazulia2025@gmail.com', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '04122655883', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(49, 6, 'Cronista del Municipio:', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'REYBER PARRA CONTRERAS', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'cronistamaracaibo@gmail.com', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true); 
        $pdf->ln(6);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(49, 6, 'CONTRALORÍA ESTADAL', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(49, 6, 'NOMBRES Y APELLIDOS DEL CONTRALOR (A)', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'MANUEL RAMÓN NUÑEZ GONZÁLEZ ', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'MANUELNUNEZ60@GMAIL.COM', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '04146187769', 1, 'L', 0, 0, '', '', true); 
        $pdf->ln(6);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true); 
        $pdf->ln(6);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(49, 6, 'DOMICILIO LEGAL:', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true); 
        $pdf->ln(6);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(196, 6, 'AV. 1B ENTRE 97 Y 98 (CIEGA) MARACAIBO ESTADO ZULIA', 1, 'L', 0, 0, '', '', true);   
        $pdf->ln(6);
        $pdf->MultiCell(49, 6, 'TELÉFONO (S)', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'PAGINA WEB', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(98, 6, 'CORREO (S) ELECTRÓNICO (S)', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(98, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(196, 6, 'CONSEJO LEGISLATIVO:', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(49, 6, 'NOMBRES Y APELLIDOS DEL PRESIDENTE (A):', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'MAGDELY VALBUENA MUÑOZ', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'clezpresidencia@gmail.com', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '04127727720', 1, 'L', 0, 0, '', '', true); 
        $pdf->ln(6);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(49, 6, 'NOMBRES Y APELLIDOS DEL SECRETARIO (A):', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'OVANNY AVILA', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '04246855296', 1, 'L', 0, 0, '', '', true); 
        $pdf->ln(6);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(98, 6, 'DOMICILIO LEGAL:  PALACIO LEGISLATIVO, CALLE 95 CON AV. 5 (CALLE OBISPALAZO), FRENTE A LA PLAZA BOLIVAR, MARACAIBO ESTADO ZULIA.', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(98, 6, 'CORREO ELECTRONICO: clezpresidencia@gmail.com', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(49, 6, 'TELÉFONO (S)', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, 'PAGINA WEB', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(98, 6, 'CORREO (S) ELECTRÓNICO (S)', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);    
        $pdf->ln(6);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(196, 6, 'CONSEJO LOCAL DE PLANIFICACIÓN PÚBLICA:', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(49, 6, 'NOMBRES Y APELLIDOS DE LOS CONSEJEROS (AS): ', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(49, 6, '', 1, 'L', 0, 0, '', '', true);         
        
        

        $pdf->SetY(262);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-219);
        $pdf->ln(2);
        $pdf->SetFont('', '', 7);
        $pdf->setCellHeightRatio(1);


        
/******Inicio POLÍTICA PRESUPUESTARIA Y FINANCIERA DE LA ENTIDAD FEDERAL******/ 
        
        
         $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(1.2);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(95, 5, 'POLÍTICA PRESUPUESTARIA Y FINANCIERA DE LA ENTIDAD FEDERAL', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->setCellHeightRatio(1.2);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(196, 6, 'DESCRIPCIÓN:', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(6);
        $pdf->MultiCell(196, 228, '', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(4);
        $pdf->SetFont('', 'B', 6);
        $pdf->MultiCell(49, 6, 'POLITICA DE FINANCIAMIENTO: ', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('', '', 6);
        $pdf->MultiCell(147, 6, 'La Política de Financiamiento se fundamenta en la Constitución de la República Bolivariana de Venezuela, bajo los principios de responsabilidad fiscal, equilibrio presupuestario y sostenibilidad financiera, como pilares de la administración pública. Asimismo, se orienta en el Plan de la Patria de las 7 Grandes Transformaciones 2025 2031, hacia la construcción de un modelo económico productivo, sustentable y socialista, donde la comuna constituye el espacio territorial de transición al Socialismo.
En tal sentido, la Gobernación Bolivariana del Estado Zulia, en el marco del nuevo mapa político territorial, debe garantizar la correcta estimación de los recursos financieros provenientes de aportes constitucionales y legales, ingresos propios y otras fuentes de financiamiento, asegurando su administración con ética, transparencia y prudencia, y promoviendo la integración de todos los sectores productivos.
La política de financiamiento se orienta hacia:
1.	Optimización de los recursos presupuestarios, asegurando su uso eficiente y responsable.
2.	Diversificación de las fuentes de ingreso, reduciendo la dependencia de aportes externos y fortaleciendo la autonomía financiera del Estado.
3.	Responsabilidad fiscal y prudencia financiera, garantizando que todo financiamiento se ajuste a la capacidad de pago y a los principios de sostenibilidad.
4.	Impulso al bienestar colectivo y al fortalecimiento de los factores productivos regionales, como condición indispensable para el desarrollo económico y social del Zulia.
', 0, 'J', 0, 0, '', '', true);

        $pdf->ln(40);
        $pdf->SetFont('', 'B', 6);
        $pdf->MultiCell(49, 6, 'POLITICA DE GASTOS:', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('', '', 6);
        $pdf->MultiCell(147, 6, 'En el marco del nuevo mapa político-territorial del Estado Zulia, la Política de Gastos constituye el conjunto de orientaciones, lineamientos y criterios en materia de gasto público, con el propósito de optimizar los recursos presupuestarios y garantizar la eficiencia en la gestión pública. Su finalidad es propiciar y apoyar las estrategias de crecimiento y desarrollo local, necesarias para impulsar cambios favorables en las condiciones de bienestar de los zulianos y zulianas, así como en el fortalecimiento de los factores productivos regionales.
La presente política erige la Ética Moral, la Transparencia y la Prudencia en el Gasto Público como ejes transversales de toda la acción gubernamental, comprometiéndose a restaurar la confianza ciudadana mediante una gestión impecable de los recursos, sometiendo cada decisión a la participación popular y comunal, y asegurando que la asignación de fondos se realice con rigor y eficiencia para satisfacer las necesidades colectivas, combatiendo la opacidad y consolidando la probidad como valor esencial de todo funcionario y funcionaria pública.
En este contexto, la formulación de la política de gastos se fundamenta en un proceso participativo y democrático que comprendió la realización de 300 asambleas populares, la consulta de proyectos a las 576 comunas y circuitos comunales, así como encuentros con empresas de propiedad social directa e indirecta, emprendedores y empresas de propiedad familiar, además de reuniones con cámaras empresariales, acuicultores y pescadores, asegurando la integración de todos los sectores productivos.
En consecuencia, los lineamientos centrales de la Política de Gastos de la Gobernación Bolivariana del Estado Zulia son:
1.	Priorizar los servicios públicos esenciales: salud, educación, saneamiento ambiental, agua potable, energía y transporte.
2.	Reimpulsar la producción agrícola, acuícola y ganadera, como pilares de la seguridad y soberanía alimentaria.
3.	Fortalecer la comuna como espacio territorial del socialismo, donde se gestan nuevas formas de gestión financiera y productiva.
4.	Apoyar a las empresas de propiedad social y familiar, promoviendo la transición hacia un modelo económico comunal y corresponsable.
5.	Transferir progresivamente competencias al Poder Popular, consolidando la participación directa en la planificación, ejecución y control del gasto público.
6.	Promover el desarrollo sustentable, asegurando que cada inversión contribuya a la preservación ambiental y al bienestar colectivo.
7.	Ejercer austeridad y responsabilidad fiscal, garantizando la correcta administración de ingresos y egresos del Estado.
', 0, 'J', 0, 0, '', '', true); 

        $pdf->ln(60);
        $pdf->SetFont('', 'B', 6);
        $pdf->MultiCell(49, 6, 'POLITICA DE COBERTURA DE LOS SERVICIOS A PRESTAR POR LA ENTIDAD FEDERAL:', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('', '', 6);
        $pdf->MultiCell(147, 6, 'La política de cobertura de actividades y servicios a prestar por el estado Zulia se fundamenta en el ejercicio del gobierno desde las comunidades, mediante Gabinetes Comunales de Servicios que garanticen atención efectiva a las necesidades del Territorio Comunal. Se asume el liderazgo y la acción directa donde sea requerida para la resolución de problemas, promoviendo la unidad en la acción como principio esencial de la estructura de gobierno. Se impulsa un modelo de gestión moderno y eficiente, basado en el respeto mutuo, la colaboración entre servidores públicos y ciudadanía, la ética orientada al bien común y la equidad en toda gestión. Asimismo, se establece la reorganización del territorio y el acompañamiento de iniciativas sociales que propicien la integración, la paz y la unión de los pueblos en las áreas de la nueva frontera de paz, consolidando un sistema de atención integral y participativa. En consecuencia se estableceran las sigioentes acciones:
-APOYO A LA PRODUCCIÓN PETROLERA PETROQUÍMICA, AGRÍCOLA Y AGROINDUSTRIAL
- DESARROLLO ECONÓMICO INCLUSIVO
- ECONOMIA DE ESCALA
- FORTALECIMIENTO DE LAS CAPACIDADES PRODUCTIVAS DEL ESTADO ZULIA
- DIVERSIFICACIÓN PRODUCTIVA Y EL FOMENTO DE UN NUEVO MODELO EXPORTADOR
- MECANISMOS DE BLINDAJE PARA LA GENERACIÓN, CAPTACIÓN, INVERSIÓN Y ADMINISTRACIÓN DE DIVISAS.
- PLAN ESTADAL DE PLAN DE ATENCIÓN INTEGRAL A LOS SISTEMAS DE AGUAS SERVIDAS.
-LA RUTA DEL AGUA.
- PLAN ESTADAL DE VIALIDAD.
- PLAN ZULIA EN EL OESTE 
- PLAN ESTADAL DE VIVIENDA Y HABITAT-PLAN DE ELECTRICIDAD:
-SUSTITUCION DE TRANSFORMADORES
-ALUMBRADO PÚBLICO
-PROYECTOS DE LAS CONSULTAS POPULARES.
-ATENCIÓN DE AVERIAS
-REDUCIR EL NÚMERO DE FAMILIAS QUE SE VEN OBLIGADAS A COMPRAR GAS LICUADO DE PETRÓLEO (GLP).
-PLAN ZULIA EN PAZ Y SEGURA.
-PLAN CAYAPA DE LA SALUD
-PLAN CORAZÓN ZULIANO
- PLAN CAYAPA DE LA EDUCACION: ESCUELAS ABIERTAS. 
-PROGRAMA REGIONAL DE SEMILLEROS CIENTÍFICOS
-DESCOLONIZACIÓN DE LA CIENCIA Y TECNOLOGÍA APLICADA A UN SISTEMA PRODUCTIVO SOBERANO.
-DESARROLLAR EL MODELO DE EDUCACIÓN TÉCNICA, UNIVERSITARIA Y EN OFICIOS TERRITORIALIZADO Y SINCRONIZADO CON LA CONSTRUCCIÓN DE LA ECONOMÍA ZULIANA
-TEAM TAWALA
-LA RUTA CULTURAL PATRIMONIAL
-FORTALECIMIENTO DE LA CULTURA, USOS Y COSTUMBRES Y TRADICIONES DE LOS PUEBLOS INDÍGENAS
-BANCO REGIONAL DE INNOVACIÓN PRODUCTIVA
- DESARROLLO DE LAS TECNOLOGÍAS DE INFORMACIÓN Y COMUNICACIÓN
-LA RUTA CULTURAL PATRIMONIAL
-FORTALECIMIENTO DE LA CULTURA, USOS Y COSTUMBRES Y TRADICIONES DE LOS PUEBLOS INDÍGENAS. 
-ATENCIÓN DIRECTA AL PUEBLO (1X10) SISTEMA DE MISIONES Y GRANDES  MISIONES, ATENCIÓN A LOS NIÑOS, NIÑAS Y ADOLESCENTES (NNA )Y ADULTOS MAYORES).
-TURISMO PRODUCTIVO SUSTENTABLE
-DESARROLLO COMUNAL SOSTENIBLE
-IMPULSAR PROGRAMAS DE RECICLAJE Y RECOLECCIÓN DE DESECHOS SÓLIDOS.
-IMPULSAR PLANES DE ARBORIZACIÓN, EMBELLICIMIENTO DEL ESTADO Y PROMOVER LA CONFORMACIÓN DE BRIGADAS AMBIENTALES CON EL PODER POPULAR.
-PROMOVER PLANES DE EDUCACIÓN AMBIENTAL
-AMPLIAR LAS CAPACIDADES PRODUCTIVAS ESTADALES DE LOS MINERALES.
- IMPULSAR ESTRETEGIAS PARA LA SOSTENIBILIDAD AMBIENTAL.
', 0, 'J', 0, 0, '', '', true);         
        
        

        $pdf->SetY(262);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-219);
        $pdf->ln(2);
        $pdf->SetFont('', '', 7);
        $pdf->setCellHeightRatio(1);        
        
        

        $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        /******Portada Titulo II*********/
        $pdf->SetAlpha(0.3);
        $pdf->Image(public_path().'/images/escudo_zulia.png', 15, 40, 190, 190, 'PNG', '', '', false, 170, '', false, false, 0);
        $pdf->ln(30);
        $pdf->setAlpha(1);
        $pdf->SetFont('', '', 8);

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(1);
        //
        $pdf->SetY(15);
        $pdf->SetFont('', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(190, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(230);
        $pdf->SetFont('', 'B', 12);
        //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
        $pdf->writeHTML('<b><u>TITULO II<u/></b>', true, false, true, false, 'R');
        $pdf->ln(1);
        //$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>1, 'depth_h'=>1, 'color'=>array(255,0,0), 'opacity'=>1, 'blend_mode'=>'Normal'));
        $pdf->MultiCell(195, 5, 'PRESUPUESTO DE RECURSOS', 0, 'R', 0, 0, '', '', true);
        $pdf->ln(10);
        // set border width
        $pdf->SetLineWidth(0.508);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->setCellHeightRatio(0);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        $pdf->ln(2);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->SetFont('', 'B', 11);
        $pdf->MultiCell(70, 5, 'PRESUPUESTO DE RECURSOS', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '(EN BOLÍVARES) ', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->SetFont('', '', 8);
        $pdf->setCellHeightRatio(1.2);

        $pdf->SetFont('', 'B', 8);
        $pdf->MultiCell(40, 10, 'CÓDIGO'.chr(10).'(Recursos)', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(10, 11, chr(10).'RAMO', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(30, 5, 'SUB - RAMOS', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(5);

        $pdf->SetFont('', 'B', 6);
        $pdf->MultiCell(10, 5, '', 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(10, 6, 'GEN.', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(10, 6, 'ESP.', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(10, 6, 'SUB. ESP.', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(-15);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(40, 21, '', 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(116, 21, chr(10).chr(10).'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(40, 21, chr(10).chr(10).'MONTO', 1, 'C', 0, 0, '', '', true);

        $pdf->ln(21);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(10, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(116, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(40, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->SetY(267);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-219);
        $pdf->ln(2);
        $pdf->SetFont('', '', 7);
        $pdf->setCellHeightRatio(1);

        $presupuesto_uno = tab_presupuesto_ingreso::join('mantenimiento.tab_partidas as t01', 't01.co_partida', '=', DB::raw('left(mantenimiento.tab_presupuesto_ingreso.nu_partida::character varying, 3)'))
        ->select('t01.co_partida', 'tx_nombre', DB::raw('sum(mo_partida) as mo_partida'))
        ->where('mantenimiento.tab_presupuesto_ingreso.id_tab_ejercicio_fiscal', '=', $ejercicio)
        ->where('t01.id_tab_ejercicio_fiscal', '=', $ejercicio)
        ->where('mantenimiento.tab_presupuesto_ingreso.in_activo', '=', true)
        ->groupBy('t01.co_partida')
        ->groupBy('tx_nombre')
        ->orderBy('co_partida', 'ASC')
        ->get();

        $movimiento_ingreso = 0;
        $culminado_ingreso = false;

        foreach ($presupuesto_uno as $key => $value_presupuesto_uno) {

            $pdf->SetFont('', 'B', 8);
            //$pdf->writeHTMLCell(10,5, '', '', '<u><b>'.$value_presupuesto_uno->co_partida.'</b></u>', 0, 0, 0, true, 'C', true);
            $pdf->MultiCell(10, 5, trim($value_presupuesto_uno->co_partida), 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->writeHTMLCell(116, 5, '', '', '<u><b>'.$value_presupuesto_uno->tx_nombre.'</b></u>', 0, 0, 0, true, 'L', true);
            $pdf->MultiCell(40, 5, number_format($value_presupuesto_uno->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->ln(8);

            $presupuesto_dos = tab_presupuesto_ingreso::join('mantenimiento.tab_partidas as t01', 't01.co_partida', '=', DB::raw('left(mantenimiento.tab_presupuesto_ingreso.nu_partida::character varying, 3)'))
            ->join('mantenimiento.tab_partidas as t02', 't02.co_partida', '=', DB::raw('left(mantenimiento.tab_presupuesto_ingreso.nu_partida::character varying, 5)'))
            ->select('t02.co_partida', 't02.tx_nombre', DB::raw('sum(mo_partida) as mo_partida'))
            ->where('mantenimiento.tab_presupuesto_ingreso.id_tab_ejercicio_fiscal', '=', $ejercicio)
            ->where('t01.id_tab_ejercicio_fiscal', '=', $ejercicio)
            ->where('t02.id_tab_ejercicio_fiscal', '=', $ejercicio)
            ->where('mantenimiento.tab_presupuesto_ingreso.in_activo', '=', true)
            ->where('t01.co_partida', '=', $value_presupuesto_uno->co_partida)
            ->groupBy('t02.co_partida')
            ->groupBy('t02.tx_nombre')
            ->orderBy('t02.co_partida', 'ASC')
            ->get();

            foreach ($presupuesto_dos as $key => $value_presupuesto_dos) {

                $pdf->SetFont('', 'B', 8);
                $pdf->MultiCell(10, 5, substr(trim($value_presupuesto_dos->co_partida), 0, 3), 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, substr(substr(trim($value_presupuesto_dos->co_partida), 0, 5), 3), 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(2, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(114, 5, $value_presupuesto_dos->tx_nombre, 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(40, 5, number_format($value_presupuesto_dos->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                $pdf->ln(8);

                $presupuesto_tres = tab_presupuesto_ingreso::join('mantenimiento.tab_partidas as t01', 't01.co_partida', '=', DB::raw('left(mantenimiento.tab_presupuesto_ingreso.nu_partida::character varying, 3)'))
                ->join('mantenimiento.tab_partidas as t02', 't02.co_partida', '=', DB::raw('left(mantenimiento.tab_presupuesto_ingreso.nu_partida::character varying, 5)'))
                ->join('mantenimiento.tab_partidas as t03', 't03.co_partida', '=', DB::raw('left(mantenimiento.tab_presupuesto_ingreso.nu_partida::character varying, 7)'))
                ->select('t03.co_partida', 't03.tx_nombre', DB::raw('sum(mo_partida) as mo_partida'))
                ->where('mantenimiento.tab_presupuesto_ingreso.id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('t01.id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('t02.id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('t03.id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('mantenimiento.tab_presupuesto_ingreso.in_activo', '=', true)
                ->where('t01.co_partida', '=', $value_presupuesto_uno->co_partida)
                ->where('t02.co_partida', '=', $value_presupuesto_dos->co_partida)
                ->groupBy('t03.co_partida')
                ->groupBy('t03.tx_nombre')
                ->orderBy('t03.co_partida', 'ASC')
                ->get();

                foreach ($presupuesto_tres as $key => $value_presupuesto_tres) {

                    $pdf->SetFont('', '', 8);
                    $pdf->MultiCell(10, 5, substr(trim($value_presupuesto_tres->co_partida), 0, 3), 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 5, substr(substr(trim($value_presupuesto_tres->co_partida), 0, 5), 3), 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 5, substr(substr(trim($value_presupuesto_tres->co_partida), 0, 7), 5), 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(4, 5, '', 0, 'C', 0, 0, '', '', true);
                    //$pdf->MultiCell(112, 5, $value_presupuesto_tres->tx_nombre, 0, 'L', 0, 0, '', '', true);
                    $pdf->writeHTMLCell(112, 5, '', '', '<u>'.$value_presupuesto_tres->tx_nombre.'</u>', 0, 0, 0, true, 'L', true);
                    $pdf->MultiCell(40, 5, number_format($value_presupuesto_tres->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                    $pdf->ln(5);

                    $presupuesto_cuatro = tab_presupuesto_ingreso::select('nu_partida as co_partida', 'de_partida as tx_nombre', 'mo_partida')
                    ->where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                    ->where('in_activo', '=', true)
                    ->where(DB::raw('left(nu_partida::character varying, 7)::numeric'), '=', $value_presupuesto_tres->co_partida)
                    ->orderBy('nu_partida', 'ASC')
                    ->get();

                    foreach ($presupuesto_cuatro as $key => $value_presupuesto_cuatro) {

                        $pdf->SetFont('', '', 8);
                        $pdf->MultiCell(10, 5, substr(trim($value_presupuesto_cuatro->co_partida), 0, 3), 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 5, substr(substr(trim($value_presupuesto_cuatro->co_partida), 0, 5), 3), 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 5, substr(substr(trim($value_presupuesto_cuatro->co_partida), 0, 7), 5), 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 5, substr(substr(trim($value_presupuesto_cuatro->co_partida), 0, 9), 7), 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(6, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(110, 5, $value_presupuesto_cuatro->tx_nombre, 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(40, 5, number_format($value_presupuesto_cuatro->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                        $condicionPartida = strlen($value_presupuesto_cuatro->tx_nombre);
                        if ($condicionPartida >= 60) {
                            $pdf->ln(8);
                        } else {
                            $pdf->ln(8);
                        }

                    }

                }

            }

            $movimiento_ingreso = $movimiento_ingreso + $value_presupuesto_uno->mo_partida;

        }

        $culminado_ingreso = true;

        if($culminado_ingreso ==true) {
            $pdf->SetFont('', 'B', 8);
            $pdf->setCellHeightRatio(1.5);
            $pdf->SetY(262);
            $pdf->MultiCell(156, 5, 'TOTAL GENERAL', 1, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(40, 5, number_format($movimiento_ingreso, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        }

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        /******Portada Titulo III*********/
        $pdf->SetAlpha(0.3);
        $pdf->Image(public_path().'/images/escudo_zulia.png', 15, 40, 190, 190, 'PNG', '', '', false, 170, '', false, false, 0);
        $pdf->ln(30);
        $pdf->setAlpha(1);
        $pdf->SetFont('', '', 8);

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(1);
        //
        $pdf->SetY(15);
        $pdf->SetFont('', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(190, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(230);
        $pdf->SetFont('', 'B', 12);
        //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
        $pdf->writeHTML('<b><u>TITULO III<u/></b>', true, false, true, false, 'R');
        $pdf->ln(1);
        $pdf->MultiCell(195, 5, 'PRESUPUESTO DE GASTOS', 0, 'R', 0, 0, '', '', true);
        $pdf->ln(10);
        // set border width
        $pdf->SetLineWidth(0.508);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->setCellHeightRatio(0);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        $pdf->ln(2);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->SetFont('', 'B', 11);
        $pdf->MultiCell(90, 5, 'INDICE DE CATEGORIAS DE PROYECTOS Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->setCellHeightRatio(1.2);

        $pdf->ln(21);
        $pdf->SetFont('', 'B', 6);
        $pdf->StartTransform();
        $pdf->Rotate(90);
        $pdf->MultiCell(21, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->MultiCell(21, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->MultiCell(21, 10, chr(10).'ACCIÓN  ESPECÍFICA', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->StopTransform();
        $pdf->ln(-51);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(30, 21, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(83, 21, chr(10).chr(10).'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(83, 21, chr(10).chr(10).'UNIDAD EJECUTORA', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(21);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(219);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-219);
        $pdf->ln(2);
        $pdf->SetFont('', '', 7);
        $pdf->setCellHeightRatio(1);

        $categoria_uno = vista_distribucion_presupuesto::select('co_sector', 'tx_descripcion')
        ->where('ef_uno', '=', $ejercicio)
        ->where('id_tab_tipo_ejecutor', '=', 1)
        ->groupBy('co_sector')
        ->groupBy('tx_descripcion')
        ->orderBy('co_sector', 'ASC')
        ->get();

        foreach ($categoria_uno as $key => $value_categoria_uno) {

            $pdf->SetFont('', 'B', 7);
            $pdf->MultiCell(10, 5, $value_categoria_uno->co_sector, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(83, 5, mb_strtoupper($value_categoria_uno->tx_descripcion, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(83, 5, '', 0, 'L', 0, 0, '', '', true);
            $pdf->ln(5);

            $categoria_dos = vista_distribucion_presupuesto::select('nu_original', 'de_nombre')
            ->where('ef_uno', '=', $ejercicio)
            ->where('co_sector', '=', $value_categoria_uno->co_sector)
            ->where('id_tab_tipo_ejecutor', '=', 1)
            ->groupBy('nu_original')
            ->groupBy('de_nombre')
            ->orderBy('nu_original', 'ASC')
            ->get();

            foreach ($categoria_dos as $key => $value_categoria_dos) {

                $pdf->SetFont('', '', 7);
                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, substr($value_categoria_dos->nu_original, -2), 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                //$pdf->MultiCell(83, 5, mb_strtoupper($value_categoria_dos->de_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                $pdf->writeHTMLCell(83, 5, '', '', '<u>'.mb_strtoupper($value_categoria_dos->de_nombre, 'UTF-8').'</u>', 0, 0, 0, true, 'L', true);
                $pdf->MultiCell(83, 5, '', 0, 'L', 0, 0, '', '', true);

                $condicionPartida = strlen($value_categoria_dos->de_nombre);
                if ($condicionPartida >= 60) {
                    $pdf->ln(10);
                } else {
                    $pdf->ln(5);
                }

                $start_y = $pdf->GetY();

                if ($start_y >= 260) {

                    $pdf->AddPage();

                    // reset font stretching  reset font spacing
                    $pdf->setFontStretching(100);
                    $pdf->setFontSpacing(0);
                    $pdf->SetLineWidth(0.150);
                    $pdf->setCellHeightRatio(2);

                    $pdf->SetFont('', 'B', 7);
                    $pdf->setCellHeightRatio(1.2);
                    $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->setCellHeightRatio(2);
                    $pdf->SetFont('', 'B', 11);
                    $pdf->MultiCell(90, 5, 'INDICE DE CATEGORIAS DE PROYECTOS Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
                    $pdf->ln(8);
                    $pdf->SetFont('', 'B', 7);
                    $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->ln(-10);
                    $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->ln(19);
                    $pdf->setCellHeightRatio(1.2);

                    $pdf->ln(21);
                    $pdf->SetFont('', 'B', 6);
                    $pdf->StartTransform();
                    $pdf->Rotate(90);
                    $pdf->MultiCell(21, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(10);
                    $pdf->MultiCell(21, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(10);
                    $pdf->MultiCell(21, 10, chr(10).'ACCIÓN  ESPECÍFICA', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(10);
                    $pdf->StopTransform();
                    $pdf->ln(-51);
                    $pdf->SetFont('', 'B', 9);
                    $pdf->MultiCell(30, 21, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(83, 21, chr(10).chr(10).'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(83, 21, chr(10).chr(10).'UNIDAD EJECUTORA', 1, 'C', 0, 0, '', '', true);
                    $pdf->ln(21);
                    $pdf->setCellHeightRatio(1);
                    $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(219);
                    $pdf->SetFont('', '', 7);
                    $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                    $pdf->ln(-219);
                    $pdf->ln(2);
                    $pdf->SetFont('', '', 7);
                    $pdf->setCellHeightRatio(1);

                }

                $categoria_tres = vista_distribucion_presupuesto::select('nu_ae', 'de_ae')
                ->where('ef_uno', '=', $ejercicio)
                ->where('co_sector', '=', $value_categoria_uno->co_sector)
                ->where('nu_original', '=', $value_categoria_dos->nu_original)
                ->where('id_tab_tipo_ejecutor', '=', 1)
                ->groupBy('nu_ae')
                ->groupBy('de_ae')
                ->orderBy('nu_ae', 'ASC')
                ->get();

                foreach ($categoria_tres as $key => $value_categoria_tres) {

                    $pdf->SetFont('', '', 7);
                    $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 5, substr($value_categoria_tres->nu_ae, -2), 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(83, 5, mb_strtoupper($value_categoria_tres->de_ae, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                    //$pdf->writeHTMLCell(83,5, '', '', '<u>'.mb_strtoupper($value_categoria_tres->de_ae, 'UTF-8').'</u>', 0, 0, 0, true, 'L', true);
                    $pdf->ln(0);

                    $categoria_cuatro = vista_distribucion_presupuesto::select('tx_ejecutor')
                    ->where('ef_uno', '=', $ejercicio)
                    ->where('co_sector', '=', $value_categoria_uno->co_sector)
                    ->where('nu_original', '=', $value_categoria_dos->nu_original)
                    ->where('nu_ae', '=', $value_categoria_tres->nu_ae)
                    ->where('id_tab_tipo_ejecutor', '=', 1)
                    ->groupBy('tx_ejecutor')
                    ->groupBy('id_ejecutor')
                    ->orderBy('id_ejecutor', 'ASC')
                    ->get();

                    $lista_ejecutores = '';
                    $linea = 0;
                    $j = 0;
                    $cant = count($categoria_cuatro);

                    foreach ($categoria_cuatro as $key => $value_categoria_cuatro) {
                        $pdf->SetFont('', '', 6);
                        $pdf->MultiCell(113, 5, '', 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 5, '- '.$value_categoria_cuatro->tx_ejecutor, 0, 'L', 0, 0, '', '', true);
                        //$pdf->ln(5);

                        $condicionPartida = strlen($value_categoria_cuatro->tx_ejecutor);
                        if ($condicionPartida >= 60) {
                            $pdf->ln(7);
                        } else {
                            $pdf->ln(4);
                        }
                        $j++;
                        $start_y = $pdf->GetY();

                        if ($start_y >= 260) {
                            
                        if($j!=$cant){
                            
                            $pdf->AddPage();

                            // reset font stretching  reset font spacing
                            $pdf->setFontStretching(100);
                            $pdf->setFontSpacing(0);
                            $pdf->SetLineWidth(0.150);
                            $pdf->setCellHeightRatio(2);

                            $pdf->SetFont('', 'B', 7);
                            $pdf->setCellHeightRatio(1.2);
                            $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->setCellHeightRatio(2);
                            $pdf->SetFont('', 'B', 11);
                            $pdf->MultiCell(90, 5, 'INDICE DE CATEGORIAS DE PROYECTOS Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
                            $pdf->ln(8);
                            $pdf->SetFont('', 'B', 7);
                            $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->ln(-10);
                            $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                            $pdf->ln(19);
                            $pdf->setCellHeightRatio(1.2);

                            $pdf->ln(21);
                            $pdf->SetFont('', 'B', 6);
                            $pdf->StartTransform();
                            $pdf->Rotate(90);
                            $pdf->MultiCell(21, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
                            $pdf->ln(10);
                            $pdf->MultiCell(21, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                            $pdf->ln(10);
                            $pdf->MultiCell(21, 10, chr(10).'ACCIÓN  ESPECÍFICA', 1, 'L', 0, 0, '', '', true);
                            $pdf->ln(10);
                            $pdf->StopTransform();
                            $pdf->ln(-51);
                            $pdf->SetFont('', 'B', 9);
                            $pdf->MultiCell(30, 21, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(83, 21, chr(10).chr(10).'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(83, 21, chr(10).chr(10).'UNIDAD EJECUTORA', 1, 'C', 0, 0, '', '', true);
                            $pdf->ln(21);
                            $pdf->setCellHeightRatio(1);
                            $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
                            $pdf->ln(219);
                            $pdf->SetFont('', '', 7);
                            $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                            $pdf->ln(-219);
                            $pdf->ln(2);
                            $pdf->SetFont('', '', 7);
                            $pdf->setCellHeightRatio(1);

                           
                            
                            /*****Sector*********/
                            $pdf->SetFont('', 'B', 7);
                            $pdf->MultiCell(10, 5, $value_categoria_uno->co_sector, 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(83, 5, mb_strtoupper($value_categoria_uno->tx_descripcion, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(83, 5, '', 0, 'L', 0, 0, '', '', true);
                            $pdf->ln(5);
                            /*****Proy/Ac*******/
                            $pdf->SetFont('', '', 7);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, substr($value_categoria_dos->nu_original, -2), 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            //$pdf->MultiCell(83, 5, mb_strtoupper($value_categoria_dos->de_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                            $pdf->writeHTMLCell(83, 5, '', '', '<u>'.mb_strtoupper($value_categoria_dos->de_nombre, 'UTF-8').'</u>', 0, 0, 0, true, 'L', true);
                            $pdf->MultiCell(83, 5, '', 0, 'L', 0, 0, '', '', true);

                            $condicionPartida = strlen($value_categoria_dos->de_nombre);
                            if ($condicionPartida >= 60) {
                                $pdf->ln(10);
                            } else {
                                $pdf->ln(5);
                            }
                            /*****Ae*******/
                            $pdf->SetFont('', '', 7);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, substr($value_categoria_tres->nu_ae, -2), 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(83, 5, mb_strtoupper($value_categoria_tres->de_ae, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                            $pdf->ln(1);
                            }

                        }

                    }

                    /*foreach ($categoria_cuatro as $key => $value_categoria_cuatro) {
                      $linea = $linea + 2;
                      $lista_ejecutores.= '- '.$value_categoria_cuatro->tx_ejecutor.chr(10);
                    }

                    $pdf->SetFont('','',5);
                    $pdf->MultiCell(83, $linea, $lista_ejecutores, 0, 'L', 0, 0, '', '', true);

                    $pdf->ln($linea);

                    $condicionPartida = strlen($value_categoria_tres->de_ae);
                    if ($condicionPartida >= 60) {
                      $pdf->ln(10);
                    }else {
                      $pdf->ln(5);
                    }*/

                    $condicionPartida = strlen($value_categoria_tres->de_ae);
                    if ($condicionPartida >= 50) {
                        $pdf->ln(5);
                    }

                    $start_y = $pdf->GetY();

                    if ($start_y >= 260) {

                        $pdf->AddPage();

                        // reset font stretching  reset font spacing
                        $pdf->setFontStretching(100);
                        $pdf->setFontSpacing(0);
                        $pdf->SetLineWidth(0.150);
                        $pdf->setCellHeightRatio(2);

                        $pdf->SetFont('', 'B', 7);
                        $pdf->setCellHeightRatio(1.2);
                        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->setCellHeightRatio(2);
                        $pdf->SetFont('', 'B', 11);
                        $pdf->MultiCell(90, 5, 'INDICE DE CATEGORIAS DE PROYECTOS Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
                        $pdf->ln(8);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->ln(-10);
                        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(19);
                        $pdf->setCellHeightRatio(1.2);

                        $pdf->ln(21);
                        $pdf->SetFont('', 'B', 6);
                        $pdf->StartTransform();
                        $pdf->Rotate(90);
                        $pdf->MultiCell(21, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->MultiCell(21, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->MultiCell(21, 10, chr(10).'ACCIÓN  ESPECÍFICA', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->StopTransform();
                        $pdf->ln(-51);
                        $pdf->SetFont('', 'B', 9);
                        $pdf->MultiCell(30, 21, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 21, chr(10).chr(10).'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 21, chr(10).chr(10).'UNIDAD EJECUTORA', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(21);
                        $pdf->setCellHeightRatio(1);
                        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(219);
                        $pdf->SetFont('', '', 7);
                        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->ln(-219);
                        $pdf->ln(2);
                        $pdf->SetFont('', '', 7);
                        $pdf->setCellHeightRatio(1);
                        
                            /*****Sector*********/
                            $pdf->SetFont('', 'B', 7);
                            $pdf->MultiCell(10, 5, $value_categoria_uno->co_sector, 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(83, 5, mb_strtoupper($value_categoria_uno->tx_descripcion, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(83, 5, '', 0, 'L', 0, 0, '', '', true);
                            $pdf->ln(5);
                            /*****Proy/Ac*******/
                            $pdf->SetFont('', '', 7);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, substr($value_categoria_dos->nu_original, -2), 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            //$pdf->MultiCell(83, 5, mb_strtoupper($value_categoria_dos->de_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                            $pdf->writeHTMLCell(83, 5, '', '', '<u>'.mb_strtoupper($value_categoria_dos->de_nombre, 'UTF-8').'</u>', 0, 0, 0, true, 'L', true);
                            $pdf->MultiCell(83, 5, '', 0, 'L', 0, 0, '', '', true);

                            $condicionPartida = strlen($value_categoria_dos->de_nombre);
                            if ($condicionPartida >= 60) {
                                $pdf->ln(10);
                            } else {
                                $pdf->ln(5);
                            }                       

                    }

                }
                
                    $start_y = $pdf->GetY();

                    if ($start_y >= 260) {

                        $pdf->AddPage();

                        // reset font stretching  reset font spacing
                        $pdf->setFontStretching(100);
                        $pdf->setFontSpacing(0);
                        $pdf->SetLineWidth(0.150);
                        $pdf->setCellHeightRatio(2);

                        $pdf->SetFont('', 'B', 7);
                        $pdf->setCellHeightRatio(1.2);
                        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->setCellHeightRatio(2);
                        $pdf->SetFont('', 'B', 11);
                        $pdf->MultiCell(90, 5, 'INDICE DE CATEGORIAS DE PROYECTOS Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
                        $pdf->ln(8);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->ln(-10);
                        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(19);
                        $pdf->setCellHeightRatio(1.2);

                        $pdf->ln(21);
                        $pdf->SetFont('', 'B', 6);
                        $pdf->StartTransform();
                        $pdf->Rotate(90);
                        $pdf->MultiCell(21, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->MultiCell(21, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->MultiCell(21, 10, chr(10).'ACCIÓN  ESPECÍFICA', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->StopTransform();
                        $pdf->ln(-51);
                        $pdf->SetFont('', 'B', 9);
                        $pdf->MultiCell(30, 21, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 21, chr(10).chr(10).'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 21, chr(10).chr(10).'UNIDAD EJECUTORA', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(21);
                        $pdf->setCellHeightRatio(1);
                        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(219);
                        $pdf->SetFont('', '', 7);
                        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->ln(-219);
                        $pdf->ln(2);
                        $pdf->SetFont('', '', 7);
                        $pdf->setCellHeightRatio(1);
                        
                            /*****Sector*********/
                            $pdf->SetFont('', 'B', 7);
                            $pdf->MultiCell(10, 5, $value_categoria_uno->co_sector, 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(83, 5, mb_strtoupper($value_categoria_uno->tx_descripcion, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(83, 5, '', 0, 'L', 0, 0, '', '', true);
                            $pdf->ln(5);                       

                    }                

            }
            
                     $start_y = $pdf->GetY();

                    if ($start_y >= 250) {

                        $pdf->AddPage();

                        // reset font stretching  reset font spacing
                        $pdf->setFontStretching(100);
                        $pdf->setFontSpacing(0);
                        $pdf->SetLineWidth(0.150);
                        $pdf->setCellHeightRatio(2);

                        $pdf->SetFont('', 'B', 7);
                        $pdf->setCellHeightRatio(1.2);
                        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->setCellHeightRatio(2);
                        $pdf->SetFont('', 'B', 11);
                        $pdf->MultiCell(90, 5, 'INDICE DE CATEGORIAS DE PROYECTOS Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
                        $pdf->ln(8);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->ln(-10);
                        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(19);
                        $pdf->setCellHeightRatio(1.2);

                        $pdf->ln(21);
                        $pdf->SetFont('', 'B', 6);
                        $pdf->StartTransform();
                        $pdf->Rotate(90);
                        $pdf->MultiCell(21, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->MultiCell(21, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->MultiCell(21, 10, chr(10).'ACCIÓN  ESPECÍFICA', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->StopTransform();
                        $pdf->ln(-51);
                        $pdf->SetFont('', 'B', 9);
                        $pdf->MultiCell(30, 21, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 21, chr(10).chr(10).'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 21, chr(10).chr(10).'UNIDAD EJECUTORA', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(21);
                        $pdf->setCellHeightRatio(1);
                        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 219, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(83, 219, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(219);
                        $pdf->SetFont('', '', 7);
                        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->ln(-219);
                        $pdf->ln(2);
                        $pdf->SetFont('', '', 7);
                        $pdf->setCellHeightRatio(1);                       

                    }           
            

        }

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->SetFont('', 'B', 9);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(100, 8, 'RESUMEN DE LOS CREDITOS PRESUPUESTARIOS A NIVEL DE SECTORES, PROYECTOS Y/O ACCIÓNES CENTRALIZADAS', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '(EN BOLIVARES)', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->setCellHeightRatio(1.2);

        $pdf->ln(21);
        $pdf->SetFont('', 'B', 6);
        $pdf->StartTransform();
        $pdf->Rotate(90);
        $pdf->MultiCell(21, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->MultiCell(21, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(20);
        $pdf->StopTransform();
        $pdf->ln(-51);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(20, 21, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(92, 21, chr(10).chr(10).'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(42, 21, chr(10).chr(10).'SUB-TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(42, 21, chr(10).chr(10).'TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(21);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(10, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(92, 214, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(42, 214, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(42, 214, '', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(219);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-219);
        $pdf->ln(2);
        $pdf->SetFont('', '', 7);
        $pdf->setCellHeightRatio(1);

        $movimiento_credito = 0;

        $lista_credito_uno = vista_distribucion_presupuesto::select('co_sector', 'tx_descripcion', DB::raw('sum(monto) as mo_partida'))
        ->where('ef_uno', '=', $ejercicio)
        ->where('id_tab_tipo_ejecutor', '=', 1)
        ->groupBy('co_sector')
        ->groupBy('tx_descripcion')
        ->orderBy('co_sector', 'ASC')
        ->get();

        foreach ($lista_credito_uno as $key => $value_credito_uno) {

            $pdf->SetFont('', 'B', 7);
            $pdf->MultiCell(10, 5, $value_credito_uno->co_sector, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(92, 5, mb_strtoupper($value_credito_uno->tx_descripcion, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(42, 5, '', 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(42, 5, number_format($value_credito_uno->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->ln(5);

            $lista_credito_dos = vista_distribucion_presupuesto::select('nu_original', 'de_nombre', DB::raw('sum(monto) as mo_partida'))
            ->where('ef_uno', '=', $ejercicio)
            ->where('co_sector', '=', $value_credito_uno->co_sector)
            ->where('id_tab_tipo_ejecutor', '=', 1)
            ->groupBy('nu_original')
            ->groupBy('de_nombre')
            ->orderBy('nu_original', 'ASC')
            ->get();

            foreach ($lista_credito_dos as $key => $value_credito_dos) {

                $pdf->SetFont('', '', 7);
                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, substr($value_credito_dos->nu_original, -2), 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(92, 5, mb_strtoupper($value_credito_dos->de_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(42, 5, number_format($value_credito_dos->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(42, 5, '', 0, 'R', 0, 0, '', '', true);

                $condicionPartida = strlen($value_credito_dos->de_nombre);
                if ($condicionPartida >= 60) {
                    $pdf->ln(8);
                } else {
                    $pdf->ln(5);
                }

                $movimiento_credito = $movimiento_credito + $value_credito_dos->mo_partida;

                $start_y = $pdf->GetY();

                if ($start_y >= 260) {

                    $pdf->SetFont('', 'B', 8);
                    $pdf->setCellHeightRatio(1.5);
                    $pdf->SetY(262);
                    $pdf->MultiCell(112, 5, 'TOTAL GENERAL', 1, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(42, 5, '', 1, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(42, 5, number_format($movimiento_credito, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

                    $pdf->AddPage();

                    // reset font stretching  reset font spacing
                    $pdf->setFontStretching(100);
                    $pdf->setFontSpacing(0);
                    $pdf->SetLineWidth(0.150);
                    $pdf->setCellHeightRatio(2);

                    $pdf->SetFont('', 'B', 7);
                    $pdf->setCellHeightRatio(1.2);
                    $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->setCellHeightRatio(2);
                    $pdf->SetFont('', 'B', 9);
                    $pdf->setCellHeightRatio(1.2);
                    $pdf->MultiCell(100, 8, 'RESUMEN DE LOS CREDITOS PRESUPUESTARIOS A NIVEL DE SECTORES, PROYECTOS Y/O ACCIÓNES CENTRALIZADAS', 0, 'C', 0, 0, '', '', true);
                    $pdf->setCellHeightRatio(2);
                    $pdf->ln(8);
                    $pdf->SetFont('', 'B', 7);
                    $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(90, 5, '(EN BOLIVARES)', 0, 'C', 0, 0, '', '', true);
                    $pdf->ln(-10);
                    $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->ln(19);
                    $pdf->setCellHeightRatio(1.2);

                    $pdf->ln(21);
                    $pdf->SetFont('', 'B', 6);
                    $pdf->StartTransform();
                    $pdf->Rotate(90);
                    $pdf->MultiCell(21, 10, chr(10).'SECTOR', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(10);
                    $pdf->MultiCell(21, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(20);
                    $pdf->StopTransform();
                    $pdf->ln(-51);
                    $pdf->SetFont('', 'B', 9);
                    $pdf->MultiCell(20, 21, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(92, 21, chr(10).chr(10).'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(42, 21, chr(10).chr(10).'SUB-TOTAL', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(42, 21, chr(10).chr(10).'TOTAL', 1, 'C', 0, 0, '', '', true);
                    $pdf->ln(21);
                    $pdf->setCellHeightRatio(1);
                    $pdf->MultiCell(10, 214, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 214, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(92, 214, '', 1, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(42, 214, '', 1, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(42, 214, '', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(219);
                    $pdf->SetFont('', '', 7);
                    $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                    $pdf->ln(-219);
                    $pdf->ln(2);
                    $pdf->SetFont('', '', 7);
                    $pdf->setCellHeightRatio(1);

                    /*****Sector*****/
                    $pdf->SetFont('', 'B', 7);
                    $pdf->MultiCell(10, 5, $value_credito_uno->co_sector, 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(92, 5, mb_strtoupper($value_credito_uno->tx_descripcion, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(42, 5, '', 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(42, 5, number_format($value_credito_uno->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                    $pdf->ln(5);

                }

            }

        }

        $pdf->SetFont('', 'B', 8);
        $pdf->setCellHeightRatio(1.5);
        $pdf->SetY(262);
        $pdf->MultiCell(112, 5, 'TOTAL GENERAL', 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(42, 5, '', 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(42, 5, number_format($movimiento_credito, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->SetFont('', 'B', 9);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(90, 8, 'RESUMEN DE LOS CREDITOS PRESUPUESTARIOS A NIVEL DE PARTIDAS', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '(EN BOLIVARES)', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->setCellHeightRatio(1.2);

        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(20, 21, chr(10).chr(10).'PARTIDA', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(134, 21, chr(10).chr(10).'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(42, 21, chr(10).chr(10).'ASIGNACION PRESUPUESTARIA', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(21);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(20, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(134, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(42, 214, '', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(219);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-219);
        $pdf->ln(2);
        $pdf->SetFont('', '', 7);
        $pdf->setCellHeightRatio(1);

        $partida_credito = 0;

        $partida_credito_uno = vista_distribucion_presupuesto::join('mantenimiento.tab_partidas as t01', 't01.co_partida', '=', DB::raw('left(public.vista_distribucion_presupuesto.co_partida, 3)'))
        ->select('t01.co_partida', 'tx_nombre', DB::raw('sum(monto) as mo_partida'))
        ->where('ef_uno', '=', $ejercicio)
        ->where('t01.id_tab_ejercicio_fiscal', '=', $ejercicio)
        ->where('id_tab_tipo_ejecutor', '=', 1)
        ->groupBy('t01.co_partida')
        ->groupBy('tx_nombre')
        ->orderBy('t01.co_partida', 'ASC')
        ->get();

        foreach ($partida_credito_uno as $key => $value_pc_uno) {

            $pdf->SetFont('', '', 8);
            $pdf->MultiCell(20, 5, trim($value_pc_uno->co_partida), 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(134, 5, mb_strtoupper($value_pc_uno->tx_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(42, 5, number_format($value_pc_uno->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->ln(10);

            $partida_credito = $partida_credito + $value_pc_uno->mo_partida;

        }

        $pdf->SetFont('', 'B', 8);
        $pdf->setCellHeightRatio(1.5);
        $pdf->SetY(262);
        $pdf->MultiCell(154, 5, 'TOTAL GENERAL', 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(42, 5, number_format($partida_credito, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(1.2);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(95, 5, 'RESUMEN DEL COSTO DE LOS RECURSOS HUMANOS'.chr(10).'EN LA ENTIDAD FEDERAL CLASIFICADOS SEGUN SU TIPO', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->setCellHeightRatio(1.2);

        $ejercicio_anterior = $ejercicio-1;

        $pdf->SetFont('', 'B', 8);
        $pdf->MultiCell(20, 20, chr(10).chr(10).'TIPO DE PERSONAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(0);
        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(88, 5, $ejercicio_anterior, 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(88, 5, $ejercicio, 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(88, 5, 'EN BOLIVARES ANUALES', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(88, 5, 'EN BOLIVARES ANUALES', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->SetFont('', 'B', 5);
        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(24, 5, 'N° DE CARGOS', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(14, 10, chr(10).'SUELDO Y SALARIO', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(18, 10, chr(10).'COMPENSACION', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(15, 10, chr(10).'PRIMAS', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(17, 10, chr(10).'TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->SetFont('', 'B', 4);
        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, chr(10).'M', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, chr(10).'F', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, 'TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(-5);
        $pdf->SetFont('', 'B', 5);
        $pdf->MultiCell(108, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(24, 5, 'N° DE CARGOS', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(14, 10, chr(10).'SUELDO Y SALARIO', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(18, 10, chr(10).'COMPENSACION', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(15, 10, chr(10).'PRIMAS', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(17, 10, chr(10).'TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->SetFont('', 'B', 4);
        $pdf->MultiCell(108, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, chr(10).'M', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, chr(10).'F', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, 'TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(20, 210, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(14, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(18, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(15, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(17, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(14, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(18, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(15, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(17, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(220);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-220);
        $pdf->ln(2);
        $pdf->SetFont('', '', 7);
        $pdf->setCellHeightRatio(1);

        $tipo_personal = tab_tipo_personal::select('id', 'nu_codigo', 'de_tipo_personal', 'id_padre')
        ->where('in_detalle_jubilado_pensionado', '=', false)
        ->orderBy('nu_codigo', 'ASC')
        ->get();

        //**anio anterior***//
        $total_masculino_ant = 0;
        $total_femenino_ant = 0;
        $total_mf_ant = 0;
        $total_mo_sueldo_ant = 0;
        $total_mo_compensacion_ant = 0;
        $total_mo_primas_ant = 0;
        $total_sueldo_todo_ant = 0;

        //**anio actual***//

        $total_masculino = 0;
        $total_femenino = 0;
        $total_mf = 0;
        $total_mo_sueldo = 0;
        $total_mo_compensacion = 0;
        $total_mo_primas = 0;
        $total_sueldo_todo = 0;

        $pdf->setFontSpacing('-0.200');

        foreach ($tipo_personal as $key => $value_tipo_personal) {

            $pdf->SetFont('', '', 6);
            if($value_tipo_personal->id_padre==0) {

                $pdf->writeHTMLCell(20, 5, '', '', '<u><b>'.trim($value_tipo_personal->nu_codigo).' '.trim($value_tipo_personal->de_tipo_personal).'</b></u>', 0, 0, 0, true, 'L', true);

                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio_anterior)
                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                ->orderBy('id', 'ASC')
                ->get();

                if(!$clasificador_tipo->isEmpty()) {

                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {
                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 6);
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', '', 6);                        

                        $total_masculino_ant = $total_masculino_ant + $value_clasificador_tipo->nu_masculino;
                        $total_femenino_ant = $total_femenino_ant + $value_clasificador_tipo->nu_femenino;
                        $total_mf_ant = $total_mf_ant + $total_sexo;
                        $total_mo_sueldo_ant = $total_mo_sueldo_ant + $value_clasificador_tipo->mo_sueldo;
                        $total_mo_compensacion_ant = $total_mo_compensacion_ant + $value_clasificador_tipo->mo_compensacion;
                        $total_mo_primas_ant = $total_mo_primas_ant + $value_clasificador_tipo->mo_primas;
                        $total_sueldo_todo_ant = $total_sueldo_todo_ant + $total_sueldo;

                    }

                } else {

                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                }

                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                ->orderBy('id', 'ASC')
                ->get();

                if(!$clasificador_tipo->isEmpty()) {

                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 6);
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', '', 6);

                        $total_masculino = $total_masculino + $value_clasificador_tipo->nu_masculino;
                        $total_femenino = $total_femenino + $value_clasificador_tipo->nu_femenino;
                        $total_mf = $total_mf + $total_sexo;
                        $total_mo_sueldo = $total_mo_sueldo + $value_clasificador_tipo->mo_sueldo;
                        $total_mo_compensacion = $total_mo_compensacion + $value_clasificador_tipo->mo_compensacion;
                        $total_mo_primas = $total_mo_primas + $value_clasificador_tipo->mo_primas;
                        $total_sueldo_todo = $total_sueldo_todo + $total_sueldo;

                    }

                } else {

                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                }

            } else {

                $pdf->MultiCell(20, 5, trim($value_tipo_personal->nu_codigo).' '.trim($value_tipo_personal->de_tipo_personal), 0, 'L', 0, 0, '', '', true);

                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio_anterior)
                  ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                  ->orderBy('id', 'ASC')
                  ->get();

                if(!$clasificador_tipo->isEmpty()) {

                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                        $pdf->SetFont('', 'B', 6);
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        }else{
                        $pdf->SetFont('', '', 6);
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);    
                        }
                        
                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                        $total_masculino_ant = $total_masculino_ant + $value_clasificador_tipo->nu_masculino;
                        $total_femenino_ant = $total_femenino_ant + $value_clasificador_tipo->nu_femenino;
                        $total_mf_ant = $total_mf_ant + $total_sexo;
                        $total_mo_sueldo_ant = $total_mo_sueldo_ant + $value_clasificador_tipo->mo_sueldo;
                        $total_mo_compensacion_ant = $total_mo_compensacion_ant + $value_clasificador_tipo->mo_compensacion;
                        $total_mo_primas_ant = $total_mo_primas_ant + $value_clasificador_tipo->mo_primas;
                        
                        $total_sueldo_todo_ant = $total_sueldo_todo_ant + $total_sueldo;
                        }

                    }

                } else {

                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                }

                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                ->orderBy('id', 'ASC')
                ->get();

                if(!$clasificador_tipo->isEmpty()) {

                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                        $pdf->SetFont('', 'B', 6);    
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        }else{
                        $pdf->SetFont('', '', 6);
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);    
                        }
                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                        $total_masculino = $total_masculino + $value_clasificador_tipo->nu_masculino;
                        $total_femenino = $total_femenino + $value_clasificador_tipo->nu_femenino;
                        $total_mf = $total_mf + $total_sexo;
                        $total_mo_sueldo = $total_mo_sueldo + $value_clasificador_tipo->mo_sueldo;
                        $total_mo_compensacion = $total_mo_compensacion + $value_clasificador_tipo->mo_compensacion;
                        $total_mo_primas = $total_mo_primas + $value_clasificador_tipo->mo_primas;
                        
                        $total_sueldo_todo = $total_sueldo_todo + $total_sueldo;
                        }

                    }

                } else {

                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                }
            }
            $pdf->ln(10);

        }

        $pdf->SetFont('', 'B', 6);
        $pdf->setCellHeightRatio(1.5);
        $pdf->SetY(257);
        $pdf->MultiCell(20, 6, 'TOTALES', 1, 'R', 0, 0, '', '', true);
        $pdf->SetFont('', 'B', 5);
        $pdf->MultiCell(8, 6, number_format($total_masculino_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(8, 6, number_format($total_femenino_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(8, 6, number_format($total_mf_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(14, 6, number_format($total_mo_sueldo_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(18, 6, number_format($total_mo_compensacion_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(15, 6, number_format($total_mo_primas_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(17, 6, number_format($total_sueldo_todo_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->SetFont('', 'B', 5);
        $pdf->MultiCell(8, 6, number_format($total_masculino, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(8, 6, number_format($total_femenino, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(8, 6, number_format($total_mf, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(14, 6, number_format($total_mo_sueldo, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(18, 6, number_format($total_mo_compensacion, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(15, 6, number_format($total_mo_primas, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(17, 6, number_format($total_sueldo_todo, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

        $pdf->setFontSpacing('0');
        
/******Inicio PERSONAL JUBILADO Y PENSIONADO POR GENERO******/        
        
         $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(1.2);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(95, 5, 'PERSONAL JUBILADO Y PENSIONADO POR GENERO', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->setCellHeightRatio(1.2);

        $ejercicio_anterior = $ejercicio-1;

        $pdf->SetFont('', 'B', 8);
        $pdf->MultiCell(20, 20, chr(10).chr(10).'TIPO DE PERSONAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(0);
        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(88, 5, $ejercicio_anterior, 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(88, 5, $ejercicio, 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(88, 5, 'EN BOLIVARES ANUALES', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(88, 5, 'EN BOLIVARES ANUALES', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->SetFont('', 'B', 5);
        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(24, 5, 'N° DE CARGOS', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(14, 10, chr(10).'SUELDO Y SALARIO', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(18, 10, chr(10).'COMPENSACION', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(15, 10, chr(10).'PRIMAS', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(17, 10, chr(10).'TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->SetFont('', 'B', 4);
        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, chr(10).'M', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, chr(10).'F', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, 'TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(-5);
        $pdf->SetFont('', 'B', 5);
        $pdf->MultiCell(108, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(24, 5, 'N° DE CARGOS', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(14, 10, chr(10).'SUELDO Y SALARIO', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(18, 10, chr(10).'COMPENSACION', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(15, 10, chr(10).'PRIMAS', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(17, 10, chr(10).'TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->SetFont('', 'B', 4);
        $pdf->MultiCell(108, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, chr(10).'M', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, chr(10).'F', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 5, 'TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(20, 210, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(14, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(18, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(15, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(17, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(8, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(14, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(18, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(15, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(17, 210, '', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(220);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-220);
        $pdf->ln(2);
        $pdf->SetFont('', '', 7);
        $pdf->setCellHeightRatio(1);

        $tipo_personal = tab_tipo_personal::select('id', 'nu_codigo', 'de_tipo_personal', 'id_padre')
        ->whereIn('id', [13, 14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30])
        ->orderBy('nu_codigo', 'ASC')
        ->get();

        //**anio anterior***//
        $total_masculino_ant = 0;
        $total_femenino_ant = 0;
        $total_mf_ant = 0;
        $total_mo_sueldo_ant = 0;
        $total_mo_compensacion_ant = 0;
        $total_mo_primas_ant = 0;
        $total_sueldo_todo_ant = 0;

        //**anio actual***//

        $total_masculino = 0;
        $total_femenino = 0;
        $total_mf = 0;
        $total_mo_sueldo = 0;
        $total_mo_compensacion = 0;
        $total_mo_primas = 0;
        $total_sueldo_todo = 0;

        $pdf->setFontSpacing('-0.200');

        foreach ($tipo_personal as $key => $value_tipo_personal) {

            $pdf->SetFont('', '', 6);
            if($value_tipo_personal->id_padre==0) {

                $pdf->writeHTMLCell(20, 5, '', '', '<u><b>'.trim($value_tipo_personal->nu_codigo).' '.trim($value_tipo_personal->de_tipo_personal).'</b></u>', 0, 0, 0, true, 'L', true);

                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio_anterior)
                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                ->orderBy('id', 'ASC')
                ->get();

                if(!$clasificador_tipo->isEmpty()) {

                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {
                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 6);
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', '', 6);                        

                        $total_masculino_ant = $total_masculino_ant + $value_clasificador_tipo->nu_masculino;
                        $total_femenino_ant = $total_femenino_ant + $value_clasificador_tipo->nu_femenino;
                        $total_mf_ant = $total_mf_ant + $total_sexo;
                        $total_mo_sueldo_ant = $total_mo_sueldo_ant + $value_clasificador_tipo->mo_sueldo;
                        $total_mo_compensacion_ant = $total_mo_compensacion_ant + $value_clasificador_tipo->mo_compensacion;
                        $total_mo_primas_ant = $total_mo_primas_ant + $value_clasificador_tipo->mo_primas;
                        $total_sueldo_todo_ant = $total_sueldo_todo_ant + $total_sueldo;

                    }

                } else {

                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                }

                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                ->orderBy('id', 'ASC')
                ->get();

                if(!$clasificador_tipo->isEmpty()) {

                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 6);
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', '', 6);

                        $total_masculino = $total_masculino + $value_clasificador_tipo->nu_masculino;
                        $total_femenino = $total_femenino + $value_clasificador_tipo->nu_femenino;
                        $total_mf = $total_mf + $total_sexo;
                        $total_mo_sueldo = $total_mo_sueldo + $value_clasificador_tipo->mo_sueldo;
                        $total_mo_compensacion = $total_mo_compensacion + $value_clasificador_tipo->mo_compensacion;
                        $total_mo_primas = $total_mo_primas + $value_clasificador_tipo->mo_primas;
                        $total_sueldo_todo = $total_sueldo_todo + $total_sueldo;

                    }

                } else {

                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                }

            } else {
                
                if($value_tipo_personal->id==13 || $value_tipo_personal->id==14 ){
                $pdf->SetFont('', 'B', 6);    
                $pdf->MultiCell(20, 5, trim($value_tipo_personal->nu_codigo).' '.trim($value_tipo_personal->de_tipo_personal), 0, 'L', 0, 0, '', '', true);
                }else{
                 $pdf->SetFont('', '', 6);   
                 $pdf->MultiCell(20, 5, trim($value_tipo_personal->nu_codigo).' '.trim($value_tipo_personal->de_tipo_personal), 0, 'L', 0, 0, '', '', true);
                }
                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio_anterior)
                  ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                  ->orderBy('id', 'ASC')
                  ->get();

                if(!$clasificador_tipo->isEmpty()) {

                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                        $pdf->SetFont('', 'B', 6);
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        }else{
                        $pdf->SetFont('', 'B', 6);
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);    
                        }
                        
                        if($value_tipo_personal->id==13 || $value_tipo_personal->id==14 ){
                            
                        }else{
                        $total_masculino_ant = $total_masculino_ant + $value_clasificador_tipo->nu_masculino;
                        $total_femenino_ant = $total_femenino_ant + $value_clasificador_tipo->nu_femenino;
                        $total_mf_ant = $total_mf_ant + $total_sexo;
                        $total_mo_sueldo_ant = $total_mo_sueldo_ant + $value_clasificador_tipo->mo_sueldo;
                        $total_mo_compensacion_ant = $total_mo_compensacion_ant + $value_clasificador_tipo->mo_compensacion;
                        $total_mo_primas_ant = $total_mo_primas_ant + $value_clasificador_tipo->mo_primas;
                        
                        $total_sueldo_todo_ant = $total_sueldo_todo_ant + $total_sueldo;
                        }

                    }

                } else {

                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                }

                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                ->orderBy('id', 'ASC')
                ->get();

                if(!$clasificador_tipo->isEmpty()) {

                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                        $pdf->SetFont('', 'B', 6);    
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                        }else{
                        $pdf->SetFont('', 'B', 6);
                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);    
                        }
                        if($value_tipo_personal->id==13 || $value_tipo_personal->id==14 ){
                            
                        }else{
                        $total_masculino = $total_masculino + $value_clasificador_tipo->nu_masculino;
                        $total_femenino = $total_femenino + $value_clasificador_tipo->nu_femenino;
                        $total_mf = $total_mf + $total_sexo;
                        $total_mo_sueldo = $total_mo_sueldo + $value_clasificador_tipo->mo_sueldo;
                        $total_mo_compensacion = $total_mo_compensacion + $value_clasificador_tipo->mo_compensacion;
                        $total_mo_primas = $total_mo_primas + $value_clasificador_tipo->mo_primas;
                        
                        $total_sueldo_todo = $total_sueldo_todo + $total_sueldo;
                        }

                    }

                } else {

                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                }
            }
            $pdf->ln(10);

        }

        $pdf->SetFont('', 'B', 6);
        $pdf->setCellHeightRatio(1.5);
        $pdf->SetY(257);
        $pdf->MultiCell(20, 6, 'TOTALES', 1, 'R', 0, 0, '', '', true);
        $pdf->SetFont('', 'B', 5);
        $pdf->MultiCell(8, 6, number_format($total_masculino_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(8, 6, number_format($total_femenino_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(8, 6, number_format($total_mf_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(14, 6, number_format($total_mo_sueldo_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(18, 6, number_format($total_mo_compensacion_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(15, 6, number_format($total_mo_primas_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(17, 6, number_format($total_sueldo_todo_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->SetFont('', 'B', 5);
        $pdf->MultiCell(8, 6, number_format($total_masculino, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(8, 6, number_format($total_femenino, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(8, 6, number_format($total_mf, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(14, 6, number_format($total_mo_sueldo, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(18, 6, number_format($total_mo_compensacion, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(15, 6, number_format($total_mo_primas, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(17, 6, number_format($total_sueldo_todo, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

        $pdf->setFontSpacing('0');
        
        
        
/******Inicio ENTES DESCENTRALIZADOS ADSCRITOS A LA ENTIDAD FEDERALS******/ 
        
        
         $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(1.2);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(95, 5, 'RELACIÓN DE ENTES DESCENTRALIZADOS ADSCRITOS A LA ENTIDAD FEDERAL', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->setCellHeightRatio(1.2);

        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(45, 21, chr(10).chr(10).'NOMBRE DEL ENTE', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72, 21, chr(10).chr(10).'OBJETO DE CREACIÓN', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(37, 21, chr(10).chr(10).'ASIGNACIÓN PRESUPUESTARIA ANUAL (Bs.)', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(42, 21, chr(10).chr(10).'OBSERVACIONES', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(21);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(45, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(37, 214, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(42, 214, '', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(219);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-219);
        $pdf->ln(2);
        $pdf->SetFont('', '', 7);
        $pdf->setCellHeightRatio(1);        
        
        
            $ac_entes = tab_ac::select('public.t46_acciones_centralizadas.id_ejecutor', 'tx_ejecutor_poa', 'objeto_creacion', 'ac_observaciones',DB::raw('sum(public.t46_acciones_centralizadas.monto_calc) as mo_ente'))
            ->join('mantenimiento.tab_ejecutores as t03', 't03.id_ejecutor', '=', 'public.t46_acciones_centralizadas.id_ejecutor')
            ->where('id_ejercicio', '=', $ejercicio)
            ->where('id_tab_tipo_ejecutor', '=', 2)
            ->groupBy('public.t46_acciones_centralizadas.id_ejecutor')
            ->groupBy('tx_ejecutor_poa')
            ->groupBy('objeto_creacion')
            ->groupBy('ac_observaciones')
            ->orderBy('public.t46_acciones_centralizadas.id_ejecutor', 'ASC')
            ->get(); 
            

        
        foreach ($ac_entes as $key => $value_ac_entes) {
            
//         $pdf->SetWidths(array(70,42,42,42));         
//         $pdf->SetAligns(array("L","L","R","L")); 
//         $pdf->SetFont('', '', 8); 
//         $pdf->Row(array(utf8_decode(trim($value_ac_entes->id_ejecutor).' - '.trim($value_ac_entes->tx_ejecutor_poa)),utf8_decode(trim($value_ac_entes->objeto_creacion)),number_format($value_ac_entes->mo_ente, 0, ',', '.'),trim($value_ac_entes->ac_observaciones)),0,0);            
//
            $pdf->SetFont('', '', 6);
            $pdf->MultiCell(45, 5, trim($value_ac_entes->id_ejecutor).' - '.trim($value_ac_entes->tx_ejecutor_poa), 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(72, 5, mb_strtoupper($value_ac_entes->objeto_creacion, 'UTF-8'), 0, 'J', 0, 0, '', '', true);
            $pdf->MultiCell(37, 5, number_format($value_ac_entes->mo_ente, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(42, 5, trim($value_ac_entes->ac_observaciones), 0, 'J', 0, 0, '', '', true);
            $ln_c = strlen($value_ac_entes->objeto_creacion);
            $ln_o = strlen($value_ac_entes->ac_observaciones);
            
            if($ln_o>$ln_c){
            $ln = $ln_o;    
            }else{
            $ln = $ln_c;    
            }
            
            if($ln>600){
            $pdf->Ln(60);    
            }else{
            if($ln>400){
            $pdf->Ln(40);    
            }else{
            if($ln>200){
            $pdf->Ln(25);    
            }else{
            if($ln>100){
            $pdf->Ln(20);    
            }else{
            $pdf->Ln(15);   
            }
            }                
            }                
            }
            
                $start_y = $pdf->GetY();

                $culminado = false;

                if ($start_y >= 235) {

         $pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(1.2);
        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(95, 5, 'RELACIÓN DE ENTES DESCENTRALIZADOS ADSCRITOS A LA ENTIDAD FEDERAL', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->setCellHeightRatio(1.2);

        $pdf->SetFont('', 'B', 9);
        $pdf->MultiCell(45, 21, chr(10).chr(10).'NOMBRE DEL ENTE', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72, 21, chr(10).chr(10).'OBJETO DE CREACIÓN', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(37, 21, chr(10).chr(10).'ASIGNACIÓN PRESUPUESTARIA ANUAL (Bs.)', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(42, 21, chr(10).chr(10).'OBSERVACIONES', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(21);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(45, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72, 214, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(37, 214, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(42, 214, '', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(219);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-219);
        $pdf->ln(2);
        $pdf->SetFont('', '', 6);
        $pdf->setCellHeightRatio(1);  

                }            


        }

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);     
        
        
        

     

        /******Inicio ENTIDAD FEDERAL POR ESCALA DE SUELDOS******/

        $escala_salarial_grupo = tab_escala_salarial::join('mantenimiento.tab_tipo_empleado as t01', 't01.id', '=', 'mantenimiento.tab_escala_salarial.id_tab_tipo_empleado')
        ->select('t01.id', 'de_tipo_empleado')
        ->where('id_tab_ejercicio_fiscal', '=', $ejercicio)
        ->groupBy('t01.id')
        ->groupBy('de_tipo_empleado')
        ->orderBy('t01.id', 'ASC')
        ->get();

        foreach($escala_salarial_grupo as $key => $value_escala_salarial_grupo) {

            // reset font stretching  reset font spacing
            $pdf->setFontStretching(100);
            $pdf->setFontSpacing(0);
            $pdf->SetLineWidth(0.150);
            $pdf->setCellHeightRatio(2);

            $pdf->AddPage();

            // reset font stretching  reset font spacing
            $pdf->setFontStretching(100);
            $pdf->setFontSpacing(0);
            $pdf->SetLineWidth(0.150);
            $pdf->setCellHeightRatio(2);

            $pdf->SetFont('', 'B', 7);
            $pdf->setCellHeightRatio(1.2);
            $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->setCellHeightRatio(1.2);
            $pdf->SetFont('', 'B', 9);
            if($value_escala_salarial_grupo->de_tipo_empleado=='OBREROS'){
            $pdf->MultiCell(95, 5, 'RESUMEN DE LOS RECURSOS HUMANOS DE LA ENTIDAD FEDERAL'.chr(10).'POR ESCALA DE SALARIOS', 0, 'C', 0, 0, '', '', true);
            }else{
            $pdf->MultiCell(95, 5, 'RESUMEN DE LOS RECURSOS HUMANOS DE LA ENTIDAD FEDERAL'.chr(10).'POR ESCALA DE SUELDOS', 0, 'C', 0, 0, '', '', true);    
            }
            $pdf->setCellHeightRatio(2);
            $pdf->ln(8);
            $pdf->SetFont('', 'B', 7);
            $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->ln(-10);
            $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
            $pdf->ln(19);
            $pdf->setCellHeightRatio(1.2);

            $pdf->SetFont('', 'B', 8);
            $pdf->MultiCell(20, 20, chr(10).chr(10).'GRUPO', 1, 'C', 0, 0, '', '', true);
            $pdf->SetFont('', 'B', 9);
            $pdf->setFontSpacing('0.254');
            if($value_escala_salarial_grupo->de_tipo_empleado=='OBREROS'){
            $pdf->MultiCell(88, 20, chr(10).chr(10).'ESCALA DE SALARIOS', 1, 'C', 0, 0, '', '', true);
            }else{
            $pdf->MultiCell(88, 20, chr(10).chr(10).'ESCALA DE SUELDOS', 1, 'C', 0, 0, '', '', true);    
            }
            $pdf->setFontSpacing('0');
            $pdf->SetFont('', 'B', 8);
            $pdf->MultiCell(88, 5, 'ESTIMADO PARA '.$ejercicio, 1, 'C', 0, 0, '', '', true);
            $pdf->ln(5);
            $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(88, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(88, 5, $value_escala_salarial_grupo->de_tipo_empleado, 1, 'C', 0, 0, '', '', true);
            $pdf->ln(5);
            $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(88, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(68, 5, 'Nº DE CARGO', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(20, 10, chr(10).'MONTO Bs.', 1, 'C', 0, 0, '', '', true);
            $pdf->ln(5);
            $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(88, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(22, 5, 'Masculino', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(22, 5, 'Femenino', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(24, 5, 'Total', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->ln(5);
            $pdf->setCellHeightRatio(1);
            $pdf->MultiCell(20, 215, '', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(88, 215, '', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(22, 215, '', 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(22, 215, '', 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(24, 215, '', 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(20, 215, '', 1, 'L', 0, 0, '', '', true);
            $pdf->ln(220);
            $pdf->SetFont('', '', 7);
            $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
            $pdf->ln(-220);
            $pdf->ln(2);
            $pdf->SetFont('', '', 7);
            $pdf->setCellHeightRatio(1);

            $escala_salarial = tab_escala_salarial::where('id_tab_ejercicio_fiscal', '=', $ejercicio)
            ->where('id_tab_tipo_empleado', '=', $value_escala_salarial_grupo->id)
            ->orderBy('id', 'ASC')
            ->get();

            $total_mo_sexo_m = 0;
            $total_mo_sexo_f = 0;
            $total_mo_sexo_mf = 0;
            $total_mo_todo = 0;

            $pdf->SetFont('', '', 8);

            foreach ($escala_salarial as $key => $value_escala_salarial) {

                $total_sexo_mf = $value_escala_salarial->nu_masculino + $value_escala_salarial->nu_femenino;

                $pdf->MultiCell(20, 215, $value_escala_salarial->de_grupo, 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(88, 215, $value_escala_salarial->de_escala_salarial, 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(22, 215, number_format($value_escala_salarial->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(22, 215, number_format($value_escala_salarial->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(24, 215, number_format($total_sexo_mf, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(20, 215, number_format($value_escala_salarial->mo_escala_salarial, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                $pdf->ln(6);

                $total_mo_sexo_m = $total_mo_sexo_m + $value_escala_salarial->nu_masculino;
                $total_mo_sexo_f = $total_mo_sexo_f + $value_escala_salarial->nu_femenino;
                $total_mo_sexo_mf = $total_mo_sexo_m + $total_mo_sexo_f;
                $total_mo_todo = $total_mo_todo + $value_escala_salarial->mo_escala_salarial;

            }

            $pdf->SetFont('', 'B', 8);
            $pdf->setCellHeightRatio(1.5);
            $pdf->SetY(262);
            $pdf->MultiCell(108, 6, 'TOTALES', 1, 'R', 0, 0, '', '', true);
            $pdf->SetFont('', 'B', 8);
            $pdf->MultiCell(22, 6, number_format($total_mo_sexo_m, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(22, 6, number_format($total_mo_sexo_f, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(24, 6, number_format($total_mo_sexo_mf, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(20, 6, number_format($total_mo_todo, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

        }

        /******Inicio de Objetivos Sectoriales******/

        $objetivos = tab_objetivo_sectorial::join('mantenimiento.tab_sectores as t01', 't01.id', '=', 'mantenimiento.tab_objetivo_sectorial.id_tab_sectores')
          ->select(
              'mantenimiento.tab_objetivo_sectorial.id',
              'id_tab_ejercicio_fiscal',
              'id_tab_sectores',
              'de_objetivo_sectorial',
              'tx_codigo',
              'tx_descripcion'
          )
          ->where('id_tab_ejercicio_fiscal', '=', $ejercicio)
          ->where('mantenimiento.tab_objetivo_sectorial.in_activo', '=', true)
          ->orderBy('tx_codigo', 'ASC')
          ->get();

        foreach ($objetivos as $key => $value) {

            $pdf->AddPage();

            // reset font stretching  reset font spacing
            $pdf->setFontStretching(100);
            $pdf->setFontSpacing(0);
            $pdf->SetLineWidth(0.150);
            $pdf->setCellHeightRatio(2);

            /******Portada Titulo Sectores*********/
            $pdf->SetAlpha(0.3);
            $pdf->Image(public_path().'/images/escudo_zulia.png', 15, 40, 190, 190, 'PNG', '', '', false, 170, '', false, false, 0);
            $pdf->ln(30);
            $pdf->setAlpha(1);
            $pdf->SetFont('', '', 8);

            // reset font stretching  reset font spacing
            $pdf->setFontStretching(100);
            $pdf->setFontSpacing(1);
            //
            $pdf->SetY(15);
            $pdf->SetFont('', 'B', 14);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(190, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
            $pdf->ln(230);
            $pdf->SetFont('', 'B', 12);
            //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
            $pdf->writeHTML('<b><u>SECTOR: '.$value->tx_codigo.'<u/></b>', true, false, true, false, 'R');
            $pdf->ln(0);
            $pdf->MultiCell(195, 5, mb_strtoupper($value->tx_descripcion, 'UTF-8'), 0, 'R', 0, 0, '', '', true);
            $pdf->ln(10);
            // set border width
            $pdf->SetLineWidth(0.508);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->setCellHeightRatio(0);
            $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
            $pdf->ln(2);
            $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
            // reset font stretching  reset font spacing
            $pdf->setFontStretching(100);
            $pdf->setFontSpacing(0);
            $pdf->SetLineWidth(0.150);
            $pdf->setCellHeightRatio(2);

            $pdf->AddPage();

            // reset font stretching  reset font spacing
            $pdf->setFontStretching(100);
            $pdf->setFontSpacing(0);
            $pdf->SetLineWidth(0.150);
            $pdf->setCellHeightRatio(2);

            $pdf->SetFont('', 'B', 7);
            $pdf->setCellHeightRatio(1.2);
            $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->setCellHeightRatio(2);
            $pdf->SetFont('', 'B', 11);
            $pdf->MultiCell(90, 5, chr(10).'OBJETIVOS SECTORIALES', 0, 'C', 0, 0, '', '', true);
            $pdf->ln(8);
            $pdf->SetFont('', 'B', 7);
            $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->ln(-10);
            $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
            $pdf->ln(19);
            $pdf->setCellHeightRatio(1.2);

            $pdf->MultiCell(20, 5, '', 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(20, 5, 'CODIGO', 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(156, 5, 'DENOMINACION', 1, 'L', 0, 0, '', '', true);
            $pdf->ln(5);
            $pdf->MultiCell(20, 5, 'SECTOR:', 1, 'C', 0, 0, '', '', true);
            $pdf->SetFont('', '', 8);
            $pdf->MultiCell(20, 5, $value->tx_codigo, 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(156, 5, mb_strtoupper($value->tx_descripcion, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
            $pdf->ln(5);
            $pdf->SetFont('', 'B', 9);
            $pdf->MultiCell(196, 10, chr(10).'DESCRIPCIÓN', 1, 'C', 0, 0, '', '', true);
            $pdf->ln(10);
            $pdf->setCellHeightRatio(1);
            $pdf->MultiCell(196, 219, '', 1, 'C', 0, 0, '', '', true);
            $pdf->ln(220);
            $pdf->SetFont('', '', 7);
            $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
            $pdf->ln(-220);
            $pdf->ln(2);
            $pdf->SetFont('', '', 7);
            $pdf->setCellHeightRatio(1);

            $pdf->MultiCell(10, 5, '', 0, 'L', 0, 0, '', '', true);
            $pdf->setCellHeightRatio(2);
            $pdf->writeHTMLCell(176, 5, '', '', nl2br($value->de_objetivo_sectorial), 0, 0, 0, true, 'J', true);
            $pdf->setCellHeightRatio(1.2);

            // reset font stretching  reset font spacing
            $pdf->setFontStretching(100);
            $pdf->setFontSpacing(0);
            $pdf->SetLineWidth(0.150);
            $pdf->setCellHeightRatio(2);

//            var_dump(strlen($value->de_objetivo_sectorial));
            if(strlen($value->de_objetivo_sectorial)>4000){
            $pdf->AddPage();
            $pdf->MultiCell(196, 255, '', 1, 'C', 0, 0, '', '', true);
            $pdf->SetY(265);
            $pdf->SetFont('', '', 7);
            $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
            $pdf->ln(-220);
            $pdf->ln(2);
            $pdf->SetFont('', '', 7);
            $pdf->setCellHeightRatio(1);            
            
            
            }
            /*Listado de Proyectos*/
            // reset font stretching  reset font spacing
            $pdf->setFontStretching(100);
            $pdf->setFontSpacing(0);

            $pr_lista = tab_proyecto::join('mantenimiento.tab_sectores as t01', 't01.tx_codigo', '=', 'public.t26_proyectos.clase_sector')
            ->join('mantenimiento.tab_ejecutores as t02', 't02.id_ejecutor', '=', 'public.t26_proyectos.id_ejecutor')
            ->select(
                'id_proyecto',
                'nombre',
                'clase_sector',
                'tx_descripcion',
                'public.t26_proyectos.id_ejecutor',
                'tx_ejecutor',
                'public.t26_proyectos.descripcion'
            )
            ->where('id_ejercicio', '=', $ejercicio)
            ->where('clase_sector', '=', $value->tx_codigo)
            ->where('edo_reg', '=', true)
            ->orderBy('id_proyecto', 'ASC')
            ->get();

            foreach ($pr_lista as $key => $value_pr) {

                // reset font stretching  reset font spacing
                $pdf->setFontStretching(100);
                $pdf->setFontSpacing(0);
                $pdf->SetLineWidth(0.150);
                $pdf->setCellHeightRatio(2);

                $pdf->AddPage();
                /******Portada Titulo Sectores*********/
                $pdf->SetAlpha(0.3);
                $pdf->Image(public_path().'/images/escudo_zulia.png', 15, 40, 190, 190, 'PNG', '', '', false, 170, '', false, false, 0);
                $pdf->ln(30);
                $pdf->setAlpha(1);
                $pdf->SetFont('', '', 8);

                // reset font stretching  reset font spacing
                $pdf->setFontStretching(100);
                $pdf->setFontSpacing(1);
                //
                $pdf->SetY(15);
                $pdf->SetFont('', 'B', 14);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->MultiCell(190, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                $pdf->ln(200);
                $pdf->SetFont('', 'B', 12);
                //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
                $pdf->writeHTML('<b><u>SECTOR: '.$value_pr->clase_sector.'<u/></b>', true, false, true, false, 'L');
                $pdf->ln(0);
                $pdf->writeHTML('<b><u>PROYECTO Y/O ACCIÓN CENTRALIZADA: '.substr($value_pr->id_proyecto, -3).'<u/></b>', true, false, true, false, 'L');
                $pdf->ln(0);
                $pdf->MultiCell(195, 5, mb_strtoupper($value_pr->nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                $pdf->ln(30);
                // set border width
                $pdf->SetLineWidth(0.508);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetFillColor(0, 0, 0);
                $pdf->setCellHeightRatio(0);
                $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
                $pdf->ln(2);
                $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
                // reset font stretching  reset font spacing
                $pdf->setFontStretching(100);
                $pdf->setFontSpacing(0);
                $pdf->SetLineWidth(0.150);
                $pdf->setCellHeightRatio(2);

                $pdf->AddPage();

                // reset font stretching  reset font spacing
                $pdf->setFontStretching(100);
                $pdf->setFontSpacing(0);
                $pdf->SetLineWidth(0.150);
                $pdf->setCellHeightRatio(2);

                $pdf->SetFont('', 'B', 7);
                $pdf->setCellHeightRatio(1.2);
                $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->setCellHeightRatio(2);
                $pdf->SetFont('', 'B', 11);
                $pdf->MultiCell(90, 5, 'DESCRIPCIÓN DEL PROYECTO Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
                $pdf->ln(8);
                $pdf->SetFont('', 'B', 7);
                $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->ln(-10);
                $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(19);
                $pdf->setCellHeightRatio(1.2);

                $pdf->MultiCell(40, 5, '', 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(20, 5, 'CODIGO', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(136, 5, 'DENOMINACION', 1, 'L', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->MultiCell(40, 5, 'SECTOR:', 1, 'C', 0, 0, '', '', true);
                $pdf->SetFont('', '', 8);
                $pdf->MultiCell(20, 5, $value_pr->clase_sector, 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(136, 5, mb_strtoupper($value_pr->tx_descripcion, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->SetFont('', 'B', 8);
                $pdf->MultiCell(40, 10, 'PROYECTO Y/O ACCÓN CENTRALIZADA:', 1, 'C', 0, 0, '', '', true);
                $pdf->SetFont('', '', 8);
                $pdf->MultiCell(20, 10, substr($value_pr->id_proyecto, -3), 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(136, 10, mb_strtoupper($value_pr->nombre, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                $pdf->ln(10);
                $pdf->SetFont('', 'B', 8);
                $pdf->MultiCell(40, 5, 'UNIDAD EJECUTORA:', 1, 'C', 0, 0, '', '', true);
                $pdf->SetFont('', '', 8);
                $pdf->MultiCell(20, 5, $value_pr->id_ejecutor, 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(136, 5, mb_strtoupper($value_pr->tx_ejecutor, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->SetFont('', 'B', 9);
                $pdf->MultiCell(196, 10, chr(10).'DESCRIPCIÓN', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(10);
                $pdf->setCellHeightRatio(1);
                $pdf->MultiCell(196, 204, '', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(207);
                $pdf->SetFont('', '', 7);
                $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                $pdf->ln(-207);
                $pdf->ln(2);
                $pdf->SetFont('', '', 7);
                $pdf->setCellHeightRatio(1);

                $pdf->MultiCell(10, 5, '', 0, 'L', 0, 0, '', '', true);
                $pdf->setCellHeightRatio(2);
                $pdf->writeHTMLCell(176, 5, '', '', nl2br($value_pr->descripcion), 0, 0, 0, true, 'L', true);
                $pdf->setCellHeightRatio(1.2);

                // reset font stretching  reset font spacing
                $pdf->setFontStretching(100);
                $pdf->setFontSpacing(0);
                $pdf->SetLineWidth(0.150);
                $pdf->setCellHeightRatio(2);

                $pdf->AddPage();

                // reset font stretching  reset font spacing
                $pdf->setFontStretching(100);
                $pdf->setFontSpacing(0);
                $pdf->SetLineWidth(0.150);
                $pdf->setCellHeightRatio(2);

                $pdf->SetFont('', 'B', 7);
                $pdf->setCellHeightRatio(1.2);
                $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->setCellHeightRatio(2);
                $pdf->SetFont('', 'B', 10);
                $pdf->setCellHeightRatio(1);
                $pdf->MultiCell(90, 5, 'CREDITOS PRESUPUESTARIOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
                $pdf->setCellHeightRatio(2);
                $pdf->ln(8);
                $pdf->SetFont('', 'B', 7);
                $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(90, 5, '(EN BOLÍVARES) ', 0, 'C', 0, 0, '', '', true);
                $pdf->ln(-10);
                $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(19);
                $pdf->setCellHeightRatio(1.2);

                $pdf->MultiCell(40, 5, '', 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(20, 5, 'CODIGO', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(136, 5, 'DENOMINACION', 1, 'L', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->SetFont('', 'B', 7);
                $pdf->MultiCell(40, 8, 'SECTOR / PROYECTO Y/O A. CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                $pdf->SetFont('', '', 8);
                $pdf->MultiCell(20, 8, $value_pr->clase_sector.'.'.substr($value_pr->id_proyecto, -3), 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(136, 8, mb_strtoupper($value_pr->nombre, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                $pdf->ln(8);
                $pdf->SetFont('', 'B', 8);
                $pdf->MultiCell(156, 5, 'PARTIDA', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(40, 10, 'ASIGNACION PRESUPUESTARIA', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->SetFont('', 'B', 8);
                $pdf->MultiCell(40, 5, 'CODIGO', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(116, 5, 'DENOMINACION', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->setCellHeightRatio(1);
                //$pdf->MultiCell(196, 216, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(40, 207, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(116, 207, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(40, 207, '', 1, 'L', 0, 0, '', '', true);
                $pdf->ln(219);
                $pdf->SetFont('', '', 7);
                $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                $pdf->ln(-219);
                $pdf->ln(2);
                $pdf->SetFont('', '', 7);
                $pdf->setCellHeightRatio(1);
                $start_y = 0;
                $movimiento = 0;


                $pr_lista_partida = tab_proyecto_ae_partida::join('public.t39_proyecto_acc_espec as t01', 't01.co_proyecto_acc_espec', '=', 'public.t42_proyecto_acc_espec_partida.co_proyecto_acc_espec')
                ->join('mantenimiento.tab_partidas as t02', 't02.co_partida', '=', DB::raw('left(public.t42_proyecto_acc_espec_partida.co_partida, 3)'))
                ->select(
                    DB::raw('t02.co_partida as partida'),
                    'tx_nombre',
                    DB::raw('sum(public.t42_proyecto_acc_espec_partida.nu_monto) as mo_partida')
                )
                ->where('t02.id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('t01.id_proyecto', '=', $value_pr->id_proyecto)
                ->where('public.t42_proyecto_acc_espec_partida.edo_reg', '=', true)
                ->where('t01.edo_reg', '=', true)
                ->groupBy('partida')
                ->groupBy('tx_nombre')
                ->orderBy('partida', 'ASC')
                ->get();

                $total_partida_pr = 0;

                foreach ($pr_lista_partida as $key => $value_pr_partida) {

                    $pdf->MultiCell(40, 5, $value_pr_partida->partida, 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(116, 5, mb_strtoupper($value_pr_partida->tx_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(40, 5, number_format($value_pr_partida->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                    $pdf->ln(5);

                    $total_partida_pr = $total_partida_pr + $value_pr_partida->mo_partida;

                }

                $pdf->SetFont('', 'B', 8);
                $pdf->setCellHeightRatio(1.5);
                $pdf->SetY(257);
                $pdf->MultiCell(156, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(40, 5, number_format($total_partida_pr, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

            }

            // reset font stretching  reset font spacing
            $pdf->setFontStretching(100);
            $pdf->setFontSpacing(0);
            $pdf->SetLineWidth(0.150);
            $pdf->setCellHeightRatio(2);

            $ac_lista = tab_ac::join('mantenimiento.tab_sectores as t01', 't01.id', '=', 'public.t46_acciones_centralizadas.id_subsector')
            ->join('mantenimiento.tab_ac_predefinida as t02', 't02.id', '=', 'public.t46_acciones_centralizadas.id_accion')
            ->join('mantenimiento.tab_ejecutores as t03', 't03.id_ejecutor', '=', 'public.t46_acciones_centralizadas.id_ejecutor')
            ->select('id_accion', 'de_nombre', 'nu_original', 'co_sector')
            ->where('id_ejercicio', '=', $ejercicio)
            ->where('co_sector', '=', $value->tx_codigo)
            ->where('id_tab_tipo_ejecutor', '=', 1)
            ->groupBy('id_accion')
            ->groupBy('de_nombre')
            ->groupBy('nu_original')
            ->groupBy('co_sector')
            ->orderBy('co_sector', 'ASC')
            ->orderBy('id_accion', 'ASC')
            ->get();

            foreach ($ac_lista as $key => $value_ac) {

                $pdf->AddPage();
                /******Portada Titulo Sectores*********/
                $pdf->SetAlpha(0.3);
                $pdf->Image(public_path().'/images/escudo_zulia.png', 15, 40, 190, 190, 'PNG', '', '', false, 170, '', false, false, 0);
                $pdf->ln(30);
                $pdf->setAlpha(1);
                $pdf->SetFont('', '', 8);

                // reset font stretching  reset font spacing
                $pdf->setFontStretching(100);
                $pdf->setFontSpacing(1);
                //
                $pdf->SetY(15);
                $pdf->SetFont('', 'B', 14);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->MultiCell(190, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                $pdf->ln(210);
                $pdf->SetFont('', 'B', 12);
                //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
                $pdf->writeHTML('<b><u>SECTOR: '.$value_ac->co_sector.'<u/></b>', true, false, true, false, 'L');
                $pdf->ln(0);
                $pdf->writeHTML('<b><u>PROYECTO Y/O ACCIÓN CENTRALIZADA: '.$value_ac->nu_original.'<u/></b>', true, false, true, false, 'L');
                $pdf->ln(0);
                $pdf->MultiCell(195, 5, mb_strtoupper($value_ac->de_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                $pdf->ln(20);
                // set border width
                $pdf->SetLineWidth(0.508);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetFillColor(0, 0, 0);
                $pdf->setCellHeightRatio(0);
                $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
                $pdf->ln(2);
                $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
                // reset font stretching  reset font spacing
                $pdf->setFontStretching(100);
                $pdf->setFontSpacing(0);
                $pdf->SetLineWidth(0.150);
                $pdf->setCellHeightRatio(2);

                $valor_sector = trim($value_ac->co_sector);
                $valor_original = trim($value_ac->nu_original);
                if($valor_sector = '01') {
                    if($valor_original == 51) {

                        $pdf->AddPage();

                        // reset font stretching  reset font spacing
                        $pdf->setFontStretching(100);
                        $pdf->setFontSpacing(0);
                        $pdf->SetLineWidth(0.150);
                        $pdf->setCellHeightRatio(2);

                        $pdf->SetFont('', 'B', 7);
                        $pdf->setCellHeightRatio(1.2);
                        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->setCellHeightRatio(2);
                        $pdf->SetFont('', 'B', 10);
                        $pdf->setCellHeightRatio(1);
                        $pdf->MultiCell(90, 5, 'RECURSOS HUMANOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA '.chr(10).'CLASIFICADOS POR TIPO', 0, 'C', 0, 0, '', '', true);
                        $pdf->setCellHeightRatio(2);
                        $pdf->ln(8);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->ln(-10);
                        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(19);
                        $pdf->setCellHeightRatio(1.2);

                        $pdf->MultiCell(40, 5, '', 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(20, 5, 'CODIGO', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(136, 5, 'DENOMINACION', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(40, 7, 'SECTOR', 1, 'L', 0, 0, '', '', true);
                        $pdf->SetFont('', '', 8);
                        $pdf->MultiCell(20, 7, $value_ac->co_sector, 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(136, 7, mb_strtoupper($value->tx_descripcion, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(7);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(40, 7, 'PROYECTO Y/O A. CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                        $pdf->SetFont('', '', 8);
                        $pdf->MultiCell(20, 7, $value_ac->nu_original, 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(136, 7, mb_strtoupper($value_ac->de_nombre, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(8);

                        $ejercicio_anterior = $ejercicio-1;

                        $pdf->SetFont('', 'B', 8);
                        $pdf->MultiCell(20, 20, chr(10).chr(10).'TIPO DE PERSONAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(0);
                        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(88, 5, $ejercicio_anterior, 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(88, 5, $ejercicio, 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(88, 5, 'EN BOLIVARES ANUALES', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(88, 5, 'EN BOLIVARES ANUALES', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->SetFont('', 'B', 5);
                        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(24, 5, 'N° DE CARGOS', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 10, chr(10).'SUELDO Y SALARIO', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 10, chr(10).'COMPENSACION', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 10, chr(10).'PRIMAS', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 10, chr(10).'TOTAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->SetFont('', 'B', 4);
                        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, chr(10).'M', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, chr(10).'F', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, 'TOTAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(-5);
                        $pdf->SetFont('', 'B', 5);
                        $pdf->MultiCell(108, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(24, 5, 'N° DE CARGOS', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 10, chr(10).'SUELDO Y SALARIO', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 10, chr(10).'COMPENSACION', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 10, chr(10).'PRIMAS', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 10, chr(10).'TOTAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->SetFont('', 'B', 4);
                        $pdf->MultiCell(108, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, chr(10).'M', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, chr(10).'F', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, 'TOTAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->setCellHeightRatio(1);
                        $pdf->MultiCell(20, 200, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 200, '', 1, 'L', 0, 0, '', '', true);

                        $pdf->SetY(267);
                        $pdf->SetFont('', '', 7);
                        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->ln(-200);
                        $pdf->ln(2);
                        $pdf->SetFont('', '', 7);
                        $pdf->setCellHeightRatio(1);

                        $tipo_personal = tab_tipo_personal::select('id', 'nu_codigo', 'de_tipo_personal', 'id_padre')
                        ->whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11])
                        ->orderBy('nu_codigo', 'ASC')
                        ->get();

                        //**anio anterior***//
                        $total_masculino_ant = 0;
                        $total_femenino_ant = 0;
                        $total_mf_ant = 0;
                        $total_mo_sueldo_ant = 0;
                        $total_mo_compensacion_ant = 0;
                        $total_mo_primas_ant = 0;
                        $total_sueldo_todo_ant = 0;

                        //**anio actual***//

                        $total_masculino = 0;
                        $total_femenino = 0;
                        $total_mf = 0;
                        $total_mo_sueldo = 0;
                        $total_mo_compensacion = 0;
                        $total_mo_primas = 0;
                        $total_sueldo_todo = 0;

                        $pdf->setFontSpacing('-0.200');

                        foreach ($tipo_personal as $key => $value_tipo_personal) {

                            $pdf->SetFont('', '', 6);
                            if($value_tipo_personal->id_padre==0) {

                                $pdf->writeHTMLCell(20, 5, '', '', '<u><b>'.trim($value_tipo_personal->nu_codigo).' '.trim($value_tipo_personal->de_tipo_personal).'</b></u>', 0, 0, 0, true, 'L', true);

                                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio_anterior)
                                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                                ->orderBy('id', 'ASC')
                                ->get();

                                if(!$clasificador_tipo->isEmpty()) {

                                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {
                                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->SetFont('', 'B', 6);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->SetFont('', '', 6);

                                        $total_masculino_ant = $total_masculino_ant + $value_clasificador_tipo->nu_masculino;
                                        $total_femenino_ant = $total_femenino_ant + $value_clasificador_tipo->nu_femenino;
                                        $total_mf_ant = $total_mf_ant + $total_sexo;
                                        $total_mo_sueldo_ant = $total_mo_sueldo_ant + $value_clasificador_tipo->mo_sueldo;
                                        $total_mo_compensacion_ant = $total_mo_compensacion_ant + $value_clasificador_tipo->mo_compensacion;
                                        $total_mo_primas_ant = $total_mo_primas_ant + $value_clasificador_tipo->mo_primas;
                                        $total_sueldo_todo_ant = $total_sueldo_todo_ant + $total_sueldo;

                                    }

                                } else {

                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                                }

                                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                                ->orderBy('id', 'ASC')
                                ->get();

                                if(!$clasificador_tipo->isEmpty()) {

                                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                                        $total_masculino = $total_masculino + $value_clasificador_tipo->nu_masculino;
                                        $total_femenino = $total_femenino + $value_clasificador_tipo->nu_femenino;
                                        $total_mf = $total_mf + $total_sexo;
                                        $total_mo_sueldo = $total_mo_sueldo + $value_clasificador_tipo->mo_sueldo;
                                        $total_mo_compensacion = $total_mo_compensacion + $value_clasificador_tipo->mo_compensacion;
                                        $total_mo_primas = $total_mo_primas + $value_clasificador_tipo->mo_primas;
                                        $total_sueldo_todo = $total_sueldo_todo + $total_sueldo;

                                    }

                                } else {

                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                                }

                            } else {

                                $pdf->MultiCell(20, 5, trim($value_tipo_personal->nu_codigo).' '.trim($value_tipo_personal->de_tipo_personal), 0, 'L', 0, 0, '', '', true);

                                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio_anterior)
                                  ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                                  ->orderBy('id', 'ASC')
                                  ->get();

                                if(!$clasificador_tipo->isEmpty()) {

                                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                                        $pdf->SetFont('', 'B', 6);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        }else{
                                        $pdf->SetFont('', '', 6);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);    
                                        }
                                        
                                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                                        $total_masculino_ant = $total_masculino_ant + $value_clasificador_tipo->nu_masculino;
                                        $total_femenino_ant = $total_femenino_ant + $value_clasificador_tipo->nu_femenino;
                                        $total_mf_ant = $total_mf_ant + $total_sexo;
                                        $total_mo_sueldo_ant = $total_mo_sueldo_ant + $value_clasificador_tipo->mo_sueldo;
                                        $total_mo_compensacion_ant = $total_mo_compensacion_ant + $value_clasificador_tipo->mo_compensacion;
                                        $total_mo_primas_ant = $total_mo_primas_ant + $value_clasificador_tipo->mo_primas;
                                        
                                        $total_sueldo_todo_ant = $total_sueldo_todo_ant + $total_sueldo;
                                        }

                                    }

                                } else {

                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                                }

                                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                                ->orderBy('id', 'ASC')
                                ->get();

                                if(!$clasificador_tipo->isEmpty()) {

                                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                                        $pdf->SetFont('', 'B', 6);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        }else{
                                        $pdf->SetFont('', '', 6);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);    
                                        }
                                        
                                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                                        $total_masculino = $total_masculino + $value_clasificador_tipo->nu_masculino;
                                        $total_femenino = $total_femenino + $value_clasificador_tipo->nu_femenino;
                                        $total_mf = $total_mf + $total_sexo;
                                        $total_mo_sueldo = $total_mo_sueldo + $value_clasificador_tipo->mo_sueldo;
                                        $total_mo_compensacion = $total_mo_compensacion + $value_clasificador_tipo->mo_compensacion;
                                        $total_mo_primas = $total_mo_primas + $value_clasificador_tipo->mo_primas;
                                        
                                        $total_sueldo_todo = $total_sueldo_todo + $total_sueldo;
                                        }

                                    }

                                } else {

                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                                }
                            }
                            $pdf->ln(10);

                        }

                        $pdf->SetFont('', 'B', 8);
                        $pdf->setCellHeightRatio(1.5);
                        $pdf->SetY(261);
                        $pdf->MultiCell(20, 6, 'TOTALES', 1, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 5);
                        $pdf->MultiCell(8, 6, number_format($total_masculino_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 6, number_format($total_femenino_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 6, number_format($total_mf_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(14, 6, number_format($total_mo_sueldo_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 6, number_format($total_mo_compensacion_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 6, number_format($total_mo_primas_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 6, number_format($total_sueldo_todo_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 5);
                        $pdf->MultiCell(8, 6, number_format($total_masculino, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 6, number_format($total_femenino, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 6, number_format($total_mf, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(14, 6, number_format($total_mo_sueldo, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 6, number_format($total_mo_compensacion, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 6, number_format($total_mo_primas, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 6, number_format($total_sueldo_todo, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

                        $pdf->setFontSpacing('0');

                        /******Inicio ENTIDAD FEDERAL POR ESCALA DE SUELDOS******/

                        $escala_salarial_grupo = tab_escala_salarial::join('mantenimiento.tab_tipo_empleado as t01', 't01.id', '=', 'mantenimiento.tab_escala_salarial.id_tab_tipo_empleado')
                        ->select('t01.id', 'de_tipo_empleado')
                        ->where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                        ->groupBy('t01.id')
                        ->groupBy('de_tipo_empleado')
                        ->orderBy('t01.id', 'ASC')
                        ->get();

                        foreach($escala_salarial_grupo as $key => $value_escala_salarial_grupo) {

                            // reset font stretching  reset font spacing
                            $pdf->setFontStretching(100);
                            $pdf->setFontSpacing(0);
                            $pdf->SetLineWidth(0.150);
                            $pdf->setCellHeightRatio(2);

                            $pdf->AddPage();

                            // reset font stretching  reset font spacing
                            $pdf->setFontStretching(100);
                            $pdf->setFontSpacing(0);
                            $pdf->SetLineWidth(0.150);
                            $pdf->setCellHeightRatio(2);

                            $pdf->SetFont('', 'B', 7);
                            $pdf->setCellHeightRatio(1.2);
                            $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->setCellHeightRatio(2);
                            $pdf->SetFont('', 'B', 10);
                            $pdf->setCellHeightRatio(1);
                            if($value_escala_salarial_grupo->de_tipo_empleado=='OBREROS'){
                            $pdf->MultiCell(90, 5, 'RECURSOS HUMANOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA '.chr(10).'POR ESCALA DE SALARIOS', 0, 'C', 0, 0, '', '', true);
                            }else{
                            $pdf->MultiCell(90, 5, 'RECURSOS HUMANOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA '.chr(10).'POR ESCALA DE SUELDOS', 0, 'C', 0, 0, '', '', true);  
                            }                            
                            $pdf->setCellHeightRatio(2);
                            $pdf->ln(8);
                            $pdf->SetFont('', 'B', 7);
                            $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->ln(-10);
                            $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                            $pdf->ln(19);
                            $pdf->setCellHeightRatio(1.2);

                            $pdf->MultiCell(40, 5, '', 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(20, 5, 'CODIGO', 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(136, 5, 'DENOMINACION', 1, 'L', 0, 0, '', '', true);
                            $pdf->ln(5);
                            $pdf->SetFont('', 'B', 7);
                            $pdf->MultiCell(40, 7, 'SECTOR', 1, 'L', 0, 0, '', '', true);
                            $pdf->SetFont('', '', 8);
                            $pdf->MultiCell(20, 7, $value_ac->co_sector, 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(136, 7, mb_strtoupper($value->tx_descripcion, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                            $pdf->ln(7);
                            $pdf->SetFont('', 'B', 7);
                            $pdf->MultiCell(40, 7, 'PROYECTO Y/O A. CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                            $pdf->SetFont('', '', 8);
                            $pdf->MultiCell(20, 7, $value_ac->nu_original, 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(136, 7, mb_strtoupper($value_ac->de_nombre, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                            $pdf->ln(8);

                            $pdf->SetFont('', 'B', 8);
                            $pdf->MultiCell(20, 20, chr(10).chr(10).'GRUPO', 1, 'C', 0, 0, '', '', true);
                            $pdf->SetFont('', 'B', 9);
                            $pdf->setFontSpacing('0.254');
                            if($value_escala_salarial_grupo->de_tipo_empleado=='OBREROS'){
                            $pdf->MultiCell(88, 20, chr(10).chr(10).'ESCALA DE SALARIOS', 1, 'C', 0, 0, '', '', true);
                            }else{
                            $pdf->MultiCell(88, 20, chr(10).chr(10).'ESCALA DE SUELDOS', 1, 'C', 0, 0, '', '', true);    
                            }                            
                            $pdf->setFontSpacing('0');
                            $pdf->SetFont('', 'B', 8);
                            $pdf->MultiCell(88, 5, 'ESTIMADO PARA '.$ejercicio, 1, 'C', 0, 0, '', '', true);
                            $pdf->ln(5);
                            $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(88, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(88, 5, $value_escala_salarial_grupo->de_tipo_empleado, 1, 'C', 0, 0, '', '', true);
                            $pdf->ln(5);
                            $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(88, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(68, 5, 'Nº DE CARGO', 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(20, 10, chr(10).'MONTO Bs.', 1, 'C', 0, 0, '', '', true);
                            $pdf->ln(5);
                            $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(88, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(22, 5, 'Masculino', 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(22, 5, 'Femenino', 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(24, 5, 'Total', 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->ln(5);
                            $pdf->setCellHeightRatio(1);
                            $pdf->MultiCell(20, 200, '', 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(88, 200, '', 1, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(22, 200, '', 1, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(22, 200, '', 1, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(24, 200, '', 1, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(20, 200, '', 1, 'L', 0, 0, '', '', true);
                            $pdf->SetY(267);
                            $pdf->SetFont('', '', 7);
                            $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                            $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                            $pdf->ln(-203);
                            $pdf->ln(4);
                            $pdf->SetFont('', '', 7);
                            $pdf->setCellHeightRatio(1);

                            $escala_salarial = tab_escala_salarial::where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                            ->where('id_tab_tipo_empleado', '=', $value_escala_salarial_grupo->id)
                            ->orderBy('id', 'ASC')
                            ->get();

                            $total_mo_sexo_m = 0;
                            $total_mo_sexo_f = 0;
                            $total_mo_sexo_mf = 0;
                            $total_mo_todo = 0;

                            $pdf->SetFont('', '', 8);

                            foreach ($escala_salarial as $key => $value_escala_salarial) {

                                $total_sexo_mf = $value_escala_salarial->nu_masculino + $value_escala_salarial->nu_femenino;

                                $pdf->MultiCell(20, 215, $value_escala_salarial->de_grupo, 0, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(88, 215, $value_escala_salarial->de_escala_salarial, 0, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(22, 215, number_format($value_escala_salarial->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                $pdf->MultiCell(22, 215, number_format($value_escala_salarial->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                $pdf->MultiCell(24, 215, number_format($total_sexo_mf, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                $pdf->MultiCell(20, 215, number_format($value_escala_salarial->mo_escala_salarial, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                                $pdf->ln(6);

                                $total_mo_sexo_m = $total_mo_sexo_m + $value_escala_salarial->nu_masculino;
                                $total_mo_sexo_f = $total_mo_sexo_f + $value_escala_salarial->nu_femenino;
                                $total_mo_sexo_mf = $total_mo_sexo_m + $total_mo_sexo_f;
                                $total_mo_todo = $total_mo_todo + $value_escala_salarial->mo_escala_salarial;

                            }

                            $pdf->SetFont('', 'B', 8);
                            $pdf->setCellHeightRatio(1.5);
                            $pdf->SetY(261);
                            $pdf->MultiCell(108, 6, 'TOTALES', 1, 'R', 0, 0, '', '', true);
                            $pdf->SetFont('', 'B', 8);
                            $pdf->MultiCell(22, 6, number_format($total_mo_sexo_m, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                            $pdf->MultiCell(22, 6, number_format($total_mo_sexo_f, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                            $pdf->MultiCell(24, 6, number_format($total_mo_sexo_mf, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                            $pdf->MultiCell(20, 6, number_format($total_mo_todo, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

                        }

                    }
                }

                if($valor_sector = '01') {
                    if($valor_original == 53) {

                        $pdf->AddPage();

                        // reset font stretching  reset font spacing
                        $pdf->setFontStretching(100);
                        $pdf->setFontSpacing(0);
                        $pdf->SetLineWidth(0.150);
                        $pdf->setCellHeightRatio(2);

                        $pdf->SetFont('', 'B', 7);
                        $pdf->setCellHeightRatio(1.2);
                        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->setCellHeightRatio(2);
                        $pdf->SetFont('', 'B', 10);
                        $pdf->setCellHeightRatio(1);
                        $pdf->MultiCell(90, 5, 'RECURSOS HUMANOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA '.chr(10).'CLASIFICADOS POR TIPO', 0, 'C', 0, 0, '', '', true);
                        $pdf->setCellHeightRatio(2);
                        $pdf->ln(8);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->ln(-10);
                        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(19);
                        $pdf->setCellHeightRatio(1.2);

                        $pdf->MultiCell(40, 5, '', 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(20, 5, 'CODIGO', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(136, 5, 'DENOMINACION', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(40, 7, 'SECTOR', 1, 'L', 0, 0, '', '', true);
                        $pdf->SetFont('', '', 8);
                        $pdf->MultiCell(20, 7, $value_ac->co_sector, 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(136, 7, mb_strtoupper($value->tx_descripcion, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(7);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(40, 7, 'PROYECTO Y/O A. CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                        $pdf->SetFont('', '', 8);
                        $pdf->MultiCell(20, 7, $value_ac->nu_original, 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(136, 7, mb_strtoupper($value_ac->de_nombre, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(8);

                        $ejercicio_anterior = $ejercicio-1;

                        $pdf->SetFont('', 'B', 8);
                        $pdf->MultiCell(20, 20, chr(10).chr(10).'TIPO DE PERSONAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(0);
                        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(88, 5, $ejercicio_anterior, 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(88, 5, $ejercicio, 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(88, 5, 'EN BOLIVARES ANUALES', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(88, 5, 'EN BOLIVARES ANUALES', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->SetFont('', 'B', 5);
                        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(24, 5, 'N° DE CARGOS', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 10, chr(10).'SUELDO Y SALARIO', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 10, chr(10).'COMPENSACION', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 10, chr(10).'PRIMAS', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 10, chr(10).'TOTAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->SetFont('', 'B', 4);
                        $pdf->MultiCell(20, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, chr(10).'M', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, chr(10).'F', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, 'TOTAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(-5);
                        $pdf->SetFont('', 'B', 5);
                        $pdf->MultiCell(108, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(24, 5, 'N° DE CARGOS', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 10, chr(10).'SUELDO Y SALARIO', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 10, chr(10).'COMPENSACION', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 10, chr(10).'PRIMAS', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 10, chr(10).'TOTAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->SetFont('', 'B', 4);
                        $pdf->MultiCell(108, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, chr(10).'M', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, chr(10).'F', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 5, 'TOTAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(5);
                        $pdf->setCellHeightRatio(1);
                        $pdf->MultiCell(20, 200, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(14, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 200, '', 1, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 200, '', 1, 'L', 0, 0, '', '', true);

                        $pdf->SetY(267);
                        $pdf->SetFont('', '', 7);
                        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->ln(-200);
                        $pdf->ln(2);
                        $pdf->SetFont('', '', 7);
                        $pdf->setCellHeightRatio(1);

                        $tipo_personal = tab_tipo_personal::select('id', 'nu_codigo', 'de_tipo_personal', 'id_padre')
                        ->whereIn('id', [12, 13, 14])
                        ->orderBy('nu_codigo', 'ASC')
                        ->get();

                        //**anio anterior***//
                        $total_masculino_ant = 0;
                        $total_femenino_ant = 0;
                        $total_mf_ant = 0;
                        $total_mo_sueldo_ant = 0;
                        $total_mo_compensacion_ant = 0;
                        $total_mo_primas_ant = 0;
                        $total_sueldo_todo_ant = 0;

                        //**anio actual***//

                        $total_masculino = 0;
                        $total_femenino = 0;
                        $total_mf = 0;
                        $total_mo_sueldo = 0;
                        $total_mo_compensacion = 0;
                        $total_mo_primas = 0;
                        $total_sueldo_todo = 0;

                        $pdf->setFontSpacing('-0.200');

                        foreach ($tipo_personal as $key => $value_tipo_personal) {

                            $pdf->SetFont('', '', 6);
                            if($value_tipo_personal->id_padre==0) {

                                $pdf->writeHTMLCell(20, 5, '', '', '<u><b>'.trim($value_tipo_personal->nu_codigo).' '.trim($value_tipo_personal->de_tipo_personal).'</b></u>', 0, 0, 0, true, 'L', true);

                                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio_anterior)
                                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                                ->orderBy('id', 'ASC')
                                ->get();

                                if(!$clasificador_tipo->isEmpty()) {

                                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {
                                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                                        $total_masculino_ant = $total_masculino_ant + $value_clasificador_tipo->nu_masculino;
                                        $total_femenino_ant = $total_femenino_ant + $value_clasificador_tipo->nu_femenino;
                                        $total_mf_ant = $total_mf_ant + $total_sexo;
                                        $total_mo_sueldo_ant = $total_mo_sueldo_ant + $value_clasificador_tipo->mo_sueldo;
                                        $total_mo_compensacion_ant = $total_mo_compensacion_ant + $value_clasificador_tipo->mo_compensacion;
                                        $total_mo_primas_ant = $total_mo_primas_ant + $value_clasificador_tipo->mo_primas;
                                        $total_sueldo_todo_ant = $total_sueldo_todo_ant + $total_sueldo;

                                    }

                                } else {

                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                                }

                                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                                ->orderBy('id', 'ASC')
                                ->get();

                                if(!$clasificador_tipo->isEmpty()) {

                                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                                        $total_masculino = $total_masculino + $value_clasificador_tipo->nu_masculino;
                                        $total_femenino = $total_femenino + $value_clasificador_tipo->nu_femenino;
                                        $total_mf = $total_mf + $total_sexo;
                                        $total_mo_sueldo = $total_mo_sueldo + $value_clasificador_tipo->mo_sueldo;
                                        $total_mo_compensacion = $total_mo_compensacion + $value_clasificador_tipo->mo_compensacion;
                                        $total_mo_primas = $total_mo_primas + $value_clasificador_tipo->mo_primas;
                                        $total_sueldo_todo = $total_sueldo_todo + $total_sueldo;

                                    }

                                } else {

                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                                }

                            } else {

                                $pdf->MultiCell(20, 5, trim($value_tipo_personal->nu_codigo).' '.trim($value_tipo_personal->de_tipo_personal), 0, 'L', 0, 0, '', '', true);

                                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio_anterior)
                                  ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                                  ->orderBy('id', 'ASC')
                                  ->get();

                                if(!$clasificador_tipo->isEmpty()) {

                                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                                        $pdf->SetFont('', 'B', 6);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        }else{
                                        $pdf->SetFont('', '', 6);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);    
                                        }

                                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                                        $total_masculino_ant = $total_masculino_ant + $value_clasificador_tipo->nu_masculino;
                                        $total_femenino_ant = $total_femenino_ant + $value_clasificador_tipo->nu_femenino;
                                        $total_mf_ant = $total_mf_ant + $total_sexo;
                                        $total_mo_sueldo_ant = $total_mo_sueldo_ant + $value_clasificador_tipo->mo_sueldo;
                                        $total_mo_compensacion_ant = $total_mo_compensacion_ant + $value_clasificador_tipo->mo_compensacion;
                                        $total_mo_primas_ant = $total_mo_primas_ant + $value_clasificador_tipo->mo_primas;
                                        
                                        $total_sueldo_todo_ant = $total_sueldo_todo_ant + $total_sueldo;
                                        }                                        
                                        

                                    }

                                } else {

                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                                }

                                $clasificador_tipo = tab_clasificador_tipo::where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('id_tab_tipo_personal', '=', $value_tipo_personal->id)
                                ->orderBy('id', 'ASC')
                                ->get();

                                if(!$clasificador_tipo->isEmpty()) {

                                    foreach ($clasificador_tipo as $key => $value_clasificador_tipo) {

                                        $total_sexo = $value_clasificador_tipo->nu_masculino + $value_clasificador_tipo->nu_femenino;
                                        $total_sueldo = $value_clasificador_tipo->mo_sueldo + $value_clasificador_tipo->mo_compensacion + $value_clasificador_tipo->mo_primas;

                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_masculino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($value_clasificador_tipo->nu_femenino, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(8, 5, number_format($total_sexo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(14, 5, number_format($value_clasificador_tipo->mo_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(18, 5, number_format($value_clasificador_tipo->mo_compensacion, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(15, 5, number_format($value_clasificador_tipo->mo_primas, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                                        $pdf->SetFont('', 'B', 6);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        }else{
                                        $pdf->SetFont('', '', 6);
                                        $pdf->MultiCell(17, 5, number_format($total_sueldo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);    
                                        }

                                        if($value_tipo_personal->id==1 || $value_tipo_personal->id==10 || $value_tipo_personal->id==12){
                                        $total_masculino = $total_masculino + $value_clasificador_tipo->nu_masculino;
                                        $total_femenino = $total_femenino + $value_clasificador_tipo->nu_femenino;
                                        $total_mf = $total_mf + $total_sexo;
                                        $total_mo_sueldo = $total_mo_sueldo + $value_clasificador_tipo->mo_sueldo;
                                        $total_mo_compensacion = $total_mo_compensacion + $value_clasificador_tipo->mo_compensacion;
                                        $total_mo_primas = $total_mo_primas + $value_clasificador_tipo->mo_primas;
                                        
                                        $total_sueldo_todo = $total_sueldo_todo + $total_sueldo;
                                        }

                                    }

                                } else {

                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(8, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(14, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(18, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(15, 5, '', 0, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(17, 5, '', 0, 'R', 0, 0, '', '', true);

                                }
                            }
                            $pdf->ln(10);

                        }

                        $pdf->SetFont('', 'B', 8);
                        $pdf->setCellHeightRatio(1.5);
                        $pdf->SetY(261);
                        $pdf->MultiCell(20, 6, 'TOTALES', 1, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 5);
                        $pdf->MultiCell(8, 6, number_format($total_masculino_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 6, number_format($total_femenino_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 6, number_format($total_mf_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(14, 6, number_format($total_mo_sueldo_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 6, number_format($total_mo_compensacion_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 6, number_format($total_mo_primas_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 6, number_format($total_sueldo_todo_ant, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 5);
                        $pdf->MultiCell(8, 6, number_format($total_masculino, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 6, number_format($total_femenino, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(8, 6, number_format($total_mf, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(14, 6, number_format($total_mo_sueldo, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(18, 6, number_format($total_mo_compensacion, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(15, 6, number_format($total_mo_primas, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(17, 6, number_format($total_sueldo_todo, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

                        $pdf->setFontSpacing('0');

                    }
                }

                $pdf->AddPage();

                // reset font stretching  reset font spacing
                $pdf->setFontStretching(100);
                $pdf->setFontSpacing(0);
                $pdf->SetLineWidth(0.150);
                $pdf->setCellHeightRatio(2);

                $pdf->SetFont('', 'B', 7);
                $pdf->setCellHeightRatio(1.2);
                $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->setCellHeightRatio(2);
                $pdf->SetFont('', 'B', 10);
                $pdf->setCellHeightRatio(1);
                $pdf->MultiCell(90, 5, 'CREDITOS PRESUPUESTARIOS DEL PROYECTO Y/O ACCIÓN CENTRALIZADA', 0, 'C', 0, 0, '', '', true);
                $pdf->setCellHeightRatio(2);
                $pdf->ln(8);
                $pdf->SetFont('', 'B', 7);
                $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(90, 5, '(EN BOLÍVARES) ', 0, 'C', 0, 0, '', '', true);
                $pdf->ln(-10);
                $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(19);
                $pdf->setCellHeightRatio(1.2);

                $pdf->MultiCell(40, 5, '', 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(20, 5, 'CODIGO', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(136, 5, 'DENOMINACION', 1, 'L', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->SetFont('', 'B', 7);
                $pdf->MultiCell(40, 8, 'SECTOR / PROYECTO Y/O A. CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                $pdf->SetFont('', '', 8);
                $pdf->MultiCell(20, 8, $value_ac->co_sector.'.'.$value_ac->nu_original, 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(136, 8, mb_strtoupper($value_ac->de_nombre, 'UTF-8'), 1, 'L', 0, 0, '', '', true);
                $pdf->ln(8);
                $pdf->SetFont('', 'B', 8);
                $pdf->MultiCell(156, 5, 'PARTIDA', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(40, 10, 'ASIGNACION PRESUPUESTARIA', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->SetFont('', 'B', 8);
                $pdf->MultiCell(40, 5, 'CODIGO', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(116, 5, 'DENOMINACION', 1, 'C', 0, 0, '', '', true);
                $pdf->ln(5);
                $pdf->setCellHeightRatio(1);
                //$pdf->MultiCell(196, 216, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(40, 207, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(116, 207, '', 1, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(40, 207, '', 1, 'L', 0, 0, '', '', true);
                $pdf->ln(215);
                $pdf->SetFont('', '', 7);
                $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                $pdf->ln(-215);
                $pdf->ln(2);
                $pdf->SetFont('', '', 7);
                $pdf->setCellHeightRatio(1);

                $ac_lista_partida = tab_ac_ae_partida::join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
                ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
                ->join('mantenimiento.tab_partidas as t03', 't03.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
                ->join('mantenimiento.tab_sectores as t04', function ($join) {
                    $join->on('t04.co_sector', '=', 't02.co_sector')
                        ->on('t04.nu_nivel', '=', DB::raw('1'));
                })
                ->join('mantenimiento.tab_ejecutores as t05', 't05.id_ejecutor', '=', 't01.id_ejecutor')
                //->join(DB::raw('inner join mantenimiento.tab_partidas as t03 on left(public.t54_ac_ae_partidas.co_partida, 3) = t03.co_partida'))
                ->select(
                    't02.co_sector',
                    't01.id_accion',
                    DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3) as partida'),
                    'tx_nombre',
                    DB::raw('sum(public.t54_ac_ae_partidas.monto) as mo_partida')
                )
                ->where('t01.id_ejercicio', '=', $ejercicio)
                ->where('t03.id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('t01.id_accion', '=', $value_ac->id_accion)
                ->where('t02.co_sector', '=', $value_ac->co_sector)
                ->where('id_tab_tipo_ejecutor', '=', 1)
                ->groupBy('t02.co_sector')
                ->groupBy('t01.id_accion')
                ->groupBy('partida')
                ->groupBy('tx_nombre')
                ->orderBy('partida', 'ASC')
                ->get();

                $total_partida = 0;

                foreach ($ac_lista_partida as $key => $value_ac_partida) {

                    $pdf->MultiCell(40, 5, $value_ac_partida->partida, 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(116, 5, mb_strtoupper($value_ac_partida->tx_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(40, 5, number_format($value_ac_partida->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                    $pdf->ln(5);

                    $total_partida = $total_partida + $value_ac_partida->mo_partida;

                }

                $pdf->SetFont('', 'B', 8);
                $pdf->setCellHeightRatio(1.5);
                $pdf->SetY(257);
                $pdf->MultiCell(156, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(40, 5, number_format($total_partida, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

            }

        }

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        /*$pdf->AddPage();

        //reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);*/
        /******Portada Titulo Sectores*********/
        /*$pdf->SetAlpha(0.3);
        $pdf->Image(public_path().'/images/escudo_zulia.png', 15, 40, 190, 190, 'PNG', '', '', false, 170, '', false, false, 0);
        $pdf->ln(30);
        $pdf->setAlpha(1);
        $pdf->SetFont('','',8);

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(1);
        //
        $pdf->SetY(15);
        $pdf->SetFont('','B',14);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(190, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(220);
        $pdf->SetFont('','B',12);
        //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
        $pdf->writeHTML('<b><u>ANEXOS<u/></b>', true, false, true, false, 'R');
        $pdf->ln(0);
        $pdf->writeHTML('<b>RELACIÓN DE OBRAS</b>', true, false, true, false, 'R');
        $pdf->ln(10);
        // set border width
        $pdf->SetLineWidth(0.508);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(0,0,0);
        $pdf->setCellHeightRatio(0);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        $pdf->ln(2);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);*/

        /*$pdf->AddPage();

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->SetFont('','B',8);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(30, 5, 'GOBERNACIÓN '.chr(10).'DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->SetFont('','B',10);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(90, 5, 'RELACIÓN DE OBRAS', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->SetFont('','',8);

        $pdf->MultiCell(196, 230, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(0);
        $pdf->SetFont('','B',7);
        $pdf->MultiCell(40, 2, 'CODIGO', 1, 'C', 0, 0, '', '', true);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(116, 15, 'DENOMINACION', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(40, 15, 'MONTO', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->SetFont('','B',7);
        $pdf->MultiCell(20, 10, 'SECTOR', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(20, 2, 'PROY. Y/O A. CENTRAL', 1, 'C', 0, 0, '', '', true);

        $pdf->ln(230);
        $pdf->SetFont('','',7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-230);*/

        /*$tabla_obra_lista = '
        <table border="0.5" style="width:100%" cellspacing="0" cellpadding="4">
        <thead>
        <tr style="font-size: 8px;" nobr="true">
          <th style="text-align: center;width:20%" colspan="2"><strong>CODIGO</strong></th>
          <th style="text-align: center;width:60%" rowspan="2"><strong><br>DENOMINACION</strong></th>
          <th style="text-align: center;width:20%" rowspan="2"><strong><br>MONTO</strong></th>
        </tr>
        <tr style="font-size: 8px;" nobr="true">
          <th style="text-align: center;width:10%"><strong>SECTOR</strong></th>
          <th style="text-align: center;width:10%"><strong>PROY. Y/O A.CENTRAL</strong></th>
        </tr>
        </thead>
        <tbody>';

        $tabla_obra_lista.='
        <tr nobr="true">
          <td style="text-align: rigth;width:80%" colspan="3"><b>TOTAL GENERAL</b></td>
          <td style="text-align: rigth;width:20%"><b>'.number_format($total_partida, 2, ',', '.').'</b></td>
        </tr>
        </tbody>
        </table>';

        $pdf->writeHTML(Helper::htmlComprimir($tabla_obra_lista), true, false, false, false, '');*/

        //$pdf->AddPage();

        /*$pdf->AddPage();
        /******Portada Titulo Sectores*********/
        /*$pdf->SetAlpha(0.3);
        $pdf->Image(public_path().'/images/escudo_zulia.png', 15, 40, 190, 190, 'PNG', '', '', false, 170, '', false, false, 0);
        $pdf->ln(30);
        $pdf->setAlpha(1);
        $pdf->SetFont('','',8);

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(1);
        //
        $pdf->SetY(15);
        $pdf->SetFont('','B',14);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(190, 5, 'GOBERNACIÓN DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(220);
        $pdf->SetFont('','B',12);
        //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
        $pdf->writeHTML('<b><u>ANEXOS<u/></b>', true, false, true, false, 'R');
        $pdf->ln(0);
        $pdf->writeHTML('<b>FONDO DE COMPENSACION INTERTERRITORIAL (FCI)</b>', true, false, true, false, 'R');
        $pdf->ln(10);
        // set border width
        $pdf->SetLineWidth(0.508);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(0,0,0);
        $pdf->setCellHeightRatio(0);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        $pdf->ln(2);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);*/

        /*$pdf->AddPage();

        $pdf->SetFont('','B',8);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(30, 5, 'GOBERNACIÓN '.chr(10).'DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->SetFont('','B',10);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(90, 5, 'RELACIÓN DE PROYECTOS DE INVERSIÓN A SER FINANCIADOS A TRAVÉS DEL FONDO DE COMPENSACION INTERTERRITORIAL', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('','B',8);
        $pdf->MultiCell(55, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->SetFont('','',8);

        $pdf->MultiCell(196, 230, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(0);
        $pdf->SetFont('','B',7);
        $pdf->MultiCell(40, 2, 'CODIGO', 1, 'C', 0, 0, '', '', true);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(116, 15, 'DENOMINACION', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(40, 15, 'MONTO', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(5);
        $pdf->SetFont('','B',7);
        $pdf->MultiCell(20, 10, 'SECTOR', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(20, 2, 'PROY. Y/O A. CENTRAL', 1, 'C', 0, 0, '', '', true);

        $pdf->ln(230);
        $pdf->SetFont('','',7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-230);

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(1);*/

        $pdf->AddPage();
        /******Portada Titulo Sectores*********/
        $pdf->SetAlpha(0.3);
        $pdf->Image(public_path().'/images/escudo_zulia.png', 15, 40, 190, 190, 'PNG', '', '', false, 170, '', false, false, 0);
        $pdf->ln(30);
        $pdf->setAlpha(1);
        $pdf->SetFont('', '', 8);

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(1);
        //
        $pdf->SetY(15);
        $pdf->SetFont('', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(190, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(220);
        $pdf->SetFont('', 'B', 12);
        //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
        $pdf->writeHTML('<b><u>ANEXOS<u/></b>', true, false, true, false, 'R');
        $pdf->ln(0);
        $pdf->writeHTML('<b>DISTRIBUCIÓN DE SITUADOS</b>', true, false, true, false, 'R');
        $pdf->ln(10);
        // set border width
        $pdf->SetLineWidth(0.508);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->setCellHeightRatio(0);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        $pdf->ln(2);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->AddPage();

        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->SetFont('', 'B', 10);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(90, 5, 'DISTRIBUCIÓN DE SITUADOS A NIVEL DE MUNICIPIOS', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->SetFont('', '', 8);

        $pdf->MultiCell(196, 230, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(0);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(20, 15, chr(10).'CÓDIGO', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(46, 15, chr(10).'MUNICIPIOS', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 15, chr(10).'POBLACIÒN ULTIMO CENSO', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 15, chr(10).'45 % PARTES IGUALES', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 15, chr(10).'50% EN FUNCIÒN DE LA POBLACIÒN', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 15, chr(10).'5 % SUPERFICIE TERRITORIAL', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(30, 15, chr(10).'TOTAL SITUADO INGRESOS PROPIOS', 1, 'C', 0, 0, '', '', true);

        $pdf->ln(15);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(20, 208, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(46, 208, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 208, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(25, 208, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(25, 208, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(25, 208, '', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(30, 208, '', 1, 'L', 0, 0, '', '', true);

        $pdf->SetY(267);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-219);
        $pdf->ln(5);
        $pdf->SetFont('', '', 7);
        $pdf->setCellHeightRatio(1);

        $distribucion_municipio = tab_distribucion_municipio::join('mantenimiento.tab_municipio as t01', 't01.id', '=', 'mantenimiento.tab_distribucion_municipio.id_tab_municipio')
        ->select(
            'mantenimiento.tab_distribucion_municipio.id',
            'id_tab_ejercicio_fiscal',
            'id_tab_municipio',
            'co_partida',
            'nu_base_censo',
            'nu_factor_poblacion',
            'cuatrocinco_ppi',
            'cincocero_fpp',
            'superficie_km',
            'superficie_factor',
            'extension_territorio',
            'mo_total',
            'de_municipio'
        )
        ->where('id_tab_ejercicio_fiscal', '=', $ejercicio)
        ->where('tab_distribucion_municipio.in_activo', '=', true)
        ->orderby('de_municipio', 'ASC')
        ->get();

        $contador_municipio = 0;
        $total_nu_base_censo = 0;
        $total_cuatrocinco_ppi = 0;
        $total_cincocero_fpp = 0;
        $total_superficie_km = 0;
        $total_mo_total = 0;

        $pdf->SetFont('', '', 8);

        foreach ($distribucion_municipio as $key => $value_distribucion_municipio) {

            $contador_municipio++;
            $municipio = str_pad($contador_municipio, 3, "0", STR_PAD_LEFT);

            $pdf->MultiCell(20, 5, $municipio, 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(46, 5, $value_distribucion_municipio->de_municipio, 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, number_format($value_distribucion_municipio->nu_base_censo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, number_format($value_distribucion_municipio->cuatrocinco_ppi, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, number_format($value_distribucion_municipio->cincocero_fpp, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, number_format($value_distribucion_municipio->superficie_km, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(30, 5, number_format($value_distribucion_municipio->mo_total, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->ln(8);

            $total_nu_base_censo = $total_nu_base_censo + $value_distribucion_municipio->nu_base_censo;
            $total_cuatrocinco_ppi = $total_cuatrocinco_ppi + $value_distribucion_municipio->cuatrocinco_ppi;
            $total_cincocero_fpp = $total_cincocero_fpp + $value_distribucion_municipio->cincocero_fpp;
            $total_superficie_km = $total_superficie_km + $value_distribucion_municipio->superficie_km;
            $total_mo_total = $total_mo_total + $value_distribucion_municipio->mo_total;

        }

        $pdf->SetFont('', 'B', 8);
        $pdf->setCellHeightRatio(1.5);
        $pdf->SetY(250);
        $pdf->MultiCell(66, 7, '', 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(25, 7, '', 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(25, 7, '', 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(25, 7, '', 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(25, 7, '', 1, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(30, 7, '', 1, 'R', 0, 0, '', '', true);
        $pdf->ln(1);
        $pdf->MultiCell(66, 7, 'TOTALES', 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(25, 7, number_format($total_nu_base_censo, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(25, 7, number_format($total_cuatrocinco_ppi, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(25, 7, number_format($total_cincocero_fpp, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(25, 7, number_format($total_superficie_km, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
        $pdf->MultiCell(30, 7, number_format($total_mo_total, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(1);

        $pdf->AddPage();
        /******Portada Titulo Sectores*********/
        $pdf->SetAlpha(0.3);
        $pdf->Image(public_path().'/images/escudo_zulia.png', 15, 40, 190, 190, 'PNG', '', '', false, 170, '', false, false, 0);
        $pdf->ln(30);
        $pdf->setAlpha(1);
        $pdf->SetFont('', '', 8);

        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(1);
        //
        $pdf->SetY(15);
        $pdf->SetFont('', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(190, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(220);
        $pdf->SetFont('', 'B', 12);
        //$pdf->MultiCell(190, 5, 'TITULO I', 0, 'R', 0, 0, '', '', true);
        $pdf->writeHTML('<b><u>ANEXOS<u/></b>', true, false, true, false, 'R');
        $pdf->ln(0);
        $pdf->writeHTML('<b>RELACIÓN DE TRANSFERENCIAS</b>', true, false, true, false, 'R');
        $pdf->ln(10);
        // set border width
        $pdf->SetLineWidth(0.508);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->setCellHeightRatio(0);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        $pdf->ln(2);
        $pdf->Cell(195, 0, '', 'B', 1, 'R', 1, '', 0, false, 'T', 'R');
        // reset font stretching  reset font spacing
        $pdf->setFontStretching(100);
        $pdf->setFontSpacing(0);
        $pdf->SetLineWidth(0.150);
        $pdf->setCellHeightRatio(2);

        $pdf->AddPage();
        $movimiento = 0;
        $movimiento_capital = 0;
        $pdf->SetFont('', 'B', 7);
        $pdf->setCellHeightRatio(1.2);
        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->SetFont('', 'B', 10);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(90, 5, 'RELACIÓN DE TRANSFERENCIAS', 0, 'C', 0, 0, '', '', true);
        $pdf->setCellHeightRatio(2);
        $pdf->ln(8);
        $pdf->SetFont('', 'B', 7);
        $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
        $pdf->ln(-10);
        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(19);
        $pdf->SetFont('', '', 8);

        $pdf->MultiCell(196, 240, '', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(0);
        $pdf->SetFont('', 'B', 7);
        $pdf->ln(30);
        $pdf->StartTransform();
        $pdf->Rotate(90);
        $pdf->MultiCell(30, 10, 'SECTOR', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->MultiCell(30, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->MultiCell(30, 10, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->MultiCell(30, 10, 'SUB - PARTIDA GENERICA', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->MultiCell(30, 10, 'SUB - PARTIDA ESPECIFICA', 1, 'L', 0, 0, '', '', true);
        $pdf->ln(10);
        $pdf->StopTransform();
        $pdf->ln(-80);
        $pdf->SetFont('', 'B', 8);
        $pdf->setCellHeightRatio(10);
        $pdf->MultiCell(50, 30, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(71, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 30, 'CORRIENTES', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 30, 'CAPITAL', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 30, 'MONTO TOTAL', 1, 'C', 0, 0, '', '', true);
        $pdf->ln(30);
        $pdf->setCellHeightRatio(1);
        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(71, 205, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
        $pdf->SetY(267);
        $pdf->SetFont('', '', 7);
        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
        $pdf->ln(-210);
        $pdf->ln(2);
        $pdf->SetFont('', '', 7);
        $pdf->setCellHeightRatio(0.8);

        /*$ac_transferencia_uno = tab_ac_ae_partida::
        join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
        ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
        ->join('mantenimiento.tab_ac_predefinida as t03', 't03.id', '=', 't01.id_accion')
        ->join('mantenimiento.tab_sectores as t04', 't04.tx_codigo', '=', 't02.co_sector')
        ->join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
        ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 5)'))
        ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 7)'))
        ->select( 't02.co_sector', 't04.tx_descripcion', DB::raw('sum(public.t54_ac_ae_partidas.monto) as mo_partida') )
        ->where('t01.id_ejercicio', '=', $ejercicio)
        ->where('t05.id_tab_ejercicio_fiscal', '=', $ejercicio)
        ->where('t06.id_tab_ejercicio_fiscal', '=', $ejercicio)
        ->where('t07.id_tab_ejercicio_fiscal', '=', $ejercicio)
        ->where(DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'), '=', '407')
        ->groupBy('t02.co_sector')
        ->groupBy('t04.tx_descripcion')
        ->orderBy('t02.co_sector','ASC')
        ->get();*/

        $ac_transferencia_uno = vista_relacion_transferencia::select('co_sector', 'tx_descripcion', DB::raw('sum(monto) as mo_partida'))
        ->where('ef_uno', '=', $ejercicio)
        ->where('ef_dos', '=', $ejercicio)
        ->where('ef_tres', '=', $ejercicio)
        ->where('ef_cuatro', '=', $ejercicio)
        ->where('id_tab_tipo_ejecutor', '=', 1)
        ->groupBy('co_sector')
        ->groupBy('tx_descripcion')
        ->groupBy('np_uno')
        ->orderBy('co_sector', 'ASC')
        ->get();

        foreach ($ac_transferencia_uno as $key => $value_transferencia) {

            $pdf->SetFont('', 'B', 7);

            $pdf->MultiCell(10, 5, $value_transferencia->co_sector, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(71, 5, mb_strtoupper($value_transferencia->tx_descripcion, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, number_format($value_transferencia->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, number_format($value_transferencia->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
            $pdf->ln(5);

            $pdf->SetFont('', '', 7);

            /*$ac_transferencia_dos = tab_ac_ae_partida::
            join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
            ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
            ->join('mantenimiento.tab_ac_predefinida as t03', 't03.id', '=', 't01.id_accion')
            ->join('mantenimiento.tab_sectores as t04', 't04.tx_codigo', '=', 't02.co_sector')
            ->join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
            ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 5)'))
            ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 7)'))
            ->select( 't03.nu_original', 't03.de_nombre', DB::raw('sum(public.t54_ac_ae_partidas.monto) as mo_partida') )
            ->where('t01.id_ejercicio', '=', $ejercicio)
            ->where('t05.id_tab_ejercicio_fiscal', '=', $ejercicio)
            ->where('t06.id_tab_ejercicio_fiscal', '=', $ejercicio)
            ->where('t07.id_tab_ejercicio_fiscal', '=', $ejercicio)
            ->where(DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'), '=', '407')
            ->where('t02.co_sector', '=', $value_transferencia->co_sector)
            ->groupBy('t03.nu_original')
            ->groupBy('t03.de_nombre')
            ->orderBy('nu_original','ASC')
            ->get();*/

            $ac_transferencia_dos = vista_relacion_transferencia::select('nu_original', 'de_nombre', DB::raw('sum(monto) as mo_partida'))
            ->where('ef_uno', '=', $ejercicio)
            ->where('ef_dos', '=', $ejercicio)
            ->where('ef_tres', '=', $ejercicio)
            ->where('ef_cuatro', '=', $ejercicio)
            ->where('co_sector', '=', $value_transferencia->co_sector)
            ->where('id_tab_tipo_ejecutor', '=', 1)
            ->groupBy('nu_original')
            ->groupBy('de_nombre')
            ->groupBy('np_uno')
            ->orderBy('nu_original', 'ASC')
            ->get();

            foreach ($ac_transferencia_dos as $key => $value_transferencia_dos) {

                $pdf->SetFont('', '', 7);
                $pdf->setCellHeightRatio(1.2);

                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                //$pdf->MultiCell(10, 5, $value_transferencia_dos->nu_original, 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, substr($value_transferencia_dos->nu_original, -2), 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(2, 5, '', 0, 'C', 0, 0, '', '', true);
                if($value_transferencia->co_sector=='15' && $value_transferencia_dos->nu_original=='56') {
                    $pdf->MultiCell(69, 5, mb_strtoupper($value_transferencia_dos->de_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, number_format($value_transferencia_dos->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, number_format($value_transferencia_dos->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                    //$pdf->ln(5);
                } else {
                    $pdf->MultiCell(69, 5, mb_strtoupper($value_transferencia_dos->de_nombre, 'UTF-8'), 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, number_format($value_transferencia_dos->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, number_format($value_transferencia_dos->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                    //$pdf->ln(5);
                }

                $condicionAcPr = strlen($value_transferencia_dos->de_nombre);
                if ($condicionAcPr >= 30) {
                    $pdf->ln(10);
                } else {
                    $pdf->ln(5);
                }
                if ($condicionAcPr >= 100) {
                    $pdf->ln(5);
                }

                $pdf->SetFont('', '', 7);
                $pdf->setCellHeightRatio(0.8);

                $start_y = $pdf->GetY();

                $culminado = false;

                if ($start_y >= 245) {

                    $pdf->SetFont('', 'B', 8);
                    $pdf->setCellHeightRatio(1.5);
                    $pdf->SetY(262);
                    $pdf->MultiCell(121, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
                    $pdf->SetFont('', 'B', 7);
                    $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, '', 1, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

                    $pdf->SetFont('', '', 7);

                    // reset font stretching  reset font spacing
                    $pdf->setFontStretching(100);
                    $pdf->setFontSpacing(0);
                    $pdf->SetLineWidth(0.150);
                    $pdf->setCellHeightRatio(2);

                    $pdf->AddPage();

                    $pdf->SetFont('', 'B', 7);
                    $pdf->setCellHeightRatio(1.2);
                    $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->setCellHeightRatio(2);
                    $pdf->SetFont('', 'B', 10);
                    $pdf->setCellHeightRatio(1);
                    $pdf->MultiCell(90, 5, 'RELACIÓN DE TRANSFERENCIAS', 0, 'C', 0, 0, '', '', true);
                    $pdf->setCellHeightRatio(2);
                    $pdf->ln(8);
                    $pdf->SetFont('', 'B', 7);
                    $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
                    $pdf->ln(-10);
                    $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->ln(19);
                    $pdf->SetFont('', '', 8);

                    $pdf->MultiCell(196, 240, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->ln(0);
                    $pdf->SetFont('', 'B', 7);
                    $pdf->ln(30);
                    $pdf->StartTransform();
                    $pdf->Rotate(90);
                    $pdf->MultiCell(30, 10, 'SECTOR', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(10);
                    $pdf->MultiCell(30, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(10);
                    $pdf->MultiCell(30, 10, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(10);
                    $pdf->MultiCell(30, 10, 'SUB - PARTIDA GENERICA', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(10);
                    $pdf->MultiCell(30, 10, 'SUB - PARTIDA ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                    $pdf->ln(10);
                    $pdf->StopTransform();
                    $pdf->ln(-80);
                    $pdf->SetFont('', 'B', 8);
                    $pdf->setCellHeightRatio(10);
                    $pdf->MultiCell(50, 30, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(71, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 30, 'CORRIENTES', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 30, 'CAPITAL', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 30, 'MONTO TOTAL', 1, 'C', 0, 0, '', '', true);
                    $pdf->ln(30);
                    $pdf->setCellHeightRatio(1);
                    $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(71, 205, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                    $pdf->SetY(267);
                    $pdf->SetFont('', '', 7);
                    $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                    $pdf->ln(-210);
                    $pdf->ln(2);
                    $pdf->SetFont('', '', 7);
                    $pdf->setCellHeightRatio(0.8);

                }


                $pdf->SetFont('', '', 7);

                /*$ac_transferencia_tres = tab_ac_ae_partida::
                join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
                ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
                ->join('mantenimiento.tab_ac_predefinida as t03', 't03.id', '=', 't01.id_accion')
                ->join('mantenimiento.tab_sectores as t04', 't04.tx_codigo', '=', 't02.co_sector')
                ->join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
                ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 5)'))
                ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 7)'))
                ->select( 't05.co_partida', 't05.tx_nombre', DB::raw('sum(public.t54_ac_ae_partidas.monto) as mo_partida') )
                ->where('t01.id_ejercicio', '=', $ejercicio)
                ->where('t05.id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('t06.id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('t07.id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where(DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'), '=', '407')
                ->where('t02.co_sector', '=', $value_transferencia->co_sector)
                ->where('t03.nu_original', '=', $value_transferencia_dos->nu_original)
                ->groupBy('t05.co_partida')
                ->groupBy('t05.tx_nombre')
                ->orderBy('t05.co_partida','ASC')
                ->get();*/

                $ac_transferencia_tres = vista_relacion_transferencia::join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 3)'))
                ->select('t05.co_partida', 'np_uno as tx_nombre', DB::raw('sum(monto) as mo_partida'))
                ->where('ef_uno', '=', $ejercicio)
                ->where('ef_dos', '=', $ejercicio)
                ->where('ef_tres', '=', $ejercicio)
                ->where('ef_cuatro', '=', $ejercicio)
                ->where('t05.id_tab_ejercicio_fiscal', '=', $ejercicio)
                ->where('id_tab_tipo_ejecutor', '=', 1)
                ->where('co_sector', '=', $value_transferencia->co_sector)
                ->where('nu_original', '=', $value_transferencia_dos->nu_original)
                ->groupBy('t05.co_partida')
                ->groupBy('np_uno')
                ->orderBy('co_partida', 'ASC')
                ->get();

                foreach ($ac_transferencia_tres as $key => $value_transferencia_tres) {

                    $pdf->SetFont('', '', 7);

                    $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 5, trim($value_transferencia_tres->co_partida), 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(4, 5, '', 0, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(67, 5, $value_transferencia_tres->tx_nombre, 0, 'L', 0, 0, '', '', true);
                    //$pdf->MultiCell(25, 5, number_format($value_transferencia_tres->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                    //$pdf->MultiCell(25, 5, number_format($value_transferencia_tres->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                    $pdf->ln(5);

                    $pdf->SetFont('', '', 7);

                    $start_y = $pdf->GetY();

                    $culminado = false;

                    if ($start_y >= 245) {

                        $pdf->SetFont('', 'B', 8);
                        $pdf->setCellHeightRatio(1.5);
                        $pdf->SetY(262);
                        $pdf->MultiCell(121, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 5, '', 1, 'R', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

                        $pdf->SetFont('', '', 7);

                        // reset font stretching  reset font spacing
                        $pdf->setFontStretching(100);
                        $pdf->setFontSpacing(0);
                        $pdf->SetLineWidth(0.150);
                        $pdf->setCellHeightRatio(2);

                        $pdf->AddPage();

                        $pdf->SetFont('', 'B', 7);
                        $pdf->setCellHeightRatio(1.2);
                        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->setCellHeightRatio(2);
                        $pdf->SetFont('', 'B', 10);
                        $pdf->setCellHeightRatio(1);
                        $pdf->MultiCell(90, 5, 'RELACIÓN DE TRANSFERENCIAS', 0, 'C', 0, 0, '', '', true);
                        $pdf->setCellHeightRatio(2);
                        $pdf->ln(8);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
                        $pdf->ln(-10);
                        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(19);
                        $pdf->SetFont('', '', 8);

                        $pdf->MultiCell(196, 240, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(0);
                        $pdf->SetFont('', 'B', 7);
                        $pdf->ln(30);
                        $pdf->StartTransform();
                        $pdf->Rotate(90);
                        $pdf->MultiCell(30, 10, 'SECTOR', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->MultiCell(30, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->MultiCell(30, 10, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->MultiCell(30, 10, 'SUB - PARTIDA GENERICA', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->MultiCell(30, 10, 'SUB - PARTIDA ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                        $pdf->ln(10);
                        $pdf->StopTransform();
                        $pdf->ln(-80);
                        $pdf->SetFont('', 'B', 8);
                        $pdf->setCellHeightRatio(10);
                        $pdf->MultiCell(50, 30, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(71, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 30, 'CORRIENTES', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 30, 'CAPITAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 30, 'MONTO TOTAL', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(30);
                        $pdf->setCellHeightRatio(1);
                        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(71, 205, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                        $pdf->ln(2);
                        $pdf->SetFont('', '', 7);
                        $pdf->setCellHeightRatio(0.8);

                        $pdf->SetY(267);
                        $pdf->SetFont('', '', 7);
                        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                        $pdf->ln(-210);

                    }

                    /*$ac_transferencia_cuatro = tab_ac_ae_partida::
                    join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
                    ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
                    ->join('mantenimiento.tab_ac_predefinida as t03', 't03.id', '=', 't01.id_accion')
                    ->join('mantenimiento.tab_sectores as t04', 't04.tx_codigo', '=', 't02.co_sector')
                    ->join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
                    ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 5)'))
                    ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 7)'))
                    ->select( 't06.co_partida', 't06.tx_nombre', DB::raw('sum(public.t54_ac_ae_partidas.monto) as mo_partida') )
                    ->where('t01.id_ejercicio', '=', $ejercicio)
                    ->where('t05.id_tab_ejercicio_fiscal', '=', $ejercicio)
                    ->where('t06.id_tab_ejercicio_fiscal', '=', $ejercicio)
                    ->where('t07.id_tab_ejercicio_fiscal', '=', $ejercicio)
                    ->where(DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'), '=', '407')
                    ->where('t02.co_sector', '=', $value_transferencia->co_sector)
                    ->where('t03.nu_original', '=', $value_transferencia_dos->nu_original)
                    ->where('t05.co_partida', '=', $value_transferencia_tres->co_partida)
                    ->groupBy('t06.co_partida')
                    ->groupBy('t06.tx_nombre')
                    ->orderBy('t06.co_partida','ASC')
                    ->get();*/

                    $ac_transferencia_cuatro = vista_relacion_transferencia::join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 3)'))
                    ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 5)'))
                    ->select('t06.co_partida', 'np_dos as tx_nombre', DB::raw('sum(monto) as mo_partida'))
                    ->where('ef_uno', '=', $ejercicio)
                    ->where('ef_dos', '=', $ejercicio)
                    ->where('ef_tres', '=', $ejercicio)
                    ->where('ef_cuatro', '=', $ejercicio)
                    ->where('t05.id_tab_ejercicio_fiscal', '=', $ejercicio)
                    ->where('t06.id_tab_ejercicio_fiscal', '=', $ejercicio)
                    ->where('co_sector', '=', $value_transferencia->co_sector)
                    ->where('nu_original', '=', $value_transferencia_dos->nu_original)
                    ->where('t05.co_partida', '=', $value_transferencia_tres->co_partida)
                    ->where('id_tab_tipo_ejecutor', '=', 1)
                    ->groupBy('t06.co_partida')
                    ->groupBy('np_dos')
                    ->orderBy('t06.co_partida', 'ASC')
                    ->get();

                    foreach ($ac_transferencia_cuatro as $key => $value_transferencia_cuatro) {

                        $pdf->SetFont('', 'B', 7);
                        $pdf->setCellHeightRatio(1);

                        $nivel_tres = trim($value_transferencia_tres->co_partida);
                        $nivel_cuatro = substr(substr(trim($value_transferencia_cuatro->co_partida), 0, 5), 3);

                        $partida_capital_uno = '40703';
                        //              $partida_capital_dos = '40701';
                        $partida_capital_dos = '40801';
                        $partida_referencia = $nivel_tres.$nivel_cuatro;

                        $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(10, 5, substr(substr(trim($value_transferencia_cuatro->co_partida), 0, 5), 3), 0, 'C', 0, 0, '', '', true);
                        //$pdf->writeHTMLCell(10,5, '', '', '<u>'.substr(substr(trim($value_transferencia_cuatro->co_partida), 0, 5), 3).'</u>', 0, 0, 0, true, 'C', true);
                        $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(6, 5, '', 0, 'C', 0, 0, '', '', true);
                        $pdf->MultiCell(65, 5, $value_transferencia_cuatro->tx_nombre, 0, 'L', 0, 0, '', '', true);

                        if($partida_capital_uno == $partida_referencia) {
                            if($value_transferencia->co_sector=='15' && $value_transferencia_dos->nu_original=='56') {
                                //$pdf->writeHTMLCell(65,5, '', '', '<u>'.$value_transferencia_cuatro->tx_nombre.'</u>', 0, 0, 0, true, 'L', true);
                                $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 5, number_format($value_transferencia_cuatro->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                            } else {
                                $pdf->MultiCell(25, 5, number_format($value_transferencia_cuatro->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                            }

                        } elseif($partida_capital_dos == $partida_referencia) {

                            //$pdf->writeHTMLCell(65,5, '', '', '<u>'.$value_transferencia_cuatro->tx_nombre.'</u>', 0, 0, 0, true, 'L', true);
                            $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                            $pdf->MultiCell(25, 5, number_format($value_transferencia_cuatro->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                        } else {

                            //$pdf->writeHTMLCell(65,5, '', '', '<u>'.$value_transferencia_cuatro->tx_nombre.'</u>', 0, 0, 0, true, 'L', true);
                            $pdf->MultiCell(25, 5, number_format($value_transferencia_cuatro->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                            $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);

                        }

                        $pdf->MultiCell(25, 5, number_format($value_transferencia_cuatro->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                        $condicionPartida = strlen($value_transferencia_cuatro->tx_nombre);
                        if ($condicionPartida >= 30) {
                            $pdf->ln(10);
                        } else {
                            $pdf->ln(5);
                        }

                        $pdf->SetFont('', '', 7);
                        $pdf->setCellHeightRatio(0.8);

                        /*$ac_transferencia_cinco = tab_ac_ae_partida::
                        join('public.t46_acciones_centralizadas as t01', 't01.id', '=', 'public.t54_ac_ae_partidas.id_accion_centralizada')
                        ->join('mantenimiento.tab_sectores as t02', 't02.id', '=', 't01.id_subsector')
                        ->join('mantenimiento.tab_ac_predefinida as t03', 't03.id', '=', 't01.id_accion')
                        ->join('mantenimiento.tab_sectores as t04', 't04.tx_codigo', '=', 't02.co_sector')
                        ->join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'))
                        ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 5)'))
                        ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.t54_ac_ae_partidas.co_partida, 7)'))
                        ->select( 't07.co_partida', 't07.tx_nombre', DB::raw('sum(public.t54_ac_ae_partidas.monto) as mo_partida') )
                        ->where('t01.id_ejercicio', '=', $ejercicio)
                        ->where('t05.id_tab_ejercicio_fiscal', '=', $ejercicio)
                        ->where('t06.id_tab_ejercicio_fiscal', '=', $ejercicio)
                        ->where('t07.id_tab_ejercicio_fiscal', '=', $ejercicio)
                        ->where(DB::raw('left(public.t54_ac_ae_partidas.co_partida, 3)'), '=', '407')
                        ->where('t02.co_sector', '=', $value_transferencia->co_sector)
                        ->where('t03.nu_original', '=', $value_transferencia_dos->nu_original)
                        ->where('t05.co_partida', '=', $value_transferencia_tres->co_partida)
                        ->where('t06.co_partida', '=', $value_transferencia_cuatro->co_partida)
                        ->groupBy('t07.co_partida')
                        ->groupBy('t07.tx_nombre')
                        ->orderBy('t07.co_partida','ASC')
                        ->get();*/

                        $ac_transferencia_cinco = vista_relacion_transferencia::join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 3)'))
                        ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 5)'))
                        ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 7)'))
                        ->select('t07.co_partida', 'np_tres as tx_nombre', DB::raw('sum(monto) as mo_partida'))
                        ->where('ef_uno', '=', $ejercicio)
                        ->where('ef_dos', '=', $ejercicio)
                        ->where('ef_tres', '=', $ejercicio)
                        ->where('ef_cuatro', '=', $ejercicio)
                        ->where('t05.id_tab_ejercicio_fiscal', '=', $ejercicio)
                        ->where('t06.id_tab_ejercicio_fiscal', '=', $ejercicio)
                        ->where('t07.id_tab_ejercicio_fiscal', '=', $ejercicio)
                        ->where('co_sector', '=', $value_transferencia->co_sector)
                        ->where('nu_original', '=', $value_transferencia_dos->nu_original)
                        ->where('t05.co_partida', '=', $value_transferencia_tres->co_partida)
                        ->where('t06.co_partida', '=', $value_transferencia_cuatro->co_partida)
                        ->where('id_tab_tipo_ejecutor', '=', 1)
                        ->groupBy('t07.co_partida')
                        ->groupBy('np_tres')
                        ->orderBy('t07.co_partida', 'ASC')
                        ->get();

                        foreach ($ac_transferencia_cinco as $key => $value_transferencia_cinco) {

                            $pdf->SetFont('', 'B', 7);
                            $pdf->setCellHeightRatio(1);

                            $nivel_tres = trim($value_transferencia_tres->co_partida);
                            $nivel_cuatro = substr(substr(trim($value_transferencia_cuatro->co_partida), 0, 5), 3);
                            $nivel_cinco = substr(substr(trim($value_transferencia_cinco->co_partida), 0, 7), 5);

                            $partida_capital_uno = '4070303';
                            //                $partida_capital_dos = '4070103';
                            $partida_capital_dos = '4080103';
                            $partida_referencia = $nivel_tres.$nivel_cuatro.$nivel_cinco;

                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                            //$pdf->MultiCell(10, 5, substr(substr(trim($value_transferencia_cinco->co_partida), 0, 7), 5), 0, 'C', 0, 0, '', '', true);
                            $pdf->writeHTMLCell(10, 5, '', '', '<u>'.substr(substr(trim($value_transferencia_cinco->co_partida), 0, 7), 5).'</u>', 0, 0, 0, true, 'C', true);
                            $pdf->MultiCell(6, 5, '', 0, 'C', 0, 0, '', '', true);
                            //$pdf->MultiCell(65, 5, $value_transferencia_cinco->tx_nombre, 0, 'L', 0, 0, '', '', true);
                            $pdf->writeHTMLCell(65, 5, '', '', '<u>'.$value_transferencia_cinco->tx_nombre.'</u>', 0, 0, 0, true, 'L', true);

                            if($partida_capital_uno == $partida_referencia) {

                                //$pdf->MultiCell(25, 5, number_format($value_transferencia_cinco->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                                $pdf->writeHTMLCell(26, 5, '', '', '<u>'.number_format($value_transferencia_cinco->mo_partida, 0, ',', '.').'</u>', 0, 0, 0, true, 'R', true);

                                $movimiento_capital =   $movimiento_capital + $value_transferencia_cinco->mo_partida;

                            } elseif($partida_capital_dos == $partida_referencia) {

                                //$pdf->MultiCell(25, 5, number_format($value_transferencia_cinco->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                                $pdf->writeHTMLCell(26, 5, '', '', '<u>'.number_format($value_transferencia_cinco->mo_partida, 0, ',', '.').'</u>', 0, 0, 0, true, 'R', true);

                            } else {

                                //$pdf->MultiCell(25, 5, number_format($value_transferencia_cinco->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                $pdf->writeHTMLCell(26, 5, '', '', '<u>'.number_format($value_transferencia_cinco->mo_partida, 0, ',', '.').'</u>', 0, 0, 0, true, 'R', true);
                                $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);

                            }

                            //$pdf->MultiCell(25, 5, number_format($value_transferencia_cinco->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                            $pdf->writeHTMLCell(25, 5, '', '', '<u>'.number_format($value_transferencia_cinco->mo_partida, 0, ',', '.').'</u>', 0, 0, 0, true, 'R', true);

                            $pdf->SetFont('', '', 7);

                            $condicionPartida = strlen($value_transferencia_cinco->tx_nombre);
                            if ($condicionPartida >= 30 && $condicionPartida < 60) {
                                $pdf->ln(7);
                            } elseif($condicionPartida >= 60) {
                                $pdf->ln(10);
                            } else {
                                $pdf->ln(5);
                            }

                            $nivel_tres = trim($value_transferencia_tres->co_partida);
                            $nivel_cuatro = substr(substr(trim($value_transferencia_cuatro->co_partida), 0, 5), 3);
                            $nivel_cinco = substr(substr(trim($value_transferencia_cinco->co_partida), 0, 7), 5);

                            $partida_desagregado_municipio = $nivel_tres.$nivel_cuatro;

                            $tab_distribucion_municipio = tab_distribucion_municipio::join('mantenimiento.tab_municipio as t01', 't01.id', '=', 'mantenimiento.tab_distribucion_municipio.id_tab_municipio')
                            ->select('mantenimiento.tab_distribucion_municipio.id', 'co_partida', 'mo_total', 'de_municipio')
                            ->where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                            ->where('tab_distribucion_municipio.in_activo', '=', true)
                            ->where(DB::raw('left(co_partida::bigint::text::varchar, 5)'), '=', trim($partida_desagregado_municipio))
                            ->orderBy('de_municipio', 'ASC')
                            ->get();

                            foreach ($tab_distribucion_municipio as $key => $value_distribucion_municipio) {

                                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(10, 5, '', 0, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(6, 5, '', 0, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(65, 5, $value_distribucion_municipio->de_municipio, 0, 'L', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 5, number_format($value_distribucion_municipio->mo_total, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 5, '', 0, 'R', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 5, number_format($value_distribucion_municipio->mo_total, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                                $pdf->ln(5);

                                $start_y = $pdf->GetY();

                                if ($start_y >= 245) {

                                    $pdf->SetFont('', 'B', 8);
                                    $pdf->setCellHeightRatio(1.5);
                                    $pdf->SetY(262);
                                    $pdf->MultiCell(121, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
                                    $pdf->SetFont('', 'B', 7);
                                    $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(25, 5, '', 1, 'R', 0, 0, '', '', true);
                                    $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

                                    $pdf->SetFont('', '', 7);

                                    // reset font stretching  reset font spacing
                                    $pdf->setFontStretching(100);
                                    $pdf->setFontSpacing(0);
                                    $pdf->SetLineWidth(0.150);
                                    $pdf->setCellHeightRatio(2);

                                    $pdf->AddPage();

                                    $pdf->SetFont('', 'B', 7);
                                    $pdf->setCellHeightRatio(1.2);
                                    $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                                    $pdf->setCellHeightRatio(2);
                                    $pdf->SetFont('', 'B', 10);
                                    $pdf->setCellHeightRatio(1);
                                    $pdf->MultiCell(90, 5, 'RELACIÓN DE TRANSFERENCIAS', 0, 'C', 0, 0, '', '', true);
                                    $pdf->setCellHeightRatio(2);
                                    $pdf->ln(8);
                                    $pdf->SetFont('', 'B', 7);
                                    $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                                    $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
                                    $pdf->ln(-10);
                                    $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                                    $pdf->ln(19);
                                    $pdf->SetFont('', '', 8);

                                    $pdf->MultiCell(196, 240, '', 1, 'C', 0, 0, '', '', true);
                                    $pdf->ln(0);
                                    $pdf->SetFont('', 'B', 7);
                                    $pdf->ln(30);
                                    $pdf->StartTransform();
                                    $pdf->Rotate(90);
                                    $pdf->MultiCell(30, 10, 'SECTOR', 1, 'L', 0, 0, '', '', true);
                                    $pdf->ln(10);
                                    $pdf->MultiCell(30, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                                    $pdf->ln(10);
                                    $pdf->MultiCell(30, 10, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
                                    $pdf->ln(10);
                                    $pdf->MultiCell(30, 10, 'SUB - PARTIDA GENERICA', 1, 'L', 0, 0, '', '', true);
                                    $pdf->ln(10);
                                    $pdf->MultiCell(30, 10, 'SUB - PARTIDA ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                                    $pdf->ln(10);
                                    $pdf->StopTransform();
                                    $pdf->ln(-80);
                                    $pdf->SetFont('', 'B', 8);
                                    $pdf->setCellHeightRatio(10);
                                    $pdf->MultiCell(50, 30, '', 0, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(71, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(25, 30, 'CORRIENTES', 1, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(25, 30, 'CAPITAL', 1, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(25, 30, 'MONTO TOTAL', 1, 'C', 0, 0, '', '', true);
                                    $pdf->ln(30);
                                    $pdf->setCellHeightRatio(1);
                                    $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(71, 205, '', 1, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                    $pdf->ln(2);
                                    $pdf->SetFont('', '', 7);
                                    $pdf->setCellHeightRatio(0.8);

                                    $pdf->SetY(267);
                                    $pdf->SetFont('', '', 7);
                                    $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                                    $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                                    $pdf->ln(-210);

                                }

                            }

                            $movimiento = $movimiento + $value_transferencia_cinco->mo_partida;

                            if($value_transferencia_dos->nu_original == 52) {

                                $ac_transferencia_seis = vista_relacion_transferencia::join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 3)'))
                                ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 5)'))
                                ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 7)'))
                                ->join('mantenimiento.tab_partidas as t08', 't08.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 9)'))
                                ->select('id_accion_centralizada', 't08.co_partida', 'np_tres as tx_nombre', DB::raw('sum(monto) as mo_partida'))
                                ->where('ef_uno', '=', $ejercicio)
                                ->where('ef_dos', '=', $ejercicio)
                                ->where('ef_tres', '=', $ejercicio)
                                ->where('ef_cuatro', '=', $ejercicio)
                                ->where('t05.id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('t06.id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('t07.id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('t08.id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('co_sector', '=', $value_transferencia->co_sector)
                                ->where('nu_original', '=', $value_transferencia_dos->nu_original)
                                ->where('t05.co_partida', '=', $value_transferencia_tres->co_partida)
                                ->where('t06.co_partida', '=', $value_transferencia_cuatro->co_partida)
                                ->where('t07.co_partida', '=', $value_transferencia_cinco->co_partida)
                                ->where('id_tab_tipo_ejecutor', '=', 1)
                                ->groupBy('id_accion_centralizada')
                                ->groupBy('t08.co_partida')
                                ->groupBy('np_tres')
                                ->orderBy('t08.co_partida', 'ASC')
                                ->get();


                                foreach ($ac_transferencia_seis as $key => $value_transferencia_seis) {

                                    $ac_transferencia_cinco_detalle = tab_ac_es_partida_desagregado::select('co_partida', 'de_denominacion', 'mo_partida')
                                    ->where('id_tab_ejercicio_fiscal', '=', $ejercicio)
                                    ->where('td_tab_ac', '=', $value_transferencia_seis->id_accion_centralizada)
                                    //->where('id_tab_ac_ae_predefinida', '=', $value_distribucion_siete->id_tab_ac_ae_predef)
                                    ->where(DB::raw('left(co_partida::bigint::text::varchar, 9)'), '=', trim($value_transferencia_seis->co_partida))
                                    ->orderBy('co_partida', 'ASC')
                                    ->get();

                                    //                  $ac_transferencia_cinco_detalle = vista_relacion_transferencia::
                                    //                  join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 3)'))
                                    //                  ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 5)'))
                                    //                  ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 7)'))
                                    //                  ->select( 'public.vista_relacion_transferencia.id_ejecutor', DB::raw('sum(monto) as mo_partida') )
                                    //                  ->where('ef_uno', '=', $ejercicio)
                                    //                  ->where('ef_dos', '=', $ejercicio)
                                    //                  ->where('ef_tres', '=', $ejercicio)
                                    //                  ->where('ef_cuatro', '=', $ejercicio)
                                    //                  ->where('t05.id_tab_ejercicio_fiscal', '=', $ejercicio)
                                    //                  ->where('t06.id_tab_ejercicio_fiscal', '=', $ejercicio)
                                    //                  ->where('t07.id_tab_ejercicio_fiscal', '=', $ejercicio)
                                    //                  ->where('co_sector', '=', $value_transferencia->co_sector)
                                    //                  ->where('nu_original', '=', $value_transferencia_dos->nu_original)
                                    //                  ->where('t05.co_partida', '=', $value_transferencia_tres->co_partida)
                                    //                  ->where('t06.co_partida', '=', $value_transferencia_cuatro->co_partida)
                                    //                  ->where('t07.co_partida', '=', $value_transferencia_cinco->co_partida)
                                    //                  ->where('id_tab_tipo_ejecutor', '=', 1)
                                    //                  ->groupBy('public.vista_relacion_transferencia.id_ejecutor')
                                    //                  ->orderBy('public.vista_relacion_transferencia.id_ejecutor','ASC')
                                    //                  ->get();

                                    foreach ($ac_transferencia_cinco_detalle as $key => $value_distribucion_ejecutor) {

                                        //                    $condicionEjecutor = strlen(self::obtenerEjecutor($value_distribucion_ejecutor->id_ejecutor));
                                        $condicionEjecutor = strlen($value_distribucion_ejecutor->de_denominacion);
                                        if ($condicionEjecutor >= 30 && $condicionEjecutor < 60) {
                                            $alto_ejecutor = 7;
                                        } elseif($condicionEjecutor >= 60) {
                                            $alto_ejecutor = 10;
                                        } else {
                                            $alto_ejecutor = 5;
                                        }

                                        $pdf->MultiCell(10, $alto_ejecutor, '', 0, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(10, $alto_ejecutor, '', 0, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(10, $alto_ejecutor, '', 0, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(10, $alto_ejecutor, '', 0, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(10, $alto_ejecutor, '', 0, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(6, $alto_ejecutor, '', 0, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(65, $alto_ejecutor, $value_distribucion_ejecutor->de_denominacion, 0, 'L', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, $alto_ejecutor, number_format($value_distribucion_ejecutor->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, $alto_ejecutor, '', 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, $alto_ejecutor, number_format($value_distribucion_ejecutor->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                                        $pdf->ln($alto_ejecutor);

                                        $start_y = $pdf->GetY();

                                        if ($start_y >= 245) {

                                            $pdf->SetFont('', 'B', 8);
                                            $pdf->setCellHeightRatio(1.5);
                                            $pdf->SetY(262);
                                            $pdf->MultiCell(121, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
                                            $pdf->SetFont('', 'B', 7);
                                            $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(25, 5, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'C', 0, 0, '', '', true);

                                            $pdf->SetFont('', '', 7);

                                            // reset font stretching  reset font spacing
                                            $pdf->setFontStretching(100);
                                            $pdf->setFontSpacing(0);
                                            $pdf->SetLineWidth(0.150);
                                            $pdf->setCellHeightRatio(2);

                                            $pdf->AddPage();

                                            $pdf->SetFont('', 'B', 7);
                                            $pdf->setCellHeightRatio(1.2);
                                            $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                                            $pdf->setCellHeightRatio(2);
                                            $pdf->SetFont('', 'B', 10);
                                            $pdf->setCellHeightRatio(1);
                                            $pdf->MultiCell(90, 5, 'RELACIÓN DE TRANSFERENCIAS', 0, 'C', 0, 0, '', '', true);
                                            $pdf->setCellHeightRatio(2);
                                            $pdf->ln(8);
                                            $pdf->SetFont('', 'B', 7);
                                            $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                                            $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
                                            $pdf->ln(-10);
                                            $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->ln(19);
                                            $pdf->SetFont('', '', 8);

                                            $pdf->MultiCell(196, 240, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->ln(0);
                                            $pdf->SetFont('', 'B', 7);
                                            $pdf->ln(30);
                                            $pdf->StartTransform();
                                            $pdf->Rotate(90);
                                            $pdf->MultiCell(30, 10, 'SECTOR', 1, 'L', 0, 0, '', '', true);
                                            $pdf->ln(10);
                                            $pdf->MultiCell(30, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                                            $pdf->ln(10);
                                            $pdf->MultiCell(30, 10, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
                                            $pdf->ln(10);
                                            $pdf->MultiCell(30, 10, 'SUB - PARTIDA GENERICA', 1, 'L', 0, 0, '', '', true);
                                            $pdf->ln(10);
                                            $pdf->MultiCell(30, 10, 'SUB - PARTIDA ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                                            $pdf->ln(10);
                                            $pdf->StopTransform();
                                            $pdf->ln(-80);
                                            $pdf->SetFont('', 'B', 8);
                                            $pdf->setCellHeightRatio(10);
                                            $pdf->MultiCell(50, 30, '', 0, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(71, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(25, 30, 'CORRIENTES', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(25, 30, 'CAPITAL', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(25, 30, 'MONTO TOTAL', 1, 'C', 0, 0, '', '', true);
                                            $pdf->ln(30);
                                            $pdf->setCellHeightRatio(1);
                                            $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(71, 205, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                            $pdf->ln(2);
                                            $pdf->SetFont('', '', 7);
                                            $pdf->setCellHeightRatio(0.8);

                                            $pdf->SetY(267);
                                            $pdf->SetFont('', '', 7);
                                            $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                                            $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                                            $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                                            $pdf->ln(-210);

                                        }

                                    }

                                }

                            }

                            $filtro_codigo = [53, 56];
                            if(in_array($value_transferencia_dos->nu_original, $filtro_codigo)) {

                                $ac_transferencia_cinco_detalle = vista_relacion_transferencia::join('mantenimiento.tab_partidas as t05', 't05.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 3)'))
                                ->join('mantenimiento.tab_partidas as t06', 't06.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 5)'))
                                ->join('mantenimiento.tab_partidas as t07', 't07.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 7)'))
                                ->join('mantenimiento.tab_partidas as t08', 't08.co_partida', '=', DB::raw('left(public.vista_relacion_transferencia.co_partida, 9)'))
                                ->select('t08.co_partida', 't08.tx_nombre', DB::raw('sum(monto) as mo_partida'))
                                ->where('ef_uno', '=', $ejercicio)
                                ->where('ef_dos', '=', $ejercicio)
                                ->where('ef_tres', '=', $ejercicio)
                                ->where('ef_cuatro', '=', $ejercicio)
                                ->where('t05.id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('t06.id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('t07.id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('t08.id_tab_ejercicio_fiscal', '=', $ejercicio)
                                ->where('co_sector', '=', $value_transferencia->co_sector)
                                ->where('nu_original', '=', $value_transferencia_dos->nu_original)
                                ->where('t05.co_partida', '=', $value_transferencia_tres->co_partida)
                                ->where('t06.co_partida', '=', $value_transferencia_cuatro->co_partida)
                                ->where('t07.co_partida', '=', $value_transferencia_cinco->co_partida)
                                ->groupBy('t08.co_partida')
                                ->groupBy('t08.tx_nombre')
                                ->orderBy('t08.co_partida', 'ASC')
                                ->get();

                                foreach ($ac_transferencia_cinco_detalle as $key => $value_distribucion_partida) {

                                    $condicionPartida = strlen($value_distribucion_partida->tx_nombre);
                                    if ($condicionPartida >= 30 && $condicionPartida < 60) {
                                        $alto_partida = 7;
                                    } elseif($condicionPartida >= 60 && $condicionPartida < 90) {
                                        $alto_partida = 10;
                                    } elseif($condicionPartida >= 90) {
                                        $alto_partida = 15;
                                    } else {
                                        $alto_partida = 5;
                                    }

                                    $nivel_tres = trim($value_transferencia_tres->co_partida);
                                    $nivel_cuatro = substr(substr(trim($value_transferencia_cuatro->co_partida), 0, 5), 3);
                                    $nivel_cinco = substr(substr(trim($value_transferencia_cinco->co_partida), 0, 7), 5);

                                    $partida_capital_uno = '4070303';
                                    //                $partida_capital_dos = '4070103';
                                    $partida_capital_dos = '4080103';
                                    $partida_referencia = $nivel_tres.$nivel_cuatro.$nivel_cinco;

                                    $pdf->MultiCell(10, $alto_partida, '', 0, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(10, $alto_partida, '', 0, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(10, $alto_partida, '', 0, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(10, $alto_partida, '', 0, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(10, $alto_partida, '', 0, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(6, $alto_partida, '', 0, 'C', 0, 0, '', '', true);
                                    $pdf->MultiCell(65, $alto_partida, $value_distribucion_partida->tx_nombre, 0, 'L', 0, 0, '', '', true);

                                    if($partida_capital_uno == $partida_referencia) {

                                        $pdf->MultiCell(25, $alto_partida, '', 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, $alto_partida, number_format($value_distribucion_partida->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, $alto_partida, number_format($value_distribucion_partida->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);


                                    } elseif($partida_capital_dos == $partida_referencia) {

                                        $pdf->MultiCell(25, $alto_partida, '', 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, $alto_partida, number_format($value_distribucion_partida->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, $alto_partida, number_format($value_distribucion_partida->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                                    } else {

                                        $pdf->MultiCell(25, $alto_partida, number_format($value_distribucion_partida->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, $alto_partida, '', 0, 'R', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, $alto_partida, number_format($value_distribucion_partida->mo_partida, 0, ',', '.'), 0, 'R', 0, 0, '', '', true);

                                    }




                                    $pdf->ln($alto_partida);

                                    $start_y = $pdf->GetY();

                                    if ($start_y >= 245) {

                                        $pdf->SetFont('', 'B', 8);
                                        $pdf->setCellHeightRatio(1.5);
                                        $pdf->SetY(262);
                                        $pdf->MultiCell(121, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
                                        $pdf->SetFont('', 'B', 7);
                                        $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, 5, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'C', 0, 0, '', '', true);

                                        $pdf->SetFont('', '', 7);

                                        // reset font stretching  reset font spacing
                                        $pdf->setFontStretching(100);
                                        $pdf->setFontSpacing(0);
                                        $pdf->SetLineWidth(0.150);
                                        $pdf->setCellHeightRatio(2);

                                        $pdf->AddPage();

                                        $pdf->SetFont('', 'B', 7);
                                        $pdf->setCellHeightRatio(1.2);
                                        $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                                        $pdf->setCellHeightRatio(2);
                                        $pdf->SetFont('', 'B', 10);
                                        $pdf->setCellHeightRatio(1);
                                        $pdf->MultiCell(90, 5, 'RELACIÓN DE TRANSFERENCIAS', 0, 'C', 0, 0, '', '', true);
                                        $pdf->setCellHeightRatio(2);
                                        $pdf->ln(8);
                                        $pdf->SetFont('', 'B', 7);
                                        $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                                        $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
                                        $pdf->ln(-10);
                                        $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->ln(19);
                                        $pdf->SetFont('', '', 8);

                                        $pdf->MultiCell(196, 240, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->ln(0);
                                        $pdf->SetFont('', 'B', 7);
                                        $pdf->ln(30);
                                        $pdf->StartTransform();
                                        $pdf->Rotate(90);
                                        $pdf->MultiCell(30, 10, 'SECTOR', 1, 'L', 0, 0, '', '', true);
                                        $pdf->ln(10);
                                        $pdf->MultiCell(30, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                                        $pdf->ln(10);
                                        $pdf->MultiCell(30, 10, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
                                        $pdf->ln(10);
                                        $pdf->MultiCell(30, 10, 'SUB - PARTIDA GENERICA', 1, 'L', 0, 0, '', '', true);
                                        $pdf->ln(10);
                                        $pdf->MultiCell(30, 10, 'SUB - PARTIDA ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                                        $pdf->ln(10);
                                        $pdf->StopTransform();
                                        $pdf->ln(-80);
                                        $pdf->SetFont('', 'B', 8);
                                        $pdf->setCellHeightRatio(10);
                                        $pdf->MultiCell(50, 30, '', 0, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(71, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, 30, 'CORRIENTES', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, 30, 'CAPITAL', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, 30, 'MONTO TOTAL', 1, 'C', 0, 0, '', '', true);
                                        $pdf->ln(30);
                                        $pdf->setCellHeightRatio(1);
                                        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(71, 205, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                        $pdf->ln(2);
                                        $pdf->SetFont('', '', 7);
                                        $pdf->setCellHeightRatio(0.8);

                                        $pdf->SetY(267);
                                        $pdf->SetFont('', '', 7);
                                        $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                                        $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                                        $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                                        $pdf->ln(-210);

                                    }

                                }

                            }

                            $pdf->SetFont('', '', 7);
                            $pdf->setCellHeightRatio(0.8);

                            $start_y = $pdf->GetY();

                            $culminado = false;

                            if ($start_y >= 245) {

                                $pdf->SetFont('', 'B', 8);
                                $pdf->setCellHeightRatio(1.5);
                                $pdf->SetY(262);
                                $pdf->MultiCell(121, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
                                $pdf->SetFont('', 'B', 7);
                                $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 5, '', 1, 'R', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);

                                $pdf->SetFont('', '', 7);

                                // reset font stretching  reset font spacing
                                $pdf->setFontStretching(100);
                                $pdf->setFontSpacing(0);
                                $pdf->SetLineWidth(0.150);
                                $pdf->setCellHeightRatio(2);

                                $pdf->AddPage();

                                $pdf->SetFont('', 'B', 7);
                                $pdf->setCellHeightRatio(1.2);
                                $pdf->MultiCell(40, 5, 'GOBERNACIÓN BOLIVARIANA DEL ESTADO ZULIA', 0, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 5, '', 0, 'C', 0, 0, '', '', true);
                                $pdf->setCellHeightRatio(2);
                                $pdf->SetFont('', 'B', 10);
                                $pdf->setCellHeightRatio(1);
                                $pdf->MultiCell(90, 5, 'RELACIÓN DE TRANSFERENCIAS', 0, 'C', 0, 0, '', '', true);
                                $pdf->setCellHeightRatio(2);
                                $pdf->ln(8);
                                $pdf->SetFont('', 'B', 7);
                                $pdf->MultiCell(65, 5, 'PRESUPUESTO '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                                $pdf->MultiCell(90, 5, '(EN BOLÍVARES)', 0, 'C', 0, 0, '', '', true);
                                $pdf->ln(-10);
                                $pdf->MultiCell(196, 18, '', 1, 'C', 0, 0, '', '', true);
                                $pdf->ln(19);
                                $pdf->SetFont('', '', 8);

                                $pdf->MultiCell(196, 240, '', 1, 'C', 0, 0, '', '', true);
                                $pdf->ln(0);
                                $pdf->SetFont('', 'B', 7);
                                $pdf->ln(30);
                                $pdf->StartTransform();
                                $pdf->Rotate(90);
                                $pdf->MultiCell(30, 10, 'SECTOR', 1, 'L', 0, 0, '', '', true);
                                $pdf->ln(10);
                                $pdf->MultiCell(30, 10, 'PROY. Y/O ACCIÓN CENTRALIZADA', 1, 'L', 0, 0, '', '', true);
                                $pdf->ln(10);
                                $pdf->MultiCell(30, 10, 'PARTIDA', 1, 'L', 0, 0, '', '', true);
                                $pdf->ln(10);
                                $pdf->MultiCell(30, 10, 'SUB - PARTIDA GENERICA', 1, 'L', 0, 0, '', '', true);
                                $pdf->ln(10);
                                $pdf->MultiCell(30, 10, 'SUB - PARTIDA ESPECIFICA', 1, 'L', 0, 0, '', '', true);
                                $pdf->ln(10);
                                $pdf->StopTransform();
                                $pdf->ln(-80);
                                $pdf->SetFont('', 'B', 8);
                                $pdf->setCellHeightRatio(10);
                                $pdf->MultiCell(50, 30, '', 0, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(71, 30, 'DENOMINACIÓN', 1, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 30, 'CORRIENTES', 1, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 30, 'CAPITAL', 1, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 30, 'MONTO TOTAL', 1, 'C', 0, 0, '', '', true);
                                $pdf->ln(30);
                                $pdf->setCellHeightRatio(1);
                                $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(10, 205, '', 1, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(71, 205, '', 1, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(25, 205, '', 1, 'C', 0, 0, '', '', true);
                                $pdf->ln(2);
                                $pdf->SetFont('', '', 7);
                                $pdf->setCellHeightRatio(0.8);

                                $pdf->SetY(267);
                                $pdf->SetFont('', '', 7);
                                $pdf->MultiCell(29, 5, 'Pag. '.$pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(), 0, 'L', 0, 0, '', '', true);
                                $pdf->MultiCell(151, 5, '', 0, 'C', 0, 0, '', '', true);
                                $pdf->MultiCell(16, 5, 'GEZ: '.$ejercicio, 0, 'L', 0, 0, '', '', true);
                                $pdf->ln(-210);

                            }

                        }

                    }

                }

            }

        }

        $culminado = true;

        if($culminado ==true) {
            $pdf->SetFont('', 'B', 8);
            $pdf->setCellHeightRatio(1.5);
            $pdf->SetY(262);
            $pdf->MultiCell(121, 5, 'TOTAL', 1, 'R', 0, 0, '', '', true);
            $pdf->SetFont('', 'B', 7);
            $pdf->MultiCell(25, 5, number_format($movimiento-$movimiento_capital, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, number_format($movimiento_capital, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(25, 5, number_format($movimiento, 0, ',', '.'), 1, 'R', 0, 0, '', '', true);
        }

        //Cierre de Reporte
        $pdf->lastPage();
        $pdf->output('LEY_DE_PRESUPUESTO_'.$ejercicio.'_'.date("H:i:s").'.pdf', 'D');
    }

    public function obtenerEjecutor($id_ejecutor)
    {

        $ejecutor = tab_ejecutores::select('id', 'id_ejecutor', 'tx_ejecutor')
        ->where('id_ejecutor', '=', $id_ejecutor)
        ->first();

        return trim($ejecutor->tx_ejecutor);
    }
}
