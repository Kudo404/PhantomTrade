@extends('layouts.app')

@section('title', 'Edit Gear — Phantom Trade')

@section('content')
<div class="mx-auto my-10 max-w-3xl rounded-2xl border border-gray-700 bg-gray-900 p-6 text-gray-100 shadow-xl sm:p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight">Edit Gear</h1>
        <p class="mt-2 text-sm text-gray-400">Update gear details, pricing, energy cost, or photo.</p>
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

    <form method="POST" action="{{ url('/gears/'.$gear->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="mb-2 block text-sm font-semibold text-gray-200">Gear Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $gear->name) }}" maxlength="255" required
                class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-gray-100">
            @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="type" class="mb-2 block text-sm font-semibold text-gray-200">Gear Type</label>
            <select id="type" name="type" required class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-gray-100">
                @foreach ($types as $type)
                    <option value="{{ $type }}" {{ old('type', $gear->type) === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            @error('type') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="mb-2 block text-sm font-semibold text-gray-200">Description</label>
            <textarea id="description" name="description" rows="4" maxlength="1000" required
                class="w-full resize-y rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-gray-100">{{ old('description', $gear->description) }}</textarea>
            @error('description') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="ecto_cost" class="mb-2 block text-sm font-semibold text-gray-200">Price (₱ Ecto)</label>
                <input id="ecto_cost" name="ecto_cost" type="number" min="0" step="1" value="{{ old('ecto_cost', $gear->ecto_cost) }}" required
                    class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-gray-100">
                @error('ecto_cost') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="energy_cost" class="mb-2 block text-sm font-semibold text-gray-200">Energy Cost</label>
                <input id="energy_cost" name="energy_cost" type="number" min="0" step="1" value="{{ old('energy_cost', $gear->energy_cost) }}" required
                    class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-gray-100">
                @error('energy_cost') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="image" class="mb-2 block text-sm font-semibold text-gray-200">Replace Product Photo <span class="font-normal text-gray-400">(optional)</span></label>
            @if ($gear->image_path)
                <div class="mb-4">
                    <p class="mb-2 text-xs text-gray-400">Current photo</p>
                    <img src="{{ asset('storage/' . ltrim($gear->image_path, '/')) }}" alt="{{ $gear->name }}" class="max-h-56 rounded-xl border border-gray-700 object-contain"
                        onerror="this.style.display='none'">
                </div>
            @endif
            <p class="mb-3 text-sm text-gray-400">JPG, PNG, or WebP. Maximum file size: 2 MB. Leave blank to keep the current photo.</p>
            <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                class="block w-full cursor-pointer rounded-lg border border-dashed border-gray-600 bg-gray-800 text-sm text-gray-300">
            @error('image') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="flex flex-wrap gap-3 border-t border-gray-700 pt-6">
            <button type="submit" class="rounded-lg bg-lime-400 px-5 py-3 font-bold text-gray-950 hover:bg-lime-300">Save Changes</button>
            <a href="{{ url('/gears') }}" class="rounded-lg border border-gray-600 bg-gray-800 px-5 py-3 font-semibold text-gray-200 hover:bg-gray-700">Cancel</a>
        </div>
    </form>
</div>
@endsection
