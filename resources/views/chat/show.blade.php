<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Chats' => route('chat.index'), 'Conversation' => '#']" />

    @php
        $other_user = $conversation->getOtherUser(auth()->id());
    @endphp

    <div x-data="chatApp({{ $conversation->id }}, {{ auth()->id() }})" x-init="init()" class="flex flex-col max-w-4xl mx-auto mb-8"
        style="height: calc(100vh - 250px); min-height: 500px;">

        {{-- Header --}}
        <x-card class="mb-4 rounded-b-none flex-shrink-0 !mx-6">
            <div class="flex items-center gap-4">
                @if (auth()->user()->role->value === 'user' && $other_user->doctor)
                    <x-img :doctor="$other_user->doctor" class="!w-20 !h-20 !m-0" />
                @endif

                <div>
                    <h2 class="text-xl font-semibold text-slate-800">
                        @if (auth()->user()->role->value === 'user')
                            Dr. {{ $other_user->name }}
                        @else
                            {{ $other_user->name }}
                        @endif
                    </h2>
                </div>
            </div>
        </x-card>

        {{-- Messages Container --}}
        <x-card class="flex-1 flex flex-col overflow-hidden rounded-t-none rounded-b-none mb-4 p-0 !mx-6 min-h-0">
            <div x-ref="messagesContainer" class="flex-1 min-h-0 overflow-y-auto p-4 space-y-4 w-full">

                {{-- الرسائل الموجودة --}}
                @foreach ($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div
                            class="max-w-[70%] flex gap-2 items-end {{ $message->sender_id === auth()->id() ? 'flex-row-reverse' : 'flex-row' }}">

                            @if ($message->sender_id !== auth()->id() && auth()->user()->role->value === 'user' && $message->sender->doctor)
                                <x-img :doctor="$message->sender->doctor" class="!w-10 !h-10 !m-0 flex-shrink-0" />
                            @endif

                            <div class="flex flex-col">
                                <div
                                    class="px-4 py-2 rounded-2xl {{ $message->sender_id === auth()->id() ? 'bg-purple-600 text-white' : 'bg-gray-200 text-slate-800' }}">
                                    <p class="text-sm break-words"
                                        style="overflow-wrap: anywhere; word-break: break-word;">
                                        {{ $message->message }}
                                    </p>
                                </div>
                                <p
                                    class="text-xs text-slate-500 mt-1 {{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                                    {{ $message->created_at->format('h:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- الرسائل الجديدة --}}
                <template x-for="(message, index) in newMessages"
                    :key="`msg-${message.id || index}-${message.created_at || Date.now()}`">
                    <div class="flex" :class="message.sender_id === currentUserId ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[70%] flex gap-2 items-end"
                            :class="message.sender_id === currentUserId ? 'flex-row-reverse' : 'flex-row'">

                            <template x-if="message.sender_id !== currentUserId && message.sender_image">
                                <img :src="message.sender_image" :alt="message.sender_name"
                                    class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            </template>

                            <div class="flex flex-col">
                                <div class="px-4 py-2 rounded-2xl"
                                    :class="message.sender_id === currentUserId ? 'bg-purple-600 text-white' :
                                        'bg-gray-200 text-slate-800'">
                                    <p class="text-sm break-words"
                                        style="overflow-wrap: anywhere; word-break: break-word;"
                                        x-text="message.message"></p>
                                </div>
                                <p class="text-xs text-slate-500 mt-1"
                                    :class="message.sender_id === currentUserId ? 'text-right' : 'text-left'"
                                    x-text="formatTime(message.created_at)"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </x-card>

        {{-- Input --}}
        <x-card class="rounded-t-none p-0 flex-shrink-0 !mx-6">
            <form @submit.prevent="sendMessage()" class="flex gap-2 p-4">
                <input type="text" x-model="messageText" placeholder="Type a message..."
                    class="flex-1 rounded-lg border-0 py-2 px-4 text-sm text-gray-700 placeholder-gray-400 ring-1 ring-gray-300 focus:ring-2 focus:ring-purple-500 focus:ring-offset-0 shadow-sm"
                    :disabled="sending" @keydown.enter.prevent="sendMessage()">
                <x-button type="submit" x-bind:disabled="!canSend">
                    <span x-show="!sending">Send</span>
                    <span x-show="sending">Sending...</span>
                </x-button>
            </form>
        </x-card>
    </div>

    @push('scripts')
        <script>
            function chatApp(conversationId, currentUserId) {
                return {
                    conversationId: conversationId,
                    currentUserId: currentUserId,
                    messageText: '',
                    newMessages: [],
                    sending: false,
                    echo: null,

                    get canSend() {
                        return this.messageText && this.messageText.trim().length > 0 && !this.sending;
                    },

                    init() {
                        setTimeout(() => {
                            if (!window.Echo) {
                                console.error('Echo not available');
                                return;
                            }

                            const channelName = `conversation.${this.conversationId}`;
                            const fullChannelName = `private-${channelName}`;

                            const messageHandler = (e) => {
                                if (e.sender_id !== this.currentUserId) {
                                    const messageExists = this.newMessages.some(msg => msg.id === e.id);
                                    if (messageExists) return;

                                    if (!e.id) {
                                        console.error('Message missing ID:', e);
                                        return;
                                    }

                                    this.newMessages.push({
                                        id: e.id,
                                        conversation_id: e.conversation_id,
                                        sender_id: e.sender_id,
                                        sender_name: e.sender_name,
                                        sender_image: e.sender_image,
                                        message: e.message,
                                        is_read: e.is_read,
                                        created_at: e.created_at
                                    });

                                    this.scrollToBottom();
                                    this.markAsRead();
                                }
                            };

                            this.echo = window.Echo.private(channelName)
                                .subscribed(() => {
                                    setTimeout(() => {
                                        const pusherChannel = window.Echo.connector.pusher.channels
                                            .channels[fullChannelName];
                                        if (pusherChannel) {
                                            pusherChannel.bind('message.sent', messageHandler);
                                        }
                                    }, 100);
                                })
                                .error((error) => {
                                    console.error('Subscription error:', error);
                                });

                            this.echo.listen('message.sent', messageHandler);
                        }, 500);

                        this.$nextTick(() => {
                            this.scrollToBottom();
                            this.markAsRead();
                        });
                    },

                    sendMessage() {
                        if (!this.messageText.trim() || this.sending) return;

                        this.sending = true;
                        const messageContent = this.messageText.trim();
                        this.messageText = '';

                        axios.post(`/chat/${this.conversationId}/messages`, {
                                message: messageContent
                            })
                            .then(response => {
                                if (response.data.success && response.data.message) {
                                    const messageExists = this.newMessages.some(msg => msg.id === response.data.message.id);
                                    if (!messageExists) {
                                        this.newMessages.push(response.data.message);
                                        this.scrollToBottom();
                                    }
                                }
                            })
                            .catch(error => {
                                alert('Failed to send message. Please try again.');
                                this.messageText = messageContent;
                            })
                            .finally(() => {
                                this.sending = false;
                            });
                    },

                    scrollToBottom() {
                        this.$nextTick(() => {
                            const container = this.$refs.messagesContainer;
                            if (container) {
                                container.scrollTop = container.scrollHeight;
                            }
                        });
                    },

                    markAsRead() {
                        axios.patch(`/chat/${this.conversationId}/read`)
                            .catch(error => console.error('Error marking as read:', error));
                    },

                    formatTime(timestamp) {
                        const date = new Date(timestamp);
                        return date.toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });
                    },

                    destroy() {
                        if (this.echo) {
                            window.Echo.leave(`conversation.${this.conversationId}`);
                        }
                    }
                }
            }
        </script>
    @endpush
</x-layout>
