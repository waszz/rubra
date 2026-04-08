<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recurso;
use App\Models\ComposicionItem;

class ComposicionesSeeder extends Seeder
{
    public function run(): void
    {
        $composiciones = [
            // --- TUS COMPOSICIONES ANTERIORES ---
            ['nombre' => 'Hormigón Pobre H8 (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.850], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.750], ['nombre' => 'Cemento Portland', 'cantidad' => 0.150], ['nombre' => 'Agua', 'cantidad' => 0.180], ['nombre' => 'Peón', 'cantidad' => 4.0]]],
            ['nombre' => 'Hormigón Estructural H15 (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.700], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.700], ['nombre' => 'Cemento Portland', 'cantidad' => 0.250], ['nombre' => 'Agua', 'cantidad' => 0.170], ['nombre' => 'Oficial Albañil', 'cantidad' => 2.0], ['nombre' => 'Peón', 'cantidad' => 6.0]]],
            ['nombre' => 'Hormigón Estructural H20 (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.700], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.650], ['nombre' => 'Cemento Portland', 'cantidad' => 0.300], ['nombre' => 'Agua', 'cantidad' => 0.160], ['nombre' => 'Oficial Albañil', 'cantidad' => 2.0], ['nombre' => 'Peón', 'cantidad' => 6.0]]],
            ['nombre' => 'Hormigón Estructural H25 (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.700], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.600], ['nombre' => 'Cemento Portland', 'cantidad' => 0.350], ['nombre' => 'Aditivo Plastificante', 'cantidad' => 3.5], ['nombre' => 'Agua', 'cantidad' => 0.150], ['nombre' => 'Oficial Albañil', 'cantidad' => 2.0], ['nombre' => 'Peón', 'cantidad' => 6.0]]],
            ['nombre' => 'Hormigón Estructural H30 (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.700], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.550], ['nombre' => 'Cemento Portland', 'cantidad' => 0.400], ['nombre' => 'Aditivo Plastificante', 'cantidad' => 4.0], ['nombre' => 'Agua', 'cantidad' => 0.140], ['nombre' => 'Oficial Albañil', 'cantidad' => 2.5], ['nombre' => 'Peón', 'cantidad' => 6.5]]],
            ['nombre' => 'Hormigón Ciclópeo (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Piedra Bruta', 'cantidad' => 0.400], ['nombre' => 'Hormigón Pobre H8 (m3)', 'cantidad' => 0.600], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.5], ['nombre' => 'Peón', 'cantidad' => 4.0]]],
            ['nombre' => 'Hormigón Visto (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Hormigón Elaborado H25', 'cantidad' => 0.150], ['nombre' => 'Tabla Pino 1x6"', 'cantidad' => 1.2], ['nombre' => 'Puntal Metálico Telescópico', 'cantidad' => 0.5], ['nombre' => 'Desencofrante', 'cantidad' => 0.1], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.2], ['nombre' => 'Medio Oficial', 'cantidad' => 1.0], ['nombre' => 'Peón', 'cantidad' => 0.5]]],
            ['nombre' => 'Viga de Riostra de Hormigón Armado (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Hormigón Elaborado H20', 'cantidad' => 1.05], ['nombre' => 'Hierro Ø 12mm', 'cantidad' => 60.0], ['nombre' => 'Hierro Ø 6mm', 'cantidad' => 20.0], ['nombre' => 'Alambre de Amarre', 'cantidad' => 0.8], ['nombre' => 'Tabla Pino 1x6"', 'cantidad' => 4.0], ['nombre' => 'Oficial Albañil', 'cantidad' => 5.0], ['nombre' => 'Medio Oficial', 'cantidad' => 3.0], ['nombre' => 'Peón', 'cantidad' => 5.0]]],

            // --- NUEVOS ÍTEMS ---
            ['nombre' => 'Losa Radier de Hormigón (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Hormigón Elaborado H20', 'cantidad' => 0.15], ['nombre' => 'Malla Sima 15x15', 'cantidad' => 1.1], ['nombre' => 'Film Polietileno 200 micrones', 'cantidad' => 1.1], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.8], ['nombre' => 'Peón', 'cantidad' => 1.2]]],
            // REEMPLAZAR la que ya existe por esta:
