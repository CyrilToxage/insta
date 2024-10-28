<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paramètres du profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Informations du profil Instagram --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Photo de profil et Biographie') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Mettez à jour votre photo de profil et votre biographie.') }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <div>
                                <div class="flex items-center space-x-6">
                                    <div class="shrink-0">
                                        <img class="h-16 w-16 object-cover rounded-full"
                                             src="{{ Auth::user()->profile_photo
                                                ? Storage::url(Auth::user()->profile_photo)
                                                : '/images/default-avatar.png' }}"
                                             alt="{{ Auth::user()->name }}">
                                    </div>
                                    <label class="block">
                                        <span class="sr-only">Choisir une photo</span>
                                        <input type="file"
                                               name="profile_photo"
                                               class="block w-full text-sm text-gray-500
                                                      file:mr-4 file:py-2 file:px-4
                                                      file:rounded-full file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-blue-50 file:text-blue-700
                                                      hover:file:bg-blue-100"
                                               accept="image/*">
                                    </label>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
                            </div>

                            <div>
                                <x-input-label for="bio" :value="__('Biographie')" />
                                <textarea id="bio"
                                          name="bio"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                          rows="4">{{ old('bio', Auth::user()->bio) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            {{-- Informations du profil existantes --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Mot de passe --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Suppression du compte --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
