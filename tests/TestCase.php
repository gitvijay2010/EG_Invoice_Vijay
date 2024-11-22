<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use App\Models\Admin;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    // Create and return a user for testing
    protected function authenticatedUser()
    {
        return User::factory()->create();
    }

    // Authenticate a user for the tests
    protected function actingAsAuthenticatedUser()
    {
        $user = $this->authenticatedUser();
        $this->actingAs($user);
        return $user;
    }

    // Create and return an admin for testing
    protected function authenticatedAdmin()
    {
        return Admin::factory()->create();
        // return Admin::firstOrFail();
    }

    // Authenticate an admin for the tests
    protected function actingAsAuthenticatedAdmin()
    {
        $admin = $this->authenticatedAdmin();
        $this->actingAs($admin, 'admin');
        return $admin;
    }
}