['nombre' => 'Zapata de Hormigón Armado (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.75], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.65], ['nombre' => 'Cemento Portland', 'cantidad' => 0.35], ['nombre' => 'Hierro Ø 10mm', 'cantidad' => 45], ['nombre' => 'Alambre de Amarre', 'cantidad' => 0.8], ['nombre' => 'Oficial Albañil', 'cantidad' => 5.0], ['nombre' => 'Peón', 'cantidad' => 8.0]]],
            ['nombre' => 'Columna de Hormigón Armado (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Hormigón Elaborado H25', 'cantidad' => 1.05], ['nombre' => 'Hierro Ø 16mm', 'cantidad' => 80.0], ['nombre' => 'Hierro Ø 8mm', 'cantidad' => 25.0], ['nombre' => 'Alambre de Amarre', 'cantidad' => 1.0], ['nombre' => 'Madera Pino (Tablas)', 'cantidad' => 12.0], ['nombre' => 'Clavos 3"', 'cantidad' => 0.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 12.0], ['nombre' => 'Peón', 'cantidad' => 12.0]]],
            ['nombre' => 'Viga de Encadenado (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Hormigón Elaborado H20', 'cantidad' => 1.05], ['nombre' => 'Hierro Ø 12mm', 'cantidad' => 60.0], ['nombre' => 'Hierro Ø 6mm', 'cantidad' => 20.0], ['nombre' => 'Alambre de Amarre', 'cantidad' => 0.8], ['nombre' => 'Madera Pino (Tablas)', 'cantidad' => 10.0], ['nombre' => 'Clavos 3"', 'cantidad' => 0.4], ['nombre' => 'Oficial Albañil', 'cantidad' => 10.0], ['nombre' => 'Peón', 'cantidad' => 10.0]]],
            ['nombre' => 'Losa de Hormigón Armado 12cm (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Hormigón Elaborado H20', 'cantidad' => 0.125], ['nombre' => 'Malla Electrosoldada 15x15', 'cantidad' => 1.1], ['nombre' => 'Madera Pino (Tablas)', 'cantidad' => 3.0], ['nombre' => 'Puntal de Eucaliptus', 'cantidad' => 1.5], ['nombre' => 'Clavos 3"', 'cantidad' => 0.2], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.5], ['nombre' => 'Peón', 'cantidad' => 1.5]]],
            ['nombre' => 'Hormigón 3:2:1 (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.700], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.690], ['nombre' => 'Cemento Portland', 'cantidad' => 0.214], ['nombre' => 'Peón', 'cantidad' => 7.0]]],
            ['nombre' => 'Muro Ladrillo Prensa 0.25 (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Ladrillo de Prensa', 'cantidad' => 120], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.120], ['nombre' => 'Oficial Albañil', 'cantidad' => 2.0], ['nombre' => 'Peón', 'cantidad' => 1.2]]],
            ['nombre' => 'Revoque Exterior Grueso (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Arena Gruesa', 'cantidad' => 0.015], ['nombre' => 'Cemento Portland', 'cantidad' => 0.002], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.6], ['nombre' => 'Peón', 'cantidad' => 0.25]]],
            ['nombre' => 'Elevación Muro Ticholo 12 (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Ticholo 12x25x25', 'cantidad' => 16], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.025], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.8], ['nombre' => 'Peón', 'cantidad' => 0.45]]],
            ['nombre' => 'Colocación de Porcelanato (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Porcelanato 60x60', 'cantidad' => 1.05], ['nombre' => 'Adhesivo Cerámicos', 'cantidad' => 5.0], ['nombre' => 'Pastina para Juntas', 'cantidad' => 0.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.2], ['nombre' => 'Peón', 'cantidad' => 0.6]]],
            ['nombre' => 'Pintura Látex en Paredes (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Pintura Látex Interior', 'cantidad' => 0.3], ['nombre' => 'Enduido Plástico', 'cantidad' => 0.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.4]]],
            ['nombre' => 'Instalación Eléctrica (Boca)', 'unidad' => 'un', 'items' => [['nombre' => 'Corrugado 3/4"', 'cantidad' => 5.0], ['nombre' => 'Cable Unipolar 1.5mm', 'cantidad' => 15.0], ['nombre' => 'Caja Octogonal Chapa', 'cantidad' => 1.0], ['nombre' => 'Oficial Albañil', 'cantidad' => 3.0]]],
            ['nombre' => 'Impermeabilización Azotea (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Membrana Asfáltica 4mm', 'cantidad' => 1.1], ['nombre' => 'Pintura Asfáltica', 'cantidad' => 0.4], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.6], ['nombre' => 'Peón', 'cantidad' => 0.3]]],
            ['nombre' => 'Tabique de Yeso (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Placa Yeso 12.5mm', 'cantidad' => 2.1], ['nombre' => 'Perfil Galv. PGC 100', 'cantidad' => 2.5], ['nombre' => 'Lana de Vidrio 50mm', 'cantidad' => 1.05], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.2], ['nombre' => 'Peón', 'cantidad' => 0.6]]],
            ['nombre' => 'Viga H.A. (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.72], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.62], ['nombre' => 'Cemento Portland', 'cantidad' => 0.36], ['nombre' => 'Hierro Ø 12mm', 'cantidad' => 110], ['nombre' => 'Hierro Ø 6mm', 'cantidad' => 25], ['nombre' => 'Madera Pino (Tablas)', 'cantidad' => 20], ['nombre' => 'Oficial Albañil', 'cantidad' => 14.0], ['nombre' => 'Peón', 'cantidad' => 18.0]]],
            // Agregar al array $composiciones:

['nombre' => 'Muro Ladrillo Campo 0.25 (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Ladrillo de Campo', 'cantidad' => 110], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.115], ['nombre' => 'Oficial Albañil', 'cantidad' => 2.2], ['nombre' => 'Peón', 'cantidad' => 1.4]]],

