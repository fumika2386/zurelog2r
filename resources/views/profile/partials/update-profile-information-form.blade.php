<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>


    
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

                <!-- {{-- プロフィール画像 --}} -->
        <div>
            <x-input-label for="avatar" value="プロフィール画像" />
            <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full">
            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
            @if(auth()->user()->avatar_path)
                <img
                    src="{{ asset('storage/'.auth()->user()->avatar_path) }}"
                    alt="avatar"
                    class="mt-3 rounded-full object-cover"
                    style="width:96px;height:96px;object-fit:cover;border-radius:9999px"
                >
            @endif
        </div>

        <!-- -- 自己紹介 -- -->
        <div>
            <x-input-label for="description" value="自己紹介" />
            <textarea id="description" name="description" rows="5"
                    class="mt-1 block w-full">{{ old('description', auth()->user()->description) }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        @php
        $allTags = \Illuminate\Support\Facades\Schema::hasTable('tags')
            ? \App\Models\Tag::orderBy('sort_order')->get()
            : collect();
        $myTagIds = auth()->user()->tags()->pluck('tags.id')->all();
        @endphp

        @if($allTags->isNotEmpty())
        <div class="mt-6">
            <label class="block font-semibold mb-2">ハッシュタグ（自分をタグづけ）</label>
            <div class="flex flex-wrap gap-2">
            @foreach($allTags as $tag)
                <label class="inline-flex items-center gap-2 rounded-full border px-3 py-1 cursor-pointer">
                <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                        @checked(in_array($tag->id, old('tags', $myTagIds)))
                        class="form-checkbox text-primary-600">
                <span class="text-sm">#{{ $tag->name }}</span>
                </label>
            @endforeach
            </div>
            @error('tags') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            @error('tags.*') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>
        @endif




        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>

        

    </form>
</section>

