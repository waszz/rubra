<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use Livewire\WithFileUploads;
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
    use AutorizaProyecto, WithFileUploads;

    public Proyecto $proyecto;

    /**
     * Recalcula el total del presupuesto y lo guarda en el campo presupuesto_total del proyecto.
     * Se debe llamar después de cualquier cambio relevante en el presupuesto.
     */
    private function actualizarPresupuestoTotalGuardado(): void
    {
        $datos = $this->obtenerDatosPresupuesto();
        $subtotalBase = $datos['total'];
        $beneficio = $subtotalBase * (($this->proyecto->beneficio ?? 0) / 100);
        $subtotalConBeneficio = $subtotalBase + $beneficio;
        $iva = $subtotalConBeneficio * (($this->proyecto->impuestos ?? 22) / 100);
        $totalFinal = $subtotalConBeneficio + $iva;
        $this->proyecto->presupuesto_total = $totalFinal;
        $this->proyecto->save();
    }

    // UI
    public $nodosAbiertos     = [];
    public $rubrosExpandidos  = false; // estado del toggle global de rubros padre
    public $ejExpandidos      = false; // estado del toggle global en vista ejecución
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

    // ── Modal editar item APU ────────────────────────────────
    public bool $modalEditarItemApu        = false;
    public ?int $editItemApuId             = null;
    public string $editItemApuNombre       = '';
    public string $editItemApuCantidad     = '';
    public ?int $editItemApuRecursoId      = null;
    public array $editItemApuSugeridos     = [];
    public float $editItemApuNodoCantidad  = 1; // cantidad del nodo APU en el presupuesto

    // Modal agregar item APU
    public bool $modalAgregarItemApu      = false;
    public ?int $apuComposicionId         = null;
    // Cuando se abre el modal de recursos normales en modo APU
    public ?int $apuComposicionIdCtx      = null;
    public string $nuevoItemApuNombre     = '';
    public string $nuevoItemApuCantidad   = '';
    public ?int $nuevoItemApuRecursoId    = null;
    public array $nuevoItemApuSugeridos   = [];

    // Modal eliminar item APU
    public bool $modalEliminarItemApu = false;
    public ?int $eliminarItemApuId    = null;
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
    public $incluirCargaSocial = true;

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
    public $excelIncluirCargaSocial = true;

    // Historial undo/redo
    public $historialEstados = [];
    public $indexHistorial = -1;

    // Vista activa: 'presupuesto' | 'ejecucion'
    public $vistaActiva = 'presupuesto';

    // Modo lectura: true cuando el proyecto está en ejecución (presupuesto bloqueado)
    public $modoLectura = false;

    // Nodo copiado para pegar
    public ?int $nodoCopiadoId = null;

    // ── Modal importar presupuesto ────────────────────────────
    public bool  $modalImportarPresupuesto = false;
    public string $tipoImportPresupuesto   = 'pdf';
    public $archivoImportPresupuesto       = null;
    public array $importPresupuestoResult  = [];
    public bool  $importandoPresupuesto    = false;

    // ── Modal eliminar todo ───────────────────────────────────
    public bool $modalEliminarTodo = false;

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

        // Todas las categorías inician cerradas

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
    $this->incluirCargaSocial = true;
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
    $this->excelIncluirCargaSocial = true;
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
                'incluirCargaSocial'  => $this->incluirCargaSocial,
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
        $pdf        = Pdf::loadHTML($html)->setPaper('A4', 'landscape');
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
            'borders'   => ['allBorders' => ['borderStyle' => $Thin, 'color' => ['rgb' => 'DDDDDD']]],
            'alignment' => ['vertical' => 'center', 'wrapText' => false],
            'font'      => ['size' => 9],
        ];
        $styleCatRow = [
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1a1a1a']],
            'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => 'E8E8E8']],
            'borders'   => ['allBorders' => ['borderStyle' => $Thin, 'color' => ['rgb' => 'CCCCCC']]],
            'alignment' => ['vertical' => 'center'],
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

        // Fila Beneficio: mostrarla claramente en el Excel exportado.
        // (Anteriormente se exportaba como texto blanco sobre fondo blanco;
        // eso la hacía "transparente" al abrir el archivo.)
        if ($pctBeneficio > 0) {
            $styleBeneficioVisible = [
                'font'      => ['color' => ['rgb' => '444444'], 'size' => 9, 'bold' => true],
                'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => $Left],
                'borders'   => ['allBorders' => ['borderStyle' => $Thin]],
            ];
            $sheet->setCellValue('A' . $row, 'Beneficio (' . number_format($pctBeneficio, 0) . '%)');
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($styleBeneficioVisible);
            $sheet->setCellValue('G' . $row, '$ ' . number_format($beneficioMonto, 2, ',', '.'));
            $sheet->getStyle('G' . $row)->applyFromArray($styleBeneficioVisible);
            $sheet->getRowDimension($row)->setRowHeight(16);
            $row++;
        }

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
        if ($this->excelIncluirUnidad)        $sheet->setCellValueByColumnAndRow($col++, $row, 'Unidad');
        if ($this->excelIncluirCantidad)      $sheet->setCellValueByColumnAndRow($col++, $row, 'Cantidad');
        if ($this->excelIncluirPrecio) {
            $sheet->setCellValueByColumnAndRow($col++, $row, 'P. Unit. ' . $monedaBase);
            $sheet->setCellValueByColumnAndRow($col++, $row, 'Total');
        }
        if ($this->excelIncluirCargaSocial)   $sheet->setCellValueByColumnAndRow($col++, $row, 'Carga Social');
        $lastCol = $col - 1;
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol);
        $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($styleColHeader);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        $categoriaActual = null;
        foreach ($datos['items'] as $item) {
            // APU sub-items are internal breakdown rows — not importable separately, skip them
            if (($item['tipo'] ?? '') === 'apu_item') continue;

            if ($item['categoria'] !== '' && $item['categoria'] !== $categoriaActual) {
                $categoriaActual = $item['categoria'];
                $catSubtotal = (($datos['cat_subtotales'][$item['categoria']] ?? 0)) * (1 + $pctBeneficio / 100);
                $catCsTotal  = $datos['cat_cs_totales'][$item['categoria']] ?? 0;
                if ($this->excelIncluirPrecio && $lastCol > 1) {
                    // When CS column present: Total goes in (lastCol-1), CS goes in lastCol
                    $totalColIndex = $this->excelIncluirCargaSocial ? $lastCol - 1 : $lastCol;
                    $mergeUntil    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColIndex - 1);
                    $totalColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColIndex);
                    $sheet->setCellValue('A' . $row, $item['categoria']);
                    $sheet->mergeCells('A' . $row . ':' . $mergeUntil . $row);
                    $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($styleCatRow);
                    $sheet->setCellValueByColumnAndRow($totalColIndex, $row, $catSubtotal);
                    $sheet->getStyle($totalColLetter . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                    if ($this->excelIncluirCargaSocial) {
                        $sheet->setCellValueByColumnAndRow($lastCol, $row, $catCsTotal > 0 ? $catCsTotal : '');
                        $sheet->getStyle($lastColLetter . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                    }
                } else {
                    $sheet->setCellValue('A' . $row, $item['categoria']);
                    $sheet->mergeCells('A' . $row . ':' . $lastColLetter . $row);
                    $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($styleCatRow);
                }
                $sheet->setCellValueByColumnAndRow($lastCol + 1, $row, 'CAT');
                $sheet->getRowDimension($row)->setRowHeight(16);
                $row++;
            }

            if ($item['tipo'] === 'subrubro') {
                $styleSubrubro = [
                    'font'      => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '444444']],
                    'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => 'FFF4EE']],
                    'alignment' => ['horizontal' => $Left, 'indent' => 2],
                ];
                $col = 1;
                $sheet->setCellValueByColumnAndRow($col++, $row, $item['categoria']);
                $sheet->setCellValueByColumnAndRow($col++, $row, $item['nombre']);
                if ($this->excelIncluirUnidad)   $sheet->setCellValueByColumnAndRow($col++, $row, $item['unidad'] ?? '');
                $cantSubrubro = $item['cantidad_display'] ?? $item['cantidad'] ?? 0;
                if ($this->excelIncluirCantidad)    $sheet->setCellValueByColumnAndRow($col++, $row, $cantSubrubro);
                if ($this->excelIncluirPrecio) {
                    // Precio y subtotal incluyen todos los recursos hijos (precio_usd = perUnit sumando hijos)
                    $precioConBeneficioExcel   = ($item['precio_usd'] ?? 0) * (1 + $pctBeneficio / 100);
                    $subtotalConBeneficioExcel = $precioConBeneficioExcel * $cantSubrubro;
                    $sheet->setCellValueByColumnAndRow($col++, $row, $precioConBeneficioExcel);
                    $sheet->setCellValueByColumnAndRow($col++, $row, $subtotalConBeneficioExcel);
                    $penult = $lastCol - ($this->excelIncluirCargaSocial ? 2 : 1);
                    $penultLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($penult);
                    $totalColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($penult + 1);
                    $sheet->getStyle($penultLetter . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle($totalColLetter . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                }
                if ($this->excelIncluirCargaSocial) $sheet->setCellValueByColumnAndRow($col++, $row, $item['carga_social_total'] ?? 0);
                $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($styleSubrubro);
                $sheet->getRowDimension($row)->setRowHeight(15);
                $sheet->setCellValueByColumnAndRow($lastCol + 1, $row, 'SUB');
                $row++;
                continue;
            }

            $col = 1;
            $sheet->setCellValueByColumnAndRow($col++, $row, $item['categoria']);
            $sheet->setCellValueByColumnAndRow($col++, $row, $item['nombre']);
            if ($this->excelIncluirUnidad)      $sheet->setCellValueByColumnAndRow($col++, $row, $item['unidad']);
            if ($this->excelIncluirCantidad) {
                $cantVal = $item['cantidad_display'] ?? $item['cantidad'];
                $sheet->setCellValueByColumnAndRow($col, $row, $cantVal);
                $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.0000');
                $col++;
            }
            if ($this->excelIncluirPrecio) {
                $precioConBeneficioExcel   = ($item['precio_usd'] ?? 0) * (1 + $pctBeneficio / 100);
                $subtotalConBeneficioExcel = ($item['subtotal_display'] ?? $item['subtotal'] ?? 0) * (1 + $pctBeneficio / 100);
                $sheet->setCellValueByColumnAndRow($col, $row, $precioConBeneficioExcel);
                $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $subtotalConBeneficioExcel);
                $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $col++;
            }
            if ($this->excelIncluirCargaSocial) {
                $csVal = $item['carga_social_total'] ?? 0;
                $sheet->setCellValueByColumnAndRow($col, $row, $csVal);
                $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $col++;
            }
            $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($styleData);
            $sheet->getRowDimension($row)->setRowHeight(15);
            $sheet->setCellValueByColumnAndRow($lastCol + 1, $row, 'REC');
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
        // ANCHOS DE COLUMNA — dinámicos según columnas incluidas
        // ─────────────────────────────────────────────────────
        $colWidths = [];
        $colWidths[] = 20;  // A: Categoría
        $colWidths[] = 42;  // B: Ítem (más ancho, texto largo)
        if ($this->excelIncluirUnidad)      $colWidths[] = 10;  // Unidad
        if ($this->excelIncluirCantidad)    $colWidths[] = 12;  // Cantidad
        if ($this->excelIncluirPrecio) {
            $colWidths[] = 18;  // P. Unit.
            $colWidths[] = 18;  // Total
        }
        if ($this->excelIncluirCargaSocial) $colWidths[] = 18;  // Carga Social

        foreach ($colWidths as $i => $width) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($letter)->setWidth($width);
        }

        // Columna oculta con marcador de tipo (CAT/SUB/REC)
        $markerColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol + 1);
        $sheet->getColumnDimension($markerColLetter)->setVisible(false);

        // ─────────────────────────────────────────────────────
        // CONFIGURACIÓN DE PÁGINA PARA EXPORTAR COMO PDF
        // ─────────────────────────────────────────────────────
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $pageSetup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $pageSetup->setFitToPage(true);
        $pageSetup->setFitToWidth(1);   // todo el contenido en 1 página de ancho
        $pageSetup->setFitToHeight(0);  // sin límite de alto (N páginas)
        $pageSetup->setPrintArea('A1:' . $lastColLetter . $sheet->getHighestRow());
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

    // Subtotal y carga social por categoría.
    $catSubtotales = [];
    $catCsTotales  = [];
    foreach ($items as $item) {
        $cat = $item['categoria'] ?? '';
        if ($item['tipo'] === 'item' || $item['tipo'] === 'apu_header') {
            $catSubtotales[$cat] = ($catSubtotales[$cat] ?? 0) + ($item['subtotal'] ?? 0);
        } elseif ($item['tipo'] === 'subrubro') {
            // CS del rubro padre = suma de (CS_por_unidad × cantidad) de cada subrubro hijo
            $cantDisplay = $item['cantidad_display'] ?? $item['cantidad'] ?? 1;
            $catCsTotales[$cat] = ($catCsTotales[$cat] ?? 0) + (($item['carga_social_total'] ?? 0) * $cantDisplay);
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
        'items'           => $items,
        'total'           => $totalGeneral,
        'cat_subtotales'  => $catSubtotales,
        'cat_cs_totales'  => $catCsTotales,
    ];
}

