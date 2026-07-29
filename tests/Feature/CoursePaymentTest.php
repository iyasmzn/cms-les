<?php

namespace Tests\Feature;

use App\Models\CoursePayment;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_billing_creates_unpaid_payments_for_active_members_only(): void
    {
        $group = Group::factory()->create();
        $session = GroupSession::factory()->create(['group_id' => $group->id, 'fee' => 50000]);

        GroupMember::factory()->count(2)->active()->create(['group_id' => $group->id]);
        GroupMember::factory()->create(['group_id' => $group->id, 'status' => 'pending']);

        $count = $session->billActiveMembers();

        $this->assertSame(2, $count);
        $this->assertSame(2, $session->payments()->count());
        $this->assertSame(2, $session->payments()->where('status', 'unpaid')->count());
        $this->assertEquals(50000.0, (float) $session->payments()->first()->amount);
    }

    public function test_billing_skips_members_already_billed(): void
    {
        $group = Group::factory()->create();
        $session = GroupSession::factory()->create(['group_id' => $group->id, 'fee' => 50000]);
        GroupMember::factory()->count(2)->active()->create(['group_id' => $group->id]);

        $first = $session->billActiveMembers();
        $second = $session->billActiveMembers();

        $this->assertSame(2, $first);
        $this->assertSame(0, $second);
        $this->assertSame(2, $session->payments()->count());
    }

    public function test_billing_returns_zero_when_session_has_no_fee(): void
    {
        $group = Group::factory()->create();
        $session = GroupSession::factory()->create(['group_id' => $group->id, 'fee' => null]);
        GroupMember::factory()->active()->create(['group_id' => $group->id]);

        $this->assertSame(0, $session->billActiveMembers());
        $this->assertSame(0, $session->payments()->count());
    }

    public function test_member_payment_totals(): void
    {
        $member = GroupMember::factory()->create();
        CoursePayment::factory()->paid()->create(['group_member_id' => $member->id, 'amount' => 50000]);
        CoursePayment::factory()->create(['group_member_id' => $member->id, 'amount' => 25000, 'status' => 'unpaid']);

        $this->assertEquals(50000.0, $member->paidTotal());
        $this->assertEquals(25000.0, $member->outstandingTotal());
    }

    public function test_mark_paid_stamps_status_time_and_method(): void
    {
        $payment = CoursePayment::factory()->create(['status' => 'unpaid']);

        $payment->markPaid('transfer');

        $this->assertSame('paid', $payment->status);
        $this->assertSame('transfer', $payment->method);
        $this->assertNotNull($payment->paid_at);
    }
}
