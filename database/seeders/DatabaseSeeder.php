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
        $bomosa = Company::create([
            'name'            => 'Bomosa',
            'slug'            => 'bomosa',
            'primary_color'   => '#1565C0',
            'secondary_color' => '#FFFFFF',
        ]);

        $agrinsa = Company::create([
            'name'            => 'Agrinsa',
            'slug'            => 'agrinsa',
            'primary_color'   => '#C62828',
            'secondary_color' => '#FFFFFF',
        ]);

        $empresas = [$bomosa, $agrinsa];
        $contador = 0;

        // Categorías con sus productos: [nombre, marca, precio, existencias]
        $catalogo = [
            'Bebidas' => [
                'description' => 'Refrescos, jugos y agua',
                'products' => [
                    ['Refresco de cola 600 ml', 'Cola Max', 20.00, 50],
                    ['Jugo de naranja 1 L', 'Citrus', 35.00, 30],
                    ['Agua purificada 1 L', 'Aqua', 15.00, 80],
                ],
            ],
            'Snacks' => [
                'description' => 'Papas, galletas y dulces',
                'products' => [
                    ['Papas fritas 150 g', 'Crunchy', 30.00, 40],
                    ['Galletas de chocolate', 'Dulce Vida', 25.00, 0],
                ],
            ],
            'Abarrotes' => [
                'description' => 'Productos básicos para el hogar',
                'products' => [
                    ['Arroz 1 kg', 'Granos del Sol', 28.00, 60],
                    ['Aceite vegetal 900 ml', 'Dorado', 55.00, 25],
                ],
            ],
        ];

        foreach ($catalogo as $nombre => $datos) {
            $categoria = Category::create([
                'name'        => $nombre,
                'description' => $datos['description'],
            ]);

            foreach ($datos['products'] as [$producto, $marca, $precio, $stock]) {
                // Reparte los productos de ejemplo entre las dos empresas
                $empresa = $empresas[$contador % 2];
                $contador++;

                Product::create([
                    'company_id'  => $empresa->id,
                    'name'        => $producto,
                    'description' => $producto . ' de la marca ' . $marca,
                    'price'       => $precio,
                    'brand'       => $marca,
                    'stock'       => $stock,
                    'category_id' => $categoria->id,
                    'status'      => $stock > 0 ? 'active' : 'sold_out',
                ]);
            }
        }

        // Una oferta de ejemplo en el primer producto
        Offer::create([
            'product_id' => 1,
            'discount'   => 10,
            'start_date' => now()->toDateString(),
            'end_date'   => now()->addDays(30)->toDateString(),
        ]);

        // Un banner de ejemplo
        Banner::create([
            'title'  => 'Bienvenido a Grupo Kaboom Express',
            'link'   => null,
            'status' => 'active',
        ]);
    }
}