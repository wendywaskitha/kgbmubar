<div class="flex justify-center items-center p-4">
    <img 
        src="{{ $url }}" 
        alt="Preview Dokumen" 
        class="max-w-full max-h-[70vh] object-contain"
        onerror="this.onerror=null; this.src='{{ asset('vendor/harmony/error-image.png') }}';"
    >
</div>