['nombre' => 'Armado de Losa (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Hierro Ø 8mm', 'cantidad' => 8.5], ['nombre' => 'Alambre de Amarre', 'cantidad' => 0.2], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.8], ['nombre' => 'Peón', 'cantidad' => 0.8]]],

['nombre' => 'Encofrado de Losa (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Madera Pino (Tablas)', 'cantidad' => 5.0], ['nombre' => 'Puntal de Eucaliptus', 'cantidad' => 0.8], ['nombre' => 'Clavos 3"', 'cantidad' => 0.15], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.0], ['nombre' => 'Peón', 'cantidad' => 1.0]]],

['nombre' => 'Elevación Muro HCCA 15 (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Ladrillo HCCA 15cm', 'cantidad' => 8], ['nombre' => 'Adhesivo HCCA', 'cantidad' => 4.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.5], ['nombre' => 'Peón', 'cantidad' => 0.2]]],

['nombre' => 'Contrapiso de Hormigón (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.08], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.04], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.1], ['nombre' => 'Peón', 'cantidad' => 0.3]]],

['nombre' => 'Carpeta de Nivelación (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Arena Fina', 'cantidad' => 0.02], ['nombre' => 'Cemento Portland', 'cantidad' => 0.005], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.4], ['nombre' => 'Peón', 'cantidad' => 0.2]]],

['nombre' => 'Cielorraso de Yeso (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Placa Yeso 12.5mm', 'cantidad' => 1.05], ['nombre' => 'Perfil Galv. PGU 100', 'cantidad' => 1.2], ['nombre' => 'Tornillo Autoperforante 1"', 'cantidad' => 15], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.8], ['nombre' => 'Peón', 'cantidad' => 0.4]]],

['nombre' => 'Excavación de Zanjas (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Peón', 'cantidad' => 4.5]]],

