<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('All Tasks') }}
        </h2>
    </x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div class="space-y-2">
            <h1 class="text-4xl sm:text-6xl font-black text-slate-900 tracking-tighter leading-none">
                Workspace <span class="text-blue-600">.</span>
            </h1>
            <p class="text-lg text-slate-400 font-bold tracking-tight pl-1">
                {{ $tasks->total() }} operations active in your pipeline
            </p>
        </div>

        <a href="{{ route('tasks.create') }}" class="group relative inline-flex items-center gap-3 bg-slate-900 px-8 py-5 rounded-[2rem] text-white shadow-2xl shadow-slate-900/20 hover:bg-blue-600 transition-all active:scale-[0.98]">
            <span class="text-lg font-black tracking-tight">New Initiative</span>
            <div class="h-8 w-8 rounded-xl bg-white/10 flex items-center justify-center group-hover:bg-white group-hover:text-blue-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
        @foreach([
            ['label' => 'Total', 'val' => $counts['total']],
            ['label' => 'Closed', 'val' => $counts['completed']],
            ['label' => 'Critical', 'val' => $counts['urgent']],
            ['label' => 'Active', 'val' => $counts['pending']],
        ] as $stat)
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $stat['label'] }}</p>
                <p class="text-3xl font-black text-slate-900">{{ $stat['val'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse ($tasks as $task)
            @php
                $priority = strtolower($task->priority ?? 'medium');
                $priorityClasses = match ($priority) {
                    'urgent' => 'bg-rose-50 text-rose-600 border-rose-100',
                    'high' => 'bg-orange-50 text-orange-600 border-orange-100',
                    'medium' => 'bg-blue-50 text-blue-600 border-blue-100',
                    'low' => 'bg-slate-50 text-slate-600 border-slate-100',
                    default => 'bg-slate-50 text-slate-600 border-slate-100',
                };
                $isDone = (bool) ($task->completed ?? false);
                $taskColor = $task->color ?? '#3b82f6';
            @endphp

            <div class="group relative bg-white rounded-[2.5rem] border border-slate-100 shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all p-8 flex flex-col min-h-[320px] overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 opacity-5 rounded-bl-[4rem] group-hover:opacity-10 transition-opacity" style="background-color: {{ $taskColor }}"></div>

                <div class="mb-6 flex items-center justify-between">
                    <span class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $priorityClasses }}">
                        {{ $priority }}
                    </span>
                    <span class="text-xs font-black text-slate-300">#{{ $task->id }}</span>
                </div>

                <div class="grow">
                    <h3 class="text-2xl font-black text-slate-900 leading-tight mb-4 group-hover:text-blue-600 transition-colors">
                        <a href="{{ route('tasks.show', $task->id) }}">{{ $task->title }}</a>
                    </h3>
                    <p class="text-xs text-slate-400 font-mono mt-2">
                        {{ $task->slug }}
                    </p>

                    <p class="text-slate-500 font-medium line-clamp-3 text-sm leading-relaxed mb-4">
                        {{ $task->description ?? 'No context provided.' }}
                    </p>

                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">
                        Created {{ $task->created_at?->format('M d, Y h:i A') }}
                    </p>
                </div>

                <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-400 border border-slate-200">
                            {{ substr($task->creator?->name ?? 'U', 0, 1) }}
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $task->board_column ?? 'Default' }}</p>
                    </div>

                    @if($isDone)
                        <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                    @else
                        <div class="h-10 w-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center border border-slate-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="pt-4 mt-4 border-t border-slate-50 flex flex-wrap items-center gap-2">
                    @if($task->trashed())
                        <form method="POST" action="{{ route('tasks.restore', $task->id) }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="px-3 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100 transition-colors">
                                Restore
                            </button>
                        </form>
                    @else
                        <a href="{{ route('tasks.show', $task->id) }}" class="px-3 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100 transition-colors">
                            View
                        </a>
                        <a href="{{ route('tasks.edit', $task->id) }}" class="px-3 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100 hover:bg-amber-100 transition-colors">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" onsubmit="return confirm('Delete this task? Choose OK for Yes or Cancel for No.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-100 hover:bg-rose-100 transition-colors">
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 text-center">
                <div class="h-24 w-24 bg-slate-50 border border-slate-100 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-slate-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-1.24-.101-2.435-.296-3.597m-18.103 0c.342-.052.682-.107 1.022-.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Zero operations active</h2>
                <p class="text-slate-400 font-bold mt-2">Initialize your first task to begin the cycle.</p>
            </div>
        @endforelse
    </div>

    @if ($tasks->hasPages())
        <div class="mt-12">
            {{ $tasks->links() }}
        </div>
    @endif
</div>
</x-app-layout>
