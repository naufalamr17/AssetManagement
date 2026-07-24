<x-layout bodyClass="sima-auth-body">
    <main class="sima-auth">
        <section class="auth-showcase">
            <div class="auth-brand">
                <img src="{{ asset('img/sima-mark.svg') }}" alt="SIMA logo">
                <div><strong>SIMA</strong><span>Sistem Informasi Manajemen Aset</span></div>
            </div>
            <div class="auth-copy">
                <span class="auth-kicker">Asset intelligence workspace</span>
                <h1>Kelola seluruh siklus aset dalam satu sistem.</h1>
                <p>Dari pencatatan, mutasi, perbaikan, hingga penghapusan—semuanya lebih terstruktur dan mudah dipantau.</p>
                <div class="auth-features">
                    <div><i class="material-icons-round">domain</i><span><strong>Multi-company</strong><small>PT MLP & PT KES</small></span></div>
                    <div><i class="material-icons-round">insights</i><span><strong>Live insights</strong><small>Dashboard terpadu</small></span></div>
                    <div><i class="material-icons-round">verified_user</i><span><strong>Secure access</strong><small>Kontrol berdasarkan peran</small></span></div>
                </div>
            </div>
            <small class="auth-copyright">© {{ date('Y') }} SIMA. Internal asset management system.</small>
        </section>

        <section class="auth-form-side">
            <div class="auth-form-card">
                <span class="auth-kicker">Selamat datang kembali</span>
                <h2>Masuk ke akun Anda</h2>
                <p>Gunakan akun SIMA atau akun Microsoft perusahaan.</p>

                @if (Session::has('status'))
                <div class="alert alert-success">{{ Session::get('status') }}</div>
                @endif
                @if (Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="auth-field">
                        <label for="email">Email</label>
                        <div class="auth-input">
                            <i class="material-icons-round">mail_outline</i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@perusahaan.com" autocomplete="email" required autofocus>
                        </div>
                        @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="auth-field">
                        <label for="password">Password</label>
                        <div class="auth-input">
                            <i class="material-icons-round">lock_outline</i>
                            <input id="password" type="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
                        </div>
                        @error('password')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary auth-submit">Masuk ke SIMA <i class="material-icons-round">arrow_forward</i></button>
                </form>

                <div class="auth-divider"><span>atau lanjutkan dengan</span></div>
                <a href="{{ route('auth.azure') }}" class="microsoft-button">
                    <span class="microsoft-mark"><i></i><i></i><i></i><i></i></span>
                    Masuk dengan Microsoft
                </a>
                <p class="auth-help"><i class="material-icons-round">help_outline</i> Hubungi administrator jika Anda mengalami kendala akses.</p>
            </div>
        </section>
    </main>
</x-layout>
