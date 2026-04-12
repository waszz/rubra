<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use App\Models\Proyecto;
use App\Livewire\Concerns\AutorizaProyecto;
use App\Models\Recurso;
use App\Models\ProyectoRecurso;
use App\Models\ConfiguracionGeneral;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PresupuestoDetallado extends Component
{
    use AutorizaProyecto;

    public Proyecto $proyecto;

    // UI
    public $nodosAbiertos     = [];
    public $filtroTipo        = 'Todos';
    public $buscarSelector    = '';

    // Modal composición (agregar sub-rubro)
    public $mostrarModalSubrubro = false;
    public $modalSelector        = false;

    // Modal recursos directos
    public $mostrarModalRecursos  = false;
    public $modalSelectorRecursos = false;

    // Contexto compartido
    public $parentId     = null;
    public $categoriaCtx = '';
    public $nombreCtx    = '';

    // Modal nuevo rubro (categoría raíz)
    public $mostrarModalRubro = false;
    public $nombreRubro        = '';
    public $unidadRubro        = 'gl';

    // Formulario sub-rubro
    public $nombreSubrubro = '';
    public $unidadSubrubro = 'gl';

    // Recursos seleccionados
    public $itemsRecursos = [];

    // Modal edición
    public $mostrarModalEditar = false;
    public $editId             = null;
    public $editNombre         = '';
    public $editUnidad = '';

    // Modal eliminar
    public $mostrarModalEliminar = false;
    public $deleteId             = null;

    //Beneficio
    public $mostrarBeneficio = true;
public $mostrarModalInvitar = false;
public $usuariosDisponibles = [];
public $buscarUsuario = '';
public $usuariosSeleccionados = [];

// Modal compartir proyecto (link)
public $mostrarModalCompartir = false;
public $linkCompartible = '';
public $linkCopiado = false;
public $rolCompartir = 'supervisor'; // Rol que elegirá quien genera el link

    // Dropdown exportación
    public $mostrarDropdownExportar = false;

    // Modal exportación PDF
    public $mostrarModalPDF = false;
    public $tituloReporte = 'REPORTE DE PRESUPUESTO';
    public $alcancePresupuesto = '';
    public $condicionesGenerales = '';
    public $validezPresupuesto = '15 días';
    public $emailCliente = '';
    public $incluirEmailCliente = false;
    public $incluirAlcance = true;
    public $incluirCondiciones = true;
    public $incluirValidez = true;
    public $incluirUnidad = true;
    public $incluirCantidad = true;
    public $incluirPrecio = true;

    // Alcance de exportación: 'completo' | 'rubros_subrubros'
    public $exportScope = 'completo';

    // Modal exportación Excel
    public $mostrarModalExcel = false;
    public $tituloExcel = 'REPORTE DE PRESUPUESTO';
    public $alcanceExcel = '';
    public $condicionesExcel = '';
    public $validezExcel = '15 días';
    public $excelIncluirUnidad = true;
    public $excelIncluirCantidad = true;
    public $excelIncluirPrecio = true;

    // Historial undo/redo
    public $historialEstados = [];
    public $indexHistorial = -1;

    // Vista activa: 'presupuesto' | 'ejecucion'
    public $vistaActiva = 'presupuesto';

    // Modo lectura: true cuando el proyecto está en ejecución (presupuesto bloqueado)
    public $modoLectura = false;

    // Listeners para eventos
    protected $listeners = ['proyectoActualizado' => 'actualizarProyecto'];

    // ── MOUNT ────────────────────────────────────────────────

    public function mount(Proyecto $proyecto)
    {
        $this->autorizarAcceso($proyecto);
        $this->proyecto = $proyecto;
        $this->cargarProyecto();

        // Modo lectura para PRESUPUESTO: bloquear en ejecucion, finalizado y pausado
        // en_revision permite editar el presupuesto pero no acceder a la vista de ejecución
        $this->modoLectura = in_array($proyecto->estado_obra, ['ejecucion', 'finalizado', 'pausado']);

        $primera = $this->proyecto->proyectoRecursos->first()?->categoria;
        if ($primera) {
            $this->nodosAbiertos[] = 'cat_' . $primera;
        }

        // Guardar estado inicial para historial
        $this->guardarEstado();
    }

    public function actualizarProyecto()
    {
        $this->proyecto->refresh();
        $this->modoLectura = in_array($this->proyecto->estado_obra, ['ejecucion', 'finalizado', 'pausado']);
    }

    public function abrirModalInvitar()
    {
        // Solo el dueño del proyecto puede invitar
        if (auth()->id() !== $this->proyecto->user_id) {
            session()->flash('error', 'Solo el dueño del proyecto puede invitar miembros.');
            return;
        }

        $this->mostrarModalInvitar = true;
        $this->cargarUsuarios();
    }

public function toggleDropdownExportar()
{
    $this->mostrarDropdownExportar = !$this->mostrarDropdownExportar;
}

public function abrirModalPDF()
{
    $this->mostrarDropdownExportar = false;
    $this->mostrarModalPDF = true;
}

public function cerrarModalPDF()
{
    $this->mostrarModalPDF = false;
    $this->resetearFormularioPDF();
}

public function resetearFormularioPDF()
{
    $this->tituloReporte = 'REPORTE DE PRESUPUESTO';
    $this->alcancePresupuesto = '';
    $this->condicionesGenerales = '';
    $this->validezPresupuesto = '15 días';
    $this->emailCliente = '';
    $this->incluirEmailCliente = false;
    $this->incluirAlcance = true;
    $this->incluirCondiciones = true;
    $this->incluirValidez = true;
    $this->incluirUnidad = true;
    $this->incluirCantidad = true;
    $this->incluirPrecio = true;
}

public function abrirModalExcel()
{
    $this->mostrarDropdownExportar = false;
    $this->mostrarModalExcel = true;
}

public function cerrarModalExcel()
{
    $this->mostrarModalExcel = false;
    $this->tituloExcel = 'REPORTE DE PRESUPUESTO';
    $this->alcanceExcel = '';
    $this->condicionesExcel = '';
    $this->validezExcel = '15 días';
    $this->excelIncluirUnidad = true;
    $this->excelIncluirCantidad = true;
    $this->excelIncluirPrecio = true;
}

public function exportarPDF()
{
    try {
        // Asegurar que el proyecto y sus relaciones estén refrescados
        $this->proyecto->refresh();
        $this->cargarProyecto();

        $datos  = $this->obtenerDatosPresupuesto($this->exportScope);
        $config = ConfiguracionGeneral::instancia();

        $subtotalBase         = $datos['total'];
        $cargaSocial          = $this->calcularCargaSocialPDF();
        $pctBeneficio         = (float) ($this->proyecto->beneficio ?? 0);
        $beneficioMonto       = $subtotalBase * ($pctBeneficio / 100);
        $subtotalConBeneficio = $subtotalBase + $beneficioMonto;
        $pctImpuestos         = (float) ($this->proyecto->impuestos ?? 22);
        $impuestosMonto       = $subtotalConBeneficio * ($pctImpuestos / 100);
        $totalObra            = $subtotalConBeneficio + $impuestosMonto;
        $precioFinal          = $totalObra;

        $resumen = [
            'subtotal'               => $subtotalBase,
            'subtotal_con_beneficio' => $subtotalConBeneficio,
            'pct_beneficio'          => $pctBeneficio,
            'beneficio'              => $beneficioMonto,
            'carga_social'           => $cargaSocial,
            'pct_impuestos'          => $pctImpuestos,
            'impuestos'              => $impuestosMonto,
            'total_obra'             => $totalObra,
            'precio_final'           => $precioFinal,
        ];

        $logoBase64 = null;
        if ($config->logo_url) {
            if (str_starts_with($config->logo_url, '/storage/')) {
                $rutaLocal = Storage::disk('public')->path(
                    str_replace('/storage/', '', $config->logo_url)
                );
                if (file_exists($rutaLocal)) {
                    $mime       = mime_content_type($rutaLocal);
                    $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($rutaLocal));
                }
            } elseif (filter_var($config->logo_url, FILTER_VALIDATE_URL)) {
                try {
                    $contenido = @file_get_contents($config->logo_url);
                    if ($contenido !== false) {
                        $info       = getimagesizefromstring($contenido);
                        $mime       = $info['mime'] ?? 'image/png';
                        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($contenido);
                    }
                } catch (\Throwable) {}
            }
        }

        $html = view('exports.presupuesto-pdf', [
            'titulo'      => $this->tituloReporte,
            'proyecto'    => $this->proyecto,
            'datos'       => $datos,
            'userPlan'    => auth()->user()->plan,
            'opciones'    => [
                'incluirEmailCliente' => $this->incluirEmailCliente,
                'incluirAlcance'      => $this->incluirAlcance,
                'incluirCondiciones'  => $this->incluirCondiciones,
                'incluirValidez'      => $this->incluirValidez,
                'incluirUnidad'       => $this->incluirUnidad,
                'incluirCantidad'     => $this->incluirCantidad,
                'incluirPrecio'       => $this->incluirPrecio,
                'exportScope'         => $this->exportScope,
            ],
            'alcance'      => $this->incluirAlcance     ? $this->alcancePresupuesto  : '',
            'condiciones'  => $this->incluirCondiciones ? $this->condicionesGenerales : '',
            'validez'      => $this->incluirValidez     ? $this->validezPresupuesto  : '',
            'emailCliente' => $this->incluirEmailCliente ? $this->emailCliente        : '',
            'fecha'        => Carbon::now()->locale('es')->translatedFormat('d \d\e F \d\e Y'),
            'fechaEmision' => Carbon::now()->format('d/m/Y'),
            'config'       => $config,
            'logoBase64'   => $logoBase64,
            'resumen'      => $resumen,
            'monedaBase'   => $this->proyecto->moneda_base ?? 'USD',
            'pctBeneficio' => $pctBeneficio,
        ])->render();

        // ── PDF ───────────────────────────────────────────────
        $pdf        = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        $pdfContent = $pdf->output();
        // ─────────────────────────────────────────────────────

        $nombreSanitizado = preg_replace('/[^a-zA-Z0-9-]/', '', str_replace(' ', '-', $this->proyecto->nombre_proyecto));
        $nombreArchivo    = 'presupuesto-' . $nombreSanitizado . '-' . Carbon::now()->format('d-m-Y') . '.pdf';

        $ruta = storage_path('app/temp/' . $nombreArchivo);
        if (!is_dir(dirname($ruta))) {
            mkdir(dirname($ruta), 0755, true);
        }

        file_put_contents($ruta, $pdfContent);

        $this->mostrarModalPDF = false;
        $this->resetearFormularioPDF();

        return response()->download($ruta, $nombreArchivo)->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        session()->flash('error', 'Error al exportar PDF: ' . $e->getMessage());
        $this->mostrarModalPDF = false;
    }
}

