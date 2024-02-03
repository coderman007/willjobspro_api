<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SkillCategory;

class SkillCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Gestión Empresarial', 'description' => 'Habilidades relacionadas con la gestión empresarial.'],
            ['name' => 'Recursos Humanos', 'description' => 'Habilidades relacionadas con la gestión de recursos humanos.'],
            ['name' => 'Ventas y Marketing', 'description' => 'Habilidades relacionadas con ventas y marketing.'],
            ['name' => 'Contabilidad y Finanzas', 'description' => 'Habilidades relacionadas con contabilidad y finanzas.'],
            ['name' => 'Servicio al Cliente', 'description' => 'Habilidades relacionadas con el servicio al cliente.'],
            ['name' => 'Logística y Cadena de Suministro', 'description' => 'Habilidades relacionadas con logística y cadena de suministro.'],
            ['name' => 'Educación y Formación', 'description' => 'Habilidades relacionadas con educación y formación.'],
            ['name' => 'Salud y Cuidado Personal', 'description' => 'Habilidades relacionadas con salud y cuidado personal.'],
            ['name' => 'Diseño de Moda', 'description' => 'Habilidades relacionadas con diseño de moda.'],
            ['name' => 'Arquitectura y Construcción', 'description' => 'Habilidades relacionadas con arquitectura y construcción.'],
            ['name' => 'Medio Ambiente y Sostenibilidad', 'description' => 'Habilidades relacionadas con medio ambiente y sostenibilidad.'],
            ['name' => 'Investigación y Desarrollo', 'description' => 'Habilidades relacionadas con investigación y desarrollo.'],
            ['name' => 'Ciencia de Datos', 'description' => 'Habilidades relacionadas con ciencia de datos.'],
            ['name' => 'Agricultura y Agroindustria', 'description' => 'Habilidades relacionadas con agricultura y agroindustria.'],
            ['name' => 'Periodismo y Comunicación', 'description' => 'Habilidades relacionadas con periodismo y comunicación.'],
            ['name' => 'Turismo y Hostelería', 'description' => 'Habilidades relacionadas con turismo y hostelería.'],
            ['name' => 'Arte y Entretenimiento', 'description' => 'Habilidades relacionadas con arte y entretenimiento.'],
            ['name' => 'Servicios Legales', 'description' => 'Habilidades relacionadas con servicios legales.'],
            ['name' => 'Traducción e Idiomas', 'description' => 'Habilidades relacionadas con traducción e idiomas.'],
            ['name' => 'Psicología y Bienestar', 'description' => 'Habilidades relacionadas con psicología y bienestar.'],
            ['name' => 'Investigación de Mercados', 'description' => 'Habilidades relacionadas con investigación de mercados.'],
            ['name' => 'Publicidad y Relaciones Públicas', 'description' => 'Habilidades relacionadas con publicidad y relaciones públicas.'],
            ['name' => 'Ingeniería Civil', 'description' => 'Habilidades relacionadas con ingeniería civil.'],
            ['name' => 'Energías Renovables', 'description' => 'Habilidades relacionadas con energías renovables.'],
            ['name' => 'Recreación y Deportes', 'description' => 'Habilidades relacionadas con recreación y deportes.'],
            ['name' => 'Asesoría Financiera', 'description' => 'Habilidades relacionadas con asesoría financiera.'],
            ['name' => 'Política y Asuntos Públicos', 'description' => 'Habilidades relacionadas con política y asuntos públicos.'],
            ['name' => 'Desarrollo Comunitario', 'description' => 'Habilidades relacionadas con desarrollo comunitario.'],
            ['name' => 'Diseño Industrial', 'description' => 'Habilidades relacionadas con diseño industrial.'],
            ['name' => 'Consultoría Empresarial', 'description' => 'Habilidades relacionadas con consultoría empresarial.'],
        ];

        foreach ($categories as $category) {
            SkillCategory::create($category);
        }
    }
}
