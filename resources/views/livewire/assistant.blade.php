<div class="inv-assistant">
    <style>
        .inv-assistant { font-family: 'IBM Plex Sans', -apple-system, sans-serif; }
        .inv-assistant * { box-sizing: border-box; }
        .inv-fab {
            position: fixed; right: 24px; bottom: 24px; z-index: 80;
            display: flex; align-items: center; gap: 10px;
            background: #14213D; color: #FAF9F6; border: none; border-radius: 10px;
            padding: 12px 18px; cursor: pointer; box-shadow: 0 8px 24px rgba(15,20,32,.28);
            font-size: 14px; text-align: left;
        }
        .inv-fab:hover { background: #3C4A63; }
        .inv-fab .orb {
            width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #E8A33D, #C6842A); color: #14213D;
        }
        .inv-fab b { display: block; font-size: 13px; }
        .inv-fab span { display: block; font-size: 12px; color: #A7B1C2; }
        .inv-panel {
            position: fixed; right: 24px; bottom: 24px; z-index: 90;
            width: min(380px, calc(100vw - 48px)); height: min(520px, calc(100vh - 96px));
            background: #FAF9F6; border: 1px solid #DAD5C9; border-radius: 12px;
            display: flex; flex-direction: column; overflow: hidden;
            box-shadow: 0 16px 48px rgba(15,20,32,.30);
        }
        .inv-panel header {
            display: flex; align-items: center; gap: 10px;
            background: #14213D; color: #FAF9F6; padding: 14px 16px;
        }
        .inv-panel header b { font-size: 14px; }
        .inv-panel header span { display: block; font-size: 11px; color: #A7B1C2; font-family: 'IBM Plex Mono', monospace; }
        .inv-panel .close {
            margin-left: auto; background: none; border: none; color: #A7B1C2;
            font-size: 18px; cursor: pointer; line-height: 1; padding: 4px;
        }
        .inv-msgs { flex: 1; overflow-y: auto; padding: 14px; display: flex; flex-direction: column; gap: 10px; }
        .inv-msg { max-width: 85%; padding: 9px 12px; border-radius: 10px; font-size: 14px; line-height: 1.45; white-space: pre-wrap; }
        .inv-msg.user { align-self: flex-end; background: #14213D; color: #FAF9F6; border-bottom-right-radius: 2px; }
        .inv-msg.assistant { align-self: flex-start; background: #F1EEE7; color: #14213D; border: 1px solid #DAD5C9; border-bottom-left-radius: 2px; }
        .inv-typing { align-self: flex-start; color: #5C6B7A; font-size: 13px; font-style: italic; }
        .inv-sugg { display: flex; gap: 8px; flex-wrap: wrap; padding: 0 14px 10px; }
        .inv-sugg button {
            border: 1px solid #DAD5C9; background: #fff; color: #3C4A63;
            border-radius: 999px; padding: 6px 12px; font-size: 12px; cursor: pointer;
        }
        .inv-sugg button:hover { border-color: #C6842A; color: #14213D; }
        .inv-form { display: flex; gap: 8px; padding: 12px; border-top: 1px solid #DAD5C9; background: #fff; }
        .inv-form input {
            flex: 1; border: 1px solid #DAD5C9; border-radius: 8px;
            padding: 9px 12px; font-size: 14px; font-family: inherit;
        }
        .inv-form input:focus { outline: 2px solid #E8A33D; border-color: transparent; }
        .inv-form button {
            background: #14213D; color: #FAF9F6; border: none; border-radius: 8px;
            padding: 0 16px; font-size: 14px; cursor: pointer;
        }
        .inv-form button:disabled { opacity: .5; cursor: default; }
    </style>

    @if (!$isOpen)
        <button type="button" class="inv-fab" wire:click="toggle" aria-label="Open invenbin assistant">
            <span class="orb">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594l1.051-5.558a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594z"/><path d="M20 2v4"/><path d="M22 4h-4"/><circle cx="4" cy="20" r="2"/></svg>
            </span>
            <span><b>invenbin assistant</b><span>Ready when you are. Ask me anything.</span></span>
        </button>
    @else
        <div class="inv-panel" role="dialog" aria-label="invenbin assistant">
            <header>
                <span class="orb" style="width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#E8A33D,#C6842A);color:#14213D;flex-shrink:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0-1.966l-5.558-1.051a2 2 0 0 0-1.594 1.594z"/><path d="M20 2v4"/><path d="M22 4h-4"/><circle cx="4" cy="20" r="2"/></svg>
                </span>
                <div><b>invenbin assistant</b><span>your operations copilot</span></div>
                <button type="button" class="close" wire:click="toggle" aria-label="Close">&times;</button>
            </header>

            <div class="inv-msgs" x-data
                 x-init="new MutationObserver(() => { $el.scrollTop = $el.scrollHeight }).observe($el, { childList: true, subtree: true }); $el.scrollTop = $el.scrollHeight;">
                @if (!$authorized)
                    <div class="inv-msg assistant">You need to be signed in as a member of this workspace to use the assistant.</div>
                @elseif (empty($messages))
                    <div class="inv-msg assistant">What can I help you find? Ask about inventory, suppliers, or what needs your attention today.</div>
                @endif
                @foreach ($messages as $message)
                    <div class="inv-msg {{ $message['role'] }}">{{ $message['text'] }}</div>
                @endforeach
                <div class="inv-typing" wire:loading wire:target="sendMessage,askSuggestion">thinking…</div>
            </div>

            @if (empty($messages) && $authorized)
                <div class="inv-sugg">
                    <button type="button" wire:click="askSuggestion('What\'s at risk?')">What's at risk?</button>
                    <button type="button" wire:click="askSuggestion('Late shipments')">Late shipments</button>
                </div>
            @endif

            <form class="inv-form" wire:submit.prevent="sendMessage">
                <input type="text" wire:model="userInput" placeholder="Ask about inventory, suppliers, orders…" @if(!$authorized) disabled @endif />
                <button type="submit" wire:loading.attr="disabled" @if(!$authorized) disabled @endif>Ask</button>
            </form>
        </div>
    @endif
</div>
