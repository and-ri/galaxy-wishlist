<?php

use App\Models\User;
use App\Models\Wish;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('гість бачить лендинг', function () {
    $this->get('/')->assertOk()->assertSee('Почати');
});

test('огляд показує найближчі дні народження колег', function () {
    $user = User::factory()->create(['name' => 'Андрій Рябченко']);
    User::factory()->create(['name' => 'Олена Коваль', 'birthday' => now()->addDays(3)->subYears(30)]);

    $this->actingAs($user)->get('/')
        ->assertOk()
        ->assertSee('Привіт, Андрій')
        ->assertSee('Олена Коваль')
        ->assertSee('через 3 дні')
        ->assertSee('Вкажіть дату народження');
});

test('монограма аватара коректна для кирилиці', function () {
    $user = User::factory()->create(['name' => 'Ірина Бондар']);

    $this->actingAs($user)->get('/users')->assertOk()->assertSee('ІБ');
});

test('зняту позначку «Куплено» можна зберегти', function () {
    $user = User::factory()->create();
    $wish = Wish::create(['user_id' => $user->id, 'title' => 'Книга', 'is_purchased' => true]);

    $this->actingAs($user)->get(route('wishes.edit', $wish))
        ->assertOk()
        ->assertSee('<input type="hidden" name="is_purchased" value="0">', false);

    $this->actingAs($user)->put(route('wishes.update', $wish), [
        'title' => 'Книга',
        'is_purchased' => '0',
    ])->assertRedirect(route('wishes.index'));

    expect($wish->fresh()->is_purchased)->toBeFalse();
});

test('усі сторінки застосунку відкриваються', function () {
    $user = User::factory()->create(['birthday' => '1990-03-12']);
    $wish = Wish::create(['user_id' => $user->id, 'title' => 'Навушники', 'price' => 13999, 'currency' => 'UAH', 'priority' => 2]);

    foreach (['/wishes', '/wishes/create', "/wishes/{$wish->id}/edit", '/users', "/users/{$user->id}", '/profile'] as $uri) {
        $this->actingAs($user)->get($uri)->assertOk();
    }

    $this->actingAs($user)->get('/wishes')->assertSee("13\u{00A0}999\u{00A0}₴");
});
