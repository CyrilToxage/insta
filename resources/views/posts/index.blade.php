<x-app-layout>
    <div class="max-w-2xl mx-auto py-8">
        @foreach($posts as $post)
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="p-4 border-b flex items-center">
                    <img src="{{ $post->user->profile_photo
                        ? Storage::url($post->user->profile_photo)
                        : '/images/default-avatar.png' }}"
                         alt="Profile" class="w-8 h-8 rounded-full mr-3">
                    <a href="{{ route('profile.show', $post->user) }}"
                       class="font-semibold">{{ $post->user->name }}</a>
                </div>

                <img src="{{ Storage::url($post->image) }}" alt="Post" class="w-full">

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

                    <div class="space-y-2">
                        @foreach($post->comments->take(3) as $comment)
                            <p>
                                <a href="{{ route('profile.show', $comment->user) }}"
                                   class="font-semibold">{{ $comment->user->name }}</a>
                                {{ $comment->content }}
                            </p>
                        @endforeach
                    </div>

                    <form action="{{ route('comments.store', $post) }}" method="POST" class="mt-4">
                        @csrf
                        <input type="text" name="content" placeholder="Ajouter un commentaire..."
                               class="w-full border rounded px-3 py-1">
                    </form>
                </div>
            </div>
        @endforeach

        {{ $posts->links() }}
    </div>
</x-app-layout>
