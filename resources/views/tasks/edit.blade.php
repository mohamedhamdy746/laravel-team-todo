<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $task->title ?? 'Task Details' }}
        </h2>
    </x-slot>

@php
    $oldTags = (array) ($task->tags ?? []);
    $oldLabels = (array) ($task->labels ?? []);
    $inputClasses = "w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-slate-900 font-bold focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all outline-none placeholder:text-slate-300";
    $labelClasses = "text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block pl-2";
@endphp

<div class="max-w-4xl mx-auto">
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

    <div class="flex items-center justify-between mb-12">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Revision Mode</h1>
            <p class="text-slate-400 font-bold mt-2">ID: #{{ $task->id }} - Last modified {{ $task->updated_at?->diffForHumans() }}</p>
        </div>
        <a href="{{ route('tasks.show', $task->id) }}" class="h-14 w-14 rounded-2xl bg-white border-2 border-slate-100 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:border-blue-100 transition-all shadow-sm group">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6 group-hover:-translate-x-1 transition-transform">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
    </div>

    <form action="{{ route('tasks.update', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-[3rem] p-10 shadow-2xl shadow-slate-200/50 border border-slate-50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-[5rem] -mr-8 -mt-8 opacity-50"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative">
                <div class="md:col-span-2">
                    <label class="{{ $labelClasses }}">Project Title</label>
                    <input type="text" name="title" value="{{ old('title', $task->title) }}" required class="{{ $inputClasses }} text-2xl" placeholder="What requires attention?">
                </div>

                <div class="md:col-span-2">
                    <label class="{{ $labelClasses }}">Description</label>
                    <textarea name="description" rows="4" class="{{ $inputClasses }} resize-none" placeholder="Elaborate on the task objectives...">{{ old('description', $task->description) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="images" class="{{ $labelClasses }}">Task Images (.jpg, .png)</label>
                    <input type="file" id="images" name="images[]" accept=".jpg,.png" multiple class="{{ $inputClasses }}">
                    @error('images') <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p> @enderror
                    @error('images.*') <p class="text-rose-600 text-xs font-bold mt-2">{{ $message }}</p> @enderror
                    @if($task->images->isNotEmpty())
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($task->images as $image)
                                <img src="{{ $image->image_url }}" alt="Task image" class="rounded-lg border border-slate-200 h-24 w-full object-cover">
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Priority Tier</label>
                    <select name="priority" class="{{ $inputClasses }} appearance-none">
                        @foreach(['low', 'medium', 'high', 'urgent'] as $p)
                            <option value="{{ $p }}" @selected(old('priority', $task->priority) == $p)>{{ strtoupper($p) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Deadline</label>
                    <input type="date" name="due_date" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Status</label>
                    <select name="status" class="{{ $inputClasses }} appearance-none">
                        <option value="to-do" @selected(old('status', $task->status) === 'to-do')>To Do</option>
                        <option value="in_progress" @selected(old('status', $task->status) === 'in_progress')>In Progress</option>
                        <option value="done" @selected(old('status', $task->status) === 'done')>Done</option>
                    </select>
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Board Column</label>
                    <input type="text" name="board_column" value="{{ old('board_column', $task->board_column) }}" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Creator</label>
                    <select name="creator_id" class="{{ $inputClasses }}">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(old('creator_id', $task->creator_id) == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Assigned To</label>
                    <select name="assignee_id" class="{{ $inputClasses }}">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(old('assignee_id', $task->assignee_id) == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Task Color (Hex)</label>
                    <div class="flex gap-4">
                        <input type="color" name="color" value="{{ old('color', $task->color ?? '#3b82f6') }}" class="h-16 w-20 rounded-2xl p-1 bg-slate-50 border-2 border-slate-100 cursor-pointer">
                        <input type="text" value="{{ old('color', $task->color ?? '#3b82f6') }}" readonly class="{{ $inputClasses }} opacity-50">
                    </div>
                </div>

                <div class="flex items-center gap-6 p-4 rounded-3xl bg-slate-50 border-2 border-slate-100">
                    <label class="flex items-center gap-4 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="completed" value="1" @checked(old('completed', $task->completed)) class="sr-only peer">
                            <div class="w-14 h-8 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 transition-colors after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:after:translate-x-6"></div>
                        </div>
                        <span class="text-sm font-black text-slate-500 uppercase tracking-widest group-hover:text-slate-900 transition-colors">Mark as Resolved</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-xl">
                <h3 class="{{ $labelClasses }} border-b border-slate-50 pb-4 mb-6">Taxonomy</h3>
                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase block mb-2">Tags (comma separated)</label>
                        <input type="text" name="tags" value="{{ old('tags', implode(', ', $oldTags)) }}" class="{{ $inputClasses }}" placeholder="ux, design, backend">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase block mb-2">Labels (comma separated)</label>
                        <input type="text" name="labels" value="{{ old('labels', implode(', ', $oldLabels)) }}" class="{{ $inputClasses }}" placeholder="sprint-1, release">
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 rounded-[2.5rem] p-10 flex flex-col justify-center items-center text-center space-y-6 shadow-2xl shadow-blue-900/20">
                <div class="h-16 w-16 rounded-2xl bg-white/10 flex items-center justify-center text-blue-400 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                <h2 class="text-white text-2xl font-black">Ready to sync?</h2>
                <p class="text-slate-400 text-sm font-medium px-6">Your changes will be immediately propagated to the master dashboard.</p>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-600/30 transition-all active:scale-[0.98] text-lg">
                    Commit Changes
                </button>
            </div>
        </div>
    </form>
</div>
</x-app-layout>
