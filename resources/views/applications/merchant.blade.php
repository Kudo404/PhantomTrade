@extends('layouts.app')

@section('title', 'Merchant Application — Phantom Trade')

@section('content')

    <div class="max-w-[640px] mt-0 mr-auto mb-0 ml-auto">

        {{-- Header --}}
        <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
            Merchant Application
        </h1>
        <p class="text-[13.5px] text-[var(--text-secondary)] mb-6">
            Become a Certified Merchant and start selling on the marketplace.
        </p>

        @if (! $qualified)
            {{-- Not qualified --}}
            <div class="card pt-7 pr-7 pb-7 pl-7">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-full bg-[var(--bg)] flex items-center justify-center">
                        <i data-lucide="lock" class="w-5 h-5 text-[var(--text-muted)]"></i>
                    </div>
                    <div>
                        <div class="text-[15px] font-semibold text-[var(--text)]">
                            You're not qualified yet
                        </div>
                        <div class="text-[12.5px] text-[var(--text-muted)]">
                            Complete more trades to unlock this application.
                        </div>
                    </div>
                </div>

                {{-- Progress --}}
                <div class="mb-5">
                    <div class="flex justify-between text-[12.5px] mb-1.5">
                        <span class="text-[var(--text-secondary)]">Completed trades</span>
                        <span class="text-[var(--text)] font-semibold">
                            {{ $progress }} / {{ $threshold }}
                        </span>
                    </div>
                    <div class="bg-[var(--bg)] rounded-[20px] h-2 overflow-hidden">
                        <div style="--tw-inline-width:{{ min(100, ($progress / max($threshold, 1)) * 100) }}%" class="h-full rounded-[20px] bg-[var(--accent)] w-[var(--tw-inline-width)]"></div>
                    </div>
                </div>

                <a href="/marketplace" class="btn inline-flex">
                    <i data-lucide="store"></i>
                    <span>Go to Marketplace</span>
                </a>
            </div>
        @else
            {{-- Qualified — show form --}}
            <div class="card pt-7 pr-7 pb-7 pl-7">

                {{-- Success banner --}}
                <div class="flex gap-2.5 pt-3 pr-3.5 pb-3 pl-3.5 bg-[#ecfdf5] [border:1px_solid_#a7f3d0] rounded-lg mb-5 text-[13px] text-[#059669]">
                    <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0 mt-[1px]"></i>
                    <div>
                        <strong class="font-semibold">You qualify!</strong>
                        You've completed {{ $progress }} trades. Fill out the form below.
                    </div>
                </div>

                <form method="POST" action="/apply/merchant">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-[18px]">
                        <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                            Business / Display Name
                        </label>
                        <input type="text" name="name" value="{{ old('name', $prefill['name']) }}" required maxlength="120" class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none"
                              >
                        @error('name') <div class="text-[var(--danger)] text-[12px] mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Contact email + phone --}}
                    <div class="grid [grid-template-columns:1fr_1fr] gap-4 mb-[18px]">
                        <div>
                            <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                                Contact Email
                            </label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $prefill['email']) }}" required maxlength="120" class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none"
                                  >
                            @error('contact_email') <div class="text-[var(--danger)] text-[12px] mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                                Contact Phone
                            </label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" required maxlength="40"
                                   placeholder="+63 9XX XXX XXXX" class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none"
                                  >
                            @error('contact_phone') <div class="text-[var(--danger)] text-[12px] mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="mb-[18px]">
                        <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                            Business Location
                        </label>
                        <input type="text" name="location_name" value="{{ old('location_name', $prefill['location']) }}" required maxlength="120" class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none"
                              >
                        @if ($prefill['latitude'])
                            <div class="text-[11.5px] text-[var(--text-muted)] mt-1">
                                Coordinates: {{ $prefill['latitude'] }}, {{ $prefill['longitude'] }}
                            </div>
                        @else
                            <div class="text-[11.5px] text-[var(--warning)] mt-1">
                                Set your location in <a href="/profile/location" class="text-[var(--accent)]">your profile</a> to auto-fill coordinates.
                            </div>
                        @endif
                        <input type="hidden" name="latitude"  value="{{ old('latitude',  $prefill['latitude']) }}">
                        <input type="hidden" name="longitude" value="{{ old('longitude', $prefill['longitude']) }}">
                        @error('location_name') <div class="text-[var(--danger)] text-[12px] mt-1">{{ $message }}</div> @enderror
                        @error('latitude')      <div class="text-[var(--danger)] text-[12px] mt-1">{{ $message }}</div> @enderror
                        @error('longitude')     <div class="text-[var(--danger)] text-[12px] mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Reason --}}
                    <div class="mb-5">
                        <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                            Why do you want to become a Merchant? <span class="text-[var(--text-muted)] normal-case font-normal">(optional)</span>
                        </label>
                        <textarea name="reason" maxlength="500" placeholder="Tell us a bit about your business goals..." class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none min-h-20 resize-y"
                                 >{{ old('reason') }}</textarea>
                        @error('reason') <div class="text-[var(--danger)] text-[12px] mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2.5 justify-end">
                        <a href="/profile" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn">
                            <i data-lucide="send"></i>
                            <span>Submit Application</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>

@endsection