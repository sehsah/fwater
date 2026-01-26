<?php

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates permissions from config', function () {
    $this->artisan('permission:sync')
        ->assertSuccessful();

    $names = config('permissions.sync', []);
    expect($names)->not->toBeEmpty();

    foreach ($names as $name) {
        expect(Permission::where('name', $name)->exists())->toBeTrue();
    }
});

it('is idempotent and does not duplicate permissions', function () {
    $this->artisan('permission:sync')->assertSuccessful();
    $firstCount = Permission::count();

    $this->artisan('permission:sync')->assertSuccessful();
    expect(Permission::count())->toBe($firstCount);
});
