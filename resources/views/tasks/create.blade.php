@extends('layouts.app')

@section('title', 'Create New Task')

@php
    $priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];
    $oldTags = array_values(array_filter((array) old('tags', [])));
    $oldSubtasks = collect((array) old('subtasks', []))
        ->map(fn ($s) => is_array($s)
            ? ['title' => (string) ($s['title'] ?? ''), 'isDone' => (bool) filter_var($s['isDone'] ?? false, FILTER_VALIDATE_BOOLEAN)]
            : ['title' => (string) $s, 'isDone' => false])
        ->filter(fn ($s) => $s['title'] !== '')
        ->values()
        ->all();
    $labelClasses = 'text-sm font-bold text-slate-700 mb-1';
    $inputBaseClasses = 'w-full bg-white border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-slate-900 '
        .'placeholder:text-slate-400 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 focus:outline-none transition-all duration-200';
@endphp

@section('content')
    <div class="mx-auto max-w-4xl py-6">
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4">
                <p class="text-sm font-black text-rose-700 mb-2">Please fix the following:</p>
                <ul class="list-disc pl-5 text-sm text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-between mb-10">
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-blue-600 transition-all group">
                <div class="h-10 w-10 rounded-xl bg-white shadow-sm border border-gray-100 flex items-center justify-center group-hover:bg-blue-50 group-hover:border-blue-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                </div>
                <span class="hidden sm:inline">Back to Dashboard</span>
            </a>
            <div class="flex items-center gap-3">
                 <span class="text-[10px] font-black tracking-[0.2em] text-slate-400 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm uppercase">
                    New Entry
                </span>
            </div>
        </div>

        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-blue-50/30 rounded-bl-[5rem] -mr-10 -mt-10"></div>

                        <div class="relative space-y-8">
                            <div>
                                <label for="title" class="text-[10px] font-black text-blue-600 uppercase tracking-widest block mb-4">Task Title</label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required autofocus
                                    class="w-full text-4xl font-black text-slate-900 border-none p-0 focus:ring-0 placeholder:text-slate-200 bg-transparent"
                                    placeholder="What are we doing?">
                                @error('title') <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-4 pt-4">
                                <label for="description" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Detailed Description</label>
                                <textarea id="description" name="description" rows="5"
                                    class="w-full bg-slate-50/50 border border-slate-100 rounded-3xl p-6 text-lg text-slate-600 focus:ring-4 focus:ring-blue-50 focus:border-blue-200 transition-all placeholder:text-slate-300 outline-none"
                                    placeholder="Add context or specific steps...">{{ old('description') }}</textarea>
                                @error('description') <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Settings -->
                <div class="space-y-6">
                    <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl space-y-8">
                        <div>
                            <label for="due_date" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Deadline</label>
                            <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}"
                                class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 px-6 font-bold text-slate-800 focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                            @error('due_date') <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="creator_id" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Creator</label>
                            <select id="creator_id" name="creator_id" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 px-6 font-bold text-slate-800 focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                                <option value="" disabled @selected(!old('creator_id'))>Select creator</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('creator_id') == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('creator_id') <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="assignee_id" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Assignee</label>
                            <select id="assignee_id" name="assignee_id" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 px-6 font-bold text-slate-800 focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                                <option value="" disabled @selected(!old('assignee_id'))>Select assignee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('assignee_id') == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('assignee_id') <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Priority Level</span>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($priorities as $value => $label)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="priority" value="{{ $value }}" class="peer sr-only" @checked(old('priority', 'medium') === $value)>
                                        <span class="flex items-center justify-center py-3 rounded-xl border border-slate-100 bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition-all hover:bg-slate-100">
                                            {{ $label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('priority') <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Status</label>
                            <select id="status" name="status" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 px-6 font-bold text-slate-800 focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                                <option value="to-do" @selected(old('status') === 'to-do')>To Do</option>
                                <option value="in_progress" @selected(old('status') === 'in_progress')>In Progress</option>
                                <option value="done" @selected(old('status') === 'done')>Done</option>
                            </select>
                            @error('status') <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-3 pt-4">
                        <button type="submit" class="w-full bg-slate-900 text-white rounded-[2rem] py-5 text-xl font-black shadow-2xl hover:bg-blue-600 transition-all transform hover:-translate-y-1 active:scale-95">
                            Create Task
                        </button>
                        <a href="{{ route('tasks.index') }}" class="block w-full text-center py-4 text-sm font-black text-slate-400 hover:text-rose-600 transition-colors uppercase tracking-widest">
                            Discard
                        </a>
                    </div>

                    <div class="bg-indigo-600 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                        <h4 class="text-lg font-black mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                            </svg>
                            Plan Ahead
                        </h4>
                        <p class="text-indigo-100 text-sm font-bold leading-relaxed">
                            Setting a deadline and priority helps the system organize your dashboard automatically.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
