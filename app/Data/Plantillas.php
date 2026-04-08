<?php

namespace App\Data;

class Plantillas
{
    public static function get(string $plantilla): array
    {
        return match($plantilla) {
            'obra_nueva_vivienda'   => static::obraVivienda(),
            'reforma_integral'      => static::reformaIntegral(),
            'steel_frame'           => static::steelFrame(),
            'piscina'               => static::piscina(),
            'quincho'               => static::quincho(),
            'instalacion_electrica' => static::instalacionElectrica(),
            'instalacion_sanitaria' => static::instalacionSanitaria(),
            'pintura'               => static::pintura(),
            'rubros_sencillos'      => static::rubrosSencillos(),
            'local_comercial'       => static::localComercial(),
            default                 => [],
        };
    }

    // ── 01. OBRA NUEVA ───────────────────────────────────────
    private static function obraVivienda(): array
    {
        return [
            ['categoria' => '01. Preliminares', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => '01. Preliminares', 'unidad' => 'gl', 'cantidad'=> 1],
            ['categoria' => '01. Preliminares', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => '02. Mov. Suelos', 'unidad' => 'm3', 'cantidad'=> 1],
            ['categoria' => '02. Mov. Suelos', 'unidad' => 'm3', 'cantidad'=> 1],
            ['categoria' => '03. Estructura', 'unidad' => 'm3', 'cantidad'=> 1],
            ['categoria' => '03. Estructura', 'unidad' => 'kg', 'cantidad'=> 1],
            ['categoria' => '04. Albañilería', 'unidad' => 'un', 'cantidad'=> 1],
            ['categoria' => '04. Albañilería', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => '05. Instalaciones', 'unidad' => 'gl', 'cantidad'=> 1],
            ['categoria' => '05. Instalaciones', 'unidad' => 'gl', 'cantidad'=> 1],
        ];
    }

    // ── 02. REFORMA INTEGRAL ──────────────────────────────────
    private static function reformaIntegral(): array
    {
        return [
            ['categoria' => 'Demoliciones', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => 'Demoliciones', 'unidad' => 'm3', 'cantidad'=> 1],
            ['categoria' => 'Albañilería', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => 'Albañilería', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => 'Terminaciones', 'unidad' => 'm2', 'cantidad'=> 1],
        ];
    }

    // ── 03. STEEL FRAME ───────────────────────────────────────
    private static function steelFrame(): array
    {
        return [
            ['categoria' => 'Platea', 'unidad' => 'm3', 'cantidad'=> 1],
            ['categoria' => 'Estructura Steel', 'unidad' => 'kg', 'cantidad'=> 1],
            ['categoria' => 'Emplacado', 'unidad' => 'un', 'cantidad'=> 1],
            ['categoria' => 'Emplacado', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => 'EIFS', 'unidad' => 'm2', 'cantidad'=> 1],
        ];
    }

    // ── 04. PISCINA ───────────────────────────────────────────
    private static function piscina(): array
    {
        return [
            ['categoria' => 'Excavación', 'unidad' => 'm3', 'cantidad'=> 1],
            ['categoria' => 'Estructura', 'unidad' => 'm3', 'cantidad'=> 1],
            ['categoria' => 'Estructura', 'unidad' => 'kg', 'cantidad'=> 1],
            ['categoria' => 'Hidráulica', 'unidad' => 'un', 'cantidad'=> 1],
            ['categoria' => 'Terminación', 'unidad' => 'un', 'cantidad'=> 1],
        ];
    }

    // ── 06. LOCAL COMERCIAL ───────────────────────────────────
    private static function localComercial(): array
    {
        return [
            ['categoria' => 'Tabiquería', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => 'Cielorraso', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => 'Fachada', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => 'Inst. Eléctrica', 'unidad' => 'gl', 'cantidad'=> 1],
        ];
    }

    // ── 08. INSTALACIÓN ELÉCTRICA ─────────────────────────────
    private static function instalacionElectrica(): array
    {
        return [
            ['categoria' => 'Canalizaciones', 'unidad' => 'ml', 'cantidad'=> 1],
            ['categoria' => 'Cableado', 'unidad' => 'ml', 'cantidad'=> 1],
            ['categoria' => 'Tablero', 'unidad' => 'un', 'cantidad'=> 1],
            ['categoria' => 'Puesta a Tierra', 'unidad' => 'un', 'cantidad'=> 1],
        ];
    }

    // ── 09. INSTALACIÓN SANITARIA ────────────────
    private static function instalacionSanitaria(): array
    {
        return [
            ['categoria' => '01. Agua', 'unidad' => 'ml', 'cantidad'=> 1],
            ['categoria' => '01. Agua', 'unidad' => 'ml', 'cantidad'=> 1],
            ['categoria' => '01. Agua', 'unidad' => 'un', 'cantidad'=> 1],
            ['categoria' => '02. Desagües', 'unidad' => 'ml', 'cantidad'=> 1],
            ['categoria' => '02. Desagües', 'unidad' => 'ml', 'cantidad'=> 1],
            ['categoria' => '02. Desagües', 'unidad' => 'un', 'cantidad'=> 1],
            ['categoria' => '02. Desagües', 'unidad' => 'un', 'cantidad'=> 1],
            ['categoria' => '03. Equipamiento', 'unidad' => 'gl', 'cantidad'=> 1],
            ['categoria' => '03. Equipamiento', 'unidad' => 'un', 'cantidad'=> 1],
        ];
    }

    // ── 05. QUINCHO ───────────────────────────────────────────
    private static function quincho(): array
    {
        return [
            ['categoria' => '01. Estructura', 'unidad' => 'm3', 'cantidad'=> 1],
            ['categoria' => '02. Mampostería', 'unidad' => 'un', 'cantidad'=> 1],
            ['categoria' => '02. Mampostería', 'unidad' => 'un', 'cantidad'=> 1],
            ['categoria' => '02. Mampostería', 'unidad' => 'kg', 'cantidad'=> 1],
            ['categoria' => '03. Techo', 'unidad' => 'ml', 'cantidad'=> 1],
            ['categoria' => '03. Techo', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => '03. Techo', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => '03. Techo', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => '04. Accesorios', 'unidad' => 'un', 'cantidad'=> 1],
            ['categoria' => '04. Accesorios', 'unidad' => 'ml', 'cantidad'=> 1],
        ];
    }

    // ── 10. PINTURA ───────────────────────────────────────────
    private static function pintura(): array
    {
        return [
            ['categoria' => 'Preparación', 'unidad' => 'kg', 'cantidad'=> 1],
            ['categoria' => 'Preparación', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => 'Acabado', 'unidad' => 'm2', 'cantidad'=> 1],
            ['categoria' => 'Exterior', 'unidad' => 'm2', 'cantidad'=> 1],
        ];
    }

    // ── RUBROS SENCILLOS ──────────────────────────────────────
    private static function rubrosSencillos(): array
    {
        return [
            ['categoria' => 'Materiales', 'unidad' => 'un', 'cantidad'=> 1],
            ['categoria' => 'Materiales', 'unidad' => 'm3', 'cantidad'=> 1],
        ];
    }
}