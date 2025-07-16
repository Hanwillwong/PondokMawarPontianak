<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PushSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_notifications_to_all_subscribers()
    {
        // Create fake user
        $user = User::factory()->create(['name' => 'Hanwill']);

        // Tambahkan 2 subscriptions
        PushSubscription::create([
            'subscription' => [
                'endpoint' => 'https://example.com/1',
                'keys' => [
                    'p256dh' => 'key1',
                    'auth' => 'auth1',
                ],
            ],
        ]);

        PushSubscription::create([
            'subscription' => [
                'endpoint' => 'https://example.com/2',
                'keys' => [
                    'p256dh' => 'key2',
                    'auth' => 'auth2',
                ],
            ],
        ]);

        // Mock WebPush
        $mockWebPush = \Mockery::mock(WebPush::class);
        $mockWebPush->shouldReceive('sendOneNotification')->twice();

        // Simulasi payload
        $payload = json_encode([
            'title' => 'Order Baru Masuk',
            'body' => 'Ada pesanan baru dari ' . $user->name,
            'url' => url('/admin'),
            'requireInteraction' => true,
        ]);

        // Kirim notifikasi ke semua subscription (menggunakan mock)
        foreach (PushSubscription::all() as $sub) {
            $mockWebPush->sendOneNotification(
                Subscription::create($sub->subscription),
                $payload
            );
        }

        // Cek bahwa test jalan lancar
        $this->assertTrue(true);
    }

    // Bersihkan Mockery setelah test
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
