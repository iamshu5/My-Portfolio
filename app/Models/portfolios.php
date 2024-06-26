<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class portfolios extends Model
{
    use HasFactory;

    protected $table = 'dbo.portfolios';
    protected $primaryKey = 'id_portf';
    protected $fillable = ['title', 'description', 'image'];
    public $timestamps = false;
}
