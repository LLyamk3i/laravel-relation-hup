<?php

namespace App\Enums;

enum PostRelations: string
{
    case LIST = 'posts/list';
    case LATEST = 'posts/latest';
    case OLDER = 'posts/older';
    case ACTIVES = 'posts/actives';
}
