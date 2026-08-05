<?php

namespace Tests\Feature;

use App\Models\CoursePayment;
use App\Models\GroupMember;
use App\Models\PaymentAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CoursePaymentSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private function billFor(User $user, float $amount = 50000): CoursePayment
    {
        $registration = GroupMember::factory()->active()->create(['user_id' => $user->id]);

        return CoursePayment::factory()->create([
            'group_member_id' => $registration->id,
            'amount' => $amount,
        ]);
    }

    public function test_the_payment_form_requires_login(): void
    {
        $bill = $this->billFor(User::factory()->create());

        $this->get(route('courses.bills.pay', $bill))->assertRedirect(route('login'));
    }

    public function test_a_member_cannot_open_someone_elses_bill(): void
    {
        $bill = $this->billFor(User::factory()->create());

        $this->actingAs(User::factory()->create())
            ->get(route('courses.bills.pay', $bill))
            ->assertNotFound();
    }

    public function test_the_form_lists_active_bank_accounts_and_qris(): void
    {
        $user = User::factory()->create();
        $bill = $this->billFor($user);

        PaymentAccount::factory()->create([
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Yayasan Les',
        ]);
        PaymentAccount::factory()->qris()->create(['label' => 'QRIS Kasir']);
        PaymentAccount::factory()->inactive()->create(['bank_name' => 'Hidden Bank']);

        $response = $this->actingAs($user)->get(route('courses.bills.pay', $bill));

        $response->assertStatus(200);
        $response->assertSee('1234567890');
        $response->assertSee('Yayasan Les');
        $response->assertSee('QRIS Kasir');
        $response->assertDontSee('Hidden Bank');
    }

    public function test_a_paid_bill_cannot_be_paid_again(): void
    {
        $user = User::factory()->create();
        $bill = $this->billFor($user);
        $bill->markPaid('cash');

        $this->actingAs($user)
            ->get(route('courses.bills.pay', $bill))
            ->assertNotFound();
    }

    public function test_a_member_can_confirm_a_cash_payment_without_proof(): void
    {
        $user = User::factory()->create();
        $bill = $this->billFor($user);

        $response = $this->actingAs($user)->post(route('courses.bills.pay.store', $bill), [
            'method' => 'cash',
        ]);

        $response->assertRedirect(route('courses.billing', ['registration' => $bill->group_member_id]));
        $response->assertSessionHas('success');

        $bill->refresh();

        $this->assertSame('review', $bill->status);
        $this->assertSame('cash', $bill->method);
        $this->assertNull($bill->payment_account_id);
        $this->assertNotNull($bill->submitted_at);
    }

    public function test_a_transfer_confirmation_stores_the_uploaded_proof(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $bill = $this->billFor($user);
        $account = PaymentAccount::factory()->create();

        $this->actingAs($user)->post(route('courses.bills.pay.store', $bill), [
            'method' => 'transfer',
            'payment_account_id' => $account->id,
            'payer_note' => 'Sent from my own account',
            'proof' => UploadedFile::fake()->create('receipt.jpg', 120, 'image/jpeg'),
        ])->assertSessionHasNoErrors();

        $bill->refresh();

        $this->assertSame('review', $bill->status);
        $this->assertSame('transfer', $bill->method);
        $this->assertSame($account->id, $bill->payment_account_id);
        $this->assertSame('Sent from my own account', $bill->payer_note);
        $this->assertNotNull($bill->proof_path);

        Storage::disk('public')->assertExists($bill->proof_path);
    }

    public function test_a_transfer_confirmation_requires_proof_and_a_destination(): void
    {
        $user = User::factory()->create();
        $bill = $this->billFor($user);

        $this->actingAs($user)
            ->post(route('courses.bills.pay.store', $bill), ['method' => 'transfer'])
            ->assertSessionHasErrors(['payment_account_id', 'proof']);

        $this->assertSame('unpaid', $bill->refresh()->status);
    }

    public function test_the_destination_must_match_the_chosen_method(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $bill = $this->billFor($user);
        $bank = PaymentAccount::factory()->create();

        $this->actingAs($user)
            ->post(route('courses.bills.pay.store', $bill), [
                'method' => 'qris',
                'payment_account_id' => $bank->id,
                'proof' => UploadedFile::fake()->create('receipt.jpg', 120, 'image/jpeg'),
            ])
            ->assertSessionHasErrors('payment_account_id');
    }

    public function test_an_inactive_destination_is_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $bill = $this->billFor($user);
        $account = PaymentAccount::factory()->inactive()->create();

        $this->actingAs($user)
            ->post(route('courses.bills.pay.store', $bill), [
                'method' => 'transfer',
                'payment_account_id' => $account->id,
                'proof' => UploadedFile::fake()->create('receipt.jpg', 120, 'image/jpeg'),
            ])
            ->assertSessionHasErrors('payment_account_id');
    }

    public function test_rejecting_a_confirmation_sends_the_bill_back_to_unpaid(): void
    {
        $user = User::factory()->create();
        $bill = $this->billFor($user);

        $bill->submitConfirmation('cash');
        $bill->reject('Amount does not match');

        $bill->refresh();

        $this->assertSame('unpaid', $bill->status);
        $this->assertSame('Amount does not match', $bill->rejection_reason);
        $this->assertNotNull($bill->rejected_at);
        $this->assertTrue($bill->isPayable());

        $this->actingAs($user)
            ->get(route('courses.billing'))
            ->assertSee('Amount does not match');
    }

    public function test_resubmitting_clears_the_previous_rejection(): void
    {
        $user = User::factory()->create();
        $bill = $this->billFor($user);

        $bill->submitConfirmation('cash');
        $bill->reject('Wrong amount');

        $this->actingAs($user)->post(route('courses.bills.pay.store', $bill), [
            'method' => 'cash',
        ])->assertSessionHasNoErrors();

        $bill->refresh();

        $this->assertSame('review', $bill->status);
        $this->assertNull($bill->rejection_reason);
        $this->assertNull($bill->rejected_at);
    }

    public function test_verifying_a_confirmation_marks_the_bill_paid(): void
    {
        $user = User::factory()->create();
        $bill = $this->billFor($user);

        $bill->submitConfirmation('transfer', PaymentAccount::factory()->create()->id, 'payment-proofs/x.jpg');
        $bill->markPaid();

        $bill->refresh();

        $this->assertSame('paid', $bill->status);
        $this->assertSame('transfer', $bill->method);
        $this->assertNotNull($bill->paid_at);
    }

    public function test_a_bill_awaiting_verification_counts_as_outstanding_not_paid(): void
    {
        $user = User::factory()->create();
        $bill = $this->billFor($user, 80000);
        $bill->submitConfirmation('cash');

        $totals = $bill->member->refresh()->paymentTotals();

        $this->assertEqualsWithDelta(80000.0, $totals['outstanding'], 0.01);
        $this->assertEqualsWithDelta(80000.0, $totals['review'], 0.01);
        $this->assertEqualsWithDelta(0.0, $totals['paid'], 0.01);

        $this->actingAs($user)
            ->get(route('courses.billing'))
            ->assertSee('Awaiting verification');
    }
}
