@props(['post'])

<div class="bg-white rounded-lg shadow mb-8"
     x-data="{
        liked: {{ $post->likes->contains(auth()->user()) ? 'true' : 'false' }},
        likesCount: {{ $post->likes->count() }},
        comments: {{ $post->comments->take(2)->values()->toJson() }},
        menuOpen: false,
        async toggleLike() {
            try {
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
            } catch (error) {
                console.error('Erreur:', error);
            }
        },
        async addComment(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok) {
                    const data = await response.json();
                    this.comments.unshift(data.comment);
                    if (this.comments.length > 2) this.comments.pop();
                    form.reset();
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }
     }">
    {{-- En-tête --}}
    <div class="p-4 border-b flex items-center justify-between">
        <div class="flex items-center">
            <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
                <img src="{{ $post->user->profile_photo
                    ? asset($post->user->profile_photo)
                    : asset('images/default-avatar.png') }}"
                     alt="Profile"
                     class="w-full h-full object-cover">
            </div>
            <a href="{{ route('profile.show', $post->user) }}"
               class="font-semibold ml-3">{{ $post->user->username ?: $post->user->name }}</a>
        </div>
        @if(auth()->id() === $post->user_id)
            <div class="relative">
                <button @click="menuOpen = !menuOpen" class="focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                        </path>
                    </svg>
                </button>
                <div x-show="menuOpen" @click.away="menuOpen = false"
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

    {{-- Image --}}
    <a href="{{ route('posts.show', $post) }}">
        <img src="{{ asset($post->image) }}" alt="Post" class="w-full">
    </a>

    {{-- Actions et commentaires --}}
    <div class="p-4">
        <div class="flex items-center mb-4">
            <button @click="toggleLike" class="focus:outline-none">
                <svg class="w-6 h-6 transition-colors duration-200"
                     :class="liked ? 'text-red-500' : 'text-gray-500'"
                     fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </button>
            <span class="ml-2" x-text="`${likesCount} likes`"></span>
        </div>

        @if($post->caption)
            <p class="mb-2">
                <a href="{{ route('profile.show', $post->user) }}"
                   class="font-semibold">{{ $post->user->username ?? $post->user->name }}</a>
                {{ $post->caption }}
            </p>
        @endif

        <div class="space-y-2">
            <template x-for="comment in comments" :key="comment.id">
                <p class="break-words">
                    <a :href="'/profile/' + comment.user.id"
                       class="font-semibold"
                       x-text="comment.user.username || comment.user.name"></a>
                    <span class="whitespace-pre-line" x-text="comment.content"></span>
                </p>
            </template>

            @if($post->comments->count() > 2)
                <a href="{{ route('posts.show', $post) }}"
                   class="text-gray-500 text-sm">
                    Voir les {{ $post->comments->count() - 2 }} autres commentaires
                </a>
            @endif
        </div>

        <p class="text-gray-500 text-xs mt-2">
            {{ $post->created_at->diffForHumans() }}
        </p>

        <form @submit.prevent="addComment"
              action="{{ route('comments.store', $post) }}"
              class="mt-4">
            @csrf
            <input type="text"
                   name="content"
                   placeholder="Ajouter un commentaire..."
                   class="w-full border rounded px-3 py-1"
                   required>
        </form>
    </div>
</div>
