<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainBlock extends Model
{
    use HasFactory;

    protected $table = 'domain_block_list';

    protected $guarded = [];
 
}
