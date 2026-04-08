<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PanelAdminExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Resumen' => new PanelResumenSheet(),
            'Historial Mensual' => new PanelHistorialSheet(),
        ];
    }
}
