<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ProtectsAgainstSpam;
use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonationController extends Controller
{
    use ProtectsAgainstSpam;

    public function index(): View
    {
        $siteName = setting('site_name', config('app.name'));

        $seo = [
            'title' => "Donasi | {$siteName}",
            'description' => "Salurkan donasi Anda untuk mendukung kegiatan dan program {$siteName}. Setiap kontribusi sangat berarti.",
            'canonical' => route('donasi.index'),
        ];

        return view('donasi.index', compact('seo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->spamProtectionRules($request));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_anonymous' => ['boolean'],
            'amount' => ['required', 'integer', 'min:1000'],
            'payment_method' => ['required', 'in:transfer_bank,qris,tunai,lainnya'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['is_anonymous'] = $request->boolean('is_anonymous');

        Donation::create($validated);

        return redirect()->route('donasi.index')
            ->with('success', 'Terima kasih! Donasi Anda telah kami terima dan akan segera kami konfirmasi.');
    }
}
