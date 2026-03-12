<x-layouts.app title="Home">
<div class="space-y-8">
    <div class="text-center">
        <h1 class="text-4xl font-bold">Swiss Knife</h1>
        <p class="mt-2 text-base-content/60">A collection of handy utility tools</p>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {{-- S3 File Browser --}}
        <a href="{{ route('s3.index') }}" class="card bg-base-100 shadow-sm transition hover:shadow-md">
            <div class="card-body items-center text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                <h2 class="card-title">S3 File Browser</h2>
                <p class="text-base-content/60">Upload, download & generate signed URLs for S3 files</p>
            </div>
        </a>
    </div>
</div>
</x-layouts.app>
