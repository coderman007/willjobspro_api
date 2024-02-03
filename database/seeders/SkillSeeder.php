<?php

namespace Database\Seeders;

use App\Models\SkillCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtén las categorías para asociar habilidades a cada una
        $categories = SkillCategory::all();

        foreach ($categories as $category) {
            $skills = $this->getSkillsByCategory($category->name);

            foreach ($skills as $skill) {
                $category->skills()->create(['name' => $skill, 'description' => "Habilidad en $skill"]);
            }
        }
    }

    private function getSkillsByCategory($categoryName): array
    {
        // Define las habilidades para cada categoría
        $skillsByCategory = [
            'Gestión Empresarial' => [
                'Planificación Estratégica',
                'Toma de Decisiones',
                'Gestión de Proyectos',
                'Liderazgo',
                'Análisis Financiero',
            ],
            'Recursos Humanos' => [
                'Selección de Personal',
                'Desarrollo Organizacional',
                'Gestión del Talento',
                'Relaciones Laborales',
                'Capacitación y Desarrollo',
            ],
            'Ventas y Marketing' => [
                'Estrategias de Ventas',
                'Marketing Digital',
                'Desarrollo de Clientes',
                'Investigación de Mercado',
                'Comunicación Efectiva',
            ],
            'Contabilidad y Finanzas' => [
                'Contabilidad Fiscal',
                'Análisis Financiero',
                'Presupuestos',
                'Auditoría',
                'Gestión de Riesgos',
            ],
            'Servicio al Cliente' => [
                'Atención al Cliente',
                'Resolución de Problemas',
                'Comunicación Empática',
                'Gestión de Quejas',
                'Orientación al Cliente',
            ],
            'Logística y Cadena de Suministro' => [
                'Gestión de Inventarios',
                'Optimización de Rutas',
                'Coordinación Logística',
                'Control de Calidad',
                'Planificación de la Cadena de Suministro',
            ],
            'Educación y Formación' => [
                'Diseño de Cursos',
                'Facilitación de Aprendizaje',
                'Evaluación Educativa',
                'Desarrollo Curricular',
                'Tecnologías Educativas',
            ],
            'Salud y Cuidado Personal' => [
                'Atención Primaria',
                'Diagnóstico Médico',
                'Planificación de Cuidados',
                'Gestión de Historias Clínicas',
                'Comunicación con el Paciente',
            ],
            'Diseño de Moda' => [
                'Diseño de Patrones',
                'Tendencias de Moda',
                'Confección de Prendas',
                'Ilustración de Moda',
                'Manejo de Materiales Textiles',
            ],
            'Arquitectura y Construcción' => [
                'Diseño Arquitectónico',
                'Planificación Urbana',
                'Gestión de Proyectos de Construcción',
                'Técnicas de Construcción',
                'Sostenibilidad en la Construcción',
            ],
            'Medio Ambiente y Sostenibilidad' => [
                'Gestión Ambiental',
                'Evaluación de Impacto Ambiental',
                'Energías Renovables',
                'Sostenibilidad Corporativa',
                'Conservación de Recursos Naturales',
            ],
            'Investigación y Desarrollo' => [
                'Metodologías de Investigación',
                'Desarrollo de Productos',
                'Investigación de Mercado',
                'Innovación Tecnológica',
                'Análisis de Datos',
            ],
            'Ciencia de Datos' => [
                'Análisis Estadístico',
                'Machine Learning',
                'Visualización de Datos',
                'Minería de Datos',
                'Programación en Python/R',
            ],
            'Agricultura y Agroindustria' => [
                'Manejo de Cultivos',
                'Tecnología Agrícola',
                'Agronegocios',
                'Investigación Agrícola',
                'Sistemas de Riego',
            ],
            'Periodismo y Comunicación' => [
                'Redacción Periodística',
                'Edición de Contenidos',
                'Investigación de Noticias',
                'Fotografía Periodística',
                'Comunicación Multimedia',
            ],
            'Turismo y Hostelería' => [
                'Gestión Hotelera',
                'Servicio al Cliente en Turismo',
                'Organización de Eventos',
                'Marketing Turístico',
                'Planificación de Viajes',
            ],
            'Arte y Entretenimiento' => [
                'Creación Artística',
                'Producción Audiovisual',
                'Diseño Gráfico',
                'Escenografía',
                'Gestión de Eventos Culturales',
            ],
            'Servicios Legales' => [
                'Asesoría Jurídica',
                'Investigación Legal',
                'Redacción de Documentos Legales',
                'Litigio',
                'Derecho Corporativo',
            ],
            'Traducción e Idiomas' => [
                'Traducción Especializada',
                'Interpretación',
                'Dominio de Idiomas Extranjeros',
                'Localización de Contenidos',
                'Estudios Lingüísticos',
            ],
            'Psicología y Bienestar' => [
                'Evaluación Psicológica',
                'Terapia Individual y Grupal',
                'Consejería',
                'Psicología Organizacional',
                'Psicología del Deporte',
            ],
            'Investigación de Mercados' => [
                'Análisis de Datos',
                'Investigación de Mercado',
                'Estadística Aplicada',
                'Metodologías de Investigación de Mercado',
                'Segmentación de Mercado',
            ],
            'Publicidad y Relaciones Públicas' => [
                'Planificación de Campañas Publicitarias',
                'Relaciones con Medios',
                'Gestión de Redes Sociales',
                'Marketing de Influencers',
                'Comunicación Estratégica',
            ],
            'Ingeniería Civil' => [
                'Diseño Estructural',
                'Gestión de Proyectos de Ingeniería',
                'Topografía',
                'Evaluación de Impacto Ambiental en Proyectos de Construcción',
                'Diseño de Infraestructuras',
            ],
            'Energías Renovables' => [
                'Diseño de Sistemas de Energía Renovable',
                'Evaluación de Recursos Naturales',
                'Eficiencia Energética',
                'Gestión de Proyectos de Energías Renovables',
                'Innovación en Tecnologías Sostenibles',
            ],
            'Recreación y Deportes' => [
                'Planificación de Eventos Deportivos',
                'Entrenamiento Deportivo',
                'Gestión de Instalaciones Deportivas',
                'Promoción de Estilos de Vida Activos',
                'Coordinación de Actividades Recreativas',
            ],
            'Asesoría Financiera' => [
                'Planificación Financiera Personal',
                'Análisis de Inversiones',
                'Gestión de Portafolio',
                'Asesoría en Jubilación',
                'Estrategias de Reducción de Deudas',
            ],
            'Política y Asuntos Públicos' => [
                'Análisis Político',
                'Campañas Electorales',
                'Diseño e Implementación de Políticas Públicas',
                'Relaciones Gubernamentales',
                'Comunicación Política',
            ],
            'Desarrollo Comunitario' => [
                'Planificación Comunitaria',
                'Participación Ciudadana',
                'Desarrollo Socioeconómico Local',
                'Gestión de Recursos Comunitarios',
                'Empoderamiento Comunitario',
            ],
            'Diseño Industrial' => [
                'Diseño de Productos',
                'Innovación en Diseño Industrial',
                'Prototipado Rápido',
                'Materiales y Procesos de Fabricación',
                'Diseño Ergonómico',
            ],
            'Consultoría Empresarial' => [
                'Diagnóstico Empresarial',
                'Optimización de Procesos',
                'Estrategia Empresarial',
                'Mejora Continua',
                'Gestión del Cambio',
            ],

        ];

        return $skillsByCategory[$categoryName] ?? [];
    }
}
