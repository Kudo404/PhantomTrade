@extends('layouts.app')

@section('title', 'Messages — Phantom Trade')

@section('content')

    <div class="max-w-[900px] mt-0 mr-auto mb-0 ml-auto">

        {{-- ═══ PAGE HEADER ═══ --}}
        <div class="flex justify-between items-end gap-4 mb-6 flex-wrap">
            <div>
                <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                    Messages
                </h1>
                <p class="text-[13.5px] text-[var(--text-secondary)]">
                    Private conversations with other users.
                </p>
            </div>

            <a href="/messages/support" class="btn">
                <i data-lucide="headset"></i>
                <span>Contact Support</span>
            </a>
        </div>

        {{-- ═══ CONVERSATIONS LIST ═══ --}}
        @if ($conversations->count())

            <div class="card overflow-hidden">
                @foreach ($conversations as $conv)
                    @php
                        $me       = auth()->user();
                        $other    = $conv->otherUser($me);
                        $lastMsg  = $conv->latestMessage;
                        $unread   = $conv->unreadCountFor($me);
                        $isSupport = $other && $other->isCustomerService();
                    @endphp

                    <a href="/messages/{{ $conv->id }}"
                      
                       class="flex items-center gap-3.5 pt-4 pr-5 pb-4 pl-5 [border-bottom:1px_solid_var(--border-subtle)] no-underline [transition:background_0.12s] hover:bg-[var(--bg)] {{ $unread > 0 ? 'bg-[rgba(15,23,42,0.02)]' : '' }}">

                        {{-- Avatar --}}
                        <div class="relative shrink-0">
                            <div class="w-11 h-11 rounded-full {{ $isSupport ? 'bg-[linear-gradient(135deg,_#7c3aed,_#a78bfa)]' : 'bg-[var(--accent)]' }} text-[#fff] flex items-center justify-center font-semibold text-[16px]">
                                {{ strtoupper(substr($other->name ?? '?', 0, 1)) }}
                            </div>
                            @if ($isSupport)
                                <div class="absolute -bottom-[2px] -right-[2px] w-5 h-5 rounded-full bg-[#7c3aed] [border:2px_solid_var(--surface)] flex items-center justify-center">
                                    <i data-lucide="headset" class="w-2.5 h-2.5 text-[#fff]"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="grow min-w-0">
                            <div class="flex items-center justify-between gap-2.5 mb-[3px]">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-[13.5px] font-semibold text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                        @if ($other)
                                            <x-user-name :user="$other" />
                                        @else
                                            Unknown User
                                        @endif
                                    </span>
                                    @if ($isSupport)
                                        <span class="bg-[rgba(124,58,237,0.12)] text-[#7c3aed] pt-0.5 pr-2 pb-0.5 pl-2 rounded-[10px] text-[9.5px] font-bold uppercase tracking-[0.05em] whitespace-nowrap">
                                            Support
                                        </span>
                                    @endif
                                </div>
                                @if ($lastMsg)
                                    <span class="text-[11px] text-[var(--text-muted)] whitespace-nowrap shrink-0">
                                        {{ $lastMsg->created_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-2.5">
                                <div class="text-[12.5px] {{ $unread > 0 ? 'text-[var(--text-secondary)]' : 'text-[var(--text-muted)]' }} whitespace-nowrap overflow-hidden text-ellipsis {{ $unread > 0 ? 'font-medium' : '' }}">
                                    @if ($lastMsg)
                                        @if ($lastMsg->sender_id === $me->id)
                                            <span class="text-[var(--text-muted)]">You:</span>
                                        @endif
                                        {{ \Illuminate\Support\Str::limit($lastMsg->body, 60) }}
                                    @else
                                        No messages yet.
                                    @endif
                                </div>

                                @if ($unread > 0)
                                    <span class="bg-[#e11d48] text-[#fff] text-[10.5px] font-bold pt-0.5 pr-2 pb-0.5 pl-2 rounded-[10px] min-w-5 text-center shrink-0">
                                        {{ $unread }}
                                    </span>
                                @endif
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>

        @else

            {{-- ═══ EMPTY STATE ═══ --}}
            <div class="[border:1px_dashed_var(--border)] rounded-lg bg-[var(--surface)] pt-16 pr-6 pb-16 pl-6 text-center">
                <div class="w-14 h-14 mt-0 mr-auto mb-4 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                    <i data-lucide="message-circle" class="w-6 h-6 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[15px] font-semibold text-[var(--text)] mb-1.5">
                    No conversations yet
                </div>
                <div class="text-[13px] text-[var(--text-muted)] mb-5 max-w-[400px] ml-auto mr-auto leading-[1.6]">
                    Start a conversation by messaging a merchant, hunter, or our Customer Service Crew.
                </div>
                <a href="/messages/support" class="btn inline-flex">
                    <i data-lucide="headset"></i>
                    <span>Contact Customer Service</span>
                </a>
            </div>

        @endif

    </div>

@endsection