@extends('layouts.app')

@section('title', 'Chat with ' . ($other->name ?? 'User') . ' — Phantom Trade')

@section('content')

    <div style="max-width:820px; margin:0 auto;">

        {{-- ═══ CHAT HEADER ═══ --}}
        <div class="card" style="padding:16px 20px; margin-bottom:16px; display:flex; align-items:center; gap:14px;">

            <a href="/messages" style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:6px; color:var(--text-secondary); text-decoration:none; flex-shrink:0;"
               onmouseover="this.style.background='var(--bg)';"
               onmouseout="this.style.background='transparent';">
                <i data-lucide="arrow-left" style="width:16px; height:16px;"></i>
            </a>

            @if ($other)
                @php $isSupport = $other->isCustomerService(); @endphp

                <div style="position:relative; flex-shrink:0;">
                    <div style="width:40px; height:40px; border-radius:50%; background:{{ $isSupport ? 'linear-gradient(135deg, #7c3aed, #a78bfa)' : 'var(--accent)' }}; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:16px;">
                        {{ strtoupper(substr($other->name, 0, 1)) }}
                    </div>
                    @if ($isSupport)
                        <div style="position:absolute; bottom:-2px; right:-2px; width:18px; height:18px; border-radius:50%; background:#7c3aed; border:2px solid var(--surface); display:flex; align-items:center; justify-content:center;">
                            <i data-lucide="headset" style="width:9px; height:9px; color:#fff;"></i>
                        </div>
                    @endif
                </div>

                <div style="flex-grow:1; min-width:0;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:14.5px; font-weight:600; color:var(--text);">
                            <x-user-name :user="$other" />
                        </span>
                        @if ($isSupport)
                            <span style="background:rgba(124,58,237,0.12); color:#7c3aed; padding:2px 8px; border-radius:10px; font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                                Customer Service
                            </span>
                        @endif
                    </div>
                    <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">
                        {{ $other->roleLabel() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- ═══ MESSAGES LIST ═══ --}}
        <div class="card" style="padding:20px; overflow:hidden;">
            <div id="messagesList" style="display:flex; flex-direction:column; gap:10px; max-height:520px; overflow-y:auto; padding-right:6px;">

                @forelse ($messages as $msg)
                    @php $isMine = $msg->sender_id === auth()->id(); @endphp

                    <div style="display:flex; {{ $isMine ? 'justify-content:flex-end;' : 'justify-content:flex-start;' }}">
                        <div style="max-width:75%; {{ $isMine ? 'background:var(--accent); color:#fff;' : 'background:var(--bg); color:var(--text);' }} padding:10px 14px; border-radius:14px; {{ $isMine ? 'border-bottom-right-radius:4px;' : 'border-bottom-left-radius:4px;' }}">
                            <div style="font-size:13.5px; line-height:1.5; word-wrap:break-word; white-space:pre-wrap;">{{ $msg->body }}</div>
                            <div style="font-size:10.5px; margin-top:4px; {{ $isMine ? 'color:rgba(255,255,255,0.7);' : 'color:var(--text-muted);' }} text-align:right;">
                                {{ $msg->created_at->format('g:i A') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; padding:40px 20px; color:var(--text-muted); font-size:13px;">
                        No messages yet. Say hi!
                    </div>
                @endforelse

            </div>
        </div>

        {{-- ═══ SEND FORM ═══ --}}
        <div class="card" style="padding:16px; margin-top:16px;">
            <form id="sendForm" method="POST" action="/messages/{{ $conversation->id }}" style="display:flex; gap:10px; align-items:flex-end; margin:0;">
                @csrf

                <textarea name="body" id="messageInput" required maxlength="2000"
                          placeholder="Type a message…"
                          rows="2"
                          style="flex:1; padding:10px 14px; border:1px solid var(--border); border-radius:8px; background:var(--surface); color:var(--text); font-size:13.5px; font-family:inherit; outline:none; resize:none; min-height:44px; max-height:120px;"></textarea>

                <button type="submit" class="btn" style="padding:11px 18px;">
                    <i data-lucide="send"></i>
                    <span>Send</span>
                </button>
            </form>
        </div>

    </div>

    {{-- ═══ CHAT SCRIPT ═══ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const list           = document.getElementById('messagesList');
            const form           = document.getElementById('sendForm');
            const input          = document.getElementById('messageInput');
            const conversationId = {{ $conversation->id }};
            const meId           = {{ auth()->id() }};

            let lastMessageId = {{ $messages->last()->id ?? 0 }};
            let polling       = true;

            function scrollToBottom() {
                list.scrollTop = list.scrollHeight;
            }
            scrollToBottom();

            function renderMessage(msg) {
                const wrapper = document.createElement('div');
                wrapper.style.cssText = 'display:flex; ' + (msg.is_mine ? 'justify-content:flex-end;' : 'justify-content:flex-start;');

                const bubble = document.createElement('div');
                bubble.style.cssText = 'max-width:75%; ' +
                    (msg.is_mine ? 'background:var(--accent); color:#fff;' : 'background:var(--bg); color:var(--text);') +
                    ' padding:10px 14px; border-radius:14px; ' +
                    (msg.is_mine ? 'border-bottom-right-radius:4px;' : 'border-bottom-left-radius:4px;');

                const body = document.createElement('div');
                body.style.cssText = 'font-size:13.5px; line-height:1.5; word-wrap:break-word; white-space:pre-wrap;';
                body.textContent = msg.body;
                bubble.appendChild(body);

                const time = document.createElement('div');
                time.style.cssText = 'font-size:10.5px; margin-top:4px; ' +
                    (msg.is_mine ? 'color:rgba(255,255,255,0.7);' : 'color:var(--text-muted);') +
                    ' text-align:right;';
                time.textContent = msg.time;
                bubble.appendChild(time);

                wrapper.appendChild(bubble);
                return wrapper;
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const body = input.value.trim();
                if (! body) return;

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

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && ! e.shiftKey) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            });

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
                } catch (err) {}

                setTimeout(poll, 5000);
            }

            setTimeout(poll, 5000);

            input.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });
        });
    </script>

    <style>
        #messagesList::-webkit-scrollbar { width: 6px; }
        #messagesList::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        #messagesList::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }
    </style>

@endsection