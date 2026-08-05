<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ProtectsAgainstSpam;
use App\Models\CoursePayment;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupSession;
use App\Models\Institution;
use App\Models\PaymentAccount;
use App\Models\User;
use App\Support\CalendarMonth;
use Closure;
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
    public function show(Request $request, Institution $institution): View
    {
        abort_unless($institution->has_groups && $institution->is_active, 404);

        $existingRegistration = $this->existingRegistration($request->user(), $institution);

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

        return view('courses.show', compact('institution', 'groups', 'existingRegistration', 'seo'));
    }

    /**
     * The registration form for joining a single group.
     */
    public function registerForm(Request $request, Institution $institution, Group $group): View|RedirectResponse
    {
        abort_unless($institution->has_groups && $institution->is_active && $group->is_active, 404);

        if ($existing = $this->existingRegistration($request->user(), $institution)) {
            return redirect()->route('courses.show', $institution)
                ->with('error', $this->alreadyRegisteredMessage($existing));
        }

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
     * The logged-in member's course bills across every registration, newest
     * first, optionally narrowed to one registration via `?registration=`.
     */
    public function billing(Request $request): View
    {
        $registrations = $request->user()->courseRegistrations()
            ->with(['group.institution', 'payments.session'])
            ->latest()
            ->get();

        $selected = null;

        if (filled($registrationId = $request->query('registration'))) {
            $selected = $registrations->firstWhere('id', (int) $registrationId);

            abort_if($selected === null, 404);
        }

        $billed = $selected ? collect([$selected]) : $registrations;

        // Newest bill first, falling back to the record's own date for bills
        // not tied to a session (one-off charges such as registration fees).
        $billed->each(fn (GroupMember $registration) => $registration->setRelation(
            'payments',
            $registration->payments
                ->sortByDesc(fn (CoursePayment $payment) => $payment->session?->date ?? $payment->created_at)
                ->values(),
        ));

        $totals = [];

        foreach ($billed as $registration) {
            foreach ($registration->paymentTotals() as $key => $amount) {
                $totals[$key] = ($totals[$key] ?? 0.0) + $amount;
            }
        }

        $totals += ['billed' => 0.0, 'paid' => 0.0, 'outstanding' => 0.0, 'review' => 0.0, 'waived' => 0.0];

        $siteName = setting('site_name', config('app.name'));

        $seo = [
            'title' => "My Bills | {$siteName}",
            'description' => 'Your course bills and payment history.',
            'robots' => 'noindex, nofollow',
        ];

        return view('courses.billing', compact('registrations', 'billed', 'selected', 'totals', 'seo'));
    }

    /**
     * The payment form for a single bill: pick a channel, see where to pay,
     * and attach proof.
     */
    public function payForm(Request $request, CoursePayment $payment): View
    {
        $this->authorizeBill($request, $payment);

        abort_unless($payment->isPayable(), 404);

        $payment->load('member.group.institution', 'session');

        $accounts = PaymentAccount::query()->active()->ordered()->get()
            ->filter(fn (PaymentAccount $account): bool => $account->isPresentable());

        $siteName = setting('site_name', config('app.name'));

        $seo = [
            'title' => "Pay Bill | {$siteName}",
            'description' => 'Settle a course bill and upload your proof of payment.',
            'robots' => 'noindex, nofollow',
        ];

        return view('courses.pay', [
            'payment' => $payment,
            'bankAccounts' => $accounts->where('type', 'bank')->values(),
            'qrisAccounts' => $accounts->where('type', 'qris')->values(),
            'seo' => $seo,
        ]);
    }

    /**
     * Record the member's payment confirmation and queue it for verification.
     * Nothing is marked paid here — an admin still has to check the proof.
     */
    public function pay(Request $request, CoursePayment $payment): RedirectResponse
    {
        $this->authorizeBill($request, $payment);

        abort_unless($payment->isPayable(), 404);

        $presentableAccounts = PaymentAccount::query()->active()->get()
            ->filter(fn (PaymentAccount $account): bool => $account->isPresentable());

        $data = $request->validate([
            'method' => ['required', Rule::in(array_keys(CoursePayment::memberMethodOptions()))],
            'payment_account_id' => [
                Rule::requiredIf(fn (): bool => in_array($request->input('method'), ['transfer', 'qris'], true)),
                'nullable',
                Rule::in($presentableAccounts->pluck('id')->all()),
                // A QRIS payment tagged with a bank account (or vice versa)
                // would send the admin looking in the wrong place.
                function (string $attribute, mixed $value, Closure $fail) use ($request, $presentableAccounts): void {
                    $expected = $request->input('method') === 'qris' ? 'qris' : 'bank';

                    if ($presentableAccounts->firstWhere('id', (int) $value)?->type !== $expected) {
                        $fail('The selected destination does not match the payment method.');
                    }
                },
            ],
            // Cash is settled in person, so proof is only demanded when money
            // was moved without a witness.
            'proof' => [
                Rule::requiredIf(fn (): bool => in_array($request->input('method'), ['transfer', 'qris'], true)),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
            'payer_note' => ['nullable', 'string', 'max:500'],
        ], [
            'payment_account_id.required' => 'Choose where you sent the payment.',
            'proof.required' => 'Attach a screenshot or photo of your transfer receipt.',
        ]);

        $proofPath = $request->hasFile('proof')
            ? $request->file('proof')->store('payment-proofs', 'public')
            : null;

        $payment->submitConfirmation(
            $data['method'],
            isset($data['payment_account_id']) ? (int) $data['payment_account_id'] : null,
            $proofPath,
            $data['payer_note'] ?? null,
        );

        return redirect()
            ->route('courses.billing', ['registration' => $payment->group_member_id])
            ->with('success', $data['method'] === 'cash'
                ? 'Thanks — we have noted that you are paying cash. The admin will confirm it once received.'
                : 'Payment confirmation sent. The admin will verify your proof shortly.');
    }

    /**
     * A bill may only be touched by the account that owns the registration.
     */
    private function authorizeBill(Request $request, CoursePayment $payment): void
    {
        abort_unless($payment->member?->user_id === $request->user()->id, 404);
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

        // Keyed by session so each row can show whether that meeting is billed.
        $paymentBySession = $registration->payments()
            ->whereNotNull('group_session_id')
            ->get()
            ->keyBy('group_session_id');

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

        return view('courses.sessions', compact('registration', 'group', 'upcoming', 'past', 'paymentBySession', 'seo'));
    }

    /**
     * Store a new group member from the public registration form.
     */
    public function register(Request $request, Institution $institution, Group $group): RedirectResponse
    {
        abort_unless($institution->has_groups && $institution->is_active && $group->is_active, 404);

        $request->validate($this->spamProtectionRules($request));

        if ($existing = $this->existingRegistration($request->user(), $institution)) {
            return back()->with('error', $this->alreadyRegisteredMessage($existing));
        }

        if (! $group->isOpen()) {
            return back()->with('error', "Group \"{$group->name}\" is currently full or closed for registration.");
        }

        // A participant may only hold one group per course unit, so the phone
        // and email checks span every group of this institution — not just the
        // one being applied for. This is what stops guests (who have no user
        // account to match on) from signing up twice.
        $activeInCourse = fn (string $column) => Rule::unique('group_members', $column)
            ->whereIn('group_id', $institution->groups()->select('id'))
            ->whereIn('status', ['pending', 'active']);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', $activeInCourse('phone')],
            'email' => ['nullable', 'email', 'max:120', $activeInCourse('email')],
            'address' => ['nullable', 'string', 'max:500'],
            'parent_name' => ['nullable', 'string', 'max:120'],
            'parent_phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'phone.unique' => 'This phone number is already registered for a group in this course.',
            'email.unique' => 'This email is already registered for a group in this course.',
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
     * The logged-in member's pending or active registration in any group of
     * this course, if any. A participant may only hold one group per course
     * unit, so this doubles as the "already registered" guard.
     */
    private function existingRegistration(?User $user, Institution $institution): ?GroupMember
    {
        if ($user === null) {
            return null;
        }

        return GroupMember::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->whereIn('group_id', $institution->groups()->select('id'))
            ->with('group')
            ->first();
    }

    /**
     * Why a member is being turned away from a second group in the same course.
     */
    private function alreadyRegisteredMessage(GroupMember $existing): string
    {
        $groupName = $existing->group?->name ?? 'another group';

        return "You are already registered in \"{$groupName}\" for this course. Only one group per course is allowed — contact us if you need to switch.";
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
