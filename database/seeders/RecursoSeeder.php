<?php

namespace Database\Seeders;

use App\Models\Recurso;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class RecursoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactivar restricciones de llaves foráneas para poder usar truncate
        Schema::disableForeignKeyConstraints();
        DB::table('recursos')->truncate();
        Schema::enableForeignKeyConstraints();

        $recursos = [
            // --- MATERIALES ---
            ['nombre' => 'Adhesivo Cerámicos', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 0.45],
            ['nombre' => 'Adhesivo HCCA', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 0.75],
            ['nombre' => 'Aditivo Acelerante', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 5.2],
            ['nombre' => 'Aditivo Hidrófugo', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 3.5],
            ['nombre' => 'Aditivo Incorporador de Aire', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 5.5],
            ['nombre' => 'Aditivo Plastificante', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 4.5],
            ['nombre' => 'Aditivo Retardador', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 4.8],
            ['nombre' => 'Agua', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 1.5],
            ['nombre' => 'Aguarrás Mineral', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 3.5],
            ['nombre' => 'Alambre de Amarre', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 2.5],
            ['nombre' => 'Antiparras', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 4.5],
            ['nombre' => 'Arena Fina', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 28],
            ['nombre' => 'Arena Gruesa', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 25],
            ['nombre' => 'Balasto', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 15],
            ['nombre' => 'Barniz Marino', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 8.5],
            ['nombre' => 'Bisagra Munición', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 3.5],
            ['nombre' => 'Bloque Hormigón 12cm', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 0.85],
            ['nombre' => 'Bloque Hormigón 15cm', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 1.05],
            ['nombre' => 'Bloque Hormigón 20cm', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 1.25],
            ['nombre' => 'Botas de Seguridad', 'tipo' => 'material', 'unidad' => 'par', 'precio_usd' => 25],
            ['nombre' => 'Cable Unipolar 1.5mm', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 0.55],
            ['nombre' => 'Cable Unipolar 2.5mm', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 0.85],
            ['nombre' => 'Caja Octogonal Chapa', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 1.2],
            ['nombre' => 'Caja Rectangular 5x10', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 1.1],
            ['nombre' => 'Cal Aérea en Polvo', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 0.15],
            ['nombre' => 'Cal Hidráulica', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 0.18],
            ['nombre' => 'Cal en Pasta', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 120],
            ['nombre' => 'Canto Rodado', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 45],
            ['nombre' => 'Casco de Seguridad', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 8.5],
            ['nombre' => 'Caño PVC 110mm', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 8.5],
            ['nombre' => 'Caño PVC 40mm', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 3.2],
            ['nombre' => 'Caño Termofusión 20mm', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 2.5],
            ['nombre' => 'Caño Termofusión 25mm', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 3.8],
            ['nombre' => 'Cemento Blanco', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 0.85],
            ['nombre' => 'Cemento Portland', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 280],
            ['nombre' => 'Cerradura Seguridad', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 25],
            ['nombre' => 'Cerámica 30x30', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 12.5],
            ['nombre' => 'Chapa Galvanizada C25', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 12.5],
            ['nombre' => 'Clavos 2"', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 2.2],
            ['nombre' => 'Clavos 3"', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 2.2],
            ['nombre' => 'Clavos 4"', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 2.2],
            ['nombre' => 'Codo PVC 110mm', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 4.5],
            ['nombre' => 'Corrugado 3/4"', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 0.65],
            ['nombre' => 'Curador Químico', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 2.8],
            ['nombre' => 'Desencofrante', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 3.2],
            ['nombre' => 'Disyuntor Diferencial', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 45],
            ['nombre' => 'Enduido Plástico', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 1.2],
            ['nombre' => 'Fenólico 18mm', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 18],
            ['nombre' => 'Fibra de Acero', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 4.2],
            ['nombre' => 'Fibra de Polipropileno', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 8.5],
            ['nombre' => 'Fieltro Asfáltico', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 1.2],
            ['nombre' => 'Fijador Sellador', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 3.5],
            ['nombre' => 'Film Polietileno 200 micrones', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 0.8],
            ['nombre' => 'Granito Gris Mara', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 85],
            ['nombre' => 'Granito Negro Absoluto', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 150],
            ['nombre' => 'Grava Clasificada', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 38],
            ['nombre' => 'Gravilla', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 32],
            ['nombre' => 'Guantes de Trabajo', 'tipo' => 'material', 'unidad' => 'par', 'precio_usd' => 2.2],
            ['nombre' => 'Hidrófugo Líquido', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 2.5],
            ['nombre' => 'Hierro Ø 6mm', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 1.1],
            ['nombre' => 'Hierro Ø 8mm', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 1.05],
            ['nombre' => 'Hierro Ø 10mm', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 1.05],
            ['nombre' => 'Hierro Ø 12mm', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 1.02],
            ['nombre' => 'Hierro Ø 16mm', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 1.02],
            ['nombre' => 'Hierro Ø 20mm', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 1.02],
            ['nombre' => 'Hierro Ø 25mm', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 1.02],
            ['nombre' => 'Hormigón Elaborado H8', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 85],
            ['nombre' => 'Hormigón Elaborado H15', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 95],
            ['nombre' => 'Hormigón Elaborado H20', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 110],
            ['nombre' => 'Hormigón Elaborado H25', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 125],
            ['nombre' => 'Hormigón Elaborado H30', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 140],
            ['nombre' => 'Hormigón Elaborado H35', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 155],
            ['nombre' => 'Hormigón Elaborado H40', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 170],
            ['nombre' => 'Hormigón Proyectado (Shotcrete)', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 190],
            ['nombre' => 'Ladrillo HCCA 10cm', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 3.5],
            ['nombre' => 'Ladrillo HCCA 12.5cm', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 4.2],
            ['nombre' => 'Ladrillo HCCA 15cm', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 4.8],
            ['nombre' => 'Ladrillo HCCA 20cm', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 6.5],
            ['nombre' => 'Ladrillo Refractario', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 1.8],
            ['nombre' => 'Ladrillo de Campo', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 0.45],
            ['nombre' => 'Ladrillo de Prensa', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 0.65],
            ['nombre' => 'Lana de Vidrio 50mm', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 4.5],
            ['nombre' => 'Lija para Madera', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 0.8],
            ['nombre' => 'Lija para Pared', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 0.6],
            ['nombre' => 'Listón Pino 1x2"', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 0.85],
            ['nombre' => 'Llave Térmica 16A', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 12],
            ['nombre' => 'Llave de Luz (Punto)', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 4.2],
            ['nombre' => 'Llave de Paso 20mm', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 12],
            ['nombre' => 'Madera Pino (Tablas)', 'tipo' => 'material', 'unidad' => 'p2', 'precio_usd' => 1.5],
            ['nombre' => 'Madera Pino (Tirantes)', 'tipo' => 'material', 'unidad' => 'p2', 'precio_usd' => 1.8],
            ['nombre' => 'Malla Electrosoldada 10x10', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 8.2],
            ['nombre' => 'Malla Electrosoldada 15x15', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 6.5],
            ['nombre' => 'Malla Sima 15x15', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 3.5],
            ['nombre' => 'Membrana Asfáltica 4mm', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 8.5],
            ['nombre' => 'Mesada Granito Cocina', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 140],
            ['nombre' => 'Mortero Premezclado', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 0.35],
            ['nombre' => 'Mármol Blanco Carrara', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 180],
            ['nombre' => 'Mármol Travertino', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 110],
            ['nombre' => 'Pastina para Juntas', 'tipo' => 'material', 'unidad' => 'kg', 'precio_usd' => 1.8],
            ['nombre' => 'Pedregullo 1-2', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 35],
            ['nombre' => 'Perfil Galv. PGC 100', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 5.5],
            ['nombre' => 'Perfil Galv. PGU 100', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 4.8],
            ['nombre' => 'Piedra Bruta', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 22],
            ['nombre' => 'Pintura Asfáltica', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 4.5],
            ['nombre' => 'Pintura Látex Exterior', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 6.5],
            ['nombre' => 'Pintura Látex Interior', 'tipo' => 'material', 'unidad' => 'l', 'precio_usd' => 4.8],
            ['nombre' => 'Polvo de Piedra', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 18],
            ['nombre' => 'Porcelanato 60x60', 'tipo' => 'material', 'unidad' => 'm2', 'precio_usd' => 28],
            ['nombre' => 'Puerta Placa Pino', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 120],
            ['nombre' => 'Placa Yeso 12.5mm', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 12],
            ['nombre' => 'Puntal Metálico Telescópico', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 15],
            ['nombre' => 'Puntal de Eucaliptus', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 4.5],
            ['nombre' => 'Silicona Transparente', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 6.5],
            ['nombre' => 'Tabla Pino 1x6"', 'tipo' => 'material', 'unidad' => 'ml', 'precio_usd' => 1.2],
            ['nombre' => 'Tablero Eléctrico 12 bocas', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 25],
            ['nombre' => 'Tee PVC 110mm', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 6.8],
            ['nombre' => 'Teja Colonial', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 0.85],
            ['nombre' => 'Ticholo 8x25x25', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 0.55],
            ['nombre' => 'Ticholo 12x25x25', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 0.75],
            ['nombre' => 'Ticholo 17x25x25', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 0.95],
            ['nombre' => 'Tierra Negra', 'tipo' => 'material', 'unidad' => 'm3', 'precio_usd' => 20],
            ['nombre' => 'Tirante Hierro C 100x50', 'tipo' => 'material', 'unidad' => 'm', 'precio_usd' => 8.5],
            ['nombre' => 'Tirante Pino 2x4"', 'tipo' => 'material', 'unidad' => 'ml', 'precio_usd' => 2.5],
            ['nombre' => 'Tomacorriente Doble', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 5.5],

            // --- EQUIPOS (EQUIPMENT) ---
            ['nombre' => 'Alisadora de Hormigón (Helicóptero)', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 35],
            ['nombre' => 'Amoladora Angular 4.5"', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 8],
            ['nombre' => 'Amoladora Angular 9"', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 12],
            ['nombre' => 'Andamio Tubular (Cuerpo)', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 5],
            ['nombre' => 'Aparejo Manual 2T', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 10],
            ['nombre' => 'Apisonador a Pison (Canguro)', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 25],
            ['nombre' => 'Atornillador a Batería', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 10],
            ['nombre' => 'Bomba Motobomba Naftera', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 22],
            ['nombre' => 'Bomba de Achique Sumergible', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 15],
            ['nombre' => 'Bomba de Hormigón Estacionaria', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 65],
            ['nombre' => 'Bomba de Hormigón con Pluma', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 90],
            ['nombre' => 'Camión Cisterna (Agua)', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 28],
            ['nombre' => 'Camión Grúa', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 45],
            ['nombre' => 'Camión Mixer (Hormigonero)', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 55],
            ['nombre' => 'Camión Volcador 8m3', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 30],
            ['nombre' => 'Camión Volcador 12m3', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 40],
            ['nombre' => 'Compresor de Aire 50L', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 15],
            ['nombre' => 'Compresor de Aire 100L', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 25],
            ['nombre' => 'Estación Total (Topografía)', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 45],
            ['nombre' => 'Excavadora sobre Orugas', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 45],
            ['nombre' => 'Generador Eléctrico 5kVA', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 25],
            ['nombre' => 'Generador Eléctrico 20kVA', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 55],
            ['nombre' => 'Grúa Móvil Telescópica', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 75],
            ['nombre' => 'Grúa Torre', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 60],
            ['nombre' => 'Hormigonera de Volteo 130L', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 15],
            ['nombre' => 'Hormigonera de Volteo 300L', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 25],
            ['nombre' => 'Manipulador Telescópico', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 38],
            ['nombre' => 'Martillo Demoledor 10kg', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 20],
            ['nombre' => 'Martillo Demoledor 30kg', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 35],
            ['nombre' => 'Minicargador (Bobcat)', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 25],
            ['nombre' => 'Montacargas de Obra', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 45],
            ['nombre' => 'Motoniveladora', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 55],
            ['nombre' => 'Nivel Láser', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 15],
            ['nombre' => 'Pala Cargadora Frontal', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 50],
            ['nombre' => 'Pistola Clavadora Neumática', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 12],
            ['nombre' => 'Placa Compactadora Reversible', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 30],
            ['nombre' => 'Plataforma Elevadora Articulada', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 85],
            ['nombre' => 'Plataforma Elevadora Tipo Tijera', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 65],
            ['nombre' => 'Regla Vibratoria', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 22],
            ['nombre' => 'Retroexcavadora', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 35],
            ['nombre' => 'Rodillo Compactador Liso', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 40],
            ['nombre' => 'Rodillo Compactador Pata de Cabra', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 42],
            ['nombre' => 'Rotomartillo SDS Plus', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 15],
            ['nombre' => 'Rotomartillo SDS Max', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 25],
            ['nombre' => 'Sierra Circular', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 10],
            ['nombre' => 'Sierra Ingletadora', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 18],
            ['nombre' => 'Soldadora Inverter', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 18],
            ['nombre' => 'Taladro Percutor', 'tipo' => 'equipment', 'unidad' => 'd', 'precio_usd' => 8],
            ['nombre' => 'Topadora (Bulldozer)', 'tipo' => 'equipment', 'unidad' => 'h', 'precio_usd' => 60],
            ['nombre' => 'Tornillo Autoperforante 1"', 'tipo' => 'material', 'unidad' => 'un', 'precio_usd' => 0.05],
            ['nombre' => 'Umbral de Mármol',           'tipo' => 'material', 'unidad' => 'm',  'precio_usd' => 25],

            // --- MANO DE OBRA (LABOR) ---
  
            ['nombre' => 'Peón',           'tipo' => 'labor', 'unidad' => 'h', 'precio_usd' => 10.2,  'social_charges_percentage' => 72],
            ['nombre' => 'Medio Oficial',  'tipo' => 'labor', 'unidad' => 'h', 'precio_usd' => 12.8,  'social_charges_percentage' => 72],
            ['nombre' => 'Oficial Albañil','tipo' => 'labor', 'unidad' => 'h', 'precio_usd' => 15.5,  'social_charges_percentage' => 72],

           
        ];

        // Insertar en bloques de 100 para optimizar el rendimiento
       $sinLabor = array_filter($recursos, fn($r) => $r['tipo'] !== 'labor');
foreach (array_chunk(array_values($sinLabor), 100) as $chunk) {
    Recurso::insert($chunk);
}

// Insertar mano de obra por separado (tiene columna extra)
$labor = array_filter($recursos, fn($r) => $r['tipo'] === 'labor');
foreach ($labor as $item) {
    Recurso::create($item);
}
    }
}

