<x-filament-panels::page>
    {{-- Peringatan: riwayat tidak tersimpan di database --}}
    <div class="aih-callout">
        <x-filament::icon icon="heroicon-o-exclamation-triangle" />
        <div>
            <p><strong>Riwayat ini tidak disimpan di database.</strong></p>
            <p>
                Data riwayat import disimpan sebagai berkas di penyimpanan lokal server, bukan di database.
                Karena itu riwayat <strong>dapat hilang sewaktu-waktu</strong> — misalnya ketika server dipindah/di-deploy ulang,
                penyimpanan dibersihkan, atau saat entri terlama otomatis terhapus (hanya 50 import terakhir yang disimpan).
                Jangan jadikan halaman ini sebagai satu-satunya catatan permanen.
            </p>
        </div>
    </div>

    @forelse ($entries as $entry)
        @php
            $importedAt = \Illuminate\Support\Carbon::parse($entry['imported_at'] ?? now());
        @endphp

        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                {{ $importedAt->translatedFormat('d M Y, H:i') }} — {{ $entry['filename'] ?? 'import.xlsx' }}
            </x-slot>

            @include('filament.teacher.import-detail', ['entry' => $entry])
        </x-filament::section>
    @empty
        <div class="aih-empty">
            Belum ada riwayat import. Lakukan import data guru untuk melihat riwayatnya di sini.
        </div>
    @endforelse
</x-filament-panels::page>
