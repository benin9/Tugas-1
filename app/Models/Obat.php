<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = [
        'nama_obat',
        'kemasan',
        'harga',
        'stok',
    ];

    /**
     * Tambah stok obat.
     */
    public function tambahStok(int $jumlah): void
    {
        $this->increment('stok', $jumlah);
    }

    /**
     * Kurangi stok obat. Stok tidak boleh minus.
     *
     * @throws \Exception jika stok tidak mencukupi
     */
    public function kurangiStok(int $jumlah): void
    {
        if ($this->stok < $jumlah) {
            throw new \Exception("Stok obat '{$this->nama_obat}' tidak mencukupi. Stok tersisa: {$this->stok}");
        }
        $this->decrement('stok', $jumlah);
    }

    public function detailPeriksas()
    {
        return $this->hasMany(DetailPeriksa::class, 'id_obat');
    }
}