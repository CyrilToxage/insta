<x-app-layout>
    <div class="max-w-4xl mx-auto py-8">
        <div class="bg-white rounded-lg shadow">
            <div class="md:flex h-[calc(100vh-8rem)]">
                <div class="md:w-2/3 md:min-h-[600px] relative bg-black">
                    <img src="{{ asset($post->image) }}" alt="Post"
                        class="absolute inset-0 w-full h-full object-contain">
                </div>

                <div class="md:w-1/3 p-4">
                    <div class="border-b pb-4 mb-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
                                <img src="{{ $post->user->profile_photo ? asset($post->user->profile_photo) : asset('images/default-avatar.png') }}"
                                    alt="Profile" class="w-full h-full object-cover">
                            </div>
                            <a href="{{ route('profile.show', $post->user) }}"
                                class="font-semibold ml-3">{{ $post->user->username ?: $post->user->name }}</a>
                        </div>
                    </div>

                    @if ($post->caption)
                        <div class="mb-4">
                            <p>
                                <a href="{{ route('profile.show', $post->user) }}"
                                    class="font-semibold">{{ $post->user->username ?? $post->user->name }}</a>
                                {{ $post->caption }}
                            </p>
                        </div>
                    @endif

                    <div class="space-y-4 max-h-96 overflow-y-auto mb-4">
                        @foreach ($post->comments as $comment)
                            <div class="flex items-start">
                                <img src="{{ $comment->user->profile_photo ? asset($comment->user->profile_photo) : asset('images/default-avatar.png') }}"
                                    alt="Profile" class="w-6 h-6 rounded-full mr-2 object-cover">
                                <div>
                                    <a href="{{ route('profile.show', $comment->user) }}"
                                        class="font-semibold">{{ $comment->user->username ?? $comment->user->name }}</a>
                                    <span class="ml-2">{{ $comment->content }}</span>
                                    <p class="text-gray-500 text-xs">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex items-center mb-4" x-data="{
                            liked: {{ $post->likes->contains(auth()->user()) ? 'true' : 'false' }},
                            likesCount: {{ $post->likes->count() }},
                            async toggleLike() {
                                const response = await fetch('{{ route('posts.like', $post) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Accept': 'application/json'
                                    }
                                });
                                if (response.ok) {
                                    this.liked = !this.liked;
                                    this.likesCount += this.liked ? 1 : -1;
                                }
                            }
                        }">
                            <button @click="toggleLike" class="focus:outline-none">
                                <svg class="w-6 h-6" :class="liked ? 'text-red-500' : 'text-gray-500'"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                            </button>
                            <span class="ml-2" x-text="`${likesCount} likes`"></span>
                        </div>

                        <form action="{{ route('comments.store', $post) }}" method="POST" class="flex space-x-2">
                            @csrf
                            <input type="text" name="content" placeholder="Ajouter un commentaire..."
                                class="flex-1 border rounded-l px-3 py-1 min-w-0" required>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded-r hover:bg-blue-600">
                                Publier
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
