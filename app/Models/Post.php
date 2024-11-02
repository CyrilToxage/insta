<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'caption',
    ];

    protected $with = ['user'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($post) {
            try {
                if ($post->image) {
                    $imagePath = public_path($post->image);
                    if (File::exists($imagePath)) {
                        File::delete($imagePath);
                        Log::info('Image supprimée: ' . $imagePath);
                    }
                }

                // Nettoyer les relations
                $post->likes()->detach();
                $post->comments()->delete();

            } catch (\Exception $e) {
                Log::error('Erreur lors de la suppression de l\'image: ' . $e->getMessage());
            }
        });

        static::creating(function ($post) {
            if (!$post->user_id) {
                $post->user_id = auth()->id();
            }
        });
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)
            ->with('user')
            ->orderBy('created_at', 'desc');
    }
}
