<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    use HasFactory;
    /**
     * @return array<string, mixed>
     */
    protected $model = Post::class;

    public function definition(): array
    {
        // Générer un nom unique pour l'image
        $randomName = Str::random(10);

        // Créer le dossier s'il n'existe pas
        if (!file_exists(public_path('images/posts'))) {
            mkdir(public_path('images/posts'), 0755, true);
        }

        // URL de l'image Picsum
        $imageUrl = "https://picsum.photos/1024/768.webp?random={$randomName}";

        // Chemin de sauvegarde
        $imagePath = "images/posts/{$randomName}.webp";

        // Télécharger et sauvegarder l'image
        $imageContent = file_get_contents($imageUrl);
        file_put_contents(public_path($imagePath), $imageContent);

        return [
            'user_id' => User::factory(),
            'image' => $imagePath,
            'caption' => $this->faker->optional(0.8)->text(200),
            'created_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}