['nombre' => 'Azotado Hidrófugo (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Arena Fina', 'cantidad' => 0.01], ['nombre' => 'Cemento Portland', 'cantidad' => 0.003], ['nombre' => 'Hidrófugo Líquido', 'cantidad' => 0.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.3], ['nombre' => 'Peón', 'cantidad' => 0.15]]],

['nombre' => 'Muro Bloque 15cm (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Bloque Hormigón 15cm', 'cantidad' => 12.5], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.03], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.9], ['nombre' => 'Peón', 'cantidad' => 0.5]]],

['nombre' => 'Encadenado Superior (m)', 'unidad' => 'm', 'items' => [['nombre' => 'Hierro Ø 8mm', 'cantidad' => 2.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.6], ['nombre' => 'Peón', 'cantidad' => 0.6]]],

['nombre' => 'Viga de Fundación (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Hierro Ø 10mm', 'cantidad' => 65], ['nombre' => 'Oficial Albañil', 'cantidad' => 4.0], ['nombre' => 'Peón', 'cantidad' => 4.0]]],

['nombre' => 'Pilar H.A. 20x20 (m)', 'unidad' => 'm', 'items' => [['nombre' => 'Hierro Ø 12mm', 'cantidad' => 4.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.5], ['nombre' => 'Peón', 'cantidad' => 1.5]]],

['nombre' => 'Muro Ladrillo Prensa 0.15 (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Ladrillo de Prensa', 'cantidad' => 60], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.05], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.4], ['nombre' => 'Peón', 'cantidad' => 0.8]]],

['nombre' => 'Revoque Fino Interior (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Arena Fina', 'cantidad' => 0.005], ['nombre' => 'Cal Aérea en Polvo', 'cantidad' => 2.2], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.4], ['nombre' => 'Peón', 'cantidad' => 0.1]]],

['nombre' => 'Capa Aisladora Horizontal (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Arena Fina', 'cantidad' => 0.015], ['nombre' => 'Cemento Portland', 'cantidad' => 0.005], ['nombre' => 'Hidrófugo Líquido', 'cantidad' => 1.0], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.35], ['nombre' => 'Peón', 'cantidad' => 0.2]]],

['nombre' => 'Piso Cerámico 30x30 (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Cerámica 30x30', 'cantidad' => 1.05], ['nombre' => 'Adhesivo Cerámicos', 'cantidad' => 4.5], ['nombre' => 'Pastina para Juntas', 'cantidad' => 0.4], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.0], ['nombre' => 'Peón', 'cantidad' => 0.5]]],

['nombre' => 'Zócalo Cerámico (m)', 'unidad' => 'm', 'items' => [['nombre' => 'Cerámica 30x30', 'cantidad' => 0.1], ['nombre' => 'Adhesivo Cerámicos', 'cantidad' => 0.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.3]]],

['nombre' => 'Revestimiento Azulejo (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Cerámica 30x30', 'cantidad' => 1.05], ['nombre' => 'Adhesivo Cerámicos', 'cantidad' => 5.0], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.4], ['nombre' => 'Peón', 'cantidad' => 0.7]]],

['nombre' => 'Cubierta Teja Colonial (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Teja Colonial', 'cantidad' => 26], ['nombre' => 'Madera Pino (Tirantes)', 'cantidad' => 2.5], ['nombre' => 'Fieltro Asfáltico', 'cantidad' => 1.1], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.5], ['nombre' => 'Peón', 'cantidad' => 1.5]]],

['nombre' => 'Cubierta Chapa Galv. (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Chapa Galvanizada C25', 'cantidad' => 1.1], ['nombre' => 'Tirante Hierro C 100x50', 'cantidad' => 1.2], ['nombre' => 'Lana de Vidrio 50mm', 'cantidad' => 1.05], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.0], ['nombre' => 'Peón', 'cantidad' => 1.0]]],

['nombre' => 'Desmonte de Tierra (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Peón', 'cantidad' => 2.5]]],

['nombre' => 'Relleno y Compactación (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Balasto', 'cantidad' => 1.3], ['nombre' => 'Peón', 'cantidad' => 3.0]]],

