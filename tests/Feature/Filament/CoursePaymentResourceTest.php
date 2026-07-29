<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\CoursePayments\Pages\ListCoursePayments;
use App\Models\CoursePayment;
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
}
