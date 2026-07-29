<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Institution;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupSeeder extends Seeder
{
    /**
     * Seed groups (kelompok) for every course, together with sample members,
     * dated sessions, and payments so the calendar, My Courses, and payment
     * tracking all have data out of the box.
     */
    public function run(): void
    {
        /** @var Collection<int, Teacher> $coaches */
        $coaches = Teacher::all();

        $templates = [
            ['name' => 'Kelompok Pemula', 'level' => 'Pemula', 'days' => ['mon', 'wed'], 'start' => '16:00', 'end' => '17:30', 'capacity' => 10],
            ['name' => 'Kelompok Lanjutan', 'level' => 'Lanjutan', 'days' => ['tue', 'thu'], 'start' => '15:30', 'end' => '17:00', 'capacity' => 8],
        ];

        Institution::query()->where('has_groups', true)->get()->each(function (Institution $course) use ($coaches, $templates): void {
            foreach ($templates as $index => $template) {
                $group = Group::updateOrCreate(
                    ['institution_id' => $course->id, 'slug' => Str::slug($template['name'])],
                    [
                        'teacher_id' => $coaches->isNotEmpty() ? $coaches->random()->id : null,
                        'name' => $template['name'],
                        'level' => $template['level'],
                        'days' => $template['days'],
                        'start_time' => $template['start'],
                        'end_time' => $template['end'],
                        'location' => $course->name,
                        'capacity' => $template['capacity'],
                        'description' => "Kelompok {$template['level']} untuk {$course->name}.",
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ],
                );

                // Only populate sample data once, so re-seeding stays clean.
                if ($group->members()->exists()) {
                    continue;
                }

                GroupMember::factory()->count(5)->active()->create(['group_id' => $group->id]);
                GroupMember::factory()->count(2)->create(['group_id' => $group->id, 'status' => 'pending']);

                // Dated sessions for this and next month, each with a fee.
                $group->generateSessions(now()->startOfMonth(), now()->addMonth()->endOfMonth(), 50000);

                // Bill active members for past sessions; mark most as paid.
                $group->sessions()->whereDate('date', '<=', now())->orderBy('date')->get()
                    ->each(function ($session): void {
                        $session->billActiveMembers();

                        $session->payments()->get()->each(function ($payment, int $idx): void {
                            if ($idx % 4 !== 0) {
                                $payment->markPaid('cash');
                            }
                        });
                    });
            }
        });
    }
}