['nombre' => 'Muro Bloque 20cm (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Bloque Hormigón 20cm', 'cantidad' => 12.5], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.04], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.0], ['nombre' => 'Peón', 'cantidad' => 0.6]]],

['nombre' => 'Muro Ticholo 17 (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Ticholo 17x25x25', 'cantidad' => 16], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.035], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.9], ['nombre' => 'Peón', 'cantidad' => 0.5]]],

['nombre' => 'Muro Ladrillo Prensa 0.30 (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Ladrillo de Prensa', 'cantidad' => 140], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.14], ['nombre' => 'Oficial Albañil', 'cantidad' => 2.4], ['nombre' => 'Peón', 'cantidad' => 1.5]]],

['nombre' => 'Revoque Interior Grueso (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Arena Gruesa', 'cantidad' => 0.015], ['nombre' => 'Cal Hidráulica', 'cantidad' => 2.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.5], ['nombre' => 'Peón', 'cantidad' => 0.2]]],

['nombre' => 'Azotado con Portland (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Arena Fina', 'cantidad' => 0.01], ['nombre' => 'Cemento Portland', 'cantidad' => 0.004], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.3]]],

['nombre' => 'Contrapiso sobre Terreno (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.06], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.03], ['nombre' => 'Peón', 'cantidad' => 0.4]]],

['nombre' => 'Carpeta bajo Piso Madera (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Arena Fina', 'cantidad' => 0.02], ['nombre' => 'Cemento Portland', 'cantidad' => 0.006], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.5]]],

['nombre' => 'Colocación Zócalo Madera (m)', 'unidad' => 'm', 'items' => [['nombre' => 'Listón Pino 1x2"', 'cantidad' => 1.05], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.25]]],

['nombre' => 'Pintura de Aberturas (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Barniz Marino', 'cantidad' => 0.25], ['nombre' => 'Aguarrás Mineral', 'cantidad' => 0.1], ['nombre' => 'Lija para Madera', 'cantidad' => 0.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.8]]],

['nombre' => 'Instalación Sanitaria (Boca)', 'unidad' => 'un', 'items' => [['nombre' => 'Caño PVC 40mm', 'cantidad' => 2.0], ['nombre' => 'Oficial Albañil', 'cantidad' => 4.0], ['nombre' => 'Peón', 'cantidad' => 2.0]]],

['nombre' => 'Muro HCCA 10cm (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Ladrillo HCCA 10cm', 'cantidad' => 8], ['nombre' => 'Adhesivo HCCA', 'cantidad' => 3.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.45], ['nombre' => 'Peón', 'cantidad' => 0.2]]],

['nombre' => 'Muro HCCA 20cm (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Ladrillo HCCA 20cm', 'cantidad' => 8], ['nombre' => 'Adhesivo HCCA', 'cantidad' => 5.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.6], ['nombre' => 'Peón', 'cantidad' => 0.3]]],

['nombre' => 'Viga de Encadenado HCCA (m)', 'unidad' => 'm', 'items' => [['nombre' => 'Hierro Ø 8mm', 'cantidad' => 2.0], ['nombre' => 'Adhesivo HCCA', 'cantidad' => 1.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.5]]],

['nombre' => 'Revestimiento Granito (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Granito Gris Mara', 'cantidad' => 1.05], ['nombre' => 'Adhesivo Cerámicos', 'cantidad' => 6.0], ['nombre' => 'Oficial Albañil', 'cantidad' => 2.5], ['nombre' => 'Peón', 'cantidad' => 1.0]]],

['nombre' => 'Colocación de Umbral (m)', 'unidad' => 'm', 'items' => [['nombre' => 'Umbral de Mármol', 'cantidad' => 1.0], ['nombre' => 'Arena Fina', 'cantidad' => 0.005], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.8]]],

['nombre' => 'Cielorraso de Mezcla (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Arena Fina', 'cantidad' => 0.01], ['nombre' => 'Cal Aérea en Polvo', 'cantidad' => 3.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.9], ['nombre' => 'Peón', 'cantidad' => 0.4]]],

