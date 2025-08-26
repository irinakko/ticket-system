<?php

namespace App;

use App\Models\User;

enum Role: string
{
    case Admin = 'admin';
    case User = 'user';
    case Agent = 'agent';
}
