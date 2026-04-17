<?php

namespace App\Livewire\Recurso;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Proyecto;
use App\Models\Recurso;
use App\Models\PrecioHistorial;

class MostrarRecursos extends Component
{
    use WithFileUploads;

    // ── Filtros ──────────────────────────────────────────────
    public string $buscar = '';
    public string $filtroTipo = '';
    public $vista = 'grid'; // lista | grid

    // ── Selección múltiple ───────────────────────────────────
    public array $selectedResources = [];
    public bool  $selectAll = false;

    // ── Modal edición ────────────────────────────────────────
    public bool $modalEditar = false;
    public ?int $editandoId  = null;

    public string $editNombre              = '';
    public string $editCodigo              = '';
    public string $editTipo                = '';
    public string $editUnidad              = '';
    public string $editPrecio              = '';
    public string $editMoneda              = 'USD';
    public string $editRegion              = 'Uruguay';
    public string $editVendedor            = '';
    public bool   $editPrecioEstimativo    = false;
    public string $editMarcaModelo         = '';
    public string $editObservaciones       = '';
    public string $editSocialChargesPercentage = '';
    public float  $cargaSocialGlobal = 0;
    // ── Modal eliminación ────────────────────────────────────
    public bool $modalEliminar         = false;
    public bool $modalEliminarMultiple = false;
    public ?int $eliminandoId          = null;

