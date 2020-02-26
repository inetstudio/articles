<?php

namespace InetStudio\ArticlesPackage\Articles\Contracts\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use InetStudio\AdminPanel\Base\Contracts\Models\BaseModelContract;

/**
 * Interface ArticleModelContract.
 */
interface ArticleModelContract extends BaseModelContract, Auditable, HasMedia
{
}
