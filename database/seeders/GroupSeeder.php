<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Institution;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Seed sample groups (kelompok) for course institutions (les).
     */
    public function run(): void
    {
        $course = Institution::where('slug', 'swimming')->first();

        if ($course === null) {
            return;
        }

        $coach = Teacher::where('institution_id', $course->id)->first()
            ?? Teacher::first();

        $groups = [
            [
                'slug' => 'beginner-a',
                'name' => 'Beginner A',
                'level' => 'Beginner',
                'days' => ['mon', 'wed'],
                'start_time' => '16:00',
                'end_time' => '17:30',
                'location' => 'Main Pool',
                'capacity' => 10,
                'description' => 'Water confidence, floating, and basic strokes for new swimmers.',
                'sort_order' => 1,
            ],
            [
                'slug' => 'intermediate-b',
                'name' => 'Intermediate B',
                'level' => 'Intermediate',
                'days' => ['tue', 'thu'],
                'start_time' => '15:00',
                'end_time' => '16:30',
                'location' => 'Main Pool',
                'capacity' => 8,
                'description' => 'Stroke refinement (freestyle, backstroke) and endurance building.',
                'sort_order' => 2,
            ],
            [
                'slug' => 'advanced-c',
                'name' => 'Advanced C',
                'level' => 'Advanced',
                'days' => ['sat'],
                'start_time' => '08:00',
                'end_time' => '10:00',
                'location' => 'Olympic Pool',
                'capacity' => 6,
                'description' => 'Competitive technique, all four strokes, and race preparation.',
                'sort_order' => 3,
            ],
        ];

        foreach ($groups as $group) {
            Group::updateOrCreate(
                ['institution_id' => $course->id, 'slug' => $group['slug']],
                array_merge($group, [
                    'institution_id' => $course->id,
                    'teacher_id' => $coach?->id,
                    'is_active' => true,
                ]),
            );
        }
    }
}