    // ── Edición de item de composición ───────────────────────
public bool $modalEditarItem       = false;
public ?int $editandoItemId        = null;
public string $editItemNombre      = '';
public string $editItemCantidad    = '';
public ?int $editItemRecursoId     = null;
public array $editItemSugeridos    = [];

// ── Agregar item a composición ────────────────────────────
public bool $modalAgregarItem         = false;
public ?int $agregarItemComposicionId = null;
public string $nuevoItemNombre        = '';
public string $nuevoItemCantidad      = '';
public ?int $nuevoItemRecursoId       = null;
public array $recursosSugeridos       = [];


// ── Eliminar item a composición ────────────────────────────
public $modalEliminarItem = false;
public $itemAEliminar = null;

// ── Modales crear ─────────────────────────────────────────
public bool $modalRecurso     = false;
public bool $modalComposicion = false;
public bool $recursoSeleccionado = false;

public string $filtroProyecto = '';
public int $perPage = 20;

// ── Modal historial de precios ────────────────────────────
public bool $modalHistorialPrecios = false;
public ?int $recursoHistorialId = null;
public $precioHistorial = [];

// ── Modal importar desde Excel ─────────────────────────────
public bool $modalImportar = false;
public string $tipoImportacion = 'material'; // material|equipment|labor
public $archivoImportacion = null;
public bool $importandoEnProgreso = false;
public array $recursosBienImportados = [];
public string $mensajeImportacion = '';
public bool $mostrarResultadosImportacion = false;

public function mount(): void
{
    $this->cargaSocialGlobal = (float) (Recurso::where('tipo', 'labor')->value('social_charges_percentage') ?? 0);
}

protected $listeners = [
    'cerrarModalRecurso'     => 'cerrarModalRecurso',
    'cerrarModalComposicion' => 'cerrarModalComposicion',
    'recursoCreado'          => '$refresh',
    'composicionCreada'      => '$refresh',
];

public function abrirModalRecurso(): void    { $this->modalRecurso = true; }
public function cerrarModalRecurso(): void   { $this->modalRecurso = false; }
public function abrirModalComposicion(): void { $this->modalComposicion = true; }
public function cerrarModalComposicion(): void{ $this->modalComposicion = false; }

// ── EDITAR ITEM ───────────────────────────────────────────
public function editarItem(int $itemId): void
{
    $item = \App\Models\ComposicionItem::with('recursoBase')->findOrFail($itemId);
    $this->editandoItemId      = $itemId;
    $this->editItemRecursoId   = $item->recurso_id;
    $this->editItemNombre      = $item->recursoBase?->nombre ?? $item->nombre;
    $this->editItemCantidad    = (string) $item->cantidad;
    $this->editItemSugeridos   = [];
    $this->modalEditarItem     = true;
}

public function buscarRecursosEditar(): void
{
    if (empty($this->editItemNombre)) {
        $this->editItemSugeridos = [];
        return;
    }

    $this->editItemSugeridos = Recurso::whereIn('tipo', ['material', 'labor', 'equipment'])
        ->where('nombre', 'like', '%' . $this->editItemNombre . '%')
        ->limit(8)
        ->get(['id', 'nombre', 'unidad'])
        ->toArray();
}

public function seleccionarRecursoEditar(int $id, string $nombre): void
{
    $this->editItemRecursoId   = $id;
    $this->editItemNombre      = $nombre;
    $this->editItemSugeridos   = [];
}

public function guardarItem(): void
{
    $this->validate([
        'editItemRecursoId' => 'required|exists:recursos,id',
        'editItemCantidad'  => 'required|numeric|min:0.001',
    ]);

    $recurso = Recurso::findOrFail($this->editItemRecursoId);
    $item = \App\Models\ComposicionItem::findOrFail($this->editandoItemId);
    $item->update([
        'recurso_id' => $this->editItemRecursoId,
        'nombre'     => $recurso->nombre,
        'cantidad'   => (float) $this->editItemCantidad,
    ]);

    $this->recalcularComposicion($item->composicion_id);
    $this->cerrarModalEditarItem();
    $this->dispatch('notify', mensaje: 'Item actualizado.', tipo: 'success');
}

public function cerrarModalEditarItem(): void
{
    $this->modalEditarItem     = false;
    $this->editandoItemId      = null;
    $this->editItemRecursoId   = null;
    $this->editItemNombre      = '';
    $this->editItemCantidad    = '';
    $this->editItemSugeridos   = [];
    $this->resetErrorBag();
}

// ── AGREGAR ITEM ──────────────────────────────────────────
public function abrirAgregarItem(int $composicionId): void
{
    $this->agregarItemComposicionId = $composicionId;
    $this->nuevoItemNombre          = '';
    $this->nuevoItemCantidad        = '';
    $this->nuevoItemRecursoId       = null;
    $this->recursosSugeridos        = [];
    $this->recursoSeleccionado      = false;
    $this->modalAgregarItem         = true;
}

public function seleccionarRecurso(int $id, string $nombre): void
{
    $this->nuevoItemRecursoId = $id;
    $this->nuevoItemNombre    = $nombre;
    $this->recursosSugeridos  = [];
}

public function buscarRecursos(): void
{
    if (empty($this->nuevoItemNombre)) {
        $this->recursosSugeridos = [];
        return;
    }

    $this->recursosSugeridos = Recurso::whereIn('tipo', ['material', 'labor', 'equipment'])
        ->where('nombre', 'like', '%' . $this->nuevoItemNombre . '%')
        ->limit(8)
        ->get(['id', 'nombre', 'unidad'])
        ->toArray();
}

public function guardarNuevoItem(): void
{
    $this->validate([
        'nuevoItemRecursoId' => 'required|exists:recursos,id',
        'nuevoItemCantidad'  => 'required|numeric|min:0.001',
    ]);

    $recurso = Recurso::findOrFail($this->nuevoItemRecursoId);

    \App\Models\ComposicionItem::create([
        'composicion_id' => $this->agregarItemComposicionId,
        'recurso_id'     => $this->nuevoItemRecursoId,
        'nombre'         => $recurso->nombre,
        'cantidad'       => (float) $this->nuevoItemCantidad,
    ]);

    $this->recalcularComposicion($this->agregarItemComposicionId);
    $this->cerrarModalAgregarItem();
    $this->dispatch('notify', mensaje: 'Item agregado.', tipo: 'success');
}

public function cerrarModalAgregarItem(): void
{
    $this->modalAgregarItem         = false;
    $this->agregarItemComposicionId = null;
    $this->nuevoItemNombre          = '';
    $this->nuevoItemCantidad        = '';
    $this->nuevoItemRecursoId       = null;
    $this->recursosSugeridos        = [];
    $this->resetErrorBag();
}

// ── HELPER ────────────────────────────────────────────────
private function recalcularComposicion(int $composicionId): void
{
    $composicion = Recurso::with('items.recursoBase')->findOrFail($composicionId);
    $total = $composicion->items->sum(fn($i) => $i->precio_total);
    $composicion->update(['precio_usd' => $total]);
}

public function abrirModalEliminar($itemId)
{
    $this->itemAEliminar = $itemId;
    $this->modalEliminarItem = true;
}
 public function cerrarModalEliminarItem(): void
{
    $this->modalEliminarItem = false;
    $this->itemAEliminar = null;
}


public function eliminarItem()
{
    $item = \App\Models\ComposicionItem::findOrFail($this->itemAEliminar);
    $composicionId = $item->composicion_id;

    $item->delete();

    $this->recalcularComposicion($composicionId);

    $this->cerrarModalEliminarItem(); 

    $this->dispatch('notify', mensaje: 'Item eliminado.', tipo: 'success');
}

