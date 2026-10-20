@auth
    @if(is_null(auth()->user()->password_set_at))
        <div class="bg-yellow-50 border-b border-yellow-200">
            <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                    <p class="text-xs font-bold text-yellow-800">
                        Amankan akunmu! Kamu belum membuat password.
                    </p>
                </div>
                <a href="{{ route('profile.edit') }}" class="text-[10px] font-extrabold bg-yellow-600 text-white px-3 py-1.5 rounded-lg whitespace-nowrap hover:bg-yellow-700 transition-colors">
                    Buat Password
                </a>
            </div>
        </div>
    @endif
@endauth