@props(['post'])

<div class="bg-white rounded-lg shadow mb-8">
    <div class="p-4 border-b flex items-center justify-between">
        <div class="flex items-center">
            <img src="{{ $post->user->profile_photo
                ? Storage::url($post->user->profile_photo)
                : '/images/default-avatar.png' }}"
                 alt="Profile" class="w-8 h-8 rounded-full mr-3">
            <a href="{{ route('profile.show', $post->user) }}"
               class="font-semibold">{{ $post->user->name }}</a>
        </div>
        @if(auth()->id() === $post->user_id)
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                        </path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                     class="absolute right-0 w-48 py-2 mt-2 bg-white rounded-lg shadow-xl">
                    <form action="{{ route('posts.destroy', $post) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')">
                            Supprimer la publication
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <a href="{{ route('posts.show', $post) }}">
        <img src="{{ Storage::url($post->image) }}" alt="Post" class="w-full">
    </a>

    <div class="p-4">
        <div class="flex items-center mb-4">
            <form action="{{ route('posts.like', $post) }}" method="POST">
                @csrf
                <button type="submit">
                    <svg class="w-6 h-6 {{ $post->likes->contains(auth()->user())
                        ? 'text-red-500' : 'text-gray-500' }}"
                         fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </button>
            </form>
            <span class="ml-2">{{ $post->likes->count() }} likes</span>
        </div>

        @if($post->caption)
            <p class="mb-2">
                <a href="{{ route('profile.show', $post->user) }}"
                   class="font-semibold">{{ $post->user->name }}</a>
                {{ $post->caption }}
            </p>
        @endif

        @if($post->comments->isNotEmpty())
            <div class="space-y-2">
                @foreach($post->comments->take(2) as $comment)
                    <p>
                        <a href="{{ route('profile.show', $comment->user) }}"
                           class="font-semibold">{{ $comment->user->name }}</a>
                        {{ $comment->content }}
                    </p>
                @endforeach

                @if($post->comments->count() > 2)
                    <a href="{{ route('posts.show', $post) }}"
                       class="text-gray-500 text-sm">
                        Voir les {{ $post->comments->count() - 2 }} autres commentaires
                    </a>
                @endif
            </div>
        @endif

        <p class="text-gray-500 text-xs mt-2">
            {{ $post->created_at->diffForHumans() }}
        </p>

        <form action="{{ route('comments.store', $post) }}" method="POST" class="mt-4">
            @csrf
            <input type="text" name="content"
                   placeholder="Ajouter un commentaire..."
                   class="w-full border rounded px-3 py-1">
        </form>
    </div>
</div>
