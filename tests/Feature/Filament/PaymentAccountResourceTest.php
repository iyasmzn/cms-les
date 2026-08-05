<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PaymentAccounts\Pages\CreatePaymentAccount;
use App\Filament\Resources\PaymentAccounts\Pages\ListPaymentAccounts;
use App\Models\PaymentAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PaymentAccountResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        $permissions = collect(['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny', 'Reorder'])
            ->map(fn (string $action): Permission => Permission::findOrCreate("{$action}:PaymentAccount", 'web'));
        $user->givePermissionTo($permissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user);
    }

    public function test_it_lists_payment_accounts(): void
    {
        $accounts = PaymentAccount::factory()->count(3)->create();

        Livewire::test(ListPaymentAccounts::class)
            ->assertCanSeeTableRecords($accounts);
    }

    public function test_it_creates_a_bank_account(): void
    {
        Livewire::test(CreatePaymentAccount::class)
            ->fillForm([
                'type' => 'bank',
                'label' => 'BCA — Main',
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_holder' => 'Yayasan Les',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(PaymentAccount::class, [
            'type' => 'bank',
            'account_number' => '1234567890',
            'is_active' => true,
        ]);
    }

    public function test_a_bank_account_requires_its_number_and_holder(): void
    {
        Livewire::test(CreatePaymentAccount::class)
            ->fillForm([
                'type' => 'bank',
                'bank_name' => null,
                'account_number' => null,
                'account_holder' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'bank_name' => 'required',
                'account_number' => 'required',
                'account_holder' => 'required',
            ]);
    }

    public function test_a_bank_account_falls_back_to_the_bank_name_for_display(): void
    {
        $account = PaymentAccount::factory()->create(['label' => null, 'bank_name' => 'Mandiri']);

        $this->assertSame('Mandiri', $account->displayName());
    }

    public function test_an_account_without_its_key_detail_is_not_presentable(): void
    {
        $bankWithoutNumber = PaymentAccount::factory()->make(['account_number' => null]);
        $qrisWithoutImage = PaymentAccount::factory()->qris()->make(['qris_image' => null]);

        $this->assertFalse($bankWithoutNumber->isPresentable());
        $this->assertFalse($qrisWithoutImage->isPresentable());
        $this->assertTrue(PaymentAccount::factory()->make()->isPresentable());
        $this->assertTrue(PaymentAccount::factory()->qris()->make()->isPresentable());
    }
}
