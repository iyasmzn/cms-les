<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\CoursePayments\CoursePaymentResource;
use App\Filament\Resources\CoursePayments\Pages\ListCoursePayments;
use App\Filament\Resources\CoursePayments\Pages\ViewCoursePayment;
use App\Models\CoursePayment;
use App\Models\PaymentAccount;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CoursePaymentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        $permissions = collect(['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny'])
            ->map(fn (string $action): Permission => Permission::findOrCreate("{$action}:CoursePayment", 'web'));
        $user->givePermissionTo($permissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user);
    }

    public function test_it_lists_payments(): void
    {
        $payments = CoursePayment::factory()->count(3)->create();

        Livewire::test(ListCoursePayments::class)
            ->assertCanSeeTableRecords($payments);
    }

    public function test_mark_paid_action_settles_a_payment(): void
    {
        $payment = CoursePayment::factory()->create(['status' => 'unpaid']);

        Livewire::test(ListCoursePayments::class)
            ->callAction(TestAction::make('markPaid')->table($payment), ['method' => 'cash'])
            ->assertHasNoErrors();

        $this->assertSame('paid', $payment->fresh()->status);
    }

    public function test_verify_action_settles_a_submitted_confirmation(): void
    {
        $payment = CoursePayment::factory()->create();
        $payment->submitConfirmation('transfer', PaymentAccount::factory()->create()->id, 'payment-proofs/proof.jpg');

        Livewire::test(ListCoursePayments::class)
            ->callAction(TestAction::make('verify')->table($payment))
            ->assertNotified();

        $payment->refresh();

        $this->assertSame('paid', $payment->status);
        $this->assertSame('transfer', $payment->method);
        $this->assertNotNull($payment->paid_at);
    }

    public function test_reject_action_returns_the_bill_to_the_member(): void
    {
        $payment = CoursePayment::factory()->create();
        $payment->submitConfirmation('cash');

        Livewire::test(ListCoursePayments::class)
            ->callAction(TestAction::make('reject')->table($payment), ['reason' => 'Proof unreadable'])
            ->assertNotified();

        $payment->refresh();

        $this->assertSame('unpaid', $payment->status);
        $this->assertSame('Proof unreadable', $payment->rejection_reason);
    }

    public function test_verify_and_reject_are_hidden_for_bills_nobody_submitted(): void
    {
        $payment = CoursePayment::factory()->create(['status' => 'unpaid']);

        Livewire::test(ListCoursePayments::class)
            ->assertActionHidden(TestAction::make('verify')->table($payment))
            ->assertActionHidden(TestAction::make('reject')->table($payment));
    }

    public function test_clicking_a_payment_row_opens_the_view_page(): void
    {
        $payment = CoursePayment::factory()->create();

        Livewire::test(ListCoursePayments::class)
            ->assertActionHasUrl(
                TestAction::make('view')->table($payment),
                CoursePaymentResource::getUrl('view', ['record' => $payment]),
            );
    }

    public function test_the_view_page_exposes_only_status_and_edit_actions(): void
    {
        $payment = CoursePayment::factory()->create();

        Livewire::test(ViewCoursePayment::class, ['record' => $payment->id])
            ->assertSuccessful()
            ->assertActionVisible('changeStatus')
            ->assertActionVisible('edit')
            ->assertActionDoesNotExist('delete');
    }

    public function test_the_view_page_renders_the_proof_in_a_zoomable_preview(): void
    {
        $payment = CoursePayment::factory()->create();
        $payment->submitConfirmation('transfer', PaymentAccount::factory()->create()->id, 'payment-proofs/proof.jpg');

        Livewire::test(ViewCoursePayment::class, ['record' => $payment->id])
            ->assertSuccessful()
            ->assertSee('/storage/payment-proofs/proof.jpg')
            ->assertSee('Click to zoom');
    }

    public function test_change_status_settles_a_payment_with_a_method(): void
    {
        $payment = CoursePayment::factory()->create();
        $payment->submitConfirmation('qris', PaymentAccount::factory()->qris()->create()->id, 'payment-proofs/proof.jpg');

        Livewire::test(ViewCoursePayment::class, ['record' => $payment->id])
            ->callAction('changeStatus', ['status' => 'paid', 'method' => 'qris'])
            ->assertNotified();

        $payment->refresh();

        $this->assertSame('paid', $payment->status);
        $this->assertSame('qris', $payment->method);
        $this->assertNotNull($payment->paid_at);
    }

    public function test_change_status_back_to_unpaid_requires_a_reason_for_a_submitted_bill(): void
    {
        $payment = CoursePayment::factory()->create();
        $payment->submitConfirmation('cash');

        Livewire::test(ViewCoursePayment::class, ['record' => $payment->id])
            ->callAction('changeStatus', ['status' => 'unpaid'])
            ->assertHasActionErrors(['reason' => 'required']);

        $this->assertSame('review', $payment->fresh()->status);

        Livewire::test(ViewCoursePayment::class, ['record' => $payment->id])
            ->callAction('changeStatus', ['status' => 'unpaid', 'reason' => 'Proof unreadable']);

        $payment->refresh();

        $this->assertSame('unpaid', $payment->status);
        $this->assertSame('Proof unreadable', $payment->rejection_reason);
    }

    public function test_change_status_can_waive_a_bill(): void
    {
        $payment = CoursePayment::factory()->create();

        Livewire::test(ViewCoursePayment::class, ['record' => $payment->id])
            ->callAction('changeStatus', ['status' => 'waived']);

        $this->assertSame('waived', $payment->fresh()->status);
    }

    public function test_the_navigation_badge_counts_pending_verifications(): void
    {
        $this->assertNull(CoursePaymentResource::getNavigationBadge());

        CoursePayment::factory()->create()->submitConfirmation('cash');

        $this->assertSame('1', CoursePaymentResource::getNavigationBadge());
    }
}
