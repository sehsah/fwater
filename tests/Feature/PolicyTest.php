<?php

use App\Models\User;
use App\Models\Property;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user has roles trait', function () {
    $user = User::factory()->create();
    expect(in_array(\Spatie\Permission\Traits\HasRoles::class, class_uses_recursive($user)))->toBeTrue();
});

test('user cannot create property without permission', function () {
    $user = User::factory()->create();

    expect($user->can('create', Property::class))->toBeFalse();
});

test('user can create property with permission', function () {
    $user = User::factory()->create();
    $permission = Permission::create(['name' => 'create_property', 'guard_name' => 'web']);
    $user->givePermissionTo($permission);

    expect($user->can('create', Property::class))->toBeTrue();
});

test('user can view property with permission', function () {
    $user = User::factory()->create();
    $permission = Permission::create(['name' => 'view_property', 'guard_name' => 'web']);
    $user->givePermissionTo($permission);

    $property = Property::create([
        'name' => 'Test Property',
        'type' => 'Villa',
        'units_count' => 1,
    ]);

    expect($user->can('view', $property))->toBeTrue();
});

test('user cannot view property without permission', function () {
    $user = User::factory()->create();

    $property = Property::create([
        'name' => 'Test Property',
        'type' => 'Villa',
        'units_count' => 1,
    ]);

    expect($user->can('view', $property))->toBeFalse();
});
