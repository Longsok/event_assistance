# Run this from your project root:
# cd C:\xampp\htdocs\event_assistance-main
# powershell -ExecutionPolicy Bypass -File create_admin_views.ps1

$base = "resources\views\admin"

# Create directories
New-Item -ItemType Directory -Force -Path "$base\users"
New-Item -ItemType Directory -Force -Path "$base\events"
New-Item -ItemType Directory -Force -Path "$base\categories"
New-Item -ItemType Directory -Force -Path "$base\task-groups"
New-Item -ItemType Directory -Force -Path "$base\templates"

# ── users/index.blade.php ─────────────────────────────────────────────────────
Set-Content "$base\users\index.blade.php" @'
<x-admin-layout>
    <x-slot name="title">Users</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-white">All Users</h2>
        <span class="text-sm text-gray-400">{{ $users->total() }} total</span>
    </div>

    <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Role</th>
                    <th class="px-6 py-3 text-left">Events</th>
                    <th class="px-6 py-3 text-left">Joined</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($users as $user)
                <tr class="hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-bold text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="text-white font-medium">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @foreach($user->roles as $role)
                            <span class="px-2 py-1 rounded text-xs font-medium
                                {{ $role->name === 'admin' ? 'bg-red-900/40 text-red-400' : 'bg-indigo-900/40 text-indigo-400' }}">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $user->events_count }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($user->id !== auth()->id())
                                @if($user->hasRole('admin'))
                                    <form method="POST" action="{{ route('admin.users.demote', $user) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs px-2 py-1 rounded bg-yellow-900/40 text-yellow-400 hover:bg-yellow-900/70">Demote</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.promote', $user) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs px-2 py-1 rounded bg-green-900/40 text-green-400 hover:bg-green-900/70">Promote</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Delete this user?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs px-2 py-1 rounded bg-red-900/40 text-red-400 hover:bg-red-900/70">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</x-admin-layout>
'@

