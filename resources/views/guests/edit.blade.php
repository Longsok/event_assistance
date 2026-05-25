<x-app-layout>
<div class="py-8 px-4 sm:px-6 max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('guests.index') }}" class="text-sm hover:underline mb-2 inline-block" style="color:#818cf8">← Guest Book</a>
        <h2 class="text-2xl font-bold text-white">Edit Guest</h2>
    </div>
    <div class="rounded-2xl border p-6" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <form method="POST" action="{{ route('guests.update', $guest) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $guest->name) }}" required
                       class="w-full rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Email</label>
                <input type="email" name="email" value="{{ old('email', $guest->email) }}"
                       class="w-full rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $guest->phone) }}"
                       class="w-full rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 resize-none"
                          style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">{{ old('notes', $guest->notes) }}</textarea>
            </div>
            <button type="submit"
                    class="w-full py-3 text-white font-semibold rounded-xl"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">Save Changes</button>
        </form>
    </div>
</div>
</x-app-layout>
