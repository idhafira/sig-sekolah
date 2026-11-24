<?php

namespace Tests\Feature;

use App\Models\Sekolah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilPublikTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function sekolah(string $npsn = '11111111'): Sekolah
    {
        return Sekolah::create([
            'npsn' => $npsn, 'nama' => "Sekolah $npsn",
            'jenjang' => 'SD', 'status' => 'Negeri',
            'alamat' => 'Jl. Uji', 'kecamatan' => 'Uji',
            'latitude' => -3.3, 'longitude' => 114.59,
        ]);
    }

    public function test_profil_publik_terbuka_tanpa_login(): void
    {
        $s = $this->sekolah();
        $s->profil()->create([
            'kepala_sekolah' => 'Bapak Uji',
            'akreditasi' => 'A',
            'email' => 'rahasia@uji.test',
        ]);

        $this->get("/profil/{$s->npsn}")
            ->assertOk()
            ->assertSee('Sekolah 11111111')
            ->assertSee('Bapak Uji')
            ->assertDontSee('rahasia@uji.test');
    }

    public function test_sekolah_tanpa_profil_tetap_tampil(): void
    {
        $s = $this->sekolah();

        $this->get("/profil/{$s->npsn}")
            ->assertOk()
            ->assertSee('Sekolah belum melengkapi deskripsi.');
    }

    public function test_npsn_tidak_dikenal_menghasilkan_404(): void
    {
        $this->get('/profil/00000000')->assertNotFound();
    }
}