# ── users/show.blade.php ──────────────────────────────────────────────────────
Set-Content "$base\users\show.blade.php" @'
<x-admin-layout>
    <x-slot name="title">User: {{ $user->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Back to Users</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-full bg-indigo-600 flex items-center justify-center text-xl font-bold text-white">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-white font-semibold text-lg">{{ $user->name }}</p>
                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Role</span>
                    <span class="text-white">{{ $user->roles->pluck('name')->join(', ') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Joined</span>
                    <span class="text-white">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Events</span>
                    <span class="text-white">{{ $user->events->count() }}</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Events</h3>
            @forelse($user->events as $event)
                <div class="flex items-center justify-between py-2 border-b border-gray-800">
                    <span class="text-gray-300">{{ $event->title }}</span>
                    <span class="text-xs px-2 py-1 rounded bg-gray-800 text-gray-400">{{ $event->status }}</span>
                </div>
            @empty
                <p class="text-gray-500">No events yet.</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>
'@

# ── events/index.blade.php ────────────────────────────────────────────────────
Set-Content "$base\events\index.blade.php" @'
<x-admin-layout>
    <x-slot name="title">Events</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-white">All Events</h2>
    </div>

    <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Organizer</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($events ?? [] as $event)
                <tr class="hover:bg-gray-800/50">
                    <td class="px-6 py-4 text-white font-medium">{{ $event->title }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $event->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $event->category->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs
                            {{ $event->status === 'ongoing' ? 'bg-green-900/40 text-green-400' : 'bg-gray-800 text-gray-400' }}">
                            {{ $event->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $event->start_date ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No events found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
'@

# ── categories/index.blade.php ────────────────────────────────────────────────
Set-Content "$base\categories\index.blade.php" @'
<x-admin-layout>
    <x-slot name="title">Categories</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-white">Event Categories</h2>
        <a href="{{ route('admin.categories.create') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
            + New Category
        </a>
    </div>

    <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Color</th>
                    <th class="px-6 py-3 text-left">Events</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($categories as $category)
                <tr class="hover:bg-gray-800/50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full" style="background:{{ $category->color }}"></div>
                            <span class="text-white font-medium">{{ $category->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $category->color }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $category->events_count }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs {{ $category->is_active ? 'bg-green-900/40 text-green-400' : 'bg-gray-800 text-gray-500' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="text-xs px-2 py-1 rounded bg-indigo-900/40 text-indigo-400 hover:bg-indigo-900/70">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                  onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button class="text-xs px-2 py-1 rounded bg-red-900/40 text-red-400 hover:bg-red-900/70">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
'@

# ── categories/create.blade.php ───────────────────────────────────────────────
Set-Content "$base\categories\create.blade.php" @'
<x-admin-layout>
    <x-slot name="title">New Category</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Back</a>
    </div>

    <div class="max-w-lg bg-gray-900 rounded-xl border border-gray-800 p-6">
        <h2 class="text-white font-semibold text-lg mb-6">Create Category</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500"
                       required>
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Color</label>
                <input type="color" name="color" value="{{ old('color', '#534AB7') }}"
                       class="w-12 h-10 rounded bg-gray-800 border border-gray-700 cursor-pointer">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Icon (optional)</label>
                <input type="text" name="icon" value="{{ old('icon') }}" placeholder="e.g. 🎉"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">{{ old('description') }}</textarea>
            </div>
            <button type="submit"
                    class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                Create Category
            </button>
        </form>
    </div>
</x-admin-layout>
'@

# ── categories/edit.blade.php ─────────────────────────────────────────────────
Set-Content "$base\categories\edit.blade.php" @'
<x-admin-layout>
    <x-slot name="title">Edit Category</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Back</a>
    </div>

    <div class="max-w-lg bg-gray-900 rounded-xl border border-gray-800 p-6">
        <h2 class="text-white font-semibold text-lg mb-6">Edit: {{ $category->name }}</h2>
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm text-gray-400 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500"
                       required>
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Color</label>
                <input type="color" name="color" value="{{ old('color', $category->color) }}"
                       class="w-12 h-10 rounded bg-gray-800 border border-gray-700 cursor-pointer">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Icon</label>
                <input type="text" name="icon" value="{{ old('icon', $category->icon) }}"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                       class="rounded bg-gray-800 border-gray-700">
                <label for="is_active" class="text-sm text-gray-400">Active</label>
            </div>
            <button type="submit"
                    class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                Update Category
            </button>
        </form>
    </div>
</x-admin-layout>
'@

# ── task-groups/index.blade.php ───────────────────────────────────────────────
Set-Content "$base\task-groups\index.blade.php" @'
<x-admin-layout>
    <x-slot name="title">Task Groups</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Add Form --}}
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Add Task Group</h3>
            <form method="POST" action="{{ route('admin.task-groups.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500"
                           required>
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Color</label>
                    <input type="color" name="color" value="{{ old('color', '#534AB7') }}"
                           class="w-12 h-10 rounded bg-gray-800 border border-gray-700 cursor-pointer">
                </div>
                <button type="submit"
                        class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Add Group
                </button>
            </form>
        </div>

        {{-- Groups List --}}
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Existing Groups ({{ $groups->count() }})</h3>
            <div class="space-y-2">
                @forelse ($groups as $group)
                <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full" style="background:{{ $group->color }}"></div>
                        <span class="text-white text-sm">{{ $group->name }}</span>
                        <span class="text-xs text-gray-500">{{ $group->event_tasks_count }} tasks</span>
                    </div>
                    <form method="POST" action="{{ route('admin.task-groups.destroy', $group) }}"
                          onsubmit="return confirm('Delete this group?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No task groups yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
'@

# ── templates/budget.blade.php ────────────────────────────────────────────────
Set-Content "$base\templates\budget.blade.php" @'
<x-admin-layout>
    <x-slot name="title">Budget Templates</x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.categories.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Categories</a>
        <span class="text-gray-600">/</span>
        <span class="text-white text-sm">Budget Templates: {{ $category->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Add Budget Item</h3>
            <form method="POST" action="{{ route('admin.budget-templates.store', $category) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Line Item *</label>
                    <input type="text" name="line_item" value="{{ old('line_item') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Suggested % *</label>
                    <input type="number" name="suggested_percentage" value="{{ old('suggested_percentage') }}" min="0" max="100" step="0.1"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Scale Trigger</label>
                    <input type="text" name="scale_trigger" value="{{ old('scale_trigger', 'any') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Add Item
                </button>
            </form>
        </div>

        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Items ({{ $templates->count() }})</h3>
            <div class="space-y-2">
                @forelse ($templates as $template)
                <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg text-sm">
                    <div>
                        <p class="text-white">{{ $template->line_item }}</p>
                        <p class="text-gray-500 text-xs">{{ $template->suggested_percentage }}%</p>
                    </div>
                    <form method="POST" action="{{ route('admin.budget-templates.destroy', [$category, $template]) }}"
                          onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No budget items yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
'@

# ── templates/schedule.blade.php ──────────────────────────────────────────────
Set-Content "$base\templates\schedule.blade.php" @'
<x-admin-layout>
    <x-slot name="title">Schedule Templates</x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.categories.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Categories</a>
        <span class="text-gray-600">/</span>
        <span class="text-white text-sm">Schedule Templates: {{ $category->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Add Session</h3>
            <form method="POST" action="{{ route('admin.schedule-templates.store', $category) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Session Name *</label>
                    <input type="text" name="session_name" value="{{ old('session_name') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Anchor *</label>
                        <select name="anchor" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                            <option value="start">Start</option>
                            <option value="end">End</option>
                            <option value="middle">Middle</option>
                            <option value="proportional">Proportional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Offset (mins) *</label>
                        <input type="number" name="offset_minutes" value="{{ old('offset_minutes', 0) }}"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Duration (mins) *</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="1"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_break" id="is_break" value="1">
                    <label for="is_break" class="text-sm text-gray-400">Is Break</label>
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Add Session
                </button>
            </form>
        </div>

        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Sessions ({{ $templates->count() }})</h3>
            <div class="space-y-2">
                @forelse ($templates as $template)
                <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg text-sm">
                    <div>
                        <p class="text-white">{{ $template->session_name }}</p>
                        <p class="text-gray-500 text-xs">{{ $template->duration_minutes }} mins · {{ $template->anchor }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.schedule-templates.destroy', [$category, $template]) }}"
                          onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No sessions yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
'@

# ── templates/timeline.blade.php ──────────────────────────────────────────────
Set-Content "$base\templates\timeline.blade.php" @'
<x-admin-layout>
    <x-slot name="title">Task Templates</x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.categories.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Categories</a>
        <span class="text-gray-600">/</span>
        <span class="text-white text-sm">Task Templates: {{ $category->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Add Task Template</h3>
            <form method="POST" action="{{ route('admin.category-templates.store', $category) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Task Name *</label>
                    <input type="text" name="task_name" value="{{ old('task_name') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Group *</label>
                    <select name="group_id" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Days Before *</label>
                        <input type="number" name="days_before" value="{{ old('days_before', 7) }}"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Priority *</label>
                        <select name="priority" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Anchor *</label>
                    <select name="anchor" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                        <option value="before_event">Before Event</option>
                        <option value="first_day">First Day</option>
                        <option value="last_day">Last Day</option>
                        <option value="after_event">After Event</option>
                        <option value="proportional">Proportional</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Add Task
                </button>
            </form>
        </div>

        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Tasks ({{ $templates->count() }})</h3>
            <div class="space-y-2">
                @forelse ($templates as $template)
                <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg text-sm">
                    <div>
                        <p class="text-white">{{ $template->task_name }}</p>
                        <p class="text-gray-500 text-xs">{{ $template->group->name ?? '-' }} · {{ $template->days_before }}d · {{ $template->priority }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.category-templates.destroy', [$category, $template]) }}"
                          onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No task templates yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
'@

Write-Host "✅ All admin views created successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Now run: php artisan view:clear" -ForegroundColor Yellow
