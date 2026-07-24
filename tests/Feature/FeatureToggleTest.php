<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_features_are_enabled_by_default(): void
    {
        $this->assertTrue(feature_enabled('toko'));
        $this->assertTrue(feature_enabled('login_register'));
        $this->assertTrue(feature_enabled('toko_checkout'));
    }

    public function test_disabling_toko_returns_not_found_for_products_and_cart(): void
    {
        Setting::set('feature_toko', false);

        $this->get(route('products.index'))->assertNotFound();
        $this->get(route('cart.index'))->assertNotFound();
    }

    public function test_disabling_login_register_returns_not_found_for_auth_pages(): void
    {
        Setting::set('feature_login_register', false);

        $this->get(route('login'))->assertNotFound();
        $this->get(route('register'))->assertNotFound();
    }

    public function test_toko_stays_open_but_checkout_is_blocked_without_login_register(): void
    {
        Setting::set('feature_login_register', false);

        // Shop browsing still works.
        $this->get(route('products.index'))->assertOk();
        $this->assertTrue(feature_enabled('toko'));

        // Checkout is unavailable.
        $this->assertFalse(feature_enabled('toko_checkout'));

        $user = User::factory()->create();
        $this->actingAs($user)->get(route('checkout.index'))->assertNotFound();
    }
}
