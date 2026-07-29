<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\SessionsCalendarWidget;
use App\Models\Group;
use App\Models\GroupSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SessionsCalendarWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_is_hidden_when_there_are_no_sessions(): void
    {
        $this->assertFalse(SessionsCalendarWidget::canView());
    }

    public function test_widget_renders_sessions_for_the_current_month(): void
    {
        $this->actingAs(User::factory()->create());

        $group = Group::factory()->create(['name' => 'Dashboard Swim Group']);
        GroupSession::factory()->create([
            'group_id' => $group->id,
            'date' => now()->startOfMonth()->addDays(9)->toDateString(),
        ]);

        Livewire::test(SessionsCalendarWidget::class)
            ->assertSuccessful()
            ->assertSee('Dashboard Swim Group');
    }

    public function test_month_navigation_changes_the_visible_month(): void
    {
        $this->actingAs(User::factory()->create());
        // A session must exist, otherwise CanAuthorizeAccess aborts on hydration.
        GroupSession::factory()->create();

        Livewire::test(SessionsCalendarWidget::class)
            ->assertSet('month', now()->format('Y-m'))
            ->call('previousMonth')
            ->assertSet('month', now()->subMonthNoOverflow()->format('Y-m'))
            ->call('nextMonth')
            ->call('nextMonth')
            ->assertSet('month', now()->addMonthNoOverflow()->format('Y-m'));
    }
}
