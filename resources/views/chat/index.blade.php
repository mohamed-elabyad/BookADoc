<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Chats' => '#']" />

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white mb-2">Chats</h1>
        <p class="text-white/80">Your conversations</p>
    </div>

    @forelse ($conversations as $conversation)
        @php
            $other_user = $conversation->getOtherUser(auth()->id());
            $latest_message = $conversation->latestMessage;
            $unread_count = app(\App\Services\ChatService::class)->unreadCount($conversation, auth()->user());
        @endphp

        <a href="{{ route('chat.show', $conversation) }}" class="block mb-4">
            <x-card class="hover:shadow-xl transition-shadow duration-200 cursor-pointer">
                <div class="flex flex-col md:flex-row items-start justify-between w-full gap-4">

                    {{-- الصورة والاسم (أقصى الشمال) --}}
                    <div class="flex items-center gap-3 flex-shrink-0 min-w-0 w-full md:w-auto">
                        @if (auth()->user()->role->value === 'user' && $other_user->doctor)
                            <x-img :doctor="$other_user->doctor" class="!w-17 !h-17 !m-0" />
                        @endif

                        <div class="min-w-0 flex-1 pt-0">
                            <h3 class="text-lg font-semibold text-slate-800 truncate">
                                @if (auth()->user()->role->value === 'user')
                                    Dr. {{ $other_user->name }}
                                @else
                                    {{ $other_user->name }}
                                @endif
                            </h3>

                        </div>
                    </div>

                    <div class="pt-0 md:pt-5 pl-0 md:pl-15 w-full md:w-auto">

                        @if ($latest_message)
                            <p class="text-sm text-slate-600 truncate">
                                {{ Str::limit($latest_message->message, 30) }}
                            </p>
                        @else
                            <p class="text-sm text-slate-400 italic">No messages yet</p>
                        @endif
                    </div>
                    {{-- الوقت وعدد الرسائل (أقصى اليمين) --}}
                    <div class="flex flex-row md:flex-col items-center md:items-end gap-2 flex-shrink-0 ml-auto">
                        @if ($latest_message)
                            <span class="text-xs text-slate-500 whitespace-nowrap">
                                {{ $latest_message->created_at->diffForHumans() }}
                            </span>
                        @endif

                        @if ($unread_count > 0)
                            <span
                                class="inline-flex items-center justify-center min-w-[24px] h-6 px-2 text-xs font-bold text-white bg-purple-600 rounded-full">
                                {{ $unread_count }}
                            </span>
                        @endif
                    </div>
                </div>
            </x-card>
        </a>
    @empty
        <x-card>
            <div class="text-center py-8">
                <p class="text-xl font-semibold text-slate-600 mb-2">No conversations yet</p>
                <p class="text-slate-500 mb-4">Start a conversation from your appointments</p>
                <x-link-button href="{{ route('appointments.index') }}">
                    View Appointments
                </x-link-button>
            </div>
        </x-card>
    @endforelse
</x-layout>
