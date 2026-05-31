<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Response;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'System Admin',
            'email'    => 'admin@complaints.test',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ── Sample users ──────────────────────────────────────────────────────
        $users = User::factory(10)->create();

        // ── Categories ────────────────────────────────────────────────────────
        $categories = collect([
            ['name' => 'Billing',         'color' => '#ef4444', 'description' => 'Payment and billing issues'],
            ['name' => 'Technical',       'color' => '#3b82f6', 'description' => 'Technical support requests'],
            ['name' => 'Customer Service','color' => '#f59e0b', 'description' => 'General customer service'],
            ['name' => 'Product',         'color' => '#10b981', 'description' => 'Product feedback and issues'],
            ['name' => 'Delivery',        'color' => '#8b5cf6', 'description' => 'Shipping and delivery complaints'],
            ['name' => 'Other',           'color' => '#6b7280', 'description' => 'Other complaints'],
        ])->map(fn($c) => Category::create($c));

        // ── Tags ─────────────────────────────────────────────────────────────
        $tags = collect([
            ['name' => 'refund-needed',   'color' => '#ef4444'],
            ['name' => 'follow-up',       'color' => '#f59e0b'],
            ['name' => 'escalated',       'color' => '#dc2626'],
            ['name' => 'first-complaint', 'color' => '#10b981'],
            ['name' => 'vip-customer',    'color' => '#8b5cf6'],
        ])->map(fn($t) => Tag::create($t));

        // ── Complaints ────────────────────────────────────────────────────────
        $statuses   = ['pending', 'open', 'in_progress', 'resolved', 'closed', 'rejected'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        $complaints = Complaint::factory(50)->make()->each(function ($c) use ($users, $categories, $statuses, $priorities) {
            $c->user_id     = $users->random()->id;
            $c->category_id = $categories->random()->id;
            $c->status      = $statuses[array_rand($statuses)];
            $c->priority    = $priorities[array_rand($priorities)];
            $c->save();
        });

        // Attach random tags to each complaint
        Complaint::all()->each(function ($c) use ($tags) {
            $c->tags()->attach($tags->random(rand(0, 2))->pluck('id'));
        });

        // ── Responses ─────────────────────────────────────────────────────────
        Complaint::all()->each(function ($c) use ($admin) {
            Response::create([
                'complaint_id' => $c->id,
                'user_id'      => $admin->id,
                'message'      => 'Thank you for reaching out. We have received your complaint and will be looking into it shortly.',
                'is_internal'  => false,
            ]);
        });
    }
}
