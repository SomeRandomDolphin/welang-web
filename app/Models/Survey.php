<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'tinggi',
    'tanggal_kejadian',
    'foto',
    'latitude',
    'longitude',
    'user_id',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
