@guest
    @if(feature_enabled('login_register'))
    <div class="relative overflow-hidden rounded-3xl p-8 sm:p-10 text-center"
         style="background:linear-gradient(135deg,#082828 0%,#08484A 60%,#0a6060 100%)"
         data-aos="fade-up">

        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-12 -left-8 w-48 h-48 rounded-full bg-white/5"></div>

        <div class="relative max-w-xl mx-auto">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>

            <h3 class="text-xl sm:text-2xl font-extrabold text-white">
                Punya cerita untuk dibagikan?
            </h3>
            <p class="text-sm sm:text-base mt-2" style="color:rgba(255,255,255,.7)">
                Bergabunglah menjadi <strong class="text-white">author</strong> dan terbitkan tulisan Anda
                di {{ setting('site_name') }}.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-6">
                <a href="{{ route('register') }}"
                   class="btn-primary text-sm py-3 px-6 justify-center w-full sm:w-auto">
                    Daftar & Jadi Author
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="{{ route('login') }}"
                   class="text-sm font-semibold text-white/80 hover:text-white transition-colors">
                    Sudah punya akun? Masuk
                </a>
            </div>
        </div>
    </div>
    @endif
@endguest
