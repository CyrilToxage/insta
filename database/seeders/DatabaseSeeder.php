<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyer les dossiers d'images avant de commencer
        $this->command->info('Nettoyage des dossiers d\'images...');
        Artisan::call('storage:clean');

        // Créer des utilisateurs
        $users = User::factory(10)->create();

        // Pour chaque utilisateur
        foreach ($users as $user) {
            // Créer entre 1 et 5 posts
            $posts = Post::factory(rand(1, 5))->create([
                'user_id' => $user->id
            ]);

            // Pour chaque post
            foreach ($posts as $post) {
                // Créer entre 0 et 3 commentaires
                $numberOfComments = rand(0, 3);
                for ($i = 0; $i < $numberOfComments; $i++) {
                    Comment::factory()->create([
                        'post_id' => $post->id,
                        'user_id' => $users->random()->id
                    ]);
                }

                // Ajouter des likes aléatoires (entre 0 et 5 likes)
                $numberOfLikes = rand(0, 5);
                $randomUsers = $users->random($numberOfLikes);
                $post->likes()->attach($randomUsers->pluck('id'));
            }

            // Ajouter des abonnements aléatoires
            $possibleFollowers = $users->except($user->id);
            $numberOfFollowers = rand(0, count($possibleFollowers));
            $followers = $possibleFollowers->random($numberOfFollowers);
            $user->followers()->attach($followers->pluck('id'));
        }

        // Créer un utilisateur de test
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'username' => 'test',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Créer quelques posts pour l'utilisateur test
        Post::factory(3)->create([
            'user_id' => $testUser->id
        ]);
    }
}
