<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Company;
use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuarios de prueba
        User::create([
            'name'     => 'Administrador Kaboom',
            'email'    => 'admin@kaboom.com',
            'phone'    => '99999999',
            'role'     => 'admin',
            'password' => 'Admin12345',
        ]);

        User::create([
            'name'     => 'Cliente de Prueba',
            'email'    => 'cliente@kaboom.com',
            'phone'    => '88888888',
            'role'     => 'customer',
            'password' => 'Cliente12345',
        ]);

        // Empresas (teléfono y correo se llenan cuando el dueño los confirme)
        $empresas = [
            'bomohsa' => Company::create([
                'name'            => 'Bomohsa',
                'slug'            => 'bomohsa',
                'primary_color'   => '#1565C0',
                'secondary_color' => '#FFFFFF',
            ]),
            'agrinsa' => Company::create([
                'name'            => 'Agrinsa',
                'slug'            => 'agrinsa',
                'primary_color'   => '#C62828',
                'secondary_color' => '#FFFFFF',
            ]),
        ];

        // Categorías: clave => [nombre, descripción]
        $categorias = [
            'insumos'      => ['Insumos agrícolas y mantenimiento', 'Mochilas fumigadoras, herbicidas, insecticidas, fertilizantes, lubricantes y repuestos'],
            'bombeo'       => ['Sistemas de bombeo y riego', 'Bombas, motores sumergibles, tanques hidroneumáticos, tuberías y aspersores'],
            'generadores'  => ['Generadores y motores', 'Generadores eléctricos, interruptores de transferencia, controladores y motores'],
            'cafe'         => ['Equipos para beneficio de café', 'Despulpadoras, fermentadoras, módulos ecológicos y molinos de nixtamal'],
            'construccion' => ['Construcción y aire comprimido', 'Concreteras, allanadoras, compactadoras, compresores y herramientas neumáticas'],
            'forraje'      => ['Equipos de forraje y ganadería', 'Picadoras de pasto, ensiladoras, desintegradores, trituradoras y ordeñadoras'],
        ];

        $cat = [];
        foreach ($categorias as $clave => [$nombre, $descripcion]) {
            $cat[$clave] = Category::create([
                'name'        => $nombre,
                'description' => $descripcion,
            ])->id;
        }

        // Productos: [empresa, categoría, nombre, marca (o null), precio, existencias]
        // Los productos 1 a 7 son consumibles de precio bajo: las pruebas de la API usan estos ids.
        $productos = [
            ['bomohsa', 'bombeo',       'Aspersor de riego Ibis',              'Sime',               200.00, 50],
            ['bomohsa', 'insumos',      'Lubricante para motor 1 litro',       null,                 350.00, 30],
            ['bomohsa', 'insumos',      'Herbicida 1 litro',                   null,                 150.00, 80],
            ['bomohsa', 'insumos',      'Fertilizante foliar 1 litro',         null,                 300.00, 40],
            ['bomohsa', 'insumos',      'Insecticida 1 litro',                 null,                 250.00, 0],
            ['bomohsa', 'insumos',      'Bioestimulante 500 ml',               null,                 280.00, 60],
            ['bomohsa', 'insumos',      'Mochila fumigadora 20 litros',        'Brunelli',           550.00, 25],

            // Bomohsa: bombeo y riego
            ['bomohsa', 'bombeo',       'Bomba eléctrica superficial DP100',   'Brunelli',          4850.00, 12],
            ['bomohsa', 'bombeo',       'Bomba de presión constante',          'DAB',              18500.00, 8],
            ['bomohsa', 'bombeo',       'Motobomba diésel',                    null,               22500.00, 6],
            ['bomohsa', 'bombeo',       'Motobomba a gasolina',                null,                9800.00, 10],
            ['bomohsa', 'bombeo',       'Motor sumergible',                    'Tesla',            15600.00, 7],
            ['bomohsa', 'bombeo',       'Tanque hidroneumático',               null,                7200.00, 9],
            ['bomohsa', 'bombeo',       'Tubería de riego (rollo)',            null,                 480.00, 100],

            // Bomohsa: generadores y motores
            ['bomohsa', 'generadores',  'Generador eléctrico diésel',          'AGG Power Solutions', 85000.00, 6],
            ['bomohsa', 'generadores',  'Generador eléctrico a gasolina',      'Brunelli',         24500.00, 8],
            ['bomohsa', 'generadores',  'Interruptor de transferencia automática ATS', 'Aisikai',  12800.00, 7],
            ['bomohsa', 'generadores',  'Controlador SKR2-B',                  null,                3200.00, 10],
            ['bomohsa', 'generadores',  'Motor estacionario diésel',           'Katsu Power',      38900.00, 6],
            ['bomohsa', 'generadores',  'Motor a gasolina GX390',              'Brunelli',         11200.00, 12],

            // Bomohsa: beneficio de café
            ['bomohsa', 'cafe',         'Despulpadora de café',                null,               16500.00, 6],
            ['bomohsa', 'cafe',         'Fermentadora de café',                null,                9400.00, 6],
            ['bomohsa', 'cafe',         'Módulo ecológico para beneficio de café', null,           28000.00, 6],
            ['bomohsa', 'cafe',         'Molino de nixtamal',                  'Brunelli',          6800.00, 10],

            // Bomohsa: construcción y aire comprimido
            ['bomohsa', 'construccion', 'Concretera',                          'Brunelli',         14200.00, 8],
            ['bomohsa', 'construccion', 'Allanadora',                          'Brunelli',         17800.00, 6],
            ['bomohsa', 'construccion', 'Vibrador para concreto',              'Brunelli',          8900.00, 8],
            ['bomohsa', 'construccion', 'Compactadora',                        'Brunelli',         19500.00, 6],
            ['bomohsa', 'construccion', 'Compresor de tornillo',               'Chicago Pneumatic', 96000.00, 6],
            ['bomohsa', 'construccion', 'Compresor de pistón',                 'Chicago Pneumatic', 32500.00, 7],
            ['bomohsa', 'construccion', 'Secador refrigerativo de aire',       'Pneumatech',       21000.00, 6],
            ['bomohsa', 'construccion', 'Herramienta neumática (llave de impacto)', null,           2800.00, 15],

            // Bomohsa: más insumos agrícolas y mantenimiento
            ['bomohsa', 'insumos',      'Desbrozadora',                        null,                7500.00, 10],
            ['bomohsa', 'insumos',      'Motosierra',                          null,                8900.00, 9],

            // Agrinsa: equipos de forraje y ganadería
            ['agrinsa', 'forraje',      'Picadora de pasto PMC-300',           'JM Estrada',       12500.00, 8],
            ['agrinsa', 'forraje',      'Picadora de pasto PN Plus 1000',      'Nogueira',         15800.00, 6],
            ['agrinsa', 'forraje',      'Picadora de pasto PN Plus 2000',      'Nogueira',         19800.00, 6],
            ['agrinsa', 'forraje',      'Picadora de pasto PN Master 3000',    'Nogueira',         26500.00, 6],
            ['agrinsa', 'forraje',      'Ensiladora EN-9F3B',                  'Nogueira',         31500.00, 6],
            ['agrinsa', 'forraje',      'Desintegrador DPM1',                  null,               11800.00, 6],
            ['agrinsa', 'forraje',      'Desintegrador DPM4',                  null,               21800.00, 6],
            ['agrinsa', 'forraje',      'Desintegrador DPMJR',                 null,               14600.00, 6],
            ['agrinsa', 'forraje',      'Trituradora MFW TO2',                 null,               17200.00, 6],
            ['agrinsa', 'forraje',      'Desvaradora',                         null,               24800.00, 6],
            ['agrinsa', 'forraje',      'Ordeñadora',                          'Barbaros',         18900.00, 6],
        ];

        foreach ($productos as [$empresa, $categoria, $nombre, $marca, $precio, $stock]) {
            Product::create([
                'company_id'  => $empresas[$empresa]->id,
                'category_id' => $cat[$categoria],
                'name'        => $nombre,
                'description' => $marca ? "{$nombre}, marca {$marca}." : "{$nombre}.",
                'price'       => $precio,
                'brand'       => $marca,
                'stock'       => $stock,
                'status'      => $stock > 0 ? 'active' : 'sold_out',
            ]);
        }

        // Ofertas de ejemplo (en los productos 1, 9 y 35)
        $ofertas = [
            [1, 10],
            [9, 8],
            [35, 15],
        ];

        foreach ($ofertas as [$productoId, $descuento]) {
            Offer::create([
                'product_id' => $productoId,
                'discount'   => $descuento,
                'start_date' => now()->toDateString(),
                'end_date'   => now()->addDays(30)->toDateString(),
            ]);
        }

        // Banners de ejemplo
        $banners = [
            'Bienvenido a Grupo Kaboom Express',
            'Soluciones de agua y energía con Bomohsa',
            'Equipos de forraje y ganadería con Agrinsa',
        ];

        foreach ($banners as $titulo) {
            Banner::create([
                'title'  => $titulo,
                'link'   => null,
                'status' => 'active',
            ]);
        }
    }
}