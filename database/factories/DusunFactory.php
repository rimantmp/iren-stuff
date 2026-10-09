<?php

namespace Database\Factories;

use App\Models\Dusun;
use App\Models\Kelurahan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dusun>
 */
class DusunFactory extends Factory
{
    protected $model = Dusun::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kelurahan_id' => Kelurahan::query()->first()->id ?? '3578011001',
            'nama' => 'Dusun '.$this->faker->streetName(),
            'rw' => 'RW 0'.$this->faker->numberBetween(1, 9),
            'rt' => 'RT 0'.$this->faker->numberBetween(1, 9),
            'kepala_dusun' => $this->faker->name(),
            'latitude' => $this->faker->latitude(-8.5, -6.5),
            'longitude' => $this->faker->longitude(110.0, 114.0),
            'keterangan' => $this->faker->sentence(),
        ];
    }
}