/**
 * Calcula la carga social total de un nodo (recursivo sobre sus hijos).
 * Para recursos labor: precio * pct_cs / 100 * cantidad * multiplier.
 * Para subrubros: suma de CS de todos sus descendientes.
 */
private function calcularCSNodo($nodo, float $multiplier = 1): float
{
    $pctGlobal = (float)($this->proyecto->carga_social ?? 0);
    $total     = 0;
    $cantNodo  = ($nodo->cantidad ?? 1) * $multiplier;

    // Recurso labor directo
    if (!is_null($nodo->recurso_id) && in_array($nodo->recurso?->tipo, ['labor', 'mano_obra'])) {
        // Si carga_social es null, usar la del recurso; si es 0 o >0, usar la del proyecto (0 fuerza 0)
        $pct = !is_null($this->proyecto->carga_social)
            ? (float)$this->proyecto->carga_social
            : (float)($nodo->recurso?->social_charges_percentage ?? 0);
        $precio = $nodo->precio_unitario ?? $nodo->precio_usd ?? 0;
        $total  = $precio * ($pct / 100) * $cantNodo;

    // Composición APU: CS = precio_unit_labor × cs% × horas × cantidad_APU
    } elseif (!is_null($nodo->recurso_id) && $nodo->recurso?->tipo === 'composition') {
        $itemsInternos = \App\Models\ComposicionItem::where('composicion_id', $nodo->recurso_id)->get();
        foreach ($itemsInternos as $interno) {
            $resBase = $interno->recursoBase;
            if (!$resBase || !in_array($resBase->tipo, ['labor', 'mano_obra'])) continue;
            $pct = !is_null($this->proyecto->carga_social)
                ? (float)$this->proyecto->carga_social
                : (float)($resBase->social_charges_percentage ?? 0);
            $total += ($resBase->precio_usd ?? 0) * ($pct / 100) * $interno->cantidad * $cantNodo;
        }

    // Subrubro: sumar CS de hijos sin propagar la cantidad del subrubro padre
    } elseif ($nodo->hijos && $nodo->hijos->count() > 0) {
        foreach ($nodo->hijos as $hijo) {
            $total += $this->calcularCSNodo($hijo, 1);
        }
    }

    return $total;
}

private function calcularCargaSocialPDF(): float
{
    $totalCS = 0;

    $this->recorrerCargaSocial(
        $this->proyecto->proyectoRecursos->whereNull('parent_id'),
        $totalCS,
        1
    );

    return $totalCS;
}

/**
 * Recorre el árbol de nodos acumulando la carga social total.
 * Fórmula: (pct_carga_social / 100 * precio_unit) * cantidad_item * cantidad_total_rubro
 *
 * $multiplier acumula las cantidades de todos los ancestros, de modo que
 * al llegar a un ítem hoja de mano de obra se multiplica por la cantidad
 * efectiva total (ej: 1,5 h/m × 80 m = 120 h totales).
 */
