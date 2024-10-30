<x-app-layout>
    <div class="max-w-2xl mx-auto py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">Résultats pour "{{ $query }}"</h2>

            @if ($users->isNotEmpty())
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Utilisateurs</h3>
                    <div class="space-y-4">
                        @foreach ($users as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="{{ $user->profile_photo ? asset($user->profile_photo) : asset('images/default-avatar.png') }}"
                                        alt="Profile" class="w-10 h-10 rounded-full mr-3 object-cover">
                                    <div>
                                        <a href="{{ route('profile.show', $user) }}"
                                            class="font-semibold">{{ $user->username ?? $user->name }}</a>
                                        @if ($user->bio)
                                            <p class="text-gray-500 text-sm">{{ Str::limit($user->bio, 50) }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if (auth()->id() !== $user->id)
                                    <form action="{{ route('profile.follow', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="bg-blue-500 text-white px-4 py-1 rounded text-sm
                                                       {{ auth()->user()->following->contains($user) ? 'bg-gray-500' : '' }}">
                                            {{ auth()->user()->following->contains($user) ? 'Ne plus suivre' : 'Suivre' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($posts->isNotEmpty())
                <div>
                    <h3 class="text-lg font-semibold mb-4">Publications</h3>
                    <div class="grid grid-cols-3 gap-4">
                        @foreach ($posts as $post)
                            <a href="{{ route('posts.show', $post) }}" class="aspect-square">
                                <img src="{{ asset($post->image) }}" alt="Post" class="w-full h-full object-cover">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($users->isEmpty() && $posts->isEmpty())
                <p class="text-gray-500">Aucun résultat trouvé pour "{{ $query }}"</p>
            @endif
        </div>
    </div>
</x-app-layout>