public function exportarExcel()
{
    try {
        // Asegurar que el proyecto y sus relaciones estén refrescados
        $this->proyecto->refresh();
        $this->cargarProyecto();

        $datos  = $this->obtenerDatosPresupuesto($this->exportScope);
        $config = ConfiguracionGeneral::instancia();

        $subtotalBase         = $datos['total'];
        $cargaSocial          = $this->calcularCargaSocialPDF();
        $pctBeneficio         = (float) ($this->proyecto->beneficio ?? 0);
        $beneficioMonto       = $subtotalBase * ($pctBeneficio / 100);
        $subtotalConBeneficio = $subtotalBase + $beneficioMonto;
        $pctImpuestos         = (float) ($this->proyecto->impuestos ?? 22);
        $impuestosMonto       = $subtotalConBeneficio * ($pctImpuestos / 100);
        $totalObra            = $subtotalConBeneficio + $impuestosMonto;
        $monedaBase           = $this->proyecto->moneda_base ?? 'USD';
        $fechaEmision         = Carbon::now()->format('d/m/Y');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Presupuesto');

        $Fill  = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID;
        $Left  = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
        $Right = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
        $Thin  = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN;

        $styleTitleBlock = [
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FF6B35']],
            'alignment' => ['horizontal' => $Left, 'vertical' => 'center'],
        ];
        $styleLabel = [
            'font'      => ['bold' => false, 'size' => 9, 'color' => ['rgb' => '888888']],
            'alignment' => ['horizontal' => $Left],
        ];
        $styleValue = [
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1a1a1a']],
            'alignment' => ['horizontal' => $Left],
        ];
        $styleCompanyName = [
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1a1a1a']],
            'alignment' => ['horizontal' => $Right],
        ];
        $styleCompanyDetail = [
            'font'      => ['size' => 8, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => $Right],
        ];
        $styleSectionHeader = [
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => '1a1a1a']],
            'alignment' => ['horizontal' => $Left, 'vertical' => 'center'],
        ];
        $styleColHeader = [
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => '1a1a1a']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
        ];
        $styleData = [
            'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
            'alignment' => ['vertical' => 'center', 'wrapText' => true],
        ];
        $styleCatRow = [
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1a1a1a']],
            'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => 'E8E8E8']],
            'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
        ];
        $styleTotalRow = [
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => '1a1a1a']],
            'alignment' => ['horizontal' => $Right],
            'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
        ];
        $styleResumenLabel = [
            'font'      => ['size' => 9, 'color' => ['rgb' => '333333']],
            'alignment' => ['horizontal' => $Left, 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
        ];
        $styleResumenValue = [
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1a1a1a']],
            'alignment' => ['horizontal' => $Right, 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
        ];
        $styleResumenTotalObra = [
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => '333333']],
            'alignment' => ['horizontal' => $Right, 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
        ];
        $styleResumenPrecioFinal = [
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => 'FF6B35']],
            'alignment' => ['horizontal' => $Right, 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
        ];
        $styleDocSectionHeader = [
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => '1a1a1a']],
            'alignment' => ['horizontal' => $Left, 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
        ];
        $styleDocSectionHint = [
            'font'      => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '888888']],
            'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => 'F5F5F5']],
            'alignment' => ['horizontal' => $Left, 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
        ];
        $styleDocSectionBody = [
            'font'      => ['size' => 9, 'color' => ['rgb' => '333333']],
            'alignment' => ['horizontal' => $Left, 'vertical' => 'top', 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
        ];

        // ─────────────────────────────────────────────────────
        // BLOQUE 1: ENCABEZADO
        // ─────────────────────────────────────────────────────
        $row = 1;

        $sheet->setCellValue('A' . $row, $this->tituloExcel);
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($styleTitleBlock);
        $sheet->getRowDimension($row)->setRowHeight(22);

        if ($config->nombre_empresa) {
            $sheet->setCellValue('E' . $row, $config->nombre_empresa);
            $sheet->mergeCells('E' . $row . ':G' . $row);
            $sheet->getStyle('E' . $row)->applyFromArray($styleCompanyName);
        }
        $row++;

        $sheet->setCellValue('A' . $row, 'Proyecto:');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($styleLabel);
        $sheet->setCellValue('C' . $row, $this->proyecto->nombre_proyecto);
        $sheet->mergeCells('C' . $row . ':D' . $row);
        $sheet->getStyle('C' . $row)->applyFromArray($styleValue);
        if ($config->rut) {
            $sheet->setCellValue('E' . $row, 'RUT: ' . $config->rut);
            $sheet->mergeCells('E' . $row . ':G' . $row);
            $sheet->getStyle('E' . $row)->applyFromArray($styleCompanyDetail);
        }
        $row++;

        $sheet->setCellValue('A' . $row, 'Fecha de Emisión:');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($styleLabel);
        $sheet->setCellValue('C' . $row, $fechaEmision);
        $sheet->mergeCells('C' . $row . ':D' . $row);
        $sheet->getStyle('C' . $row)->applyFromArray($styleValue);
        if ($config->pagina_web) {
            $sheet->setCellValue('E' . $row, 'Web: ' . $config->pagina_web);
            $sheet->mergeCells('E' . $row . ':G' . $row);
            $sheet->getStyle('E' . $row)->applyFromArray($styleCompanyDetail);
        }
        $row++;

        $sheet->setCellValue('A' . $row, 'Moneda Base:');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($styleLabel);
        $sheet->setCellValue('C' . $row, $monedaBase);
        $sheet->mergeCells('C' . $row . ':D' . $row);
        $sheet->getStyle('C' . $row)->applyFromArray($styleValue);
        if ($config->redes_sociales) {
            $sheet->setCellValue('E' . $row, 'Redes: ' . $config->redes_sociales);
            $sheet->mergeCells('E' . $row . ':G' . $row);
            $sheet->getStyle('E' . $row)->applyFromArray($styleCompanyDetail);
        }
        $row++;

        if ($config->telefonos) {
            $sheet->setCellValue('E' . $row, 'Tel: ' . $config->telefonos);
            $sheet->mergeCells('E' . $row . ':G' . $row);
            $sheet->getStyle('E' . $row)->applyFromArray($styleCompanyDetail);
        }
        $row++;

        if ($config->correo) {
            $sheet->setCellValue('E' . $row, 'Email: ' . $config->correo);
            $sheet->mergeCells('E' . $row . ':G' . $row);
            $sheet->getStyle('E' . $row)->applyFromArray($styleCompanyDetail);
        }

        $sheet->getStyle('A1:G' . $row)->getBorders()->getBottom()->setBorderStyle($Thin)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFF6B35'));
        $row += 2;

        // ─────────────────────────────────────────────────────
        // BLOQUE 2: RESUMEN DE COSTOS
        // ─────────────────────────────────────────────────────
        $sheet->setCellValue('A' . $row, 'RESUMEN DE COSTOS');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($styleSectionHeader);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        $resumenRows = [
            ['Subtotal ' . $monedaBase, $subtotalConBeneficio],
            ['Impuestos (' . number_format($pctImpuestos, 0) . '%)', $impuestosMonto],
        ];

        foreach ($resumenRows as [$label, $value]) {
            $sheet->setCellValue('A' . $row, $label);
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($styleResumenLabel);
            $sheet->setCellValue('G' . $row, '$ ' . number_format($value, 2, ',', '.'));
            $sheet->getStyle('G' . $row)->applyFromArray($styleResumenValue);
            $row++;
        }

        $sheet->setCellValue('A' . $row, 'TOTAL OBRA ' . $monedaBase);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($styleResumenTotalObra);
        $sheet->setCellValue('G' . $row, '$ ' . number_format($totalObra, 2, ',', '.'));
        $sheet->getStyle('G' . $row)->applyFromArray($styleResumenTotalObra);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        $sheet->setCellValue('A' . $row, 'PRECIO FINAL');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($styleResumenPrecioFinal);
        $sheet->setCellValue('G' . $row, '$ ' . number_format($totalObra, 2, ',', '.'));
        $sheet->getStyle('G' . $row)->applyFromArray($styleResumenPrecioFinal);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        if ($cargaSocial > 0) {
            $styleCargaSocialInfo = [
                'font'      => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '999999']],
                'alignment' => ['horizontal' => $Left],
                'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => 'F5F5F5']],
            ];
            $sheet->setCellValue('A' . $row, 'Carga Social ' . $monedaBase . ' (referencial, no incluida)');
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($styleCargaSocialInfo);
            $sheet->setCellValue('G' . $row, '$ ' . number_format($cargaSocial, 2, ',', '.'));
            $sheet->getStyle('G' . $row)->applyFromArray($styleCargaSocialInfo);
            $row++;
        }
        $row++;

        // ─────────────────────────────────────────────────────
        // BLOQUE 3: TABLA DE PRESUPUESTO
        // ─────────────────────────────────────────────────────
        $sheet->setCellValue('A' . $row, 'TABLA DE PRESUPUESTO');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($styleSectionHeader);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        $col = 1;
        $sheet->setCellValueByColumnAndRow($col++, $row, 'Categoría');
        $sheet->setCellValueByColumnAndRow($col++, $row, 'Ítem');
        $sheet->setCellValueByColumnAndRow($col++, $row, 'Descripción');
        if ($this->excelIncluirUnidad)   $sheet->setCellValueByColumnAndRow($col++, $row, 'Unidad');
        if ($this->excelIncluirCantidad) $sheet->setCellValueByColumnAndRow($col++, $row, 'Cantidad');
        if ($this->excelIncluirPrecio) {
            $sheet->setCellValueByColumnAndRow($col++, $row, 'Precio ' . $monedaBase);
            $sheet->setCellValueByColumnAndRow($col++, $row, 'Subtotal');
        }
        $lastCol = $col - 1;
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol);
        $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($styleColHeader);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        $categoriaActual = null;
        foreach ($datos['items'] as $item) {
            if ($item['categoria'] !== '' && $item['categoria'] !== $categoriaActual) {
                $categoriaActual = $item['categoria'];
                $catSubtotal = (($datos['cat_subtotales'][$item['categoria']] ?? 0)) * (1 + $pctBeneficio / 100);
                if ($this->excelIncluirPrecio && $lastCol > 1) {
                    $penultimateColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol - 1);
                    $sheet->setCellValue('A' . $row, $item['categoria']);
                    $sheet->mergeCells('A' . $row . ':' . $penultimateColLetter . $row);
                    $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($styleCatRow);
                    $sheet->setCellValueByColumnAndRow($lastCol, $row, $catSubtotal);
                    $sheet->getStyle($lastColLetter . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                    $sheet->setCellValue('A' . $row, $item['categoria']);
                    $sheet->mergeCells('A' . $row . ':' . $lastColLetter . $row);
                    $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($styleCatRow);
                }
                $row++;
            }

            if ($item['tipo'] === 'subrubro') {
                $styleSubrubro = [
                    'font'      => ['bold' => true, 'italic' => true, 'size' => 8, 'color' => ['rgb' => '444444']],
                    'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => 'F5F5F5']],
                    'alignment' => ['horizontal' => $Left, 'indent' => 2],
                ];
                $col = 1;
                $sheet->setCellValueByColumnAndRow($col++, $row, $item['categoria']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $item['nombre']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $item['descripcion'] ?? '');
                if ($this->excelIncluirUnidad)   $sheet->setCellValueByColumnAndRow($col++, $row, $item['unidad'] ?? '');
                if ($this->excelIncluirCantidad) $sheet->setCellValueByColumnAndRow($col++, $row, $item['cantidad'] ?? 0);
                if ($this->excelIncluirPrecio) {
                    $precioConBeneficioExcel   = ($item['precio_usd'] ?? 0) * (1 + $pctBeneficio / 100);
                    $subtotalConBeneficioExcel = ($item['subtotal'] ?? 0)   * (1 + $pctBeneficio / 100);
                    $sheet->setCellValueByColumnAndRow($col++, $row, $precioConBeneficioExcel);
                    $sheet->setCellValueByColumnAndRow($col++, $row, $subtotalConBeneficioExcel);
                    $penult = $lastCol - 1;
                    $penultLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($penult);
                    $sheet->getStyle($penultLetter . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle($lastColLetter . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                }
                $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($styleSubrubro);
                $row++;
                continue;
            }

            $col = 1;
            $sheet->setCellValueByColumnAndRow($col++, $row, $item['categoria']);
            $sheet->setCellValueByColumnAndRow($col++, $row, $item['nombre']);
            $sheet->setCellValueByColumnAndRow($col++, $row, $item['descripcion'] ?? '');
            if ($this->excelIncluirUnidad)   $sheet->setCellValueByColumnAndRow($col++, $row, $item['unidad']);
            if ($this->excelIncluirCantidad) $sheet->setCellValueByColumnAndRow($col++, $row, $item['cantidad']);
            if ($this->excelIncluirPrecio) {
                $precioConBeneficioExcel   = ($item['precio_usd'] ?? 0) * (1 + $pctBeneficio / 100);
                $subtotalConBeneficioExcel = ($item['subtotal'] ?? 0)   * (1 + $pctBeneficio / 100);
                $sheet->setCellValueByColumnAndRow($col++, $row, $precioConBeneficioExcel);
                $sheet->setCellValueByColumnAndRow($col++, $row, $subtotalConBeneficioExcel);
            }
            $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($styleData);
            $row++;
        }

        if ($this->excelIncluirPrecio) {
            $sheet->setCellValue('A' . $row, 'TOTAL PRESUPUESTO');
            $sheet->mergeCells('A' . $row . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol - 1) . $row);
            $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($styleTotalRow);
            $sheet->setCellValueByColumnAndRow($lastCol, $row, $datos['total'] * (1 + $pctBeneficio / 100));
            $sheet->getStyle($lastColLetter . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $row += 2;

        // ─────────────────────────────────────────────────────
        // BLOQUE 4: ALCANCE / CONDICIONES / VALIDEZ
        // ─────────────────────────────────────────────────────
        $secciones = [
            ['ALCANCE',     'Lo que se consideró presupuestar y lo que no',  $this->alcanceExcel],
            ['CONDICIONES', 'Modo de pago, moneda y condiciones comerciales', $this->condicionesExcel],
            ['VALIDEZ',     'Tiempo de vigencia del presupuesto',             $this->validezExcel],
        ];

        foreach ($secciones as [$titulo, $hint, $contenido]) {
            $sheet->setCellValue('A' . $row, $titulo);
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($styleDocSectionHeader);
            $sheet->getRowDimension($row)->setRowHeight(15);
            $row++;

            $sheet->setCellValue('A' . $row, $hint);
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($styleDocSectionHint);
            $sheet->getRowDimension($row)->setRowHeight(13);
            $row++;

            $sheet->setCellValue('A' . $row, $contenido ?: '');
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($styleDocSectionBody);
            $sheet->getRowDimension($row)->setRowHeight(40);
            $row += 2;
        }

        // ─────────────────────────────────────────────────────
        // ANCHOS DE COLUMNA
        // ─────────────────────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setWidth(18);

        // ─────────────────────────────────────────────────────
        // CONFIGURACIÓN DE PÁGINA PARA EXPORTAR COMO PDF
        // ─────────────────────────────────────────────────────
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $pageSetup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $pageSetup->setFitToPage(true);
        $pageSetup->setFitToWidth(1);   // todo el contenido en 1 página de ancho
        $pageSetup->setFitToHeight(0);  // sin límite de alto (N páginas)
        $pageSetup->setPrintArea('A1:G' . $sheet->getHighestRow());
        $pageSetup->setHorizontalCentered(true);
        $pageSetup->setRowsToRepeatAtTopByStartAndEnd(1, 1); // repetir encabezado

        $margins = $sheet->getPageMargins();
        $margins->setTop(0.5);
        $margins->setBottom(0.5);
        $margins->setLeft(0.4);
        $margins->setRight(0.4);
        $margins->setHeader(0.2);
        $margins->setFooter(0.2);

        // Pie de página con nombre empresa y fecha
        $sheet->getHeaderFooter()->setOddFooter(
            '&L&8' . ($config->nombre_empresa ?: 'Rubra') . '&R&8Generado el ' . Carbon::now()->format('d/m/Y')
        );

        // ─────────────────────────────────────────────────────
        // GUARDAR Y DESCARGAR
        // ─────────────────────────────────────────────────────
        $nombreSanitizado = preg_replace('/[^a-zA-Z0-9-]/', '', str_replace(' ', '-', $this->proyecto->nombre_proyecto));
        $nombreArchivo    = 'presupuesto-' . $nombreSanitizado . '-' . Carbon::now()->format('d-m-Y') . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $ruta   = storage_path('app/temp/' . $nombreArchivo);
        if (!is_dir(dirname($ruta))) {
            mkdir(dirname($ruta), 0755, true);
        }

        $writer->save($ruta);

        $this->mostrarModalExcel = false;

        return response()->download($ruta, $nombreArchivo)->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        session()->flash('error', 'Error al exportar Excel: ' . $e->getMessage());
        $this->mostrarModalExcel = false;
    }
}
/**
 * Obtiene los datos del presupuesto formateados para exportación
 */
private function obtenerDatosPresupuesto($scope = 'completo')
{
    $items = [];
    $total = 0;

    $this->recorrerNodos($this->proyecto->proyectoRecursos->whereNull('parent_id'), '', $items, $total);

    // Subtotal por categoría.
    $catSubtotales = [];
    foreach ($items as $item) {
        $cat = $item['categoria'] ?? '';
        if ($item['tipo'] === 'item') {
            $catSubtotales[$cat] = ($catSubtotales[$cat] ?? 0) + ($item['subtotal'] ?? 0);
        } elseif ($item['tipo'] === 'subrubro') {
            if ($scope === 'completo') {
                // En vista completa sólo sumar la parte propia del subrubro (evitar duplicar hijos)
                $own = ($item['precio_own'] ?? 0) * ($item['cantidad'] ?? 0);
                $catSubtotales[$cat] = ($catSubtotales[$cat] ?? 0) + $own;
            } else {
                // En vista rubros/subrubros sumar el subtotal completo del subrubro
                $catSubtotales[$cat] = ($catSubtotales[$cat] ?? 0) + ($item['subtotal'] ?? 0);
            }
        }
    }

    // Total general es la suma de los subtotales por categoría
    $totalGeneral = array_sum($catSubtotales);

    // Si el alcance es 'rubros_subrubros' filtramos los items para no incluir materiales
    if ($scope === 'rubros_subrubros') {
        $items = array_values(array_filter($items, fn($i) => $i['tipo'] === 'subrubro'));
    }

    return [
        'items'          => $items,
        'total'          => $totalGeneral,
        'cat_subtotales' => $catSubtotales,
    ];
}

private function calcularCargaSocialPDF(): float
{
    $totalCS = 0;

    $this->recorrerCargaSocial(
        $this->proyecto->proyectoRecursos->whereNull('parent_id'),
        $totalCS
    );

    return $totalCS;
}

private function recorrerCargaSocial($nodos, float &$totalCS): void
{
    foreach ($nodos as $nodo) {
        $precioUnitario = $nodo->precio_unitario ?? $nodo->precio_usd ?? 0;
        $costoItem = ($nodo->cantidad ?? 1) * $precioUnitario;

        // Recurso simple de mano de obra
        if (($nodo->recurso && $nodo->recurso->tipo === 'labor') || $nodo->tipo === 'labor') {
            $porcentajeCS = $nodo->recurso->social_charges_percentage
                ?? $nodo->social_charges_percentage
                ?? 0;
            $totalCS += $costoItem * ($porcentajeCS / 100);
        }

        // Composición (APU)
        if ($nodo->recurso && $nodo->recurso->tipo === 'composition') {
            $itemsInternos = \App\Models\ComposicionItem::where('composicion_id', $nodo->recurso_id)->get();
            foreach ($itemsInternos as $interno) {
                $resBase = $interno->recursoBase;
                if (!$resBase) continue;
                if (in_array($resBase->tipo, ['labor', 'mano_obra'])) {
                    $pBase = $resBase->precio_usd ?? 0;
                    $porcentajeCS = $resBase->social_charges_percentage ?? 0;
                    $totalCS += ($nodo->cantidad ?? 1) * $interno->cantidad * ($pBase * ($porcentajeCS / 100));
                }
            }
        }

        if ($nodo->hijos && count($nodo->hijos) > 0) {
            $this->recorrerCargaSocial($nodo->hijos, $totalCS);
        }
    }
}

private function recorrerNodos($nodos, $categoria = '', &$items = [], &$total = 0, $multiplier = 1)
{
    foreach ($nodos as $nodo) {
        $tieneHijos = $nodo->hijos && count($nodo->hijos) > 0;

        // Nombre de categoría para este nivel (solo los nodos top-level la definen)
        $catEste = $categoria ?: ($nodo->categoria ?: $nodo->nombre);

        if ($tieneHijos && $categoria === '') {
            // Nodo top-level con hijos → actúa como CATEGORÍA (fila gris), no se agrega como ítem
            $this->recorrerNodos($nodo->hijos, $catEste, $items, $total, $multiplier * ($nodo->cantidad ?? 1));
        } elseif (is_null($nodo->recurso_id)) {
            // Sin recurso_id → SUBRUBRO (sub-encabezado)
            // Calculamos el costo por unidad del subrubro y luego multiplicamos por su cantidad.
            $computePerUnit = function($node) use (&$computePerUnit) {
                $perUnit = 0;

                // propio por unidad
                $pNodeUnit = $node->precio_usd ?? $node->precio_unitario ?? 0;
                $perUnit += $pNodeUnit;

                // hijos
                if ($node->hijos && count($node->hijos) > 0) {
                    foreach ($node->hijos as $child) {
                        if (is_null($child->recurso_id)) {
                            // subrubro hijo: su contribución por unidad = su costo por unidad * su cantidad
                            $perUnit += $computePerUnit($child) * ($child->cantidad ?? 1);
                        } else {
                            // Para APUs (composition), precio_usd ya incluye el costo total
                            // de sus items (asignado al crear la composición), no re-expandir.
                            $p = $child->precio_usd ?? 0;
                            $cant = $child->cantidad ?? 1;
                            $perUnit += $p * $cant;
                        }
                    }
                }

                return $perUnit;
            };

            $perUnit = $computePerUnit($nodo);
            $cantidadNodo = $nodo->cantidad ?? 1;
            $subrubroSubtotal = $perUnit * $cantidadNodo * $multiplier;

            $items[] = [
                'tipo'        => 'subrubro',
                'categoria'   => $catEste,
                'nombre'      => $nodo->nombre,
                'descripcion' => '',
                'unidad'      => $nodo->unidad ?? '',
                // cantidad ya escalada por el multiplicador
                'cantidad'    => $cantidadNodo * $multiplier,
                // precio_usd: precio POR UNIDAD calculado en base a lo que contiene (para mostrar)
                'precio_usd'  => $perUnit,
                // precio_own: precio propio asignado al nodo (sin sumar hijos)
                'precio_own'  => $nodo->precio_usd ?? $nodo->precio_unitario ?? 0,
                'subtotal'    => $subrubroSubtotal,
            ];

            if ($tieneHijos) {
                $this->recorrerNodos($nodo->hijos, $catEste, $items, $total, $multiplier * $cantidadNodo);
            }
        } else {
            // Hoja: ítem normal con cantidad/precio
            // Si el registro del proyecto no tiene precio, usar el precio del recurso asociado
            $precioUnitario = $nodo->precio_usd ?? ($nodo->recurso->precio_usd ?? 0);
            $cantidadEffective = ($nodo->cantidad ?? 1) * $multiplier;
            $subtotal = $cantidadEffective * $precioUnitario;
            $total += $subtotal;
            $items[] = [
                'tipo'        => 'item',
                'categoria'   => $catEste,
                'nombre'      => $nodo->nombre,
                'descripcion' => '',
                'unidad'      => $nodo->unidad ?? '',
                'cantidad'    => $cantidadEffective,
                'precio_usd'  => $precioUnitario,
                'subtotal'    => $subtotal,
            ];
        }
    }
}

public function cargarUsuarios()
{
    $this->proyecto->refresh();

    $idsInvitados = $this->proyecto->usuarios()
        ->pluck('users.id')
        ->toArray();

    $this->usuariosDisponibles = \App\Models\User::query()
        ->where('name', 'like', '%' . $this->buscarUsuario . '%')
        ->where('invited_by', auth()->id())
        ->whereNotIn('id', $idsInvitados) // 🔥 clave
        ->limit(10)
        ->get();
}



public function invitarUsuariosSeleccionados()
{
    // Solo el dueño del proyecto puede invitar
    if (auth()->id() !== $this->proyecto->user_id) {
        session()->flash('error', 'Solo el dueño del proyecto puede invitar miembros.');
        return;
    }

    foreach ($this->usuariosSeleccionados as $userId) {

        if ($this->proyecto->usuarios()->where('user_id', $userId)->exists()) {
            continue;
        }

        $this->proyecto->usuarios()->attach($userId);
    }

    $this->usuariosSeleccionados = [];

    $this->proyecto->refresh();

    $this->cargarProyecto();
    $this->cargarUsuarios();

    $this->mostrarModalInvitar = false; // 🔥 cerrar modal
}
    // ── CARGA ────────────────────────────────────────────────

    private function cargarProyecto()
    {
        $this->proyecto->load([
            'proyectoRecursos' => fn($q) =>
                $q->whereNull('parent_id')
                  ->orderBy('orden')
                  ->with($this->relacionesRecursivas(6)),
        ]);
    }

    private function relacionesRecursivas(int $depth): array
    {
        if ($depth <= 0) return [];

        return [
            'hijos' => fn($q) => $q->orderBy('orden')->with(array_merge(
                $this->relacionesRecursivas($depth - 1),
                [
                    'recurso',
                    'recurso.items',
                    'recurso.items.recursoBase',
                ]
            )),
        ];
    }

    // ── MOVER NODOS ARRIBA / ABAJO ───────────────────────────

    public function subirNodo($id)
    {
        $nodo = ProyectoRecurso::find($id);
        if (!$nodo) return;

        $anterior = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->where('parent_id', $nodo->parent_id)
            ->where('orden', '<', $nodo->orden)
            ->orderBy('orden', 'desc')
            ->first();

        if (!$anterior) return;

        [$nodo->orden, $anterior->orden] = [$anterior->orden, $nodo->orden];
        $nodo->save();
        $anterior->save();

        $this->proyecto->refresh();
        $this->cargarProyecto();
    }

    public function bajarNodo($id)
    {
        $nodo = ProyectoRecurso::find($id);
        if (!$nodo) return;

        $siguiente = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->where('parent_id', $nodo->parent_id)
            ->where('orden', '>', $nodo->orden)
            ->orderBy('orden', 'asc')
            ->first();

        if (!$siguiente) return;

        [$nodo->orden, $siguiente->orden] = [$siguiente->orden, $nodo->orden];
        $nodo->save();
        $siguiente->save();

        $this->proyecto->refresh();
        $this->cargarProyecto();
    }

    public function toggleBeneficio()
{
    $this->mostrarBeneficio = !$this->mostrarBeneficio;
}

public function cambiarVista($vista)
{
    if (in_array($vista, ['presupuesto', 'ejecucion'])) {
        // No permitir cambiar a ejecución si el proyecto está en en_revision, activo o pausado
        if ($vista === 'ejecucion' && in_array($this->proyecto->estado_obra, ['en_revision', 'activo', 'pausado'])) {
            session()->flash('error', 'No puedes acceder a la vista de Ejecución en este estado del proyecto.');
            return;
        }
        $this->vistaActiva = $vista;
    }
}

public function actualizarCostoReal($id, $valor)
{
    $costo = filter_var($valor, FILTER_VALIDATE_FLOAT);
    if ($costo === false || $costo < 0) {
        $costo = null;
    }

    $recurso = ProyectoRecurso::find($id);
    if (!$recurso || $recurso->proyecto_id !== $this->proyecto->id) return;

    $recurso->update(['costo_real' => $costo]);
    $this->proyecto->refresh();
    $this->cargarProyecto();
}

    // ── MODAL EDITAR ─────────────────────────────────────────

    public function abrirModalEditar($id)
    {
        // Limpiar estado de edición anterior
        $this->reset(['mostrarModalEditar', 'editId', 'editNombre', 'editUnidad']);
        $this->resetErrorBag();

        $nodo = ProyectoRecurso::find($id);
        if (!$nodo) return;

        $this->editId     = $id;
        $this->editNombre = $nodo->nombre;
        $this->editUnidad = $nodo->unidad;
        $this->mostrarModalEditar = true;
    }

 public function guardarEdicion()
{
    $this->validate(['editNombre' => 'required|min:2']);

    $nodo = ProyectoRecurso::find($this->editId);
    if (!$nodo) return;

    // Si es un nodo raíz (categoría), actualizar también el campo 'categoria'
    // en todos los registros del proyecto que pertenezcan a esa categoría
    if (is_null($nodo->parent_id)) {
        $categoriaVieja = $nodo->categoria;
        ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->where('categoria', $categoriaVieja)
            ->update(['categoria' => $this->editNombre]);
    }

    $nodo->update([
        'nombre' => $this->editNombre,
        'unidad' => $this->editUnidad,
    ]);

    $this->reset(['mostrarModalEditar', 'editId', 'editNombre', 'editUnidad']);
    $this->resetErrorBag();
    $this->proyecto->refresh();
    $this->cargarProyecto();
    $this->guardarEstado();
}

    // ── MODAL ELIMINAR ───────────────────────────────────────

    public function abrirModalEliminar($id)
    {
        $this->deleteId = $id;
        $this->mostrarModalEliminar = true;
    }

    public function confirmarEliminar()
    {
        $nodo = ProyectoRecurso::find($this->deleteId);
        if (!$nodo) return;

        $this->eliminarRecursivo($nodo);

        $this->reset(['mostrarModalEliminar', 'deleteId']);
        $this->proyecto->refresh();
        $this->cargarProyecto();
        $this->guardarEstado();
    }

    private function eliminarRecursivo($nodo)
    {
        foreach ($nodo->hijos as $hijo) {
            $this->eliminarRecursivo($hijo);
        }
        $nodo->delete();
    }


    // ── TOGGLE NODOS ─────────────────────────────────────────

    public function toggleNodo($key)
    {
        if (in_array($key, $this->nodosAbiertos)) {
            $this->nodosAbiertos = array_values(array_diff($this->nodosAbiertos, [$key]));
        } else {
            $this->nodosAbiertos[] = $key;
        }
    }


    // ── MODAL RUBRO RAÍZ ──────────────────────────────────────

    public function abrirModalRubro()
    {
        $this->resetErrorBag();
        $this->reset(['nombreRubro', 'unidadRubro']);
        $this->unidadRubro = 'gl';
        $this->mostrarModalRubro = true;
    }

    public function guardarRubro()
    {
        $this->validate(['nombreRubro' => 'required|min:2']);

        $maxOrden = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->whereNull('parent_id')
            ->max('orden') ?? 0;

        $this->proyecto->proyectoRecursos()->create([
            'parent_id'  => null,
            'recurso_id' => null,
            'nombre'     => $this->nombreRubro,
            'unidad'     => $this->unidadRubro,
            'cantidad'   => 1,
            'precio_usd' => 0,
            'categoria'  => $this->nombreRubro,
            'orden'      => $maxOrden + 1,
        ]);

        $this->reset(['mostrarModalRubro', 'nombreRubro', 'unidadRubro']);
        $this->resetErrorBag();
        $this->proyecto->refresh();
        $this->cargarProyecto();
        $this->guardarEstado();
    }

    // ── MODAL SUB-RUBRO ──────────────────────────────────────

    public function abrirModalSubrubro($parentId, $categoria, $nombrePadre)
    {
        // Limpiar estado anterior completamente
        $this->resetErrorBag();
        $this->reset(['nombreSubrubro', 'unidadSubrubro', 'parentId', 'categoriaCtx', 'nombreCtx']);
        
        $this->parentId             = $parentId;
        $this->categoriaCtx         = $categoria;
        $this->nombreCtx            = $nombrePadre;
        $this->unidadSubrubro       = 'gl';
        $this->nombreSubrubro       = '';
        $this->mostrarModalSubrubro = true;
        $this->modalSelector        = false;
    }

    public function guardarSubrubro()
    {
        $this->validate(['nombreSubrubro' => 'required|min:2']);

        $maxOrden = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->where('parent_id', $this->parentId)
            ->max('orden') ?? 0;

        $this->proyecto->proyectoRecursos()->create([
            'parent_id'  => $this->parentId,
            'recurso_id' => null,
            'nombre'     => $this->nombreSubrubro,
            'unidad'     => $this->unidadSubrubro,
            'cantidad'   => 1,
            'precio_usd' => 0,
            'categoria'  => $this->categoriaCtx,
            'orden'      => $maxOrden + 1,
        ]);

        $this->nodosAbiertos[] = 'node_' . $this->parentId;
        $this->reset(['mostrarModalSubrubro', 'nombreSubrubro', 'unidadSubrubro', 'parentId', 'categoriaCtx', 'nombreCtx']);
        $this->resetErrorBag();
        $this->proyecto->refresh();
        $this->cargarProyecto();
        $this->guardarEstado();
    }


    // ── MODAL RECURSOS DIRECTOS ──────────────────────────────

    public function abrirModalRecursos($parentId, $categoria, $nombrePadre)
    {
        $this->reset(['itemsRecursos', 'buscarSelector']);
        $this->parentId              = $parentId;
        $this->categoriaCtx          = $categoria;
        $this->nombreCtx             = $nombrePadre;
        $this->mostrarModalRecursos  = true;
        $this->modalSelectorRecursos = false;
        $this->filtroTipo            = 'Todos';
    }

    public function toggleItemRecurso($recursoId, $cantidad = 1)
    {
        $index = collect($this->itemsRecursos)->search(fn($i) => $i['recurso_id'] == $recursoId);

        if ($index !== false) {
            unset($this->itemsRecursos[$index]);
            $this->itemsRecursos = array_values($this->itemsRecursos);
        } else {
            $recurso = Recurso::find($recursoId);
            if (!$recurso) return;

            $this->itemsRecursos[] = [
                'recurso_id' => $recurso->id,
                'nombre'     => $recurso->nombre,
                'unidad'     => $recurso->unidad,
                'precio_usd' => $recurso->precio_usd ?? 0,
                'cantidad'   => $cantidad,
            ];
        }
    }

    public function quitarItemRecurso($index)
    {
        unset($this->itemsRecursos[$index]);
        $this->itemsRecursos = array_values($this->itemsRecursos);
    }

    public function guardarRecursos()
    {
        $this->validate(['itemsRecursos' => 'required|array|min:1']);

        $maxOrden = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->where('parent_id', $this->parentId)
            ->max('orden') ?? 0;

        foreach ($this->itemsRecursos as $index => $item) {
            $this->proyecto->proyectoRecursos()->create([
                'parent_id'  => $this->parentId,
                'recurso_id' => $item['recurso_id'],
                'nombre'     => $item['nombre'],
                'unidad'     => $item['unidad'],
                'cantidad'   => $item['cantidad'],
                'precio_usd' => $item['precio_usd'] ?? 0,
                'categoria'  => $this->categoriaCtx,
                'orden'      => $maxOrden + $index + 1,
            ]);
        }

        $this->nodosAbiertos[] = 'node_' . $this->parentId;
        $this->mostrarModalRecursos = false;
        $this->proyecto->refresh();
        $this->cargarProyecto();
        $this->guardarEstado();
    }


    // ── ACTUALIZAR CANTIDAD ──────────────────────────────────

    public function updateCantidad($id, $nuevaCantidad)
    {
        $cantidad = filter_var($nuevaCantidad, FILTER_VALIDATE_FLOAT);
        if ($cantidad === false || $cantidad < 0) return;

        ProyectoRecurso::find($id)?->update(['cantidad' => $cantidad]);
        $this->proyecto->refresh();
        $this->cargarProyecto();
        $this->guardarEstado();
    }


    // ── FILTRO ───────────────────────────────────────────────

    public function setFiltro($tipo)
    {
        $this->filtroTipo = $tipo;
    }

    // ── UNDO / REDO ──────────────────────────────────────────

    public function guardarEstado()
    {
        // Eliminar estados futuros si existe un índice actual
        if ($this->indexHistorial < count($this->historialEstados) - 1) {
            $this->historialEstados = array_slice($this->historialEstados, 0, $this->indexHistorial + 1);
        }

        // Guardar estado actual con solo datos necesarios (sin relaciones anidadas)
        $estado = [
            'items' => ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
                ->get()
                ->map(fn($item) => [
                    'id' => $item->id,
                    'proyecto_id' => $item->proyecto_id,
                    'parent_id' => $item->parent_id,
                    'recurso_id' => $item->recurso_id,
                    'nombre' => $item->nombre,
                    'unidad' => $item->unidad,
                    'cantidad' => $item->cantidad,
                    'precio_usd' => $item->precio_usd,
                    'categoria' => $item->categoria,
                ])
                ->toArray(),
            'timestamp' => now()->timestamp,
        ];

        $this->historialEstados[] = $estado;
        $this->indexHistorial++;

        // Limitar historial a últimos 50 estados
        if (count($this->historialEstados) > 50) {
            array_shift($this->historialEstados);
            $this->indexHistorial--;
        }
    }

    public function deshacer()
    {
        if ($this->indexHistorial > 0) {
            $this->indexHistorial--;
            $this->restaurarEstado($this->indexHistorial);
        }
    }

    public function rehacer()
    {
        if ($this->indexHistorial < count($this->historialEstados) - 1) {
            $this->indexHistorial++;
            $this->restaurarEstado($this->indexHistorial);
        }
    }

    private function restaurarEstado($index)
    {
        if (!isset($this->historialEstados[$index])) return;

        $estado = $this->historialEstados[$index];
        $itemsGuardados = $estado['items'];

        // Obtener IDs guardados
        $idsGuardados = collect($itemsGuardados)->pluck('id')->toArray();
        
        // Eliminar items que no están en el estado guardado
        ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->whereNotIn('id', $idsGuardados)
            ->delete();

        // Actualizar o crear items
        foreach ($itemsGuardados as $item) {
            ProyectoRecurso::updateOrCreate(
                ['id' => $item['id']],
                [
                    'proyecto_id' => $item['proyecto_id'],
                    'parent_id' => $item['parent_id'],
                    'recurso_id' => $item['recurso_id'],
                    'nombre' => $item['nombre'],
                    'unidad' => $item['unidad'],
                    'cantidad' => $item['cantidad'],
                    'precio_usd' => $item['precio_usd'],
                    'categoria' => $item['categoria'],
                ]
            );
        }

        $this->cargarProyecto();
    }

    // ── RENDER ───────────────────────────────────────────────

    public function render()
    {
        $query = Recurso::query();

        if ($this->buscarSelector) {
            $query->where('nombre', 'like', '%' . $this->buscarSelector . '%');
        }

        if ($this->filtroTipo !== 'Todos') {
            $mapeo = [
                'Materiales'    => 'material',
                'Mano de Obra'  => 'labor',
                'Equipos'       => 'equipment',
                'Composiciones' => 'composition',
            ];
            $query->where('tipo', $mapeo[$this->filtroTipo] ?? $this->filtroTipo);
        }

        $categorias = $this->proyecto->proyectoRecursos
            ->whereNull('parent_id')
            ->groupBy('categoria');

        return view('livewire.proyecto.presupuesto-detallado', [
            'categorias'        => $categorias,
            'recursosFiltrados' => ($this->modalSelector || $this->modalSelectorRecursos)
                                    ? $query->take(30)->get()
                                    : collect([]),
            'modoLectura'       => $this->modoLectura,
            'vistaActiva'       => $this->vistaActiva,
        ])->layout('layouts.app');
    }

    /**
     * Genera un link compartible para invitar a otros usuarios a este proyecto
     */
    public function abrirModalCompartir()
    {
        // Solo el dueño del proyecto puede generar links de compartir
        if (auth()->id() !== $this->proyecto->user_id) {
            session()->flash('error', 'Solo el dueño del proyecto puede compartirlo.');
            return;
        }

        $this->rolCompartir = 'supervisor'; // Reset rol a valor por defecto
        $this->mostrarModalCompartir = true;
        $this->linkCompartible = '';
        $this->linkCopiado = false;
    }

    public function generarLinkCompartir()
    {
        // Verificar que solo el dueño pueda generar links
        if (auth()->id() !== $this->proyecto->user_id) {
            session()->flash('error', 'Solo el dueño del proyecto puede generar links de compartir.');
            return;
        }

        try {
            // Validar que se haya seleccionado un rol válido
            $rolesValidos = ['supervisor', 'presupuestador', 'jefe_obra'];
            if (!in_array($this->rolCompartir, $rolesValidos)) {
                session()->flash('error', 'Rol inválido seleccionado.');
                return;
            }

            $token = \Str::random(32);
            $expiresAt = now()->addHours(24); // Link válido por 24 horas

            // Crear invitación específica para este proyecto con el rol elegido
            $invitacion = \App\Models\Invitacion::create([
                'email'       => '', // Campo vacío para links de compartir
                'rol'         => $this->rolCompartir, // Rol elegido por quien crea el link
                'token'       => $token,
                'expires_at'  => $expiresAt,
                'invited_by'  => auth()->id(),
                'proyecto_id' => $this->proyecto->id,
            ]);

            // Generar URL compartible
            $this->linkCompartible = route('invitacion.proyecto', ['token' => $token]);
            $this->linkCopiado = false;
            session()->flash('success', 'Link generado correctamente.');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar link: ' . $e->getMessage());
        }
    }

    public function cerrarModalCompartir()
    {
        $this->mostrarModalCompartir = false;
        $this->linkCompartible = '';
        $this->linkCopiado = false;
    }

    public function copiarLink()
    {
        $this->linkCopiado = true;
        $this->dispatch('copiar-al-portapapeles', ['texto' => $this->linkCompartible]);
    }
}