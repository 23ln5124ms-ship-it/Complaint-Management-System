<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ComplaintTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_ticket_number_uses_highest_existing_number(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        DB::table('complaints')->insert([
            [
                'ticket_number' => 'CMP-2026-00050',
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => 'Existing complaint 1',
                'description' => 'Seeded duplicate test record',
                'status' => 'pending',
                'priority' => 'high',
                'attachment' => null,
                'created_at' => '2026-06-01 00:00:00',
                'updated_at' => '2026-06-01 00:00:00',
            ],
            [
                'ticket_number' => 'CMP-2026-00051',
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => 'Existing complaint 2',
                'description' => 'Seeded duplicate test record',
                'status' => 'pending',
                'priority' => 'high',
                'attachment' => null,
                'created_at' => '2026-06-02 00:00:00',
                'updated_at' => '2026-06-02 00:00:00',
            ],
        ]);

        $this->assertSame('CMP-2026-00052', Complaint::generateTicketNumber());
    }
}
