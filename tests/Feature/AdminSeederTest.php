<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_or_short_password_cannot_create_an_admin(): void
    {
        foreach ([null, '', 'short'] as $password) {
            config(['auth.local_admin_password' => $password]);

            try {
                $this->seed(UserSeeder::class);
                $this->fail('An admin must require an explicitly configured password.');
            } catch (RuntimeException $exception) {
                $this->assertStringContainsString('LOCAL_ADMIN_PASSWORD', $exception->getMessage());
            }

            $this->assertDatabaseCount('users', 0);
        }
    }

    public function test_admin_uses_the_configured_password_and_stores_only_its_hash(): void
    {
        $password = bin2hex(random_bytes(16));
        config(['auth.local_admin_password' => $password]);

        $this->seed(UserSeeder::class);

        $admin = User::where('email', 'admin@koperasiku.id')->sole();
        $this->assertTrue(Hash::check($password, $admin->password));
        $this->assertNotSame($password, $admin->password);
        $this->assertDatabaseCount('users', 1);
    }
}
