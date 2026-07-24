{{--
    Help content for the "Cara Embed Video" action on the media list page.
    Explains how to obtain a valid, supported URL for each provider so the
    embed validator (App\Services\EmbedVideo) accepts it.
--}}
{{-- Theme-aware styling for the example URL chips: a fixed light background
     left the text unreadable in dark mode, so colours are set per theme. --}}
<style>
    .fi-embed-help-url {
        display: block;
        padding: .25rem .5rem;
        border-radius: .375rem;
        word-break: break-all;
        background: #f3f4f6;
        color: #374151;
    }

    .dark .fi-embed-help-url {
        background: rgba(255, 255, 255, .07);
        color: #e5e7eb;
    }
</style>

<div style="display:flex;flex-direction:column;gap:1.25rem;font-size:.875rem;line-height:1.5;">

    <p style="color:var(--gray-500);">
        Untuk menambahkan video, gunakan menu <strong>Tambah Embed Video</strong> lalu tempel
        <em>link</em> videonya. Pastikan format link sesuai contoh di bawah, dan video bersifat
        <strong>publik</strong> agar bisa tampil.
    </p>

    {{-- YouTube --}}
    <div style="border:1px solid var(--gray-200);border-radius:.75rem;padding:1rem;">
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
            <span style="width:.6rem;height:.6rem;border-radius:9999px;background:#ff0000;display:inline-block;"></span>
            <strong>YouTube</strong>
        </div>
        <ol style="margin:0;padding-left:1.1rem;display:flex;flex-direction:column;gap:.35rem;">
            <li>Buka videonya, lalu salin link dari address bar browser.</li>
            <li>Mendukung video biasa, <strong>Shorts</strong>, dan <strong>Live</strong>.</li>
        </ol>
        <div style="margin-top:.6rem;display:flex;flex-direction:column;gap:.3rem;">
            <code class="fi-embed-help-url">https://www.youtube.com/watch?v=XXXXXXXXXXX</code>
            <code class="fi-embed-help-url">https://youtu.be/XXXXXXXXXXX</code>
            <code class="fi-embed-help-url">https://www.youtube.com/shorts/XXXXXXXXXXX</code>
        </div>
    </div>

    {{-- TikTok --}}
    <div style="border:1px solid var(--gray-200);border-radius:.75rem;padding:1rem;">
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
            <span style="width:.6rem;height:.6rem;border-radius:9999px;background:#25f4ee;display:inline-block;"></span>
            <strong>TikTok</strong>
        </div>
        <ol style="margin:0;padding-left:1.1rem;display:flex;flex-direction:column;gap:.35rem;">
            <li>Di aplikasi: tap <strong>Bagikan</strong> &rarr; <strong>Salin tautan</strong>.</li>
            <li>
                Link pendek <code>vm.tiktok.com/…</code> <strong>belum bisa</strong> langsung dipakai.
                Buka dulu link itu di browser hingga alamatnya berubah menjadi link lengkap di bawah,
                baru salin link lengkapnya.
            </li>
            <li>Di komputer: cukup salin link dari address bar saat menonton video.</li>
        </ol>
        <div style="margin-top:.6rem;">
            <code class="fi-embed-help-url">https://www.tiktok.com/@nama_akun/video/1234567890123456789</code>
        </div>
    </div>

    {{-- Instagram --}}
    <div style="border:1px solid var(--gray-200);border-radius:.75rem;padding:1rem;">
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
            <span style="width:.6rem;height:.6rem;border-radius:9999px;background:#c13584;display:inline-block;"></span>
            <strong>Instagram</strong>
        </div>
        <ol style="margin:0;padding-left:1.1rem;display:flex;flex-direction:column;gap:.35rem;">
            <li>Hanya untuk <strong>Post</strong> atau <strong>Reel</strong> dari akun publik.</li>
            <li>Buka postingan/reel, tap ikon <strong>⋯</strong> &rarr; <strong>Tautan</strong> / <strong>Salin tautan</strong>.</li>
            <li>Link <strong>profil</strong> atau <strong>story</strong> tidak bisa di-embed — harus link ke satu postingan.</li>
        </ol>
        <div style="margin-top:.6rem;display:flex;flex-direction:column;gap:.3rem;">
            <code class="fi-embed-help-url">https://www.instagram.com/reel/XXXXXXXXXXX/</code>
            <code class="fi-embed-help-url">https://www.instagram.com/p/XXXXXXXXXXX/</code>
        </div>
    </div>

    <p style="color:var(--gray-500);">
        Setelah link ditempel, pratinjau akan muncul otomatis bila link valid. Jika muncul peringatan,
        periksa kembali apakah link mengarah ke <strong>satu video</strong> (bukan halaman profil) dan videonya publik.
    </p>
</div>
