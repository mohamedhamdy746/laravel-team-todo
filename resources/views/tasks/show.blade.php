<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $task->title ?? 'Task Details' }}
        </h2>
    </x-slot>

@php
    $priority = strtolower($task->priority ?? 'medium');
    $priorityClasses = match ($priority) {
        'urgent' => 'bg-purple-100 text-purple-700 ring-1 ring-purple-200',
        'high' => 'bg-rose-100 text-rose-700 ring-1 ring-rose-200',
        'medium' => 'bg-sky-100 text-sky-700 ring-1 ring-sky-200',
        'low' => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
        default => 'bg-gray-100 text-gray-600 ring-1 ring-gray-200',
    };
    $tags = array_values(array_filter((array) ($task->tags ?? [])));
    $labels = array_values(array_filter((array) ($task->labels ?? [])));
    $subtasks = collect($task->subtasks ?? [])->all();
    $subtasksDone = count(array_filter($subtasks, fn ($s) => $s['completed'] ?? false));
    $labelClasses = 'text-sm font-bold text-slate-400 uppercase tracking-wider block mb-1';
    $taskColor = $task->color ?? '#3b82f6';
@endphp

    <div class="mx-auto max-w-5xl">
        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-blue-600 transition-all group">
                <div class="h-10 w-10 rounded-xl bg-white shadow-sm border border-gray-100 flex items-center justify-center group-hover:bg-blue-50 group-hover:border-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </div>
                <span class="hidden sm:inline">Dashboard</span>
            </a>

            <div class="flex items-center gap-3">
                @if(!$task->trashed())
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete this task? Choose OK for Yes or Cancel for No.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2.5 rounded-xl bg-white border border-gray-100 text-slate-400 hover:text-rose-600 transition-all shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </form>
                    <a href="{{ route('tasks.edit', $task->id) }}" class="inline-flex items-center gap-2 bg-slate-900 text-white px-5 py-2.5 rounded-xl font-black text-sm shadow-lg hover:bg-blue-600 transition-all">Edit Task</a>
                @else
                    <form action="{{ route('tasks.restore', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-black text-sm shadow-lg hover:bg-emerald-500 transition-all">Restore Task</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 opacity-10 rounded-bl-[5rem] -mr-8 -mt-8" style="background-color: {{ $taskColor }}"></div>

                    <div class="relative">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="px-4 py-1.5 rounded-xl text-[11px] font-black uppercase tracking-widest {{ $priorityClasses }}">{{ $priority }}</span>
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            <span class="text-xs font-mono font-black text-slate-400">{{ $task->board_column ?? 'General' }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-5xl font-black text-slate-900 leading-[1.1] tracking-tight mb-8">{{ $task->title ?? 'Untitled' }}</h1>

                        <div class="space-y-4">
                            <span class="{{ $labelClasses }}">Description</span>
                            <div class="text-xl leading-relaxed text-slate-600 font-medium">{{ $task->description ?? 'No description provided.' }}</div>
                        </div>

                        <div>
                            <span class="text-[12px] font-black text-slate-400 uppercase tracking-widest block mt-4">Images</span>
                            @if($task->images->isNotEmpty())
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($task->images as $post)
                                        <img src="{{ $post->image_url }}" alt="Task Image" class="rounded-lg border border-gray-100 shadow-sm max-h-96 w-full object-cover">
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-3 text-sm text-slate-400 font-bold">No images uploaded for this task.</p>
                            @endif
                        </div>

                        <div class="mt-12 flex flex-wrap gap-4">
                            @if(!empty($labels))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($labels as $label)
                                        <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black uppercase border border-blue-100">{{ $label }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @if(!empty($tags))
                                <div class="flex flex-wrap gap-2 border-l border-slate-100 pl-4">
                                    @foreach($tags as $tag)
                                        <span class="text-sm font-bold text-slate-400 hover:text-blue-600 transition-all cursor-default">#{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!empty($subtasks))
                    <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-xl">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Subtasks</h2>
                            <span class="text-xs font-black text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full border border-blue-100 uppercase">{{ $subtasksDone }}/{{ count($subtasks) }} Done</span>
                        </div>
                        <div class="grid gap-3">
                            @foreach($subtasks as $sub)
                                <div class="group flex items-center gap-4 p-4 rounded-2xl border transition-all {{ ($sub['completed'] ?? false) ? 'bg-emerald-50/30 border-emerald-100/50' : 'bg-slate-50 border-transparent' }}">
                                    <div class="h-8 w-8 rounded-lg flex items-center justify-center {{ ($sub['completed'] ?? false) ? 'bg-emerald-100 text-emerald-600' : 'bg-white text-slate-400 border border-slate-200' }}">
                                        @if($sub['completed'] ?? false)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        @else
                                            <div class="h-1.5 w-1.5 rounded-full bg-slate-200"></div>
                                        @endif
                                    </div>
                                    <span class="font-bold {{ ($sub['completed'] ?? false) ? 'text-emerald-700/60 line-through' : 'text-slate-700' }}">{{ $sub['title'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl">
                    <span class="{{ $labelClasses }}">Current Status</span>
                    <div class="mt-4">
                        <div class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="h-3 w-3 rounded-full animate-pulse" style="background-color: {{ $taskColor }}"></div>
                            <span class="font-black uppercase tracking-widest text-sm text-slate-700">{{ $task->status ?? 'Active' }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl">
                    <span class="{{ $labelClasses }}">Owner & Assignee</span>
                    <div class="mt-4 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-black text-xs">{{ substr($task->creator?->name ?? 'U', 0, 1) }}</div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Creator</p>
                                <p class="font-bold text-slate-800">{{ $task->creator?->name ?? 'Unassigned' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-50">
                            <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-black text-xs border border-slate-200">{{ substr($task->assignee?->name ?? 'U', 0, 1) }}</div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Assigned To</p>
                                <p class="font-bold text-slate-800">{{ $task->assignee?->name ?? 'Unassigned' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <span class="{{ $labelClasses }} mb-0">Comments</span>
                        <span class="text-xs font-black text-slate-400">{{ $task->comments->count() }} total</span>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700">
                            @foreach ($errors->all() as $message)
                                <p>{{ $message }}</p>
                            @endforeach
                        </div>
                    @endif

                    @if(!$task->trashed())
                        <form action="{{ route('tasks.comments.store', $task->id) }}" method="POST" class="space-y-3 mb-6">
                            @csrf
                            <div>
                                <label for="user_id" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Commenter</label>
                                <select id="user_id" name="user_id" class="w-full bg-slate-50 border border-slate-100 rounded-xl py-3 px-4 font-bold text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-300 outline-none transition-all">
                                    <option value="" disabled @selected(!old('user_id'))>Select user</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="body" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Comment</label>
                                <textarea id="body" name="body" rows="3" class="w-full bg-slate-50 border border-slate-100 rounded-xl py-3 px-4 text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-300 outline-none transition-all" placeholder="Write your comment...">{{ old('body') }}</textarea>
                            </div>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-black hover:bg-blue-600 transition-all">Add Comment</button>
                        </form>
                    @else
                        <p class="mb-6 text-sm font-bold text-slate-400">Restore this task first to add comments.</p>
                    @endif

                    <div class="space-y-3">
                        @forelse($task->comments as $comment)
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                                <p class="text-sm text-slate-700">{{ $comment->body }}</p>
                                <div class="mt-2 text-[11px] text-slate-500 font-bold uppercase tracking-wide">
                                    {{ $comment->user?->name ?? 'Unknown User' }} • {{ $comment->created_at?->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No comments yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl">
                    <span class="{{ $labelClasses }}">Timeline</span>
                    <div class="mt-4 space-y-6">
                        <div class="flex gap-4">
                            <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Due Date</p>
                                <p class="font-bold text-rose-600">{{ $task->due_date?->format('M d, Y') ?? 'None' }}</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.689c0-.864.933-1.406 1.683-.977l7.93 4.532a1.125 1.125 0 010 1.956l-7.93 4.532A1.125 1.125 0 013 17.756V8.689zM14.25 8.689c0-.864.933-1.406 1.683-.977l7.93 4.532a1.125 1.125 0 010 1.956l-7.93 4.532A1.125 1.125 0 0114.25 17.756V8.689z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Created At</p>
                                <p class="font-bold text-slate-700">{{ $task->created_at?->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-50 text-[10px] font-bold text-slate-300 uppercase tracking-widest">Updated: {{ $task->updated_at?->diffForHumans() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
