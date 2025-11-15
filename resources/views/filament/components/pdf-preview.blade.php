<div class="flex justify-center items-center p-4">
    <iframe 
        src="{{ $url }}" 
        type="application/pdf"
        class="w-full h-[70vh] border border-gray-300 rounded-lg"
        title="PDF Preview"
    >
        <p>Browser Anda tidak mendukung tampilan PDF. <a href="{{ $url }}" target="_blank">Klik di sini untuk membuka file.</a></p>
    </iframe>
</div>