private function recorrerCargaSocial($nodos, float &$totalCS, float $multiplier = 1): void
{
    // Si el proyecto tiene un % global de carga social definido, lo usa como override
    $pctGlobal = $this->proyecto->carga_social;

    foreach ($nodos as $nodo) {
        $cantNodo       = $nodo->cantidad ?? 1;
        $precioUnitario = $nodo->precio_unitario ?? $nodo->precio_usd ?? 0;

        // Recurso directo de mano de obra
        if (($nodo->recurso && in_array($nodo->recurso->tipo, ['labor', 'mano_obra'])) || in_array($nodo->tipo, ['labor', 'mano_obra'])) {
            $porcentajeCS = !is_null($pctGlobal)
                ? (float)$pctGlobal
                : ($nodo->recurso->social_charges_percentage ?? $nodo->social_charges_percentage ?? 0);
            $totalCS += $multiplier * $cantNodo * $precioUnitario * ($porcentajeCS / 100);
        }

        // Composición (APU): CS = precio_unit_labor × cs% × horas × cantidad_APU
        if ($nodo->recurso && $nodo->recurso->tipo === 'composition') {
            $itemsInternos = \App\Models\ComposicionItem::where('composicion_id', $nodo->recurso_id)->get();
            foreach ($itemsInternos as $interno) {
                $resBase = $interno->recursoBase;
                if (!$resBase || !in_array($resBase->tipo, ['labor', 'mano_obra'])) continue;
                $porcentajeCS = !is_null($pctGlobal)
                    ? (float)$pctGlobal
                    : ($resBase->social_charges_percentage ?? 0);
                $totalCS += $multiplier * $cantNodo * $interno->cantidad * ($resBase->precio_usd ?? 0) * ($porcentajeCS / 100);
            }
        }

        if ($nodo->hijos && count($nodo->hijos) > 0) {
            $this->recorrerCargaSocial($nodo->hijos, $totalCS, $multiplier * $cantNodo);
        }
    }
}

