<x-app-layout>
    <div class="max-w-2xl mx-auto py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">Créer une publication</h2>

            {{-- Ajout d'un message de débogage --}}
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('posts.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-4">
                @csrf

                <div class="space-y-2">
                    <label for="image" class="block text-gray-700 font-medium">Photo</label>
                    <input type="file"
                           id="image"
                           name="image"
                           accept="image/*"
                           required
                           class="block w-full">
                    @error('image')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="caption" class="block text-gray-700 font-medium">Légende</label>
                    <textarea id="caption"
                              name="caption"
                              rows="3"
                              class="w-full border rounded px-3 py-2">{{ old('caption') }}</textarea>
                    @error('caption')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Publier
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
