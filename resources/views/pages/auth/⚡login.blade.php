<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\UserSession;

new #[Title('Masuk')] class extends Component
{
    public $username;
    public $password;

    // public function title(): string
    // {
    //     return 'Masuk - StaffPort';
    // }

    public function login()
    {
        $credentials = [
            'username' => $this->username,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials)) {

            session()->regenerate();

            $sessionId = session()->getId();
            UserSession::where('user_id', Auth::id())
                ->where('session_id', '!=', $sessionId)
                ->update([
                    'is_active' => false,
                    'disconnected_at' => now(),
                ]);
            UserSession::updateOrCreate(
                [
                    'session_id' => $sessionId,
                ],
                [
                    'user_id' => Auth::id(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'last_activity' => now(),
                    'is_active' => true,
                ]
            );

            return redirect('/home');
        }

        $this->addError('login', 'Nama Pengguna atau Kata Sandi Tidak Sesuai');
    }
};
?>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50 flex items-center justify-center px-4">
    @if(session('forced_logout') === 'admin')
        <script>
            alert('Sesi Anda telah diputus.');
        </script>
    @endif
    @if(session('forced_logout') === 'other_device')
        <script>
            alert('Akun Anda telah digunakan di perangkat lain.');
        </script>
    @endif
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-[450px] h-[450px] rounded-full bg-emerald-400/10 blur-3xl"></div>
    </div>
    <div class="relative w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            <div class="text-center">
                <div class="mx-auto w-20 h-20 rounded-2xl bg-emerald-100 flex items-center justify-center">
                    <i class="ri-team-fill text-4xl text-emerald-700"></i>
                </div>
                <h1 class="mt-5 text-3xl font-black text-gray-800">Staff Management</h1>
                <p class="text-gray-500 mt-2">PT Kapuas Kencana Jaya</p>
            </div>
            <div class="grid grid-cols-3 gap-3 mt-8">
                <div class="rounded-xl bg-emerald-50 p-3 text-center">
                    <i class="ri-user-star-line text-2xl text-emerald-700"></i>
                    <p class="text-xs mt-2">Employee</p>
                </div>
                <div class="rounded-xl bg-emerald-50 p-3 text-center">
                    <i class="ri-time-line text-2xl text-emerald-700"></i>
                    <p class="text-xs mt-2">Attendance</p>
                </div>
                <div class="rounded-xl bg-emerald-50 p-3 text-center">
                    <i class="ri-bar-chart-grouped-line text-2xl text-emerald-700"></i>
                    <p class="text-xs mt-2">Monitoring</p>
                </div>
            </div>
            
            <form wire:submit.prevent="login" class="space-y-5 mt-8">
                <div>
                    <label class="text-sm font-medium text-gray-700">Nama Pengguna</label>
                    <input type="text" wire:model="username" class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Kata Sandi</label>
                    <input type="password" wire:model="password" class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <button class="w-full py-3 rounded-xl bg-emerald-700 hover:bg-emerald-600 text-white font-semibold transition">Masuk</button>
                @if ($errors->has('login'))
                    <div class="px-1 py-1 text-sm text-red-700">
                        {{ $errors->first('login') }}
                    </div>
                @endif
            </form>
            <div class="mt-8 text-center">
                <div class="flex justify-center gap-2 text-xs">
                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700">Automated</span>
                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700">Centralized</span>
                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700">Efficient</span>
                </div>
            </div>
        </div>
    </div>
</div>