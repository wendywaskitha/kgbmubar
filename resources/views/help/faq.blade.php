<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('FAQ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-6">Pertanyaan yang Sering Diajukan (FAQ)</h1>
                    
                    <div class="space-y-6">
                        @foreach($faqs as $faq)
                        <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                            <h3 class="text-xl font-semibold mb-2 text-blue-700">{{ $faq['question'] }}</h3>
                            <p class="text-gray-700">{{ $faq['answer'] }}</p>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                        <h4 class="text-lg font-semibold mb-2">Tidak menemukan jawaban yang Anda cari?</h4>
                        <p class="mb-3">Silakan hubungi tim dukungan kami untuk bantuan lebih lanjut.</p>
                        <a href="mailto:help@kgbapp.test" class="text-blue-600 hover:underline">Kirim Email ke Tim Dukungan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>