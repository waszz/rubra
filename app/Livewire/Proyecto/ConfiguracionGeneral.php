<?php

namespace App\Livewire\Proyecto;

use App\Models\ConfiguracionGeneral as ConfiguracionGeneralModel;
use App\Models\Proyecto;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ConfiguracionGeneral extends Component
{
    use WithFileUploads;

    public string $nombre_empresa   = '';
    public string $rut              = '';
    public string $logo_url         = '';
    public mixed  $logo_file        = null;
    public string $pagina_web       = '';
    public string $redes_sociales   = '';
    public string $telefonos        = '';
    public string $correo           = '';
    public string $latitud          = '-34.88140461443768';
    public string $longitud         = '-56.18880475846473';

    public string $proyecto_activo  = 'TODOS LOS PROYECTOS';
    public bool   $saved            = false;
    public string $logo_preview_url = '';
    public array $proyectos = [];

    public string $nombre_usuario = '';
public string $email_usuario  = '';
public string $password_nuevo = '';
public bool   $modalEliminarCuenta = false;
public string $confirmar_password  = '';
public string $errorPerfil = '';
public string $successPerfil = '';

    protected array $rules = [
        'nombre_empresa' => 'nullable|string|max:255',
        'rut'            => 'nullable|string|max:50',
        'logo_url'       => 'nullable|string|max:500',
        'logo_file'      => 'nullable|image|max:2048',
        'pagina_web'     => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|www\.)/i'],
        'redes_sociales' => 'nullable|string|max:255',
        'telefonos'      => 'nullable|string|max:100',
        'correo'         => 'nullable|email|max:255',
        'latitud'        => 'nullable|numeric|between:-90,90',
        'longitud'       => 'nullable|numeric|between:-180,180',
    ];

    protected array $messages = [
        'logo_url.url'     => 'El logo debe ser una URL válida.',
        'logo_file.image'  => 'El archivo debe ser una imagen.',
        'logo_file.max'    => 'La imagen no puede superar 2 MB.',
        'pagina_web.url'   => 'La página web debe ser una URL válida.',
        'correo.email'     => 'El correo debe tener un formato válido.',
        'latitud.between'  => 'La latitud debe estar entre -90 y 90.',
        'longitud.between' => 'La longitud debe estar entre -180 y 180.',
    ];

public function mount(): void
{
    $config = ConfiguracionGeneralModel::instancia();

    $this->nombre_empresa   = $config->nombre_empresa  ?? '';
    $this->rut              = $config->rut             ?? '';
    $this->logo_url         = $config->logo_url        ?? '';
    $this->pagina_web       = $config->pagina_web      ?? '';
    $this->redes_sociales   = $config->redes_sociales  ?? '';
    $this->telefonos        = $config->telefonos       ?? '';
    $this->correo           = $config->correo          ?? '';
    $this->latitud          = $config->latitud  ? (string) $config->latitud  : '-34.88140461443768';
    $this->longitud         = $config->longitud ? (string) $config->longitud : '-56.18880475846473';
    $this->logo_preview_url = $this->logo_url;

    // Proyectos reales desde la BD (del usuario actual)
   $this->proyectos = Proyecto::where('user_id', auth()->id())
    ->orderBy('nombre_proyecto')
    ->get(['id', 'nombre_proyecto', 'ubicacion_lat', 'ubicacion_lng'])
    ->toArray();

    $this->nombre_usuario = auth()->user()->name;
$this->email_usuario  = auth()->user()->email;
}

public function guardarPerfil(): void
{
    $this->validate([
        'nombre_usuario' => 'required|string|max:255',
        'email_usuario'  => 'required|email|unique:users,email,' . auth()->id(),
        'password_nuevo' => 'nullable|min:8',
    ]);

    $data = [
        'name'  => $this->nombre_usuario,
        'email' => $this->email_usuario,
    ];

    if ($this->password_nuevo) {
        $data['password'] = Hash::make($this->password_nuevo);
    }

    auth()->user()->update($data);

    $this->password_nuevo = '';
    $this->successPerfil  = 'Perfil actualizado correctamente.';
    $this->js("setTimeout(() => \$wire.set('successPerfil', ''), 3000)");
}

public function eliminarCuenta(): void
{
    if (!Hash::check($this->confirmar_password, auth()->user()->password)) {
        $this->errorPerfil = 'Contraseña incorrecta.';
        return;
    }

    $user = auth()->user();
    auth()->logout();
    $user->delete();

    redirect()->route('home');
}

    public function updatedLogoFile(): void
    {
        $this->validateOnly('logo_file');
        $this->logo_preview_url = $this->logo_file->temporaryUrl();
    }

    public function actualizarUbicacion(float $lat, float $lng): void
    {
        $this->latitud  = number_format($lat, 14, '.', '');
        $this->longitud = number_format($lng, 14, '.', '');
    }

    public function guardar(): void
    {
        $this->validate();

        $logoUrl = $this->logo_url;

        if ($this->logo_file) {
            $config = ConfiguracionGeneralModel::instancia();
            if ($config->logo_url && str_starts_with($config->logo_url, '/storage/')) {
                Storage::delete(str_replace('/storage/', 'public/', $config->logo_url));
            }
            $logoUrl = Storage::url($this->logo_file->store('logos', 'public'));
        }

    ConfiguracionGeneralModel::updateOrCreate(
    ['user_id' => auth()->id()],  // condición
    [
        'nombre_empresa' => $this->nombre_empresa,
        'rut'            => $this->rut,
        'logo_url'       => $logoUrl,
        'pagina_web'     => $this->pagina_web,
        'redes_sociales' => $this->redes_sociales,
        'telefonos'      => $this->telefonos,
        'correo'         => $this->correo,
        'latitud'        => $this->latitud,
        'longitud'       => $this->longitud,
    ]
);

        $this->logo_url         = $logoUrl;
        $this->logo_preview_url = $logoUrl ?: $this->logo_preview_url;
        $this->logo_file        = null;
        $this->saved            = true;

        $this->dispatch('config-guardada');
        $this->js("setTimeout(() => \$wire.set('saved', false), 2500)");
    }

    public function render()
    {
        return view('livewire.proyecto.configuracion-general')
            ->layout('layouts.app');
    }
}