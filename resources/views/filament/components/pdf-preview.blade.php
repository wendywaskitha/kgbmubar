@props(['url'])
<div style="width:100%;height:85vh;background:#222;padding:1rem;display:flex;justify-content:center;align-items:center;">
    <iframe src="{{ $url }}" width="100%" height="100%" style="border:0;min-height:550px;max-height:85vh;box-shadow:0 0 30px #0008;" allowfullscreen></iframe>
</div>