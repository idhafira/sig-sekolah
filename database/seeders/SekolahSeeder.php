<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['30304268', 'SMK Negeri 2 Banjarmasin', 'SMK', 'Negeri', 'Jl. Brigjen H. Hasan Basri No.6, Sungai Miai', 'Banjarmasin Utara', -3.295600, 114.589800, '05113304677'],
            ['', 'SMA Negeri 1 Banjarmasin', 'SMA', 'Negeri', 'Jl. Mulawarman No. 25, Teluk Dalam', 'Banjarmasin Tengah', -3.316510, 114.582610, '05113353467'],
            ['30304177', 'SMP Negeri 1 Banjarmasin', 'SMP', 'Negeri', 'Jl. Batu Tiban No. 23 Komplek Mulawarman, Teluk Dalam', 'Banjarmasin Tengah', -3.317000, 114.582300, '05113353581'],
            ['30304103', 'SD Negeri SN Sungai Miai 5', 'SD', 'Negeri', 'Jl. Cemara Ujung RT.22 RW.01 No.07, Sungai Miai', 'Banjarmasin Utara', -3.287200, 114.594100, null],
            ['30304267', 'SMK Negeri 1 Banjarmasin', 'SMK', 'Negeri', 'Jl. Mulawarman No. 45, Teluk Dalam', 'Banjarmasin Tengah', -3.315800, 114.583500, '05113353042'],
        ];

        foreach ($data as $d) {
            Sekolah::updateOrCreate(
                ['npsn' => $d[0]],
                [
                    'nama' => $d[1],
                    'jenjang' => $d[2],
                    'status' => $d[3],
                    'alamat' => $d[4],
                    'kecamatan' => $d[5],
                    'latitude' => $d[6],
                    'longitude' => $d[7],
                    'telepon' => $d[8],
                ]
            );
        }
    }
}