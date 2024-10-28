<x-app-layout>
    <div class="max-w-2xl mx-auto py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">Créer une publication</h2>

            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Photo</label>
                    <input type="file" name="image" accept="image/*" required>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Légende</label>
                    <textarea name="caption" rows="3"
                              class="w-full border rounded px-3 py-2"></textarea>
                    @error('caption')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Publier
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