public function actualizarCargaSocial(mixed $valor): void
{
    if ($this->modoLectura) return;
    // Si el valor es '', null o no numérico, setea a null (sin override global)
    if ($valor === '' || is_null($valor) || !is_numeric($valor)) {
        $this->proyecto->carga_social = null;
    } else {
        $this->proyecto->carga_social = (float) $valor;
    }
    $this->proyecto->save();
    $this->cargarProyecto(); // Recarga nodos y totales tras guardar
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
                'tipo'               => 'subrubro',
                'categoria'          => $catEste,
                'nombre'             => $nodo->nombre,
                'descripcion'        => '',
                'unidad'             => $nodo->unidad ?? '',
                'cantidad'           => $cantidadNodo * $multiplier,
                'cantidad_display'   => $cantidadNodo,
                'precio_usd'         => $perUnit,
                'precio_own'         => $nodo->precio_usd ?? $nodo->precio_unitario ?? 0,
                'subtotal'           => $subrubroSubtotal,
                'carga_social_total' => $this->calcularCSNodo($nodo, $cantidadNodo),
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

            $esComposicion = $nodo->recurso && $nodo->recurso->tipo === 'composition';

            $cantidadDisplay = $nodo->cantidad ?? 1; // cantidad por unidad del padre (para mostrar en PDF)

            $items[] = [
                'tipo'               => $esComposicion ? 'apu_header' : 'item',
                'categoria'          => $catEste,
                'nombre'             => $nodo->nombre,
                'descripcion'        => '',
                'unidad'             => $nodo->unidad ?? '',
                'cantidad'           => $cantidadEffective,
                'cantidad_display'   => $cantidadDisplay,
                'precio_usd'         => $precioUnitario,
                'subtotal'           => $subtotal,
                'subtotal_display'   => $cantidadDisplay * $precioUnitario,
                'carga_social_total' => $this->calcularCSNodo($nodo, 1),
            ];

            // Expandir items del APU como sub-filas para mostrar el desglose en el PDF
            if ($esComposicion && $nodo->recurso->items) {
                $compItems = $nodo->recurso->items
                    ->filter(fn($i) => !is_null($i->recursoBase))
                    ->sortBy(fn($i) => match($i->recursoBase->tipo) {
                        'material'  => 0,
                        'equipment' => 1,
                        'labor'     => 2,
                        default     => 3,
                    });

                foreach ($compItems as $compItem) {
                    $base        = $compItem->recursoBase;
                    $pUnit       = $base->precio_usd ?? 0;
                    $esLabor     = in_array($base->tipo, ['labor', 'mano_obra']);
                    $cargaSocial = $esLabor
                        ? round($pUnit * (($base->social_charges_percentage ?? 0) / 100), 4)
                        : 0;
                    $cantItem = $compItem->cantidad * $cantidadEffective;

                    $items[] = [
                        'tipo'         => 'apu_item',
                        'categoria'    => $catEste,
                        'nombre'       => $base->nombre,
                        'descripcion'  => $base->tipo,
                        'unidad'       => $base->unidad ?? '',
                        'cantidad'     => $cantItem,
                        'carga_social' => $cargaSocial,
                        'precio_usd'   => $pUnit,
                        'subtotal'     => $cantItem * $pUnit,
                        'recurso_tipo' => $base->tipo,
                    ];
                }
            }
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
    // ── CRUD ITEMS APU ───────────────────────────────────────

    public function abrirModalEditarItemApu(int $itemId, float $nodoCantidad = 1): void
    {
        if ($this->modoLectura) return;
        $this->editItemApuId              = $itemId;
        $this->editItemApuRecursoId       = $item->recurso_id;
        $this->editItemApuNombre          = $item->recursoBase?->nombre ?? $item->nombre;
        $this->editItemApuCantidad        = rtrim(rtrim(number_format((float) $item->cantidad, 6, '.', ''), '0'), '.');
        $this->editItemApuNodoCantidad    = $nodoCantidad;
        $this->editItemApuSugeridos       = [];
        $this->modalEditarItemApu         = true;
    }

    public function buscarRecursosEditarApu(): void
    {
        if (empty($this->editItemApuNombre)) {
            $this->editItemApuSugeridos = [];
            return;
        }
        $this->editItemApuSugeridos = Recurso::whereIn('tipo', ['material', 'labor', 'equipment'])
            ->where('nombre', 'like', '%' . $this->editItemApuNombre . '%')
            ->limit(8)->get(['id', 'nombre', 'unidad'])->toArray();
    }

    public function seleccionarRecursoEditarApu(int $id, string $nombre): void
    {
        $this->editItemApuRecursoId = $id;
        $this->editItemApuNombre    = $nombre;
        $this->editItemApuSugeridos = [];
    }

    public function guardarItemApu(): void
    {
        if ($this->modoLectura) return;
        $this->editItemApuCantidad = str_replace(',', '.', $this->editItemApuCantidad);
        $this->validate([
            'editItemApuRecursoId' => 'required|exists:recursos,id',
            'editItemApuCantidad'  => 'required|numeric|min:0.001',
        ]);
        $recurso = Recurso::findOrFail($this->editItemApuRecursoId);
        $item    = \App\Models\ComposicionItem::findOrFail($this->editItemApuId);
        $item->update([
            'recurso_id' => $this->editItemApuRecursoId,
            'nombre'     => $recurso->nombre,
            'cantidad'   => (float) $this->editItemApuCantidad,
        ]);
        $this->recalcularComposicion($item->composicion_id);
        $this->cerrarModalEditarItemApu();
        $this->cargarProyecto();
        $this->actualizarPresupuestoTotalGuardado();
    }

    public function cerrarModalEditarItemApu(): void
    {
        $this->modalEditarItemApu      = false;
        $this->editItemApuId           = null;
        $this->editItemApuRecursoId    = null;
        $this->editItemApuNombre       = '';
        $this->editItemApuCantidad     = '';
        $this->editItemApuNodoCantidad = 1;
        $this->editItemApuSugeridos    = [];
        $this->resetErrorBag();
    }

    public function abrirModalAgregarItemApu(int $composicionId): void
    {
        if ($this->modoLectura) return;
        $this->apuComposicionId       = $composicionId;
        $this->nuevoItemApuNombre     = '';
        $this->nuevoItemApuCantidad   = '';
        $this->nuevoItemApuRecursoId  = null;
        $this->nuevoItemApuSugeridos  = [];
        $this->modalAgregarItemApu    = true;
    }

    public function abrirModalRecursosParaApu(int $composicionId, string $nombre): void
    {
        $this->reset(['itemsRecursos', 'buscarSelector']);
        $this->apuComposicionIdCtx   = $composicionId;
        $this->nombreCtx             = $nombre;
        $this->mostrarModalRecursos  = true;
        $this->modalSelectorRecursos = false;
        $this->filtroTipo            = 'Todos';
    }

    public function buscarRecursosAgregarApu(): void
    {
        if (empty($this->nuevoItemApuNombre)) {
            $this->nuevoItemApuSugeridos = [];
            return;
        }
        $this->nuevoItemApuSugeridos = Recurso::whereIn('tipo', ['material', 'labor', 'equipment'])
            ->where('nombre', 'like', '%' . $this->nuevoItemApuNombre . '%')
            ->limit(8)->get(['id', 'nombre', 'unidad'])->toArray();
    }

    public function seleccionarRecursoAgregarApu(int $id, string $nombre): void
    {
        $this->nuevoItemApuRecursoId = $id;
        $this->nuevoItemApuNombre    = $nombre;
        $this->nuevoItemApuSugeridos = [];
    }

    public function guardarNuevoItemApu(): void
    {
        if ($this->modoLectura) return;
        $this->nuevoItemApuCantidad = str_replace(',', '.', $this->nuevoItemApuCantidad);
        $this->validate([
            'nuevoItemApuRecursoId' => 'required|exists:recursos,id',
            'nuevoItemApuCantidad'  => 'required|numeric|min:0.001',
        ]);
        $recurso = Recurso::findOrFail($this->nuevoItemApuRecursoId);
        \App\Models\ComposicionItem::create([
            'composicion_id' => $this->apuComposicionId,
            'recurso_id'     => $this->nuevoItemApuRecursoId,
            'nombre'         => $recurso->nombre,
            'cantidad'       => (float) $this->nuevoItemApuCantidad,
        ]);
        $this->recalcularComposicion($this->apuComposicionId);
        $this->cerrarModalAgregarItemApu();
        $this->cargarProyecto();
        $this->actualizarPresupuestoTotalGuardado();
    }

    public function cerrarModalAgregarItemApu(): void
    {
        $this->modalAgregarItemApu   = false;
        $this->apuComposicionId      = null;
        $this->nuevoItemApuNombre    = '';
        $this->nuevoItemApuCantidad  = '';
        $this->nuevoItemApuRecursoId = null;
        $this->nuevoItemApuSugeridos = [];
        $this->resetErrorBag();
    }

    public function abrirModalEliminarItemApu(int $itemId): void
    {
        if ($this->modoLectura) return;
        $this->eliminarItemApuId    = $itemId;
        $this->modalEliminarItemApu = true;
    }

    public function confirmarEliminarItemApu(): void
    {
        $item = \App\Models\ComposicionItem::findOrFail($this->eliminarItemApuId);
        $composicionId = $item->composicion_id;
        $item->delete();
        $this->recalcularComposicion($composicionId);
        $this->modalEliminarItemApu = false;
        $this->eliminarItemApuId    = null;
        $this->cargarProyecto();
        $this->actualizarPresupuestoTotalGuardado();
    }

    public function cerrarModalEliminarItemApu(): void
    {
        $this->modalEliminarItemApu = false;
        $this->eliminarItemApuId    = null;
    }

    private function recalcularComposicion(int $composicionId): void
    {
        $composicion = Recurso::with('items.recursoBase')->findOrFail($composicionId);
        $total = $composicion->items->sum(fn($i) => $i->precio_total);
        $composicion->update(['precio_usd' => $total]);

        // Sincronizar precio en los ProyectoRecurso de este proyecto
        \App\Models\ProyectoRecurso::where('recurso_id', $composicionId)
            ->where('proyecto_id', $this->proyecto->id)
            ->update(['precio_usd' => $total]);
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
        // Cada vez que se recarga el proyecto, actualizamos el total guardado
        $this->actualizarPresupuestoTotalGuardado();
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
        $this->actualizarPresupuestoTotalGuardado();
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
        $this->actualizarPresupuestoTotalGuardado();
    }

    public function moverNodo(int $draggedId, int $targetId, string $posicion): void
    {
        if ($this->modoLectura) return;
        $dragged = ProyectoRecurso::find($draggedId);
        $target  = ProyectoRecurso::find($targetId);
        if (!$dragged || !$target) return;
        if ($dragged->parent_id !== $target->parent_id) return;

        $siblings = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->where('parent_id', $dragged->parent_id)
            ->orderBy('orden')
            ->get();

        $items     = $siblings->reject(fn($s) => $s->id === $draggedId)->values()->all();
        $targetIdx = array_search($targetId, array_column($items, 'id'));
        if ($targetIdx === false) return;

        $insertAt = $posicion === 'before' ? $targetIdx : $targetIdx + 1;
        array_splice($items, $insertAt, 0, [$dragged]);

        foreach ($items as $index => $node) {
            ProyectoRecurso::where('id', $node->id)->update(['orden' => $index + 1]);
        }

        $this->cargarProyecto();
        $this->actualizarPresupuestoTotalGuardado();
    }

    public function copiarNodo(int $id): void
    {
        $this->nodoCopiadoId = $id;
    }

    public function cancelarCopia(): void
    {
        $this->nodoCopiadoId = null;
    }

    public function pegarNodo(int $siblingId): void
    {
        if ($this->modoLectura) return;
        if (!$this->nodoCopiadoId) return;

        $original = ProyectoRecurso::with('hijos.hijos.hijos')->find($this->nodoCopiadoId);
        $sibling  = ProyectoRecurso::find($siblingId);
        if (!$original || !$sibling) return;

        $this->_duplicarNodoRecursivo($original, $sibling->parent_id);
        $this->nodoCopiadoId = null;
        $this->cargarProyecto();
    }

    private function _duplicarNodoRecursivo(ProyectoRecurso $nodo, ?int $newParentId): void
    {
        $maxOrden = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->where('parent_id', $newParentId)
            ->max('orden') ?? 0;

        $nuevo = $nodo->replicate();
        $nuevo->parent_id = $newParentId;
        $nuevo->orden     = $maxOrden + 1;
        $nuevo->save();

        foreach ($nodo->hijos as $hijo) {
            $this->_duplicarNodoRecursivo($hijo, $nuevo->id);
        }
    }

    // ── IMPORTAR PRESUPUESTO ──────────────────────────────────

    public function abrirModalImportarPresupuesto(): void
    {
        if ($this->modoLectura) return;
        $this->modalImportarPresupuesto  = true;
        $this->importPresupuestoResult   = [];
        $this->archivoImportPresupuesto  = null;
        $this->tipoImportPresupuesto     = 'pdf';
    }

    public function cerrarModalImportarPresupuesto(): void
    {
        $this->modalImportarPresupuesto = false;
        $this->importPresupuestoResult  = [];
        $this->archivoImportPresupuesto = null;
        $this->importandoPresupuesto    = false;
    }

    public function abrirModalEliminarTodo(): void
    {
        if ($this->modoLectura) return;
        $this->modalEliminarTodo = true;
    }

    public function confirmarEliminarTodo(): void
    {
        if ($this->modoLectura) return;
        ProyectoRecurso::where('proyecto_id', $this->proyecto->id)->delete();
        $this->modalEliminarTodo = false;

        // Resetear el total del presupuesto al eliminar todo
        $this->proyecto->presupuesto_total = 0;
        $this->proyecto->save();
        $this->proyecto->refresh();

        $this->cargarProyecto();
        $this->actualizarPresupuestoTotalGuardado();
        $this->guardarEstado();
        $this->dispatch('notify', mensaje: 'Presupuesto eliminado completamente.', tipo: 'success');
        // Notificar a otros componentes para que refresquen (Livewire 3)
        $this->dispatch('proyectoActualizado');
    }

    public function importarPresupuesto(): void
    {
        if ($this->modoLectura) return;
        $this->validate([
            'archivoImportPresupuesto' => 'required|file|max:10240|mimes:pdf',
        ]);

        $this->importandoPresupuesto = true;
        $path = $this->archivoImportPresupuesto->getRealPath();

        try {
            // Solo PDF soportado para importación
            $parsed = $this->_parsePDF($path);

            $items                 = $parsed['items'];
            $beneficioExportado    = (float)($parsed['beneficio'] ?? 0);
            $parsedPrecioFinal     = (float)($parsed['preciofinal'] ?? $parsed['total'] ?? 0);

            if (empty($items)) {
                $this->importPresupuestoResult = ['error' => 'No se encontraron ítems en el archivo.'];
                $this->importandoPresupuesto   = false;
                return;
            }

            $recursosCount = count(array_filter($items, fn($it) => ($it['tipo'] ?? '') === 'recurso'));
            $creados = $this->_crearDesdeItems($items, $beneficioExportado);
            $this->cargarProyecto();
            // Recalcular y guardar total del presupuesto en el proyecto (incluye beneficio e impuestos)
            try {
                $datos = $this->obtenerDatosPresupuesto('completo');
                $subtotalBase = $datos['total'] ?? 0;
                $pctBeneficio = $beneficioExportado > 0 ? $beneficioExportado : (float)($this->proyecto->beneficio ?? 0);
                $beneficioMonto = $subtotalBase * ($pctBeneficio / 100);
                $pctImpuestos = (float)($this->proyecto->impuestos ?? 22);
                $impuestosMonto = ($subtotalBase + $beneficioMonto) * ($pctImpuestos / 100);
                $computedTotalObra = $subtotalBase + $beneficioMonto + $impuestosMonto;

                // Preferir el total detectado en el archivo (si existe), sino usar el calculado
                if ($parsedPrecioFinal > 0) {
                    $this->proyecto->presupuesto_total = round($parsedPrecioFinal, 2);
                } else {
                    $this->proyecto->presupuesto_total = round($computedTotalObra, 2);
                }
                if ($beneficioExportado > 0) {
                    $this->proyecto->beneficio = $pctBeneficio;
                }
                $this->proyecto->save();
                $this->proyecto->refresh();
                // Notificar a otros componentes Livewire (ej. listado de proyectos) para refrescar
                $this->dispatch('proyectoActualizado');
            } catch (\Throwable $e) {
                // No bloquear la importación si falla el cálculo del total
            }

            $this->importPresupuestoResult = [
                'ok' => true,
                'creados' => $creados,
                'recursos' => $recursosCount,
                'total' => $this->proyecto->presupuesto_total ?? 0,
            ];
            $this->dispatch('notify', mensaje: "Presupuesto importado: {$recursosCount} recursos, {$creados} nodos creados.", tipo: 'success');

            // Cerrar modal y limpiar archivo seleccionado al completar la importación
            $this->modalImportarPresupuesto = false;
            $this->archivoImportPresupuesto = null;
        } catch (\Throwable $e) {
            $this->importPresupuestoResult = ['error' => 'Error al procesar el archivo: ' . $e->getMessage()];
        }

        $this->importandoPresupuesto = false;
    }

    private function _parseExcel(string $path): array
    {
        $reader      = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(false);
        $spreadsheet = $reader->load($path);
        $sheet       = $spreadsheet->getActiveSheet();

        $items             = [];
        $state             = 'pre'; // pre → header → data
        $detectedBeneficio = 0.0;
        $detectedTotal     = 0.0;

        $parseNumExcel = static fn(string $s): float =>
            (float)str_replace(',', '.', preg_replace('/\.(?=\d{3}[,\.]|\d{3}$)/', '', trim($s)));
        $detectedPrecioFinalExcel = null;
        $detectedAnyTotalExcel    = null;
        $colCat    = 0;
        $colItem   = 1;
        $colUnid   = 3;
        $colCant   = 4;
        $colPrecio = 5;

        foreach ($sheet->getRowIterator() as $rowObj) {
            $rowIdx = $rowObj->getRowIndex();
            $cells  = [];
            foreach ($rowObj->getCellIterator('A', 'G') as $cell) {
                $v = $cell->getValue();
                $cells[] = is_numeric($v) ? (string)(float)$v : trim((string)($v ?? ''));
            }

            // ── Phase 1: skip everything until "TABLA DE PRESUPUESTO" ──
            if ($state === 'pre') {
                // Detect beneficio% from the resumen block: "Beneficio (35%)"
                if (preg_match('/beneficio\s*\(\s*(\d+(?:[.,]\d+)?)\s*%\)/iu', $cells[0] ?? '', $bm)) {
                    $detectedBeneficio = (float)str_replace(',', '.', $bm[1]);
                }

                // Detect explicit totals in resumen (e.g. "PRECIO FINAL USD 150.342" or "TOTAL USD 79.200")
                $rowStr = implode(' ', $cells);
                if (preg_match('/\b(?:precio\s*final|preciofinal|total(?:\s+obra)?)\b/iu', $rowStr, $lbl)) {
                    if (preg_match_all('/([0-9]+(?:[.,][0-9]{3})*(?:[.,][0-9]+)?)/', $rowStr, $ms)) {
                        $last = end($ms[1]);
                        $num = $parseNumExcel($last);
                        if (preg_match('/precio\s*final/i', $lbl[0])) {
                            $detectedPrecioFinalExcel = $num;
                        } else {
                            $detectedAnyTotalExcel = $num;
                        }
                    }
                }

                if (str_contains(strtoupper($cells[0] ?? ''), 'TABLA DE PRESUPUESTO')) {
                    $state = 'header';
                }
                continue;
            }

            // ── Phase 2: next row after marker = column headers ──
            if ($state === 'header') {
                foreach ($cells as $i => $v) {
                    $vl = mb_strtolower(trim((string)$v));
                    if (str_contains($vl, 'categor'))                                     $colCat    = $i;
                    if ($vl === 'ítem' || $vl === 'item' || str_contains($vl, 'ítem'))   $colItem   = $i;
                    if ($vl === 'unidad')                                                 $colUnid   = $i;
                    if ($vl === 'cantidad')                                               $colCant   = $i;
                    if (str_contains($vl, 'precio') && !str_contains($vl, 'final'))      $colPrecio = $i;
                }
                $state = 'data';
                continue;
            }

            // ── Phase 3: data rows ──
            $firstUpper = strtoupper($cells[0] ?? '');
            if (str_contains($firstUpper, 'TOTAL') || str_contains($firstUpper, 'ALCANCE')
                || str_contains($firstUpper, 'CONDICIONES') || str_contains($firstUpper, 'VALIDEZ')
                || str_contains($firstUpper, 'CARGA SOCIAL') || str_contains($firstUpper, 'BENEFICIO')
                || str_contains($firstUpper, 'IMPUESTO')) {
                break;
            }

            if (empty(array_filter($cells, fn($c) => $c !== '' && $c !== '0'))) continue;

            // Type marker in column H (written by our exporter) — primary detection
            // Falls back to background colour for files from older exports
            $typeMarker = strtoupper(trim((string)($sheet->getCellByColumnAndRow(8, $rowIdx)->getValue() ?? '')));

            // Background: getRGB() returns 6-char hex; some builds return 8-char ARGB — take last 6
            $bg = strtoupper(substr(
                $sheet->getStyleByColumnAndRow(1, $rowIdx)->getFill()->getStartColor()->getRGB() ?? '',
                -6
            ));

            $catVal    = trim($cells[$colCat]    ?? '');
            $itemVal   = trim($cells[$colItem]   ?? '');
            $unidVal   = trim($cells[$colUnid]   ?? '');

            // Numbers may be stored as floats ("1.5") or formatted strings ("1,5") — normalise both
            $cantRaw   = $cells[$colCant]   ?? '0';
            $precioRaw = $cells[$colPrecio] ?? '0';
            $cantVal   = is_numeric($cantRaw)
                ? (float)$cantRaw
                : (float)str_replace(',', '.', preg_replace('/[^\d,]/', '', $cantRaw));
            $precioVal = is_numeric($precioRaw)
                ? (float)$precioRaw
                : (float)str_replace(',', '.', preg_replace('/[^\d,]/', '', $precioRaw));

            // Category: explicit CAT marker OR col B is empty (cells A:penultimate are merged in the export)
            $isCat = $typeMarker === 'CAT' || ($itemVal === '' && $catVal !== '');
            // Subrubro: explicit SUB marker OR bg F5F5F5 with a non-empty name (and not a category)
            $isSub = !$isCat && $itemVal !== '' && ($typeMarker === 'SUB' || ($typeMarker === '' && $bg === 'F5F5F5'));

            if ($isCat) {
                $nombre = $catVal ?: $itemVal;
                if ($nombre !== '') {
                    $items[] = ['tipo' => 'categoria', 'nombre' => $nombre, 'unidad' => '', 'cantidad' => 1, 'precio' => 0];
                }
            } elseif ($isSub) {
                $items[] = ['tipo' => 'subrubro', 'nombre' => $itemVal, 'unidad' => $unidVal, 'cantidad' => $cantVal > 0 ? $cantVal : 1, 'precio' => $precioVal];
            } elseif ($itemVal !== '') {
                $items[] = ['tipo' => 'recurso', 'nombre' => $itemVal, 'unidad' => $unidVal, 'cantidad' => $cantVal > 0 ? $cantVal : 1, 'precio' => $precioVal];
            }
        }

        $detectedTotal = $detectedAnyTotalExcel ?? $detectedPrecioFinalExcel ?? 0;
        return ['items' => $items, 'beneficio' => $detectedBeneficio, 'preciofinal' => $detectedTotal];
    }

    private function _parsePDF(string $path): array
    {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($path);
        $text   = $pdf->getText();
        $lines  = preg_split('/\r?\n/', $text);

        $items             = [];
        $state             = 'pre'; // pre → header → data
        $pendingName       = '';    // multi-line name accumulation
        $pendingDataLine   = '';    // partial data row waiting for its continuation
        $detectedBeneficio = 0.0;
        $lastSubrubroQty   = 1.0;  // cantidad efectiva del último subrubro visto (para dividir recursos)
        $detectedPrecioFinalPdf = null;
        $detectedAnyTotalPdf    = null;
        $prevTotalLabel         = '';

        // Substrings that identify non-data lines to skip in the data section
        $skipKeywords = [
            'TABLA DE PRESUPUESTO', 'ÍTEM', 'ITEM', 'DESCRIPCI',
            'ALCANCE', 'CONDICIONES', 'VALIDEZ',
            'LO QUE SE CONSIDER', 'MODO DE PAGO', 'TIEMPO DE VIGENCIA',
            'REPORTE DE PRESUPUESTO', 'RESUMEN DE COSTOS',
            'SUBTOTAL USD', 'IMPUESTOS', 'PRECIO FINAL',
            'CARGA SOCIAL', 'CONFIDENCIAL',
        ];

        // Code prefix pattern: "01.00 ", "03.05.01 ", "100. ", "02. " etc.
        $codeRx = '/^\d{1,3}\.[\d\.]*\s*/';

        // Normalise European number: "1.234,56" → 1234.56
        $parseNum = static fn(string $s): float =>
            (float)str_replace(',', '.', preg_replace('/\.(?=\d{3}[,\.]|\d{3}$)/', '', trim($s)));

        // Helper: strip all trailing "$ amount" tokens rightward and return [head, amounts[]]
        // where amounts[0] is the leftmost $ (unit price), amounts[1] = subtotal, amounts[2] = CS.
        $stripAmounts = static function (string $line) use ($parseNum): array {
            $amounts = [];
            $working = trim(preg_replace('/\s*[—–-]+\s*$/', '', $line)); // remove trailing em-dash
            while (preg_match('/\$\s*([\d.,]+)\s*$/', $working, $m)) {
                array_unshift($amounts, $parseNum($m[1]));
                $working = trim(preg_replace('/\s*\$\s*[\d.,]+\s*$/', '', $working));
            }
            return [trim($working), $amounts];
        };

        foreach ($lines as $line) {
            $line  = trim($line);
            if ($line === '') continue;

            $upper = mb_strtoupper($line, 'UTF-8');

            // ── Phase 1: skip until table marker ──────────────────────────────
            if ($state === 'pre') {
                // Detect beneficio% from the resumen block: "Beneficio (35%)"
                if (preg_match('/beneficio\s*\(\s*(\d+(?:[.,]\d+)?)\s*%\)/iu', $line, $bm)) {
                    $detectedBeneficio = (float)str_replace(',', '.', $bm[1]);
                }

                // Detect explicit totals in resumen (e.g. "PRECIO FINAL USD 150.342" or "TOTAL USD 79.200")
                if (preg_match('/\b(?:precio\s*final|preciofinal|total(?:\s+obra)?)\b/iu', $upper, $lbl)) {
                    // try to extract number from same line
                    if (preg_match('/(?:USD\s*)?\$?\s*([\d.,]+)/i', $line, $mn)) {
                        $num = $parseNum($mn[1]);
                        if (preg_match('/precio\s*final/i', $lbl[0])) {
                            $detectedPrecioFinalPdf = $num;
                        } else {
                            $detectedAnyTotalPdf = $num;
                        }
                    } else {
                        // mark label — next numeric line may contain the amount
                        $prevTotalLabel = strtolower($lbl[0]);
                    }
                }

                // If previous line was a total label and current line contains a number, capture it
                if (!empty($prevTotalLabel) && preg_match('/([\d.,]+)/', $line, $mnum)) {
                    $num = $parseNum($mnum[1]);
                    if (str_contains($prevTotalLabel, 'precio')) {
                        $detectedPrecioFinalPdf = $num;
                    } else {
                        $detectedAnyTotalPdf = $num;
                    }
                    $prevTotalLabel = '';
                }

                if (str_contains($upper, 'TABLA DE PRESUPUESTO')) {
                    $state = 'header';
                }
                continue;
            }

            // ── Phase 2: first line after marker = column headers, skip it ────
            if ($state === 'header') {
                $state = 'data';
                continue;
            }

            // ── Phase 3: data lines ───────────────────────────────────────────

            // Hard stop at table totals row
            if (str_contains($upper, 'TOTAL PRESUPUESTO')) break;

            // If we have a buffered partial row (line ended with "$"), prepend it now
            if ($pendingDataLine !== '') {
                $line          = trim($pendingDataLine) . ' ' . $line;
                $upper         = mb_strtoupper($line, 'UTF-8');
                $pendingDataLine = '';
            }

            // Skip section headers, page-repeat headers and other noise
            $isNoise = false;
            foreach ($skipKeywords as $kw) {
                if (str_contains($upper, $kw)) { $isNoise = true; break; }
            }
            if ($isNoise) {
                $pendingName     = ''; // reset accumulation at section boundaries
                $pendingDataLine = '';
                $lastSubrubroQty = 1.0;
                continue;
            }

            // Skip lines that are purely numeric (stray page numbers, etc.)
            if (preg_match('/^[\d\s.,]+$/', $line)) continue;

            // ── Partial row: line ends with "$" (PDF split the row mid-price) ──
            // Buffer it and join with the next line before classifying.
            if (preg_match('/\$\s*$/', $line)) {
                $pendingDataLine = $line;
                continue;
            }

            // Strip all trailing "$ amount" tokens to isolate the head (name + unit + qty)
            // and collect the amounts left-to-right: [unit_price, subtotal, cs?]
            [$head, $amounts] = $stripAmounts($line);

            // No dollar signs at all → pure text, accumulate as multi-line name continuation
            if (count($amounts) === 0) {
                $pendingName = $pendingName !== '' ? $pendingName . ' ' . $line : $line;
                continue;
            }

            // Merge any pending multi-line name prefix
            if ($pendingName !== '') {
                $head        = trim($pendingName . ' ' . $head);
                $pendingName = '';
            }

            if ($head === '') continue;

            $hasCode = (bool)preg_match($codeRx, $head);

            if ($hasCode) {
                // ── Code prefix row: category or subrubro ────────────────────
                $afterCode = trim(preg_replace($codeRx, '', $head));
                $parts     = preg_split('/\s+/', $afterCode);
                $lastTok   = end($parts);

                if ($lastTok !== false && $lastTok !== ''
                    && is_numeric(str_replace(',', '.', (string)$lastTok)))
                {
                    // Last token is numeric → SUBRUBRO (has unit + qty before the prices)
                    $qty       = $parseNum($lastTok);
                    $nameParts = array_slice($parts, 0, -1);
                    $unit      = '';
                    $tok2      = end($nameParts);
                    if ($tok2 !== false && strlen($tok2) <= 6
                        && preg_match('/^[a-zA-ZáéíóúüñÁÉÍÓÚÜÑ]/u', $tok2))
                    {
                        $unit      = $tok2;
                        $nameParts = array_slice($nameParts, 0, -1);
                    }
                    $nombre = trim(implode(' ', $nameParts));
                    if ($nombre !== '') {
                        $lastSubrubroQty = $qty > 0 ? $qty : 1.0;
                        $items[] = [
                            'tipo'     => 'subrubro',
                            'nombre'   => $nombre,
                            'unidad'   => $unit ?: 'gl',
                            'cantidad' => $qty > 0 ? $qty : 1,
                            'precio'   => $amounts[0] ?? 0, // leftmost $ = unit price
                        ];
                    }
                } else {
                    // Last token is NOT numeric → CATEGORY (name only, no unit/qty)
                    $nombre = trim($afterCode);
                    if ($nombre !== '') {
                        $lastSubrubroQty = 1.0;
                        $items[] = [
                            'tipo'     => 'categoria',
                            'nombre'   => $nombre,
                            'unidad'   => '',
                            'cantidad' => 1,
                            'precio'   => 0,
                        ];
                    }
                }

            } else {
                // ── No code prefix → RESOURCE ─────────────────────────────────
                $parts     = preg_split('/\s+/', $head);
                $qty       = 1.0;
                $unit      = '';
                $nameParts = $parts;

                $tok = end($parts);
                if ($tok !== false && is_numeric(str_replace(',', '.', (string)$tok))) {
                    $qty       = $parseNum($tok);
                    $nameParts = array_slice($parts, 0, -1);
                    $tok2      = end($nameParts);
                    if ($tok2 !== false && strlen($tok2) <= 6
                        && preg_match('/^[a-zA-ZáéíóúüñÁÉÍÓÚÜÑ]/u', $tok2))
                    {
                        $unit      = $tok2;
                        $nameParts = array_slice($nameParts, 0, -1);
                    }
                }

                $nombre = trim(implode(' ', $nameParts));
                if ($nombre !== '') {
                    $items[] = [
                        'tipo'     => 'recurso',
                        'nombre'   => $nombre,
                        'unidad'   => $unit,
                        'cantidad' => $qty > 0 ? $qty : 1,
                        'precio'   => $amounts[0] ?? 0, // leftmost $ = unit price
                    ];
                }
            }

        }

        $detectedTotal = $detectedAnyTotalPdf ?? $detectedPrecioFinalPdf ?? 0;
        return ['items' => $items, 'beneficio' => $detectedBeneficio, 'preciofinal' => $detectedTotal];
    }

    private function _crearDesdeItems(array $items, float $beneficioExportado = 0.0): int
    {
        $creados          = 0;
        $catNodeId        = null;
        $subrubroNodeId   = null;

        // Reverse beneficio from exported prices.
        // Use the % detected from the file itself; fall back to project's setting.
        $pct    = $beneficioExportado > 0 ? $beneficioExportado : (float)($this->proyecto->beneficio ?? 0);
        $factor = $pct > 0 ? (1 + $pct / 100) : 1.0;

        foreach ($items as $item) {
            $nombre = trim($item['nombre'] ?? '');
            if (!$nombre) continue;

            if ($item['tipo'] === 'categoria') {
                $maxOrden = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
                    ->whereNull('parent_id')->max('orden') ?? 0;
                $node = ProyectoRecurso::create([
                    'proyecto_id' => $this->proyecto->id,
                    'parent_id'   => null,
                    'recurso_id'  => null,
                    'nombre'      => $nombre,
                    'unidad'      => 'gl',
                    'cantidad'    => 1,
                    'precio_usd'  => 0,
                    'categoria'   => $nombre,
                    'imported'    => true,
                    'orden'       => $maxOrden + 1,
                ]);
                $catNodeId      = $node->id;
                $subrubroNodeId = null;
                $creados++;

            } elseif ($item['tipo'] === 'subrubro') {
                if (!$catNodeId) continue;
                $maxOrden = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
                    ->where('parent_id', $catNodeId)->max('orden') ?? 0;
                $node = ProyectoRecurso::create([
                    'proyecto_id' => $this->proyecto->id,
                    'parent_id'   => $catNodeId,
                    'recurso_id'  => null,
                    'nombre'      => $nombre,
                    'unidad'      => $item['unidad'] ?: 'gl',
                    'cantidad'    => $item['cantidad'] ?: 1,
                    'precio_usd'  => 0,
                    'categoria'   => null,
                    'imported'    => true,
                    'orden'       => $maxOrden + 1,
                ]);
                $subrubroNodeId = $node->id;
                $creados++;

            } elseif ($item['tipo'] === 'recurso') {
                $parentId = $subrubroNodeId ?? $catNodeId;
                if (!$parentId) continue;

                // Intentar match con recurso existente en catálogo (3 niveles de confianza)
                $nombreLower = mb_strtolower(trim($nombre), 'UTF-8');
                // 1. Exacto
                $recurso = Recurso::whereRaw('LOWER(TRIM(nombre)) = ?', [$nombreLower])->first();
                // 2. El nombre del catálogo está contenido dentro del nombre importado
                //    (ej: "OFICIAL ALBAÑIL" matchea "OFICIAL ALBAÑIL (MO)")
                if (!$recurso && strlen($nombreLower) > 2) {
                    $recurso = Recurso::whereRaw(
                        'LENGTH(nombre) > 2 AND LOWER(?) LIKE CONCAT(\'%\', LOWER(TRIM(nombre)), \'%\')',
                        [$nombreLower]
                    )->orderByRaw('LENGTH(nombre) DESC')->first();
                }
                // 3. Fallback original: el nombre importado está contenido en el nombre del catálogo
                if (!$recurso) {
                    $recurso = Recurso::whereRaw('LOWER(nombre) LIKE ?', ['%' . $nombreLower . '%'])->first();
                }

                // Reverse the beneficio factor from the exported price
                $precioImportado = ($item['precio'] ?? 0) > 0
                    ? round(($item['precio'] / $factor), 6)
                    : 0;

                $maxOrden = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
                    ->where('parent_id', $parentId)->max('orden') ?? 0;

                ProyectoRecurso::create([
                    'proyecto_id' => $this->proyecto->id,
                    'parent_id'   => $parentId,
                    'recurso_id'  => $recurso?->id,
                    'nombre'      => $nombre,
                    'unidad'      => $item['unidad'] ?: ($recurso?->unidad ?? 'gl'),
                    'cantidad'    => $item['cantidad'] ?: 1,
                    'precio_usd'  => $precioImportado ?: ($recurso?->precio_usd ?? 0),
                    'categoria'   => null,
                    'imported'    => true,
                    'orden'       => $maxOrden + 1,
                ]);
                $creados++;
            }
        }

        return $creados;
    }

    public function toggleBeneficio()
{
    if ($this->modoLectura) return;
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

    // Propagar el mismo precio unitario real a todos los nodos del proyecto
    // que compartan el mismo recurso_id (mismo recurso repetido en distintos subrubros).
    if ($recurso->recurso_id) {
        ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->where('recurso_id', $recurso->recurso_id)
            ->update(['costo_real' => $costo]);
    } else {
        $recurso->update(['costo_real' => $costo]);
    }

    $this->proyecto->refresh();
    $this->cargarProyecto();
}

/**
 * Sincroniza el precio_usd de un nodo importado con el precio actual del catálogo.
 * Solo aplica a nodos con imported=true y recurso_id vinculado.
 */
public function sincronizarPrecioRecurso(int $id): void
{
    $nodo = ProyectoRecurso::find($id);
    if (!$nodo || $nodo->proyecto_id !== $this->proyecto->id) return;
    if (!$nodo->imported || !$nodo->recurso_id) return;

    $precio = $nodo->recurso?->precio_usd ?? 0;
    $nodo->update(['precio_usd' => $precio]);
    $this->cargarProyecto();
    $this->guardarEstado();
    $this->actualizarPresupuestoTotalGuardado();
}

/**
 * Sincroniza todos los recursos importados del proyecto con el precio actual del catálogo.
 */
public function sincronizarTodosLosPrecios(): void
{
    $nodos = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
        ->where('imported', true)
        ->whereNotNull('recurso_id')
        ->with('recurso')
        ->get();

    foreach ($nodos as $nodo) {
        $recurso = $nodo->recurso;
        if (!$recurso) continue;
        $precio = $recurso->precio_usd ?? 0;
        if ($precio > 0 && abs(($nodo->precio_usd ?? 0) - $precio) > 0.001) {
            $nodo->update(['precio_usd' => $precio]);
        }
    }

    $this->cargarProyecto();
    $this->guardarEstado();
    $this->actualizarPresupuestoTotalGuardado();
}

/**
 * Actualiza costo_real en múltiples nodos agrupados por recurso.
 * $ids: array de ProyectoRecurso IDs que corresponden al mismo recurso agrupado.
 * $valor: costo real TOTAL del grupo — se distribuye proporcionalmente por presupuestado.
 */
public function actualizarCostoRealGrupo(array $ids, $valor)
{
    $costo = filter_var($valor, FILTER_VALIDATE_FLOAT);
    if ($costo === false || $costo < 0) $costo = null;

    $nodos = ProyectoRecurso::whereIn('id', $ids)
        ->where('proyecto_id', $this->proyecto->id)
        ->get();
    if ($nodos->isEmpty()) return;

    if ($costo === null) {
        foreach ($nodos as $n) $n->update(['costo_real' => null]);
    } elseif ($nodos->count() === 1) {
        $nodos->first()->update(['costo_real' => $costo]);
    } else {
        // Distribuir proporcionalmente según presupuestado
        $totalPres = $nodos->sum(fn($n) => ($n->cantidad ?? 1) * ($n->precio_unitario ?? $n->precio_usd ?? 0));
        foreach ($nodos as $n) {
            $pres = ($n->cantidad ?? 1) * ($n->precio_unitario ?? $n->precio_usd ?? 0);
            $parte = $totalPres > 0 ? round($costo * ($pres / $totalPres), 2) : 0;
            $n->update(['costo_real' => $parte]);
        }
    }

    $this->proyecto->refresh();
    $this->cargarProyecto();
}

    // ── MODAL EDITAR ─────────────────────────────────────────

    public function abrirModalEditar($id)
    {
        if ($this->modoLectura) return;
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
    if ($this->modoLectura) return;
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
        if ($this->modoLectura) return;
        $this->deleteId = $id;
        $this->mostrarModalEliminar = true;
    }

    public function confirmarEliminar()
    {
        if ($this->modoLectura) return;
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

    public function expandirTodos(): void
    {
        // Abre solo los rubros padre (categorías), no los subrubros
        $catNames = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->whereNull('parent_id')
            ->pluck('categoria')
            ->filter()
            ->unique();
        $catKeys = $catNames->map(fn($n) => 'cat_' . $n)->toArray();
        $this->nodosAbiertos  = array_values(array_unique(array_merge($this->nodosAbiertos, $catKeys)));
        $this->rubrosExpandidos = true;
    }

    public function colapsarTodos(): void
    {
        $this->nodosAbiertos    = [];
        $this->rubrosExpandidos = false;
    }

    public function expandirTodosEjecucion(): void
    {
        $catNames = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->whereNull('parent_id')
            ->pluck('categoria')
            ->filter()
            ->unique();
        $catKeys = $catNames->map(fn($n) => 'ej_cat_' . \Illuminate\Support\Str::slug($n))->toArray();
        $this->nodosAbiertos = array_values(array_unique(array_merge($this->nodosAbiertos, $catKeys)));
        $this->ejExpandidos  = true;
    }

    public function colapsarTodosEjecucion(): void
    {
        // Quitar todas las claves ej_ del array
        $this->nodosAbiertos = array_values(array_filter(
            $this->nodosAbiertos,
            fn($k) => !str_starts_with($k, 'ej_')
        ));
        $this->ejExpandidos = false;
    }

    /**
     * Abre o cierra todos los subrubros (hijos directos sin recurso) de una categoría.
     */
    public function toggleSubrubrosDeCategoria(int $catNodeId, string $catKey): void
    {
        $keys = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->where('parent_id', $catNodeId)
            ->whereNull('recurso_id')
            ->pluck('id')
            ->map(fn($id) => 'node_' . $id)
            ->toArray();

        if (empty($keys)) return;

        $abiertos = array_filter($keys, fn($k) => in_array($k, $this->nodosAbiertos));

        if (count($abiertos) === count($keys)) {
            // Todos abiertos → cerrar todos (mantener categoría abierta)
            $this->nodosAbiertos = array_values(array_diff($this->nodosAbiertos, $keys));
        } else {
            // Alguno/ninguno cerrado → abrir todos
            $this->nodosAbiertos = array_values(array_unique(array_merge($this->nodosAbiertos, $keys)));
        }

        // Asegurar que la categoría padre quede abierta en el servidor
        if (!in_array($catKey, $this->nodosAbiertos)) {
            $this->nodosAbiertos[] = $catKey;
        }
    }

    /**
     * Toggle todos los subrubros directos de una categoría en vista ejecución.
     * Usa claves ej_X.
     */
    public function toggleSubrubrosEjecucion(int $catNodeId, string $catKeyEj): void
    {
        $keys = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->where('parent_id', $catNodeId)
            ->whereNull('recurso_id')
            ->pluck('id')
            ->map(fn($id) => 'ej_' . $id)
            ->toArray();

        if (empty($keys)) return;

        $abiertos = array_filter($keys, fn($k) => in_array($k, $this->nodosAbiertos));

        if (count($abiertos) === count($keys)) {
            $this->nodosAbiertos = array_values(array_diff($this->nodosAbiertos, $keys));
        } else {
            $this->nodosAbiertos = array_values(array_unique(array_merge($this->nodosAbiertos, $keys)));
        }

        if (!in_array($catKeyEj, $this->nodosAbiertos)) {
            $this->nodosAbiertos[] = $catKeyEj;
        }
    }

    /**
     * Toggle todos los subrubros de un rubro (nodo sin recurso) en vista ejecución.
     * Usa claves ej_X para los hijos directos sin recurso_id.
     */
    public function toggleSubrubrosDeRubroEjecucion(int $rubroId, string $rubroKey): void
    {
        $keys = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->where('parent_id', $rubroId)
            ->whereNull('recurso_id')
            ->pluck('id')
            ->map(fn($id) => 'ej_' . $id)
            ->toArray();

        if (empty($keys)) return;

        $abiertos = array_filter($keys, fn($k) => in_array($k, $this->nodosAbiertos));

        if (count($abiertos) === count($keys)) {
            $this->nodosAbiertos = array_values(array_diff($this->nodosAbiertos, $keys));
        } else {
            $this->nodosAbiertos = array_values(array_unique(array_merge($this->nodosAbiertos, $keys)));
        }

        if (!in_array($rubroKey, $this->nodosAbiertos)) {
            $this->nodosAbiertos[] = $rubroKey;
        }
    }


    // ── MODAL RUBRO RAÍZ ──────────────────────────────────────

    public function abrirModalRubro()
    {
        if ($this->modoLectura) return;
        $this->resetErrorBag();
        $this->reset(['nombreRubro', 'unidadRubro']);
        $this->unidadRubro = 'gl';
        $this->mostrarModalRubro = true;
    }

    public function guardarRubro()
    {
        if ($this->modoLectura) return;
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
        if ($this->modoLectura) return;
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
        if ($this->modoLectura) return;
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
        if ($this->modoLectura) return;
        $this->reset(['itemsRecursos', 'buscarSelector']);
        $this->parentId              = $parentId;
        $this->categoriaCtx          = $categoria;
        $this->nombreCtx             = $nombrePadre;
        $this->apuComposicionIdCtx   = null;
        $this->mostrarModalRecursos  = true;
        $this->modalSelectorRecursos = false;
        $this->filtroTipo            = 'Todos';
    }

    public function cancelarModalRecursos(): void
    {
        $this->mostrarModalRecursos = false;
        $this->apuComposicionIdCtx  = null;
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
        if ($this->modoLectura) return;
        $this->validate(['itemsRecursos' => 'required|array|min:1']);

        // Modo APU: guardar como ComposicionItem en lugar de ProyectoRecurso
        if ($this->apuComposicionIdCtx) {
            foreach ($this->itemsRecursos as $item) {
                \App\Models\ComposicionItem::create([
                    'composicion_id' => $this->apuComposicionIdCtx,
                    'recurso_id'     => $item['recurso_id'],
                    'nombre'         => $item['nombre'],
                    'cantidad'       => $item['cantidad'],
                ]);
            }
            $this->recalcularComposicion($this->apuComposicionIdCtx);
            $this->apuComposicionIdCtx  = null;
            $this->mostrarModalRecursos = false;
            $this->proyecto->refresh();
            $this->cargarProyecto();
            return;
        }

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
        if ($this->modoLectura) return;
        if ($this->indexHistorial > 0) {
            $this->indexHistorial--;
            $this->restaurarEstado($this->indexHistorial);
        }
    }

    public function rehacer()
    {
        if ($this->modoLectura) return;
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
        ])->layout('layouts.app', ['hideSidebar' => true]);
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