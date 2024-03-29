<?php

namespace Database\Seeders;

use App\Models\SocialNetwork;
use Illuminate\Database\Seeder;

class SocialNetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialNetworks = [
            ['name' => 'Facebook', 'url' => 'https://www.facebook.com'],
            ['name' => 'Twitter', 'url' => 'https://twitter.com'],
            ['name' => 'LinkedIn', 'url' => 'https://www.linkedin.com'],
            ['name' => 'Instagram', 'url' => 'https://www.instagram.com'],
            ['name' => 'YouTube', 'url' => 'https://www.youtube.com'],
            ['name' => 'Pinterest', 'url' => 'https://www.pinterest.com'],
            ['name' => 'Tumblr', 'url' => 'https://www.tumblr.com'],
            ['name' => 'Reddit', 'url' => 'https://www.reddit.com'],
            ['name' => 'Snapchat', 'url' => 'https://www.snapchat.com'],
            ['name' => 'WhatsApp', 'url' => 'https://www.whatsapp.com'],
            // Agrega más redes sociales si es necesario
        ];

        foreach ($socialNetworks as $network) {
            SocialNetwork::create($network);
        }
    }
}
