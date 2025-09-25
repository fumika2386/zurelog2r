<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Noto+Sans+JP:wght@400;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    {{-- Onboarding modal (Alpine.js) --}}
    @auth
    @php
        $user = auth()->user()->fresh();   // ★ DBの最新値を再取得
        $showOnboarding = is_null($user?->onboarded_at);
    @endphp
    <div x-data="{ open: {{ $showOnboarding ? 'true' : 'false' }} }">
            <div x-show="open"
                class="fixed inset-0 z-40 flex items-center justify-center bg-black/40">
                <div @click.outside="open=false"
                    class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                    <h2 class="text-xl font-semibold mb-3">ようこそ、ズレログへ</h2>
                    <ol class="list-decimal ml-5 space-y-2 text-sm text-gray-700">
                        <li>まずは <b>価値観アンケート</b> で自分のベクトルを登録</li>
                        <li>気になる <b>お題</b> から <b>投稿</b> を作成</li>
                        <li>フィードの並び替えで価値観の<b>近い/遠い</b> を体験</li>
                        <li>プロフィール編集で <b>アイコン</b> を設定</li>
                    </ol>
                    <div class="mt-4 flex items-center justify-between">
                        <a href="{{ route('values.survey.show') }}"
                        class="btn-primary px-4 py-2 rounded-xl">アンケートへ</a>
                        <form method="POST" action="{{ route('onboarding.complete') }}">
                            @csrf
                            <button class="btn-outline px-3 py-2 rounded-xl"
                                    @click="open=false">はじめる</button>
                        </form>
                    </div>
                    <p class="mt-3 text-xs text-gray-500">
                        ※ もう一度見る：ヘッダーの「?」アイコンから
                    </p>
                </div>
            </div>
        </div>
    @endauth


    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
