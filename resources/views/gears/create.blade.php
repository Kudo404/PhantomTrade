
@extends('layouts.app')

@section('content')
<div class="mx-auto my-10 max-w-3xl rounded-2xl border border-gray-700 bg-gray-900 p-6 text-gray-100 shadow-xl sm:p-8">

    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight">Add New Gear</h1>
        <p class="mt-2 text-sm text-gray-400">
            Add equipment and its product photo to the PhantomTrade shop.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-700 bg-red-950/60 p-4 text-red-200">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ url('/gears') }}"
        enctype="multipart/form-data"
        class="space-y-6"
    >
        @csrf

        <div>
            <label for="name" class="mb-2 block text-sm font-semibold text-gray-200">
                Gear Name
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                maxlength="255"
                required
                placeholder="e.g. Spectral Pulse Detector"
                class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-gray-100 placeholder-gray-500 outline-none transition focus:border-lime-400 focus:ring-2 focus:ring-lime-400/20"
            >
            @error('name')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="type" class="mb-2 block text-sm font-semibold text-gray-200">
                Gear Type
            </label>
            <select
                id="type"
                name="type"
                required
                class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-gray-100 outline-none transition focus:border-lime-400 focus:ring-2 focus:ring-lime-400/20"
            >
                @foreach ($types as $type)
                    <option
                        value="{{ $type }}"
                        {{ old('type', 'trap') === $type ? 'selected' : '' }}
                    >
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="mb-2 block text-sm font-semibold text-gray-200">
                Description
            </label>
            <textarea
                id="description"
                name="description"
                rows="4"
                maxlength="1000"
                required
                placeholder="Describe what this equipment does..."
                class="w-full resize-y rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-gray-100 placeholder-gray-500 outline-none transition focus:border-lime-400 focus:ring-2 focus:ring-lime-400/20"
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="ecto_cost" class="mb-2 block text-sm font-semibold text-gray-200">
                    Price (₱ Ecto)
                </label>
                <input
                    id="ecto_cost"
                    name="ecto_cost"
                    type="number"
                    min="0"
                    step="1"
                    value="{{ old('ecto_cost', 10) }}"
                    required
                    class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-gray-100 outline-none transition focus:border-lime-400 focus:ring-2 focus:ring-lime-400/20"
                >
                @error('ecto_cost')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="energy_cost" class="mb-2 block text-sm font-semibold text-gray-200">
                    Energy Cost
                </label>
                <input
                    id="energy_cost"
                    name="energy_cost"
                    type="number"
                    min="0"
                    step="1"
                    value="{{ old('energy_cost', 5) }}"
                    required
                    class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-gray-100 outline-none transition focus:border-lime-400 focus:ring-2 focus:ring-lime-400/20"
                >
                @error('energy_cost')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="image" class="mb-2 block text-sm font-semibold text-gray-200">
                Product Photo <span class="font-normal text-gray-400">(optional)</span>
            </label>

            <p class="mb-3 text-sm text-gray-400">
                JPG, PNG, or WebP. Maximum file size: 2 MB.
            </p>

            <input
                id="image"
                name="image"
                type="file"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                class="block w-full cursor-pointer rounded-lg border border-dashed border-gray-600 bg-gray-800 text-sm text-gray-300 file:mr-4 file:border-0 file:bg-lime-400 file:px-4 file:py-3 file:font-semibold file:text-gray-950 hover:file:bg-lime-300 focus:outline-none focus:ring-2 focus:ring-lime-400/30"
            >
            @error('image')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror

            <div id="image-preview-container" class="mt-4 hidden">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">
                    Image Preview
                </p>
                <img
                    id="image-preview"
                    alt="Selected gear photo preview"
                    class="max-h-72 w-full rounded-xl border border-gray-700 bg-gray-950 object-contain p-2"
                >
            </div>
        </div>

        <div class="flex flex-wrap gap-3 border-t border-gray-700 pt-6">
            <button
                type="submit"
                class="rounded-lg bg-lime-400 px-5 py-3 font-bold text-gray-950 transition hover:bg-lime-300 focus:outline-none focus:ring-2 focus:ring-lime-400 focus:ring-offset-2 focus:ring-offset-gray-900"
            >
                Add Gear to Shop
            </button>

            <a
                href="{{ url('/gears') }}"
                class="rounded-lg border border-gray-600 bg-gray-800 px-5 py-3 font-semibold text-gray-200 transition hover:bg-gray-700"
            >
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const imageInput = document.getElementById('image');
        const preview = document.getElementById('image-preview');
        const previewContainer = document.getElementById('image-preview-container');
        let previewUrl = null;

        imageInput.addEventListener('change', function () {
            const file = this.files && this.files[0];

            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }

            preview.removeAttribute('src');
            previewContainer.classList.add('hidden');

            if (!file) {
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            const maxSize = 2 * 1024 * 1024;

            if (!allowedTypes.includes(file.type)) {
                alert('Please select a JPG, PNG, or WebP image.');
                this.value = '';
                return;
            }

            if (file.size > maxSize) {
                alert('The image must be 2 MB or smaller.');
                this.value = '';
                return;
            }

            previewUrl = URL.createObjectURL(file);
            preview.src = previewUrl;
            previewContainer.classList.remove('hidden');
        });
    });
</script>
@endsection
