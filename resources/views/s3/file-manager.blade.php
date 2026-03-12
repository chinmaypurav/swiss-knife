<x-layouts.app title="S3 File Manager">
<div class="space-y-6">
    {{-- Breadcrumbs --}}
    <div class="breadcrumbs text-sm">
        <ul>
            <li><a href="{{ route('s3.index') }}">S3 Root</a></li>
            @if($currentPath)
                @foreach(explode('/', $currentPath) as $i => $segment)
                    <li>
                        <a href="{{ route('s3.index', ['path' => implode('/', array_slice(explode('/', $currentPath), 0, $i + 1))]) }}">
                            {{ $segment }}
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="alert alert-success">
            <span>{{ session('success') }}</span>
            <button class="btn btn-sm btn-ghost" @click="show = false">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" class="alert alert-error">
            <span>{{ session('error') }}</span>
            <button class="btn btn-sm btn-ghost" @click="show = false">✕</button>
        </div>
    @endif

    {{-- Upload Card --}}
    <div class="card bg-base-100 shadow-sm" x-data="{ uploading: false }">
        <div class="card-body">
            <h2 class="card-title">Upload File</h2>
            <form action="{{ route('s3.upload') }}" method="POST" enctype="multipart/form-data" @submit="uploading = true">
                @csrf
                <input type="hidden" name="path" value="{{ $currentPath }}">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="form-control w-full max-w-xs">
                        <label class="label" for="file-upload">
                            <span class="label-text">Choose file</span>
                        </label>
                        <input type="file" name="file" id="file-upload" class="file-input file-input-bordered w-full max-w-xs" required>
                        @error('file')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label" for="upload-path">
                            <span class="label-text">Path prefix (optional)</span>
                        </label>
                        <input type="text" name="path" id="upload-path" value="{{ $currentPath }}" class="input input-bordered w-full max-w-xs" placeholder="e.g. uploads/images">
                    </div>
                    <button type="submit" class="btn btn-primary" :disabled="uploading">
                        <span x-show="!uploading">Upload</span>
                        <span x-show="uploading" class="loading loading-spinner loading-sm"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- File Listing --}}
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Files & Directories</h2>

            @if($directories->isEmpty() && $files->isEmpty())
                <div class="text-center py-8 text-base-content/60">
                    <p>No files or directories found in this location.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Size</th>
                                <th>Last Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Parent directory link --}}
                            @if($currentPath)
                                @php
                                    $parentPath = dirname($currentPath);
                                    $parentPath = $parentPath === '.' ? '' : $parentPath;
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('s3.index', ['path' => $parentPath]) }}" class="link link-hover flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                                            ..
                                        </a>
                                    </td>
                                    <td>—</td>
                                    <td>—</td>
                                    <td></td>
                                </tr>
                            @endif

                            {{-- Directories --}}
                            @foreach($directories as $directory)
                                <tr>
                                    <td>
                                        <a href="{{ route('s3.index', ['path' => $directory['path']]) }}" class="link link-hover flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                            {{ $directory['name'] }}
                                        </a>
                                    </td>
                                    <td>—</td>
                                    <td>—</td>
                                    <td></td>
                                </tr>
                            @endforeach

                            {{-- Files --}}
                            @foreach($files as $file)
                                <tr x-data="signedUrl()">
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            {{ $file['name'] }}
                                        </div>
                                    </td>
                                    <td>{{ number_format($file['size'] / 1024, 2) }} KB</td>
                                    <td>{{ \Carbon\Carbon::createFromTimestamp($file['lastModified'])->diffForHumans() }}</td>
                                    <td>
                                        <div class="flex flex-wrap gap-2">
                                            {{-- Download --}}
                                            <a href="{{ route('s3.download', ['path' => $file['path']]) }}" class="btn btn-sm btn-outline btn-info">
                                                Download
                                            </a>

                                            {{-- Signed URL --}}
                                            <button class="btn btn-sm btn-outline btn-accent" @click="generate('{{ $file['path'] }}')">
                                                Signed URL
                                            </button>

                                            {{-- Signed URL Result --}}
                                            <template x-if="url">
                                                <div class="flex items-center gap-2">
                                                    <input type="text" :value="url" class="input input-bordered input-sm w-64" readonly>
                                                    <button class="btn btn-sm btn-ghost" @click="copy()">Copy</button>
                                                </div>
                                            </template>
                                            <span x-show="copied" x-transition class="text-success text-sm">Copied!</span>
                                            <span x-show="error" x-transition class="text-error text-sm" x-text="error"></span>

                                            {{-- Delete --}}
                                            <form action="{{ route('s3.destroy', ['path' => $file['path']]) }}" method="POST"
                                                  x-data="{ confirming: false }">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline btn-error" x-show="!confirming" @click="confirming = true">
                                                    Delete
                                                </button>
                                                <div x-show="confirming" class="flex gap-1">
                                                    <button type="submit" class="btn btn-sm btn-error">Confirm</button>
                                                    <button type="button" class="btn btn-sm btn-ghost" @click="confirming = false">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<x-slot:scripts>
<script>
    function signedUrl() {
        return {
            url: '',
            copied: false,
            error: '',
            async generate(path) {
                this.url = '';
                this.error = '';
                this.copied = false;

                try {
                    const response = await fetch('{{ route('s3.signed-url') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ path: path, expiry: 60 }),
                    });

                    if (!response.ok) {
                        const data = await response.json();
                        throw new Error(data.message || 'Failed to generate signed URL.');
                    }

                    const data = await response.json();
                    this.url = data.url;
                } catch (e) {
                    this.error = e.message;
                }
            },
            async copy() {
                await navigator.clipboard.writeText(this.url);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        };
    }
</script>
</x-slot:scripts>
</x-layouts.app>
