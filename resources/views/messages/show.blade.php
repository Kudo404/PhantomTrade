@extends('layouts.app')

@section('title', 'Chat with ' . ($other->name ?? 'User') . ' — Phantom Trade')

@section('content')

    <div class="max-w-[820px] mt-0 mr-auto mb-0 ml-auto">

        {{-- ═══ CHAT HEADER ═══ --}}
        <div class="card pt-4 pr-5 pb-4 pl-5 mb-4 flex items-center gap-3.5">

            <a href="/messages"
              
               class="flex items-center justify-center w-8 h-8 rounded-md text-[var(--text-secondary)] no-underline shrink-0  hover:bg-[var(--bg)]">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>

            @if ($other)
                @php $isSupport = $other->isCustomerService(); @endphp

                <div class="relative shrink-0">
                    <div class="w-10 h-10 rounded-full {{ $isSupport ? 'bg-[linear-gradient(135deg,_#7c3aed,_#a78bfa)]' : 'bg-[var(--accent)]' }} text-[#fff] flex items-center justify-center font-semibold text-[16px]">
                        {{ strtoupper(substr($other->name, 0, 1)) }}
                    </div>
                    @if ($isSupport)
                        <div class="absolute -bottom-[2px] -right-[2px] w-[18px] h-[18px] rounded-full bg-[#7c3aed] [border:2px_solid_var(--surface)] flex items-center justify-center">
                            <i data-lucide="headset" class="w-[9px] h-[9px] text-[#fff]"></i>
                        </div>
                    @endif
                </div>

                <div class="grow min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-[14.5px] font-semibold text-[var(--text)]">
                            <x-user-name :user="$other" />
                        </span>
                        @if ($isSupport)
                            <span class="bg-[rgba(124,58,237,0.12)] text-[#7c3aed] pt-0.5 pr-2 pb-0.5 pl-2 rounded-[10px] text-[9.5px] font-bold uppercase tracking-[0.05em]">
                                Customer Service
                            </span>
                        @endif
                    </div>
                    <div class="text-[11.5px] text-[var(--text-muted)] mt-0.5">
                        {{ $other->roleLabel() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- ═══ MESSAGES LIST ═══ --}}
        <div class="card pt-5 pr-5 pb-5 pl-5 overflow-hidden">
            <div id="messagesList" class="flex flex-col gap-2.5 max-h-[520px] overflow-y-auto pr-1.5">

                @forelse ($messages as $msg)
                    @php $isMine = $msg->sender_id === auth()->id(); @endphp

                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] [{{ $ismine ? 'background:var(--accent); color:#fff;' : 'background:var(--bg); color:var(--text);' }} padding:10px_14px] rounded-[14px] {{ $isMine ? '[border-bottom-right-radius:4px]' : '[border-bottom-left-radius:4px]' }}">
                            <div class="text-[13.5px] leading-[1.5] break-words [white-space:pre-wrap]">{{ $msg->body }}</div>
                            <div class="text-[10.5px] mt-1 [{{ $ismine ? 'color:rgba(255,255,255,0.7);' : 'color:var(--text-muted);' }} text-align:right]">
                                {{ $msg->created_at->format('g:i A') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center pt-10 pr-5 pb-10 pl-5 text-[var(--text-muted)] text-[13px]">
                        No messages yet. Say hi!
                    </div>
                @endforelse

            </div>
        </div>

        {{-- ═══ SEND FORM ═══ --}}
        <div class="card pt-4 pr-4 pb-4 pl-4 mt-4">
            <form id="sendForm" method="POST" action="/messages/{{ $conversation->id }}" class="flex gap-2.5 items-end mt-0 mr-0 mb-0 ml-0">
                @csrf

                <textarea name="body" id="messageInput" required maxlength="2000"
                          placeholder="Type a message…"
                          rows="2" class="flex-1 pt-2.5 pr-3.5 pb-2.5 pl-3.5 [border:1px_solid_var(--border)] rounded-lg bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none resize-none min-h-11 max-h-[120px]"
                         ></textarea>

                <button type="submit" class="btn pt-[11px] pr-[18px] pb-[11px] pl-[18px]">
                    <i data-lucide="send"></i>
                    <span>Send</span>
                </button>
            </form>
        </div>

    </div>

    {{-- ═══ CHAT SCRIPT ═══ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const list        = document.getElementById('messagesList');
            const form        = document.getElementById('sendForm');
            const input       = document.getElementById('messageInput');
            const conversationId = {{ $conversation->id }};
            const meId        = {{ auth()->id() }};

            let lastMessageId = {{ $messages->last()->id ?? 0 }};
            let polling       = true;

            // ── Auto-scroll to bottom on load ──
            function scrollToBottom() {
                list.scrollTop = list.scrollHeight;
            }
            scrollToBottom();

            // ── Render a message bubble ──
            function renderMessage(msg) {
                const wrapper = document.createElement('div');
                wrapper.style.cssText = 'display:flex; ' + (msg.is_mine ? 'justify-content:flex-end;' : 'justify-content:flex-start;');

                const bubble = document.createElement('div');
                bubble.style.cssText = 'max-width:75%; ' +
                    (msg.is_mine ? 'background:var(--accent); color:#fff;' : 'background:var(--bg); color:var(--text);') +
                    ' padding:10px 14px; border-radius:14px; ' +
                    (msg.is_mine ? 'border-bottom-right-radius:4px;' : 'border-bottom-left-radius:4px;');

                // Body
                const body = document.createElement('div');
                body.style.cssText = 'font-size:13.5px; line-height:1.5; word-wrap:break-word; white-space:pre-wrap;';
                body.textContent = msg.body;
                bubble.appendChild(body);

                // Time
                const time = document.createElement('div');
                time.style.cssText = 'font-size:10.5px; margin-top:4px; ' +
                    (msg.is_mine ? 'color:rgba(255,255,255,0.7);' : 'color:var(--text-muted);') +
                    ' text-align:right;';
                time.textContent = msg.time;
                bubble.appendChild(time);

                wrapper.appendChild(bubble);
                return wrapper;
            }

            // ── Send message via AJAX ──
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const body = input.value.trim();
                if (! body) return;

                // Disable input to prevent double-send
                const sendBtn = form.querySelector('button[type="submit"]');
                input.disabled = true;
                sendBtn.disabled = true;

                try {
                    const res = await fetch('/messages/' + conversationId, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ body: body })
                    });

                    if (res.ok) {
                        const data = await res.json();
                        if (data.message) {
                            // Clear the "no messages" placeholder if present
                            if (list.children.length === 1 && list.textContent.includes('No messages yet')) {
                                list.innerHTML = '';
                            }
                            list.appendChild(renderMessage(data.message));
                            lastMessageId = data.message.id;
                            scrollToBottom();
                        }
                        input.value = '';
                    } else {
                        console.warn('Send failed:', res.status);
                    }
                } catch (err) {
                    console.warn('Send error:', err);
                } finally {
                    input.disabled = false;
                    sendBtn.disabled = false;
                    input.focus();
                }
            });

            // ── Enter to send, Shift+Enter for newline ──
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && ! e.shiftKey) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            });

            // ── Poll for new messages every 5 seconds ──
            async function poll() {
                if (! polling) return;

                try {
                    const res = await fetch('/messages/' + conversationId + '/poll?after_id=' + lastMessageId, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (res.ok) {
                        const data = await res.json();
                        if (data.messages && data.messages.length > 0) {
                            if (list.children.length === 1 && list.textContent.includes('No messages yet')) {
                                list.innerHTML = '';
                            }
                            data.messages.forEach(function (msg) {
                                list.appendChild(renderMessage(msg));
                                lastMessageId = msg.id;
                            });
                            scrollToBottom();
                        }
                    }
                } catch (err) {
                    // Silent fail — will retry
                }

                setTimeout(poll, 5000);
            }

            setTimeout(poll, 5000);

            // ── Auto-grow the textarea ──
            input.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });
        });
    </script>

    <style>
        /* Smooth scrollbar for messages */
        #messagesList::-webkit-scrollbar { width: 6px; }
        #messagesList::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        #messagesList::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }
    </style>

@endsection