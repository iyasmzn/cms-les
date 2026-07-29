<?php

namespace App\Notifications;

use App\Models\GroupMember;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseRegistrationStatusUpdated extends Notification
{
    use Queueable;

    /**
     * @var array<string, string>
     */
    private const STATUS_LABELS = [
        'pending' => 'Pending',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    public function __construct(private readonly GroupMember $member) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $group = $this->member->group;
        $courseName = $group?->institution?->name ?? 'Course';
        $groupName = $group?->name ?? 'your group';
        $status = self::STATUS_LABELS[$this->member->status] ?? $this->member->status;

        $message = (new MailMessage)
            ->subject("Your registration for {$groupName} is now {$status}")
            ->greeting("Hello {$this->member->full_name},")
            ->line("Your registration for {$groupName} ({$courseName}) has been updated.")
            ->line("Current status: {$status}.");

        if ($this->member->status === 'active') {
            $message->line('You are confirmed — we look forward to seeing you!');

            if ($group && ($schedule = $group->scheduleLabel())) {
                $message->line("Schedule: {$schedule}.");
            }
        } elseif ($this->member->status === 'inactive') {
            $message->line('Please contact us if you have any questions about this change.');
        }

        return $message
            ->action('View My Courses', route('courses.mine'))
            ->salutation('Thank you, '.setting('site_name', config('app.name')));
    }
}
