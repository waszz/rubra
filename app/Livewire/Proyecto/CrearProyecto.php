<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use App\Models\Proyecto;
use App\Data\Plantillas; // Importamos la clase de plantillas
use Illuminate\Support\Facades\Auth;

class CrearProyecto extends Component
{
    public $nombre_proyecto;
    public $descripcion;
    public $notas;
    public $cliente;
    public $ubicacion;
    public $metros_cuadrados = 0;
    public $beneficio = 0;
    public $impuestos = 0;
    public $fecha_inicio;
    public $mercado;
    public $moneda_base = 'USD';
    public $horas_jornal = 8;
    public $estado = 'en_revision';
    public $plantilla_base = 'en_blanco';
    public $ubicacion_lat;
    public $ubicacion_lng;
    public $usuariosSeleccionados = [];

    // Propiedad para almacenar los recursos de la plantilla seleccionada
    public $recursos = [];

    protected $rules = [
        'nombre_proyecto'  => 'required|min:3',
        'metros_cuadrados' => 'required|numeric|min:0',
        'fecha_inicio'     => 'nullable|date',
        'descripcion'      => 'nullable|string',
        'notas'            => 'nullable|string',
        'cliente'          => 'nullable|string',
        'ubicacion'        => 'nullable|string',
        'beneficio'        => 'nullable|numeric|min:0|max:100',
        'impuestos'        => 'nullable|numeric|min:0|max:100',
        'mercado'          => 'nullable|string',
        'moneda_base'      => 'required|string',
        'horas_jornal'     => 'required|numeric|min:1',
        'estado'           => 'required|string',
        'plantilla_base'   => 'required|string',
    ];

    /**
     * Se ejecuta automáticamente cuando cambia $plantilla_base en la vista
     */
    public function updatedPlantillaBase($value)
    {
        if ($value && $value !== 'en_blanco') {
            // Buscamos los recursos definidos en App\Data\Plantillas
            $this->recursos = Plantillas::get($value);
        } else {
            $this->recursos = [];
        }
    }

    public function guardar()
    {
        $this->validate();

        // Validar límite de proyectos según el plan
        $user = Auth::user();
        $limite = $user->proyectosLimite();
        $proyectosActuales = Proyecto::where('user_id', $user->id)->count();

        if ($proyectosActuales >= $limite) {
            $this->addError('nombre_proyecto', "Tu plan \"{$user->planLabel()}\" permite hasta {$limite} proyecto(s). Mejorá tu plan para crear más.");
            return;
        }

        // Cálculo rápido (ajustar según tu lógica de costos)
        // Forzar tipos numéricos para evitar errores al dividir strings no numéricos
        $metros = (float) str_replace(',', '.', (string) $this->metros_cuadrados);
        $costo_base = $metros * 1200;

        $beneficioPct = (float) str_replace(',', '.', (string) $this->beneficio);
        $impuestosPct = (float) str_replace(',', '.', (string) $this->impuestos);

        $ganancia   = $costo_base * ($beneficioPct / 100);
        $impuesto   = $costo_base * ($impuestosPct / 100);
        $total      = $costo_base + $ganancia + $impuesto;

        // 1. Creamos el Proyecto
     $proyecto = Proyecto::create([
    'nombre_proyecto'     => $this->nombre_proyecto,
    'descripcion'         => $this->descripcion,
    'notas'               => $this->notas,
    'cliente'             => $this->cliente,
    'ubicacion'           => $this->ubicacion,
    'metros_cuadrados'    => $this->metros_cuadrados,
    'presupuesto_total'   => $total,
    'ganancia_estimada'   => $ganancia,
    'fecha_inicio'        => $this->fecha_inicio,
    'mercado'             => $this->mercado,
    'moneda_base'         => $this->moneda_base,
    'horas_jornal'        => $this->horas_jornal,
    'impuestos'           => $this->impuestos,
    'beneficio'           => $this->beneficio,
    'estado_obra'         => $this->estado,
    'estado_autorizacion' => 'pendiente',
    'plantilla_base'      => $this->plantilla_base,
    'user_id'             => Auth::id(),
    'ubicacion_lat'       => $this->ubicacion_lat,
    'ubicacion_lng'       => $this->ubicacion_lng,
]);

// agregar creador
$proyecto->usuarios()->attach(Auth::id());

// agregar invitados
if (!empty($this->usuariosSeleccionados)) {
    $proyecto->usuarios()->attach($this->usuariosSeleccionados);
}
        // 2. Si hay recursos cargados por la plantilla, los guardamos
        // Solo creamos UNA entrada por cada categoría ÚNICA (sin duplicados)
        if (count($this->recursos) > 0) {
            $categoriasUnicas = collect($this->recursos)
                ->pluck('categoria')
                ->unique()
                ->values();

            foreach ($categoriasUnicas as $categoria) {
                // Verificar que no exista ya
                $existe = $proyecto->proyectoRecursos()
                    ->where('categoria', $categoria)
                    ->where('parent_id', null)
                    ->exists();

                if (!$existe) {
                    $proyecto->proyectoRecursos()->create([
                        'categoria'  => $categoria,
                        'nombre'     => $categoria,
                        'unidad'     => 'gl',
                        'cantidad'   => 1,
                        'precio_usd' => 0,
                        'parent_id'  => null,
                    ]);
                }
            }
        }

        $this->dispatch('proyectoCreado');
        $this->reset(['nombre_proyecto', 'descripcion', 'notas', 'recursos', 'plantilla_base']); 
        $this->dispatch('cerrarModal');

        session()->flash('mensaje', 'Proyecto creado con ' . count($this->recursos) . ' recursos precargados 🚀');
    }

    public function render()
    {
        return view('livewire.proyecto.crear-proyecto');
    }
}