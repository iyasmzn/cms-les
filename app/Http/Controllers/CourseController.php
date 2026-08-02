<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ProtectsAgainstSpam;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupSession;
use App\Models\Institution;
use App\Models\User;
use App\Support\CalendarMonth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    use ProtectsAgainstSpam;

    /**
     * List course institutions (les) that run with groups.
     */
    public function index(): View
    {
        $institutions = Institution::query()
            ->active()
            ->where('has_groups', true)
            ->withCount(['groups' => fn ($query) => $query->where('is_active', true)])
            ->ordered()
            ->get();

        $siteName = setting('site_name', config('app.name'));

        $seo = [
            'title' => "Courses | {$siteName}",
            'description' => "Browse the courses (les) offered by {$siteName} and their groups: schedule, level, coach, and online registration.",
            'canonical' => route('courses.index'),
        ];

        return view('courses.index', compact('institutions', 'seo'));
    }

    /**
     * A single course institution with its active groups.
     */
    public function show(Institution $institution): View
    {
        abort_unless($institution->has_groups && $institution->is_active, 404);

        $groups = $institution->groups()
            ->active()
            ->ordered()
            ->with('teacher')
            ->withCount(['members' => fn ($query) => $query->whereIn('status', ['pending', 'active'])])
            ->get();

        $siteName = setting('site_name', config('app.name'));

        $seo = [
            'title' => "{$institution->name} | {$siteName}",
            'description' => "Groups, schedule, and registration for {$institution->name} at {$siteName}.",
            'canonical' => route('courses.show', $institution),
        ];

        return view('courses.show', compact('institution', 'groups', 'seo'));
    }

    /**
     * The registration form for joining a single group.
     */
    public function registerForm(Institution $institution, Group $group): View|RedirectResponse
    {
        abort_unless($institution->has_groups && $institution->is_active && $group->is_active, 404);

        if (! $group->isOpen()) {
            return redirect()->route('courses.show', $institution)
                ->with('error', "Group \"{$group->name}\" is currently full or closed for registration.");
        }

        $siteName = setting('site_name', config('app.name'));

        $seo = [
            'title' => "Register — {$group->name} | {$siteName}",
            'description' => "Register to join {$group->name} ({$institution->name}) at {$siteName}.",
            'canonical' => route('courses.register', [$institution, $group]),
        ];

        return view('courses.register', compact('institution', 'group', 'seo'));
    }

    /**
     * The logged-in member's own course registrations, newest first.
     */
    public function mine(Request $request): View
    {
        $registrations = $request->user()->courseRegistrations()
            ->with(['group' => fn ($query) => $query->with('institution', 'teacher'), 'payments'])
            ->latest()
            ->get();

        $siteName = setting('site_name', config('app.name'));

        $seo = [
            'title' => "My Courses | {$siteName}",
            'description' => 'Your course registrations, schedule, and status.',
            'robots' => 'noindex, nofollow',
        ];

        return view('courses.mine', compact('registrations', 'seo'));
    }

    /**
     * A month calendar of the logged-in member's course sessions, optionally
     * narrowed to a single group the member is registered in via `?group=`.
     */
    public function calendar(Request $request): View
    {
        $calendar = CalendarMonth::fromString($request->query('month'));

        $registrations = $request->user()->courseRegistrations()
            ->whereIn('status', ['pending', 'active'])
            ->with('group')
            ->get();

        $filteredGroup = null;

        if (filled($groupId = $request->query('group'))) {
            $filteredGroup = $registrations->firstWhere('group_id', (int) $groupId)?->group;

            abort_if($filteredGroup === null, 404);
        }

        $groupIds = $filteredGroup
            ? collect([$filteredGroup->id])
            : $registrations->pluck('group_id')->unique();

        $sessions = GroupSession::query()
            ->whereIn('group_id', $groupIds)
            ->whereBetween('date', [$calendar->rangeStart()->toDateString(), $calendar->rangeEnd()->toDateString()])
            ->where('status', '!=', 'cancelled')
            ->with('group.institution')
            ->ordered()
            ->get()
            ->groupBy(fn (GroupSession $session): string => $session->date->toDateString());

        $siteName = setting('site_name', config('app.name'));

        $seo = [
            'title' => "My Schedule | {$siteName}",
            'description' => 'Your course session calendar.',
            'robots' => 'noindex, nofollow',
        ];

        // Lets each day cell link straight to the member's own session list.
        $registrationByGroup = $registrations->pluck('id', 'group_id');

        return view('courses.calendar', compact('calendar', 'sessions', 'filteredGroup', 'registrationByGroup', 'seo'));
    }

    /**
     * Every session of a single group the logged-in member is registered in,
     * split into upcoming and past. Cancelled sessions stay visible here (the
     * calendar hides them) so members can see a meeting was called off.
     */
    public function sessions(Request $request, GroupMember $registration): View
    {
        abort_unless($registration->user_id === $request->user()->id, 404);

        $group = $registration->group;

        abort_if($group === null, 404);

        $group->load('institution', 'teacher');

        $allSessions = $group->sessions()->ordered()->get();

        $today = today();

        $upcoming = $allSessions->filter(fn (GroupSession $session): bool => $session->date->gte($today))->values();
        $past = $allSessions->filter(fn (GroupSession $session): bool => $session->date->lt($today))
            ->sortByDesc('date')
            ->values();

        $siteName = setting('site_name', config('app.name'));

        $seo = [
            'title' => "{$group->name} — Sessions | {$siteName}",
            'description' => "Session schedule for {$group->name}.",
            'robots' => 'noindex, nofollow',
        ];

        return view('courses.sessions', compact('registration', 'group', 'upcoming', 'past', 'seo'));
    }

    /**
     * Store a new group member from the public registration form.
     */
    public function register(Request $request, Institution $institution, Group $group): RedirectResponse
    {
        abort_unless($institution->has_groups && $institution->is_active && $group->is_active, 404);

        $request->validate($this->spamProtectionRules($request));

        if (! $group->isOpen()) {
            return back()->with('error', "Group \"{$group->name}\" is currently full or closed for registration.");
        }

        // A participant may not sign up for the same group twice while an
        // earlier registration is still pending or active.
        $activeInGroup = fn (string $column) => Rule::unique('group_members', $column)
            ->where('group_id', $group->id)
            ->whereIn('status', ['pending', 'active']);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', $activeInGroup('phone')],
            'email' => ['nullable', 'email', 'max:120', $activeInGroup('email')],
            'address' => ['nullable', 'string', 'max:500'],
            'parent_name' => ['nullable', 'string', 'max:120'],
            'parent_phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'phone.unique' => 'This phone number is already registered for this group.',
            'email.unique' => 'This email is already registered for this group.',
        ]);

        $data['status'] = 'pending';
        $data['user_id'] = $request->user()?->id;

        $member = $group->members()->create($data);

        $this->notifyAdmins($member, $group, $institution);

        $redirect = redirect()->route('courses.show', $institution)
            ->with('success', 'Registration submitted! We will contact you shortly to confirm your spot.');

        // Invite guests to create an account so they can track this registration
        // under "My Courses". Logged-in members are already linked automatically.
        if (! $request->user()) {
            $redirect->with('offer_account', [
                'name' => $data['full_name'],
                'email' => $data['email'] ?? null,
            ]);
        }

        return $redirect;
    }

    /**
     * Send a database notification to panel admins about a new registration.
     */
    private function notifyAdmins(GroupMember $member, Group $group, Institution $institution): void
    {
        $recipients = $this->adminRecipients();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::make()
            ->title('New course registration')
            ->body("{$member->full_name} registered for {$group->name} — {$institution->name}.")
            ->icon('heroicon-o-user-plus')
            ->success()
            ->sendToDatabase($recipients);
    }

    /**
     * Panel users who should be notified: super admins and anyone allowed to
     * view groups.
     *
     * @return Collection<int, User>
     */
    private function adminRecipients(): Collection
    {
        return User::query()
            ->where(function ($query): void {
                $query->whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))
                    ->orWhereHas('permissions', fn ($q) => $q->where('name', 'ViewAny:Group'))
                    ->orWhereHas('roles.permissions', fn ($q) => $q->where('name', 'ViewAny:Group'));
            })
            ->get();
    }
}
