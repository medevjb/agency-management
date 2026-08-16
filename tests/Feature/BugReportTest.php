<?php

namespace Tests\Feature;

use App\Models\BugCategory;
use App\Models\BugPriority;
use App\Models\BugReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BugReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected BugCategory $category;

    protected BugPriority $priority;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
        $this->category = BugCategory::create([
            'name' => 'Frontend',
            'slug' => 'frontend',
        ]);
        $this->priority = BugPriority::create([
            'name' => 'High',
            'slug' => 'high',
            'color' => '#ff0000',
            'level' => 3,
        ]);
    }

    public function test_bug_report_category_can_be_updated(): void
    {
        $bugReport = BugReport::create([
            'user_id' => $this->user->id,
            'bug_category_id' => $this->category->id,
            'bug_priority_id' => $this->priority->id,
            'title' => 'Dark Mode Needed',
            'description' => 'Developer expectation',
            'status' => 'pending',
        ]);

        $newCategory = BugCategory::create([
            'name' => 'Backend',
            'slug' => 'backend',
        ]);

        $response = $this->actingAs($this->user)->patch(route('bug-reports.update', $bugReport), [
            'title' => $bugReport->title,
            'description' => $bugReport->description,
            'bug_category_id' => $newCategory->id,
            'bug_priority_id' => $this->priority->id,
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('bug-reports.show', $bugReport));
        $this->assertEquals($newCategory->id, $bugReport->fresh()->bug_category_id);
    }
}
