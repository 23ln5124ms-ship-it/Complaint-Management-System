<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintResolvedAtTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolved_complaints_get_resolved_at_timestamp(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $complaint = Complaint::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'pending',
            'resolved_at' => null,
        ]);

        $complaint->update(['status' => 'resolved']);

        $this->assertNotNull($complaint->fresh()->resolved_at);
    }
}
