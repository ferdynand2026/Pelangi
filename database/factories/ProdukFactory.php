<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Produk;
use Carbon\Carbon;

class ProdukFactory extends Factory
{
    protected $model = Produk::class;

    public function definition(): array
    {
        return [
            'jenis_ikan'     => $this->faker->randomElement([
                'Tongkol', 'Tuna', 'Kakap', 'Bandeng', 'Kembung'
            ]),
            'berat'          => $this->faker->numberBetween(5, 50),
            'harga_awal'     => $this->faker->numberBetween(10000, 50000),
            'status_lelang'  => 'ditutup',
            'waktu_selesai'  => Carbon::now()->subDays(rand(1, 10)),
            'created_at'     => now(),
            'updated_at'     => now(),
        ];
    }
}
