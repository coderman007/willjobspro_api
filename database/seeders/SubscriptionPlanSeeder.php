<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear plan básico
        SubscriptionPlan::create([
            'name' => 'Básico',
            'description' => 'Plan básico con funciones estándar.',
            'price' => 0.00,
            'duration' => 30,
            'features' => json_encode(['Funciones estándar incluidas.']),
        ]);

        // Crear plan premium
        SubscriptionPlan::create([
            'name' => 'Premium',
            'description' => 'Accede a funciones premium por un precio asequible.',
            'price' => 19.99,
            'duration' => 60,
            'features' => json_encode(['Funciones estándar incluidas. + funciones premium.']),
        ]);

        // Crear plan VIP
        SubscriptionPlan::create([
            'name' => 'VIP',
            'description' => 'Experiencia VIP con todas las funciones.',
            'price' => 49.99,
            'duration' => 90,
            'features' => json_encode(['Acceso completo a todas las funciones, soporte prioritario, etc.']),
        ]);
    }
}
