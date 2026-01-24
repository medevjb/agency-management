<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions before each test
        $permissions = [
            'view profiles',
            'create profile',
            'edit profile',
            'delete profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }

    public function test_user_can_view_profiles_index(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view profiles');

        $response = $this->actingAs($user)
            ->get(route('freelance-profiles.index'));

        $response->assertStatus(200);
    }

    public function test_user_can_create_profile(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create profile');

        $response = $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('freelance-profiles.store'), [
                'profile_name' => 'John Doe',
                'username' => 'johndoe',
                'bio' => 'Experienced developer',
                'skills' => 'PHP,Laravel',
                'hourly_rate' => '$50/hr',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('freelance-profiles.index'));
        $this->assertDatabaseHas('profiles', [
            'username' => 'johndoe',
            'profile_name' => 'John Doe',
        ]);
    }

    public function test_user_can_edit_profile(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit profile');

        $profile = Profile::factory()->withUser($user->id)->create();

        $response = $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->patch(route('freelance-profiles.update', $profile), [
                'profile_name' => 'Updated Name',
                'username' => $profile->username,
                'bio' => 'Updated bio',
            ]);

        $response->assertRedirect(route('freelance-profiles.index'));
        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'profile_name' => 'Updated Name',
        ]);
    }

    public function test_user_can_delete_profile(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete profile');

        $profile = Profile::factory()->withUser($user->id)->create();

        $response = $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->delete(route('freelance-profiles.destroy', $profile));

        $response->assertRedirect(route('freelance-profiles.index'));
        $this->assertSoftDeleted('profiles', [
            'id' => $profile->id,
        ]);
    }

    public function test_username_must_be_unique(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create profile');

        Profile::factory()->create(['username' => 'taken']);

        $response = $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('freelance-profiles.store'), [
                'profile_name' => 'Test User',
                'username' => 'taken',
            ]);

        $response->assertSessionHasErrors('username');
    }
}
