<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_and_owner_can_open_dashboard(): void
    {
        $this->get('/')->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk();
    }

    public function test_owner_can_sign_in_and_sign_out(): void
    {
        $user = User::factory()->create(['password' => 'very-long-test-password']);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'very-long-test-password',
        ])->assertRedirect(route('dashboard'));

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_owner_account_can_only_be_created_from_the_terminal(): void
    {
        $this->artisan('app:create-owner')
            ->expectsQuestion('Nama pemilik', 'Pemilik Yani Kost')
            ->expectsQuestion('Email login', 'owner@example.test')
            ->expectsQuestion('Kata sandi (minimal 12 karakter)', 'password-aman-123')
            ->expectsQuestion('Ulangi kata sandi', 'password-aman-123')
            ->expectsOutput('Akun pemilik owner@example.test berhasil dibuat.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', ['email' => 'owner@example.test']);
    }

    public function test_all_dashboard_sections_require_authentication_and_render_for_owner(): void
    {
        $user = User::factory()->create();
        $routes = [
            'tenants.index',
            'payments.index',
            'payments.create',
            'reports.index',
            'arrears.index',
            'notifications.index',
        ];

        foreach ($routes as $routeName) {
            $this->get(route($routeName))->assertRedirect(route('login'));
        }

        $this->actingAs($user);
        foreach ($routes as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }

    public function test_guest_cannot_write_to_the_payment_api(): void
    {
        $this->postJson(route('api.payments.store'), [])->assertUnauthorized();
    }

    public function test_zero_and_partial_month_payments_are_rejected(): void
    {
        $tenant = $this->makeTenant(unpaidMonths: 2);
        $this->actingAs(User::factory()->create());

        $this->postJson(route('api.payments.store'), $this->paymentPayload($tenant, 0))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('amount');

        $this->postJson(route('api.payments.store'), $this->paymentPayload($tenant, 250000))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('amount');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_payment_for_all_outstanding_months_clears_the_balance(): void
    {
        $tenant = $this->makeTenant(unpaidMonths: 3);
        $tenant->update(['entry_date' => now()->subMonths(2)->startOfMonth()->toDateString()]);
        $this->actingAs(User::factory()->create());

        $this->postJson(route('api.payments.store'), $this->paymentPayload($tenant, 1500000))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'unpaid_months' => 0,
            'status' => 'lunas',
        ]);
        $this->assertDatabaseCount('payments', 3);
        $this->assertDatabaseHas('payments', [
            'tenant_id' => $tenant->id,
            'month_period' => now()->subMonths(2)->locale('id')->isoFormat('MMM YYYY'),
            'amount' => 500000,
        ]);
        $this->assertDatabaseHas('payments', [
            'tenant_id' => $tenant->id,
            'month_period' => now()->locale('id')->isoFormat('MMM YYYY'),
            'amount' => 500000,
        ]);
    }

    public function test_a_paid_month_cannot_be_charged_twice(): void
    {
        $tenant = $this->makeTenant(unpaidMonths: 1);
        $this->actingAs(User::factory()->create());
        $payload = $this->paymentPayload($tenant, 500000);

        $this->postJson(route('api.payments.store'), $payload)->assertOk();
        $this->postJson(route('api.payments.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('month_period');

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_room_cannot_be_deleted_while_it_has_rental_history(): void
    {
        $tenant = $this->makeTenant();

        $this->expectException(\Illuminate\Database\QueryException::class);
        $tenant->room->delete();
    }

    public function test_monthly_arrears_accrue_once_and_paid_months_are_skipped(): void
    {
        $tenant = $this->makeTenant(
            unpaidMonths: 1,
            lastBilledAt: now()->subMonths(2)->startOfMonth(),
        );
        $previousPeriod = now()->subMonth()->locale('id')->isoFormat('MMM YYYY');
        Payment::create([
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'amount' => 500000,
            'month_period' => $previousPeriod,
            'payment_date' => now()->subMonth()->toDateString(),
            'method' => 'Tunai',
            'status' => 'Lunas',
        ]);

        $this->actingAs(User::factory()->create());
        $this->get('/')->assertOk();
        $this->assertSame(2, $tenant->fresh()->unpaid_months);

        $this->get('/')->assertOk();
        $this->assertSame(2, $tenant->fresh()->unpaid_months);
    }

    public function test_legacy_tenant_without_billing_cursor_is_reconciled_from_paid_periods(): void
    {
        $tenant = $this->makeTenant(unpaidMonths: 1);
        $entryDate = now()->subMonths(2)->startOfMonth();
        $paidPeriod = now()->subMonth()->locale('id')->isoFormat('MMM YYYY');
        $tenant->forceFill([
            'entry_date' => $entryDate->toDateString(),
            'last_billed_at' => null,
        ])->save();

        Payment::create([
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'amount' => 500000,
            'month_period' => $paidPeriod,
            'payment_date' => now()->subMonth()->toDateString(),
            'method' => 'Tunai',
            'status' => 'Lunas',
        ]);

        $this->actingAs(User::factory()->create())->get('/')->assertOk();

        $this->assertSame(2, $tenant->fresh()->unpaid_months);
        $this->assertNotNull($tenant->fresh()->last_billed_at);
    }

    public function test_tenant_names_are_encoded_before_being_passed_to_javascript(): void
    {
        $this->makeTenant(name: "O'Brian");

        $response = $this->actingAs(User::factory()->create())->get('/')->assertOk();

        $this->assertStringContainsString('O\\u0027Brian', $response->getContent());
    }

    private function makeTenant(
        int $unpaidMonths = 1,
        ?Carbon $lastBilledAt = null,
        string $name = 'Penghuni Test',
    ): Tenant {
        $room = Room::create(['number' => 'R'.Room::count() + 1, 'price' => 500000, 'status' => 'terisi']);

        return Tenant::create([
            'room_id' => $room->id,
            'price' => 500000,
            'name' => $name,
            'phone' => '08123456789',
            'entry_date' => now()->startOfMonth()->toDateString(),
            'due_date' => now()->addMonthNoOverflow()->toDateString(),
            'last_billed_at' => ($lastBilledAt ?? now()->startOfMonth())->toDateString(),
            'unpaid_months' => $unpaidMonths,
            'status' => $unpaidMonths > 0 ? 'belum_bayar' : 'lunas',
        ]);
    }

    private function paymentPayload(Tenant $tenant, int $amount): array
    {
        return [
            'tenant_id' => $tenant->id,
            'amount' => $amount,
            'month_period' => now()->locale('id')->isoFormat('MMM YYYY'),
            'payment_date' => now()->toDateString(),
            'method' => 'Tunai',
        ];
    }
}
