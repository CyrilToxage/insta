<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- En-tête du profil --}}
            <div class="bg-white shadow sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row items-center md:items-start">
                        {{-- Photo de profil --}}
                        <div class="mb-4 md:mb-0 md:mr-10">
                            <img src="{{ $user->profile_photo ? Storage::url($user->profile_photo) : '/images/default-avatar.png' }}"
                                alt="Photo de profil" class="w-32 h-32 rounded-full object-cover">
                        </div>

                        {{-- Informations du profil --}}
                        <div class="flex-1">
                            <div class="flex flex-col md:flex-row md:items-center mb-4">
                                <h1 class="text-2xl font-semibold mb-2 md:mb-0 md:mr-6">{{ $user->username }}</h1>

                                @if (Auth::id() === $user->id)
                                    <a href="{{ route('profile.edit') }}"
                                        class="px-4 py-2 bg-gray-100 rounded-md text-sm font-semibold text-gray-700 hover:bg-gray-200">
                                        Modifier le profil
                                    </a>
                                @else
                                    <form action="{{ route('profile.follow', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @if (Auth::user()->following->contains($user))
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-4 py-2 bg-gray-100 rounded-md text-sm font-semibold text-gray-700 hover:bg-gray-200">
                                                Ne plus suivre
                                            </button>
                                        @else
                                            <button type="submit"
                                                class="px-4 py-2 bg-blue-500 rounded-md text-sm font-semibold text-white hover:bg-blue-600">
                                                Suivre
                                            </button>
                                        @endif
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
                        <div class="aspect-square relative group">
                            <a href="{{ route('posts.show', $post) }}" class="block w-full h-full">
                                <img src="{{ Storage::url($post->image) }}" alt="Publication"
                                    class="w-full h-full object-cover">

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
