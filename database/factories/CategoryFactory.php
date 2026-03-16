<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $jenis = fake()->unique()->numberBetween(1, 5);
    return [
      'jenis' => $jenis,
      'tinggi_minimal' => $jenis * 10 - 10,
      'tinggi_maksimal' => $jenis * 10,
      'ikon' => 'icons/icon_' . $jenis . '.png',
    ];
  }
}
