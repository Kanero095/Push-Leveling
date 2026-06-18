<?php

namespace Database\Seeders;

use App\Models\Mission;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkoutAndMissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Workouts
        $workouts = [
            // Push-ups
            [
                'name' => 'Knee Push-ups (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=IODxDxX7oi4',
                'type' => 'push_up',
                'description' => 'A modified push-up done on the knees to reduce intensity, ideal for beginners.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Standard Push-ups (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=IODxDxX7oi4',
                'type' => 'push_up',
                'description' => 'Classic push-ups to build upper body and core strength.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Diamond Push-ups (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=IODxDxX7oi4',
                'type' => 'push_up',
                'description' => 'Close-grip push-ups focusing heavily on triceps and inner chest.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Sit-ups
            [
                'name' => 'Crunch (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=Xyd_fa5zoEU',
                'type' => 'sit_up',
                'description' => 'Abdominal exercise targeting core muscles without straining lower back.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Standard Sit-ups (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=1fbU_MkV7NE',
                'type' => 'sit_up',
                'description' => 'Classic sit-up to build abdominal and hip flexor strength.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'V-Ups (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=iP2fjvG0g3w',
                'type' => 'sit_up',
                'description' => 'Advanced abdominal exercise bringing hands and feet together at the peak.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Squats
            [
                'name' => 'Assisted Squats (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=U3HlEF_E9fo',
                'type' => 'squat',
                'description' => 'Squatting with assistance (like holding a chair or door frame).',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Bodyweight Squats (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=U3HlEF_E9fo',
                'type' => 'squat',
                'description' => 'Classic bodyweight squat targeting quadriceps, hamstrings, and glutes.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Pistol Squats (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=U3HlEF_E9fo',
                'type' => 'squat',
                'description' => 'Single-leg squat requiring extreme balance, strength, and mobility.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Running
            [
                'name' => 'Light Jogging (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=_kGESn8ArrU',
                'type' => 'run',
                'description' => 'Comfortable paced jog to build cardiovascular endurance.',
                'reps_label' => 'kilometers',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Steady Run (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=_kGESn8ArrU',
                'type' => 'run',
                'description' => 'Consistent running pace to increase stamina.',
                'reps_label' => 'kilometers',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Sprint Interval Training (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=_kGESn8ArrU',
                'type' => 'run',
                'description' => 'High-intensity interval running for speed and anaerobic capacity.',
                'reps_label' => 'kilometers',
                'duration_label' => 'seconds',
            ],

            // Swimming
            [
                'name' => 'Recreational Swimming (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=dZgVxmf6jkA',
                'type' => 'swim',
                'description' => 'Light, leisurely swimming to build low-impact cardiovascular endurance.',
                'reps_label' => 'meters',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Breaststroke Swimming (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=dZgVxmf6jkA',
                'type' => 'swim',
                'description' => 'Consistent lap swimming focusing on breathing control and core body coordination.',
                'reps_label' => 'meters',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Sprint Interval Swimming (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=dZgVxmf6jkA',
                'type' => 'swim',
                'description' => 'High-intensity swim intervals to push peak heart rate and physical output.',
                'reps_label' => 'meters',
                'duration_label' => 'seconds',
            ],

            // Jump Rope
            [
                'name' => 'Basic Jump Rope (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=dZgVxmf6jkA',
                'type' => 'jump_rope',
                'description' => 'Slow, steady single jumps to build rhythm, foot coordination, and calf endurance.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Speed Jump Rope (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=dZgVxmf6jkA',
                'type' => 'jump_rope',
                'description' => 'Fast-paced single jumps with footwork variations (boxer step, alternate feet).',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Double Unders (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=dZgVxmf6jkA',
                'type' => 'jump_rope',
                'description' => 'Rope passes under the feet twice per single jump. Highly demanding cardio and timing.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Pull-ups
            [
                'name' => 'Assisted Pull-ups (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=eGo4IYlbE5g',
                'type' => 'pull_up',
                'description' => 'Pull-ups using resistance bands or machine assistance to support body weight.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Standard Pull-ups (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=eGo4IYlbE5g',
                'type' => 'pull_up',
                'description' => 'Classic bodyweight pull-up focusing on upper back and arm strength.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'L-Sit Pull-ups (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=eGo4IYlbE5g',
                'type' => 'pull_up',
                'description' => 'Strict pull-ups performed while holding legs in a horizontal L-shape, testing core strength.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Cycling
            [
                'name' => 'Casual Cycling (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=_kGESn8ArrU',
                'type' => 'cycle',
                'description' => 'Low-intensity outdoor or stationary cycling to build endurance and leg mobility.',
                'reps_label' => 'kilometers',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Road Cycling (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=_kGESn8ArrU',
                'type' => 'cycle',
                'description' => 'Moderate-paced cycling on paved roads or stationary cardio sessions targeting steady output.',
                'reps_label' => 'kilometers',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Mountain Biking / HIIT Cycling (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=_kGESn8ArrU',
                'type' => 'cycle',
                'description' => 'Challenging off-road trail riding or high-intensity interval cycling sprints.',
                'reps_label' => 'kilometers',
                'duration_label' => 'seconds',
            ],

            // Plank
            [
                'name' => 'Knee Plank (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=ASdvN_XEl_c',
                'type' => 'plank',
                'description' => 'Core stabilization exercise performed on elbows and knees, suitable for beginners.',
                'reps_label' => 'seconds',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Forearm Plank (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=ASdvN_XEl_c',
                'type' => 'plank',
                'description' => 'Classic full forearm plank holding your body in a straight line from head to heels.',
                'reps_label' => 'seconds',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Plank Shoulder Taps (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=ASdvN_XEl_c',
                'type' => 'plank',
                'description' => 'Full plank position while alternately tapping shoulders, requiring high core and arm stability.',
                'reps_label' => 'seconds',
                'duration_label' => 'seconds',
            ],

            // Lunges
            [
                'name' => 'Static Lunges (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=COKYKgQ8KR0',
                'type' => 'lunge',
                'description' => 'Bodyweight lunges performed in place to build quad and glute strength.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Walking Lunges (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=COKYKgQ8KR0',
                'type' => 'lunge',
                'description' => 'Continuous forward lunges to develop dynamic balance and leg muscle control.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Jumping Lunges (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=COKYKgQ8KR0',
                'type' => 'lunge',
                'description' => 'Explosive plyometric lunges switching legs mid-air for peak lower body power.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Burpees
            [
                'name' => 'Half Burpees (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=dZgVxmf6jkA',
                'type' => 'burpee',
                'description' => 'Modified burpee without the push-up or jump, great for developing movement coordination.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Standard Burpees (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=dZgVxmf6jkA',
                'type' => 'burpee',
                'description' => 'Classic full body burpee combining squat, kick back, push-up, jump back, and vertical jump.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Burpee Box Jumps (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=dZgVxmf6jkA',
                'type' => 'burpee',
                'description' => 'A standard burpee followed immediately by an explosive jump onto a plyometric box.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Gym - Bench Press / Chest Press
            [
                'name' => 'Dumbbell Chest Press (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=VmB1G1K7v94',
                'type' => 'bench_press',
                'description' => 'Dumbbell chest press on a flat bench to build chest, triceps, and front shoulders strength.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Barbell Bench Press (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=rT7DgCr-3pg',
                'type' => 'bench_press',
                'description' => 'Classic barbell bench press to build upper body power and muscle mass.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Incline Barbell Bench Press (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=SrqOu55lrYU',
                'type' => 'bench_press',
                'description' => 'Barbell bench press on an incline bench, targeting the upper portion of the pectoral muscles.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Gym - Deadlift
            [
                'name' => 'Kettlebell Deadlift (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=CAwf7n6Luuc',
                'type' => 'deadlift',
                'description' => 'A beginner-friendly deadlift variation using a kettlebell to learn proper hip hinge form.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Barbell Deadlift (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=CAwf7n6Luuc',
                'type' => 'deadlift',
                'description' => 'Standard barbell deadlift targeting the entire posterior chain, including glutes, hamstrings, and lower back.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Deficit Barbell Deadlift (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=CAwf7n6Luuc',
                'type' => 'deadlift',
                'description' => 'Deadlift performed while standing on an elevated platform (1-3 inches), increasing the range of motion and initial pull difficulty.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Gym - Bicep Curl
            [
                'name' => 'Dumbbell Bicep Curl (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=ykJmrZ5v0Oo',
                'type' => 'bicep_curl',
                'description' => 'Standing dumbbell curls targeting the biceps brachii with controlled form.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Barbell Bicep Curl (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=ykJmrZ5v0Oo',
                'type' => 'bicep_curl',
                'description' => 'Classic barbell curls to build mass and strength in the bicep muscles.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Incline Dumbbell Curl (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=ykJmrZ5v0Oo',
                'type' => 'bicep_curl',
                'description' => 'Bicep curls performed on an incline bench to place the biceps under maximum stretch for increased activation.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Gym - Shoulder Press
            [
                'name' => 'Seated Dumbbell Shoulder Press (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=qEwKCR5JCog',
                'type' => 'shoulder_press',
                'description' => 'Seated press targeting the anterior and lateral deltoids while providing back support.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Standing Barbell Overhead Press (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=qEwKCR5JCog',
                'type' => 'shoulder_press',
                'description' => 'An excellent compound exercise targeting the shoulders, triceps, and core stability.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Arnold Dumbbell Press (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=qEwKCR5JCog',
                'type' => 'shoulder_press',
                'description' => 'A variation of the dumbbell shoulder press starting with palms facing you and rotating them outward during the press.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Gym - Lat Pulldown / Row
            [
                'name' => 'Lat Pulldown (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=CAwf7n6Luuc',
                'type' => 'back_pull',
                'description' => 'A machine exercise targeting the latissimus dorsi to prepare for pull-ups.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Barbell Bent-Over Row (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=CAwf7n6Luuc',
                'type' => 'back_pull',
                'description' => 'A compound back exercise targeting the lats, traps, rhomboids, and lower back stability.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'One-Arm Dumbbell Row (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=CAwf7n6Luuc',
                'type' => 'back_pull',
                'description' => 'Unilateral rowing exercise targeting the back muscles while challenging core stability and avoiding muscular imbalances.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],

            // Gym - Squats
            [
                'name' => 'Goblet Squat (Beginner)',
                'difficulty' => 'beginner',
                'video_url' => 'https://www.youtube.com/watch?v=U3HlEF_E9fo',
                'type' => 'squat',
                'description' => 'Hold a dumbbell or kettlebell close to your chest to improve squat depth and leg strength.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Barbell Back Squat (Intermediate)',
                'difficulty' => 'intermediate',
                'video_url' => 'https://www.youtube.com/watch?v=U3HlEF_E9fo',
                'type' => 'squat',
                'description' => 'The king of lower body exercises, targeting quads, glutes, and hamstrings using a barbell.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
            [
                'name' => 'Barbell Front Squat (Advanced)',
                'difficulty' => 'advanced',
                'video_url' => 'https://www.youtube.com/watch?v=U3HlEF_E9fo',
                'type' => 'squat',
                'description' => 'A squat variation with the barbell held in front, placing more emphasis on the quadriceps and core strength.',
                'reps_label' => 'reps',
                'duration_label' => 'seconds',
            ],
        ];

        foreach ($workouts as $workout) {
            Workout::updateOrCreate(
                ['name' => $workout['name']],
                $workout
            );
        }

        // 2. Seed Missions
        $missions = [
            [
                'name' => 'Daily Push-up Challenge',
                'type' => 'push_up',
                'target' => 100,
                'base_xp' => 100,
            ],
            [
                'name' => 'Daily Sit-up Challenge',
                'type' => 'sit_up',
                'target' => 100,
                'base_xp' => 100,
            ],
            [
                'name' => 'Daily Squat Challenge',
                'type' => 'squat',
                'target' => 100,
                'base_xp' => 100,
            ],
            [
                'name' => 'Daily Road Run',
                'type' => 'run',
                'target' => 10,
                'base_xp' => 150,
            ],
        ];

        foreach ($missions as $mission) {
            Mission::updateOrCreate(
                ['type' => $mission['type']],
                $mission
            );
        }

        // 3. Seed Mock Users for Leaderboard
        $mockUsers = [
            [
                'name' => 'Saitama (One Punch)',
                'email' => 'saitama@example.com',
                'password' => 'password123',
                'xp_total' => 105000,
                'level' => 101,
                'user_level' => 'advanced',
                'phone' => '08123456789',
                'title' => 'Limit Breaker',
                'xp_categories' => [
                    'push_up' => 40000,
                    'sit_up' => 30000,
                    'squat' => 25000,
                    'run' => 10000,
                ],
            ],
            [
                'name' => 'Baki Hanma',
                'email' => 'baki@example.com',
                'password' => 'password123',
                'xp_total' => 30500,
                'level' => 45,
                'user_level' => 'advanced',
                'phone' => '08123456788',
                'title' => 'Iron Body',
                'xp_categories' => [
                    'pull_up' => 15000,
                    'bench_press' => 10000,
                    'deadlift' => 5500,
                ],
            ],
            [
                'name' => 'Son Goku',
                'email' => 'goku@example.com',
                'password' => 'password123',
                'xp_total' => 12500,
                'level' => 25,
                'user_level' => 'advanced',
                'phone' => '08123456787',
                'title' => 'Relentless',
                'xp_categories' => [
                    'squat' => 6000,
                    'push_up' => 4000,
                    'sit_up' => 2500,
                ],
            ],
            [
                'name' => 'Izuku Midoriya',
                'email' => 'deku@example.com',
                'password' => 'password123',
                'xp_total' => 4100,
                'level' => 12,
                'user_level' => 'intermediate',
                'phone' => '08123456786',
                'title' => 'Awakened',
                'xp_categories' => [
                    'run' => 2000,
                    'squat' => 1500,
                    'push_up' => 600,
                ],
            ],
        ];

        foreach ($mockUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'xp_total' => $userData['xp_total'],
                    'level' => $userData['level'],
                    'user_level' => $userData['user_level'],
                    'phone' => $userData['phone'],
                    'title' => $userData['title'],
                ]
            );

            // Seed mock category logs so they show up on filtered leaderboards
            if (isset($userData['xp_categories']) && ! WorkoutLog::where('user_id', $user->id)->exists()) {
                foreach ($userData['xp_categories'] as $type => $xpAmount) {
                    $workout = Workout::where('type', $type)->first();
                    if ($workout) {
                        WorkoutLog::create([
                            'user_id' => $user->id,
                            'workout_id' => $workout->id,
                            'reps' => 100,
                            'duration' => 600,
                            'xp_earned' => $xpAmount,
                        ]);
                    }
                }
            }
        }
    }
}