    // ── Validación ───────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'editNombre' => 'required|string|max:255',
            'editTipo'   => 'required|in:material,labor,equipment,composition',
            'editUnidad' => 'required|string|max:50',
            'editPrecio' => 'required|numeric|min:0',
            'editSocialChargesPercentage' => 'nullable|numeric|min:0|max:100',
        ];
    }

    protected function messages(): array
    {
        return [
            'editNombre.required' => 'El nombre es obligatorio.',
            'editTipo.required'   => 'El tipo es obligatorio.',
            'editUnidad.required' => 'La unidad es obligatoria.',
            'editPrecio.required' => 'El precio es obligatorio.',
            'editPrecio.numeric'  => 'El precio debe ser un número.',
            'editPrecio.min'      => 'El precio no puede ser negativo.',
        ];
    }

    // ── Reseteo al filtrar ────────────────────────────────────
    public function updatingBuscar(): void    { $this->perPage = 20; }
    public function updatingFiltroTipo(): void { $this->perPage = 20; }

    // ── Select All ────────────────────────────────────────────
    public function updatedSelectAll(bool $value): void
    {
        $this->selectedResources = $value
            ? Recurso::query()
                ->when($this->buscar,     fn($q) => $q->where('nombre', 'like', '%'.$this->buscar.'%'))
                ->when($this->filtroTipo, fn($q) => $q->where('tipo', $this->filtroTipo))
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray()
            : [];
    }

    // ── EDITAR ────────────────────────────────────────────────
    public function editar(int $id): void
    {
        $this->cerrarModalEditar();

        $recurso = Recurso::findOrFail($id);

        $this->editandoId              = $id;
        $this->editNombre              = $recurso->nombre;
        $this->editCodigo              = $recurso->codigo ?? '';
        $this->editTipo                = $recurso->tipo;
        $this->editUnidad              = $recurso->unidad;
        $this->editPrecio              = (string) $recurso->precio_usd;
        $this->editMoneda              = $recurso->moneda ?? 'USD';
        $this->editRegion              = $recurso->region ?? 'Uruguay';
        $this->editVendedor            = $recurso->vendedor ?? '';
        $this->editPrecioEstimativo    = (bool) ($recurso->precio_estimativo ?? false);
        $this->editMarcaModelo         = $recurso->marca_modelo ?? '';
        $this->editObservaciones       = $recurso->observaciones ?? '';
        $this->editSocialChargesPercentage = (string) ($recurso->social_charges_percentage ?? 0);

        $this->modalEditar = true;
    }

    public function guardarEdicion(): void
    {
        $this->validate();

        $recurso = Recurso::findOrFail($this->editandoId);
        $preciAnterior = $recurso->precio_usd;
        $precioNuevo = (float) $this->editPrecio;

        $recurso->update([
            'nombre'                    => trim($this->editNombre),
            'codigo'                    => trim($this->editCodigo) ?: null,
            'tipo'                      => $this->editTipo,
            'unidad'                    => trim($this->editUnidad),
            'precio_usd'                => $precioNuevo,
            'moneda'                    => $this->editMoneda,
            'region'                    => $this->editRegion,
            'vendedor'                  => trim($this->editVendedor) ?: null,
            'precio_estimativo'         => $this->editPrecioEstimativo,
            'marca_modelo'              => trim($this->editMarcaModelo) ?: null,
            'observaciones'             => trim($this->editObservaciones) ?: null,
            'social_charges_percentage' => $this->editTipo === 'labor' ? (float) $this->editSocialChargesPercentage : 0,
        ]);

        // Registrar en historial si el precio cambió
        if ($preciAnterior != $precioNuevo) {
            PrecioHistorial::create([
                'recurso_id' => $this->editandoId,
                'precio_anterior' => $preciAnterior,
                'precio_nuevo' => $precioNuevo,
                'razon' => null,
            ]);

            // Propagar el nuevo precio a todos los proyectos que usan este recurso,
            // excepto las filas marcadas como imported (son copias congeladas).
            \App\Models\ProyectoRecurso::where('recurso_id', $this->editandoId)
                ->where(fn($q) => $q->where('imported', false)->orWhereNull('imported'))
                ->update(['precio_usd' => $precioNuevo]);
        }

        $this->cerrarModalEditar();
        $this->dispatch('notify', mensaje: 'Recurso actualizado correctamente.', tipo: 'success');
    }

    public function cerrarModalEditar(): void
    {
        $this->resetErrorBag();
        $this->modalEditar             = false;
        $this->editandoId              = null;
        $this->editNombre              = '';
        $this->editCodigo              = '';
        $this->editTipo                = 'material';
        $this->editUnidad              = '';
        $this->editPrecio              = '';
        $this->editMoneda              = 'USD';
        $this->editRegion              = 'Uruguay';
        $this->editVendedor            = '';
        $this->editPrecioEstimativo    = false;
        $this->editMarcaModelo         = '';
        $this->editObservaciones       = '';
        $this->editSocialChargesPercentage = '';
    }

    public function actualizarCargaSocialGlobal(mixed $valor): void
    {
        $pct = max(0, min(100, (float) $valor));
        $this->cargaSocialGlobal = $pct;
        Recurso::where('tipo', 'labor')->update(['social_charges_percentage' => $pct]);
        Proyecto::query()->update(['carga_social' => $pct]);
        $this->dispatch('notify', mensaje: 'Carga social actualizada en todos los recursos y proyectos.', tipo: 'success');
    }

    // ── HISTORIAL DE PRECIOS ──────────────────────────────
    public function abrirHistorialPrecios(int $id): void
    {
        $this->recursoHistorialId = $id;
        $this->precioHistorial = PrecioHistorial::where('recurso_id', $id)
            ->orderByDesc('created_at')
            ->get();
        $this->modalHistorialPrecios = true;
    }

    public function cerrarHistorialPrecios(): void
    {
        $this->modalHistorialPrecios = false;
        $this->recursoHistorialId = null;
        $this->precioHistorial = [];
    }

    // ── ELIMINAR (individual) ─────────────────────────────────
    public function confirmarEliminar(int $id): void
    {
        $this->eliminandoId   = $id;
        $this->modalEliminar  = true;
    }

    public function eliminar(): void
    {
        Recurso::findOrFail($this->eliminandoId)->delete();
        $this->cerrarModalEliminar();
        $this->dispatch('notify', mensaje: 'Recurso eliminado.', tipo: 'success');
    }

    public function cerrarModalEliminar(): void
    {
        $this->modalEliminar = false;
        $this->eliminandoId  = null;
    }

    // ── ELIMINAR (múltiple) ───────────────────────────────────
    public function confirmarEliminacionMultiple(): void
    {
        if (empty($this->selectedResources)) return;
        $this->modalEliminarMultiple = true;
    }

    public function eliminarMultiple(): void
    {
        Recurso::whereIn('id', $this->selectedResources)->delete();
        $this->selectedResources     = [];
        $this->selectAll             = false;
        $this->modalEliminarMultiple = false;
        $this->dispatch('notify', mensaje: 'Recursos eliminados correctamente.', tipo: 'success');
    }

    public function cerrarModalEliminarMultiple(): void
    {
        $this->modalEliminarMultiple = false;
    }

    public function toggleSelectAll(): void
    {
        $user = auth()->user();
        $recursosConFiltros = Recurso::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhereNull('user_id');
            })
            ->when($this->buscar, fn($q) => $q->where('nombre', 'like', '%'.$this->buscar.'%'))
            ->when($this->filtroTipo, fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroProyecto, fn($q) => $q->whereHas('proyectos', fn($q2) =>
                $q2->where('proyectos.id', $this->filtroProyecto)
            ))
            ->pluck('id')
            ->toArray();

        if ($this->selectAll) {
            $this->selectedResources = $recursosConFiltros;
        } else {
            $this->selectedResources = [];
        }
    }

    public function updatingFiltroProyecto(): void { $this->perPage = 20; }

    public function loadMore(): void { $this->perPage += 20; }

    // ── IMPORTAR DESDE EXCEL ──────────────────────────────────
    public function abrirModalImportar(): void
    {
        $this->modalImportar = true;
        $this->tipoImportacion = 'material';
        $this->archivoImportacion = null;
        $this->mensajeImportacion = '';
        $this->mostrarResultadosImportacion = false;
    }

    public function cerrarModalImportar(): void
    {
        $this->modalImportar = false;
        $this->archivoImportacion = null;
        $this->importandoEnProgreso = false;
        $this->recursosBienImportados = [];
        $this->mensajeImportacion = '';
        $this->mostrarResultadosImportacion = false;
    }

    public function importarDesdeExcel(): void
    {
        if (!$this->archivoImportacion) {
            $this->mensajeImportacion = 'Por favor selecciona un archivo.';
            return;
        }

        $this->importandoEnProgreso = true;
        $this->recursosBienImportados = [];

        try {
            $rutaTemporal = $this->archivoImportacion->getRealPath();
            $extension = strtolower($this->archivoImportacion->getClientOriginalExtension());

            $exitosos = 0;
            $errores = [];

            // Detectar si es CSV o Excel
            if ($extension === 'csv') {
                // Procesar como CSV - auto-detectar delimitador (coma o punto y coma)
                $handle = fopen($rutaTemporal, 'r');
                if (!$handle) {
                    throw new \Exception('No se pudo abrir el archivo CSV');
                }

                // Detectar delimitador leyendo la primera línea
                $primeraLinea = fgets($handle);
                rewind($handle);
                $delimitador = substr_count($primeraLinea, ';') >= substr_count($primeraLinea, ',') ? ';' : ',';

                $rowIndex = 0;
                while (($row = fgetcsv($handle, 1000, $delimitador)) !== false) {
                    $rowIndex++;
                    // Saltar encabezado (primera fila) solo si parece un encabezado
                    if ($rowIndex === 1 && !is_numeric(trim($row[3] ?? $row[2] ?? ''))) continue;

                    // Obtener valores de las columnas
                    $nombre = trim($row[0] ?? '');
                    $codigo = trim($row[1] ?? '');
                    $unidad = trim($row[2] ?? '');
                    $precio = trim($row[3] ?? '');

                    // Soporte formato viejo (3 columnas sin código)
                    if (!is_numeric($precio) && is_numeric($unidad)) {
                        $precio = $unidad;
                        $unidad = $codigo;
                        $codigo = '';
                    }

                    // Si la fila está vacía, continuar
                    if (!$nombre) {
                        continue;
                    }

                    // Validar que tenga al menos nombre y precio
                    if (!is_numeric($precio)) {
                        $errores[] = "Fila {$rowIndex}: Faltan datos válidos (Nombre o Precio no es número)";
                        continue;
                    }

                    $this->crearRecursoDesdeImportacion($nombre, $unidad, $precio, $rowIndex, $exitosos, $errores, $codigo);
                }
                fclose($handle);

            } else {
                // Procesar como Excel (.xlsx, .xls)
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($rutaTemporal);
                $sheet = $spreadsheet->getActiveSheet();

                $maxRow = $sheet->getHighestRow();

                // Iterar por cada fila (comenzar en fila 2 para saltar encabezado)
                for ($rowIndex = 2; $rowIndex <= $maxRow; $rowIndex++) {
                    // Obtener valores de las columnas A, B, C, D
                    $nombre = trim($sheet->getCell('A' . $rowIndex)->getValue() ?? '');
                    $codigo = trim($sheet->getCell('B' . $rowIndex)->getValue() ?? '');
                    $unidad = trim($sheet->getCell('C' . $rowIndex)->getValue() ?? '');
                    $precio = trim($sheet->getCell('D' . $rowIndex)->getValue() ?? '');

                    // Soporte formato viejo (3 columnas sin código)
                    if (!is_numeric($precio) && is_numeric($unidad)) {
                        $precio = $unidad;
                        $unidad = $codigo;
                        $codigo = '';
                    }

                    // Si la fila está vacía, continuar
                    if (!$nombre) {
                        continue;
                    }

                    // Validar que tenga al menos nombre y precio
                    if (!is_numeric($precio)) {
                        $errores[] = "Fila {$rowIndex}: Faltan datos válidos (Nombre o Precio no es número)";
                        continue;
                    }

                    $this->crearRecursoDesdeImportacion($nombre, $unidad, $precio, $rowIndex, $exitosos, $errores, $codigo);
                }
            }

            // Mensaje de resultado
            $this->mensajeImportacion = "✓ {$exitosos} recursos importados correctamente.";
            if (!empty($errores)) {
                $this->mensajeImportacion .= "\n\n✗ Errores:\n" . implode("\n", $errores);
            }

            $this->mostrarResultadosImportacion = true;
            $this->dispatch('recursoCreado'); // Refrescar lista

        } catch (\Exception $e) {
            $this->mensajeImportacion = "Error al procesar el archivo: " . $e->getMessage();
            $this->mostrarResultadosImportacion = true;
        } finally {
            $this->importandoEnProgreso = false;
            $this->archivoImportacion = null;
        }
    }

    private function crearRecursoDesdeImportacion(string $nombre, string $unidad, string $precio, int $rowIndex, &$exitosos, &$errores, string $codigo = ''): void
    {
        try {
            $recurso = Recurso::create([
                'user_id'   => auth()->id(),
                'nombre'    => $nombre,
                'codigo'    => $codigo ?: null,
                'tipo'      => $this->tipoImportacion,
                'unidad'    => $unidad ?: 'unidad',
                'precio_usd'=> (float) $precio,
            ]);

            // Registrar precio inicial en historial
            PrecioHistorial::create([
                'recurso_id' => $recurso->id,
                'precio_anterior' => null,
                'precio_nuevo' => (float) $precio,
                'razon' => 'Importado desde Excel',
            ]);

            $this->recursosBienImportados[] = [
                'nombre'  => $recurso->nombre,
                'codigo'  => $recurso->codigo,
                'tipo'    => $recurso->tipo,
                'unidad'  => $recurso->unidad,
                'precio'  => $recurso->precio_usd,
            ];

            $exitosos++;
        } catch (\Exception $e) {
            $errores[] = "Fila {$rowIndex} ({$nombre}): " . $e->getMessage();
        }
    }

    // ── RENDER ────────────────────────────────────────────────
   public function render()
{
    $user = auth()->user();
    
    // Obtener solo los proyectos propios del usuario
    $proyectos = \App\Models\Proyecto::where('user_id', $user->id)
        ->orderBy('nombre_proyecto')
        ->get(['id', 'nombre_proyecto']);

    $query = Recurso::with('items.recursoBase')
        ->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereNull('user_id'); // recursos globales heredados
        })
        ->when($this->buscar, fn($q) => $q->where('nombre', 'like', '%'.$this->buscar.'%'))
        ->when($this->filtroTipo, fn($q) => $q->where('tipo', $this->filtroTipo))
        ->when($this->filtroProyecto, fn($q) => $q->whereHas('proyectos', fn($q2) =>
            $q2->where('proyectos.id', $this->filtroProyecto)
        ))
        ->orderBy('nombre');

    $total    = $query->count();
    $recursos = $query->take($this->perPage)->get();
    $hasMore  = $total > $recursos->count();

    return view('livewire.recurso.mostrar-recursos', [
        'recursos'  => $recursos,
        'proyectos' => $proyectos,
        'total'     => $total,
        'hasMore'   => $hasMore,
    ])->layout('layouts.app');
}
}