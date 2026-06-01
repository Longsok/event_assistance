<x-app-layout>
<div class="py-8 px-4 sm:px-6 max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('guests.index') }}" class="text-sm hover:underline mb-2 inline-block" style="color:#818cf8">← Guest Book</a>
        <h2 class="text-2xl font-bold" style="color:var(--text-strong)">Edit Guest</h2>
    </div>
    <div class="rounded-2xl border p-6" style="background:var(--panel);border-color:var(--border)">
        <form method="POST" action="{{ route('guests.update', $guest) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:var(--text-soft)">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $guest->name) }}" required
                       class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:var(--text-soft)">Email</label>
                <input type="email" name="email" value="{{ old('email', $guest->email) }}"
                       class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:var(--text-soft)">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $guest->phone) }}"
                       class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:var(--text-soft)">Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 resize-none"
                          style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">{{ old('notes', $guest->notes) }}</textarea>
            </div>
            <button type="submit"
                    class="w-full py-3 text-white font-semibold rounded-xl"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">Save Changes</button>
        </form>
    </div>
</div>
</x-app-layout>