<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Video Tutorial') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-6">Video Tutorial Aplikasi KGB</h1>
                    
                    <p class="mb-8">Tonton video-video tutorial berikut untuk mempelajari cara menggunakan aplikasi KGB dengan efektif.</p>
                    
                    @if($videos->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($videos as $video)
                            <div class="bg-gray-50 rounded-lg overflow-hidden shadow">
                                <div class="relative pb-[56.25%] h-0"> <!-- 16:9 Aspect Ratio -->
                                    <iframe 
                                        class="absolute top-0 left-0 w-full h-full"
                                        src="{{ $video->embedded_url }}"
                                        title="{{ $video->title }}"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-lg mb-2">{{ $video->title }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">
                                        @if($video->duration)
                                            Durasi: 
                                            @if($video->duration >= 3600)
                                                {{ intdiv($video->duration, 3600) }}:{{ str_pad(intdiv($video->duration % 3600, 60), 2, '0', STR_PAD_LEFT) }}:{{ str_pad($video->duration % 60, 2, '0', STR_PAD_LEFT) }}
                                            @else
                                                {{ intdiv($video->duration, 60) }}:{{ str_pad($video->duration % 60, 2, '0', STR_PAD_LEFT) }}
                                            @endif
                                        @else
                                            Durasi: Tidak diketahui
                                        @endif
                                    </p>
                                    <p class="text-gray-700 text-sm">{{ Str::limit($video->description, 100) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500">Belum ada video tutorial yang tersedia.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>