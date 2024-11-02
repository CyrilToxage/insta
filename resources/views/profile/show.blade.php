<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- En-tête du profil --}}
            <div class="bg-white shadow sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row items-center md:items-start">
                        {{-- Photo de profil --}}
                        <div class="mb-4 md:mb-0 md:mr-10">
                            <img src="{{ auth()->user()->profile_photo ? asset(auth()->user()->profile_photo) : asset('images/default-avatar.png') }}"
                                alt="Profile" class="h-8 w-8 rounded-full object-cover">
                        </div>

                        {{-- Informations du profil --}}
                        <div class="flex-1">
                            <div class="flex flex-col md:flex-row md:items-center mb-4">
                                <h1 class="text-2xl font-semibold mb-2 md:mb-0 md:mr-6">{{ $user->username }}</h1>

                                @if (auth()->id() !== $user->id)
                                    <form action="{{ route('profile.follow', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-4 py-2 rounded-md {{ auth()->user()->following->contains($user)
                                                ? 'bg-gray-200 hover:bg-gray-300'
                                                : 'bg-blue-500 text-white hover:bg-blue-600' }}">
                                            {{ auth()->user()->following->contains($user) ? 'Ne plus suivre' : 'Suivre' }}
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- Statistiques --}}
                            <div class="flex space-x-8 mb-4">
                                <div>
                                    <span class="font-semibold">{{ $user->posts->count() }}</span> publications
                                </div>
                                <div>
                                    <span class="font-semibold">{{ $user->followers->count() }}</span> abonnés
                                </div>
                                <div>
                                    <span class="font-semibold">{{ $user->following->count() }}</span> abonnements
                                </div>
                            </div>

                            {{-- Nom complet et bio --}}
                            <div>
                                <div class="font-semibold">{{ $user->name }}</div>
                                @if ($user->bio)
                                    <div class="mt-2 whitespace-pre-line">{{ $user->bio }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Publications --}}
            @if ($posts->isNotEmpty())
                <div class="grid grid-cols-3 gap-4">
                    @foreach ($posts as $post)
                        <div class="aspect-square relative group" x-data="{ menuOpen: false }">
                            {{-- Option de suppression (uniquement pour les posts de l'utilisateur) --}}
                            @if (auth()->id() === $post->user_id)
                                <div class="absolute top-2 right-2 z-10">
                                    <button @click.prevent="menuOpen = !menuOpen"
                                        class="p-1 rounded-full bg-black bg-opacity-50 text-white opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>

                                    {{-- Menu de suppression --}}
                                    <div x-show="menuOpen" @click.away="menuOpen = false"
                                        class="absolute right-0 mt-2 w-48 py-2 bg-white rounded-lg shadow-xl">
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

                            {{-- Lien vers le post et overlay --}}
                            <a href="{{ route('posts.show', $post) }}" class="block w-full h-full">
                                <img src="{{ asset($post->image) }}" alt="Post" class="w-full h-full object-cover">

                                {{-- Overlay avec les statistiques au survol --}}
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-8 text-white">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M2 10a8 8 0 1116 0 8 8 0 01-16 0zm8 6a6 6 0 100-12 6 6 0 000 12zm0-10a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V7a1 1 0 011-1z" />
                                        </svg>
                                        <span>{{ $post->likes->count() }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ $post->comments->count() }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="bg-white shadow sm:rounded-lg p-6 text-center">
                    <p class="text-gray-500">Aucune publication pour le moment.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
