<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class MobileSurveyEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_entry_without_catatan_is_successful(): void
    {
        $response = $this->postEntry([
            'tinggi' => '25',
            'tanggal_kejadian' => '2026-07-11T10:30:00.000',
            'latitude' => '-7.281921',
            'longitude' => '112.794521',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Data laporan berhasil dikirim')
            ->assertJsonPath('data.catatan', null);

        $this->assertDatabaseHas('surveys', [
            'id' => $response->json('data.id'),
            'catatan' => null,
        ]);
    }

    public function test_entry_stores_and_returns_catatan(): void
    {
        $catatan = 'Air mulai naik di akses jalan utama.';

        $response = $this->postEntry([
            'tinggi' => '25',
            'tanggal_kejadian' => '2026-07-11T10:30:00.000',
            'latitude' => '-7.281921',
            'longitude' => '112.794521',
            'catatan' => $catatan,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.catatan', $catatan);

        $this->assertDatabaseHas('surveys', [
            'id' => $response->json('data.id'),
            'catatan' => $catatan,
        ]);
    }

    public function test_entry_rejects_catatan_longer_than_1000_characters(): void
    {
        $response = $this->postEntry([
            'tinggi' => '25',
            'tanggal_kejadian' => '2026-07-11T10:30:00.000',
            'latitude' => '-7.281921',
            'longitude' => '112.794521',
            'catatan' => str_repeat('a', 1001),
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error');
    }

    /**
     * @param array<string, string> $payload
     */
    private function postEntry(array $payload)
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/mobile/entry', $payload);
    }
}
