<x-app-layout>
    <div class="max-w-2xl mx-auto py-8">
        {{-- Posts des utilisateurs suivis --}}
        @if($followedPosts->isNotEmpty())
            <div class="mb-12">
                <h2 class="text-xl font-semibold mb-6">Publications de vos abonnements</h2>
                @foreach($followedPosts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        @endif

        {{-- Posts populaires --}}
        @if($popularPosts->isNotEmpty())
            <div>
                <h2 class="text-xl font-semibold mb-6">Publications populaires</h2>
                @foreach($popularPosts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        @endif

        @if($followedPosts->isEmpty() && $popularPosts->isEmpty())
            <div class="text-center py-8">
                <p class="text-gray-500">Aucune publication à afficher pour le moment.</p>
                <p class="mt-2">
                    <a href="{{ route('posts.create') }}"
                       class="text-blue-500 hover:underline">
                        Créer votre première publication
                    </a>
                </p>
            </div>
        @endif
    </div>
</x-app-layout>