['nombre' => 'Muro Ladrillo Prensa 0.20 (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Ladrillo de Prensa', 'cantidad' => 90], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.09], ['nombre' => 'Oficial Albañil', 'cantidad' => 1.8], ['nombre' => 'Peón', 'cantidad' => 1.0]]],

['nombre' => 'Carpeta bajo Porcelanato (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Arena Fina', 'cantidad' => 0.025], ['nombre' => 'Cemento Portland', 'cantidad' => 0.008], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.45]]],

['nombre' => 'Losa Maciza H.A. (m3)', 'unidad' => 'm3', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.70], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.60], ['nombre' => 'Cemento Portland', 'cantidad' => 0.38], ['nombre' => 'Hierro Ø 8mm', 'cantidad' => 85], ['nombre' => 'Madera Pino (Tablas)', 'cantidad' => 15], ['nombre' => 'Clavos 3"', 'cantidad' => 0.5], ['nombre' => 'Oficial Albañil', 'cantidad' => 12.0], ['nombre' => 'Peón', 'cantidad' => 15.0]]],

['nombre' => 'Platea de Fundación (m2)', 'unidad' => 'm2', 'items' => [['nombre' => 'Pedregullo 1-2', 'cantidad' => 0.12], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.08], ['nombre' => 'Cemento Portland', 'cantidad' => 0.04], ['nombre' => 'Malla Electrosoldada 15x15', 'cantidad' => 1.1], ['nombre' => 'Membrana Asfáltica 4mm', 'cantidad' => 1.1], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.8], ['nombre' => 'Peón', 'cantidad' => 1.5]]],

['nombre' => 'Encadenado de Fundación (m)', 'unidad' => 'm', 'items' => [['nombre' => 'Hierro Ø 10mm', 'cantidad' => 3.5], ['nombre' => 'Hierro Ø 6mm', 'cantidad' => 1.2], ['nombre' => 'Arena Gruesa', 'cantidad' => 0.02], ['nombre' => 'Cemento Portland', 'cantidad' => 0.008], ['nombre' => 'Oficial Albañil', 'cantidad' => 0.7], ['nombre' => 'Peón', 'cantidad' => 0.7]]],
        ];
  foreach ($composiciones as $comp) {

            // 🔹 Crear o buscar composición
            $recurso = Recurso::firstOrCreate(
                ['nombre' => $comp['nombre']],
                [
                    'tipo' => 'composition',
                    'unidad' => $comp['unidad'],
                    'precio_usd' => 0
                ]
            );

            // 🔹 Crear items
           foreach ($comp['items'] as $item) {

    $tipoDeducido = 'material';
    if (
        str_contains($item['nombre'], 'Oficial') ||
        str_contains($item['nombre'], 'Peón') ||
        str_contains($item['nombre'], 'Medio Oficial')
    ) {
        $tipoDeducido = 'mano_obra';
    }

    // Crear recurso base si no existe
    $recursoBase = Recurso::firstOrCreate(
        ['nombre' => $item['nombre']],
        [
            'tipo'      => $tipoDeducido,
            'unidad'    => 'u',
            'precio_usd'=> 0,
        ]
    );

    // Relación composición-item CON recurso_id
    ComposicionItem::firstOrCreate(
        [
            'composicion_id' => $recurso->id,
            'nombre'         => $item['nombre'],
        ],
        [
            'cantidad'   => $item['cantidad'],
            'recurso_id' => $recursoBase->id, // ← esto faltaba
        ]
    );
}

            //  CALCULAR COSTO TOTAL DE LA COMPOSICIÓN
            $total = 0;

            $items = ComposicionItem::where('composicion_id', $recurso->id)->get();

            foreach ($items as $it) {
                $recursoBase = Recurso::where('nombre', $it->nombre)->first();

                if ($recursoBase) {
                    $total += $it->cantidad * $recursoBase->precio_usd;
                }
            }

            //  ACTUALIZAR PRECIO FINAL
            $recurso->update([
                'precio_usd' => $total
            ]);
        }
    }
}
    
