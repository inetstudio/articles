<?php

namespace InetStudio\Articles\Models;

use Cocur\Slugify\Slugify;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use InetStudio\Statuses\Models\Traits\Status;
use InetStudio\Meta\Contracts\Models\Traits\MetableContract;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Rating\Contracts\Models\Traits\RateableContract;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract;

/**
 * Class ArticleModel.
 */
class ArticleModel extends Model implements ArticleModelContract, MetableContract, HasMediaConversions, FavoritableContract, RateableContract
{
    use \Laravel\Scout\Searchable;
    use \Cviebrock\EloquentSluggable\Sluggable;
    use \InetStudio\Meta\Models\Traits\Metable;
    use \InetStudio\Tags\Models\Traits\HasTags;
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \InetStudio\Rating\Models\Traits\Rateable;
    use \InetStudio\Access\Models\Traits\Accessable;
    use \InetStudio\Uploads\Models\Traits\HasImages;
    use \InetStudio\Widgets\Models\Traits\HasWidgets;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \InetStudio\Comments\Models\Traits\HasComments;
    use \InetStudio\Products\Models\Traits\HasProducts;
    use \InetStudio\Favorites\Models\Traits\Favoritable;
    use \Cviebrock\EloquentSluggable\SluggableScopeHelpers;
    use \InetStudio\Categories\Models\Traits\HasCategories;
    use \InetStudio\Classifiers\Models\Traits\HasClassifiers;
    use \InetStudio\Ingredients\Models\Traits\HasIngredients;
    use \InetStudio\SimpleCounters\Models\Traits\HasSimpleCountersTrait;

    const HREF = '/article/';
    const MATERIAL_TYPE = 'article';

    protected $images = [
        'config' => 'articles',
        'model' => 'article',
    ];

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'articles';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'description', 'content',
        'publish_date', 'webmaster_id', 'status_id', 'corrections',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'publish_date',
    ];

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = strip_tags($value);
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strip_tags($value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = trim(str_replace("&nbsp;", '', strip_tags((isset($value['text'])) ? $value['text'] : $value)));
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = trim(str_replace("&nbsp;", '', strip_tags((isset($value['text'])) ? $value['text'] : $value)));
    }

    public function setCorrectionsAttribute($value)
    {
        $this->attributes['corrections'] = trim(str_replace("&nbsp;", '', strip_tags((isset($value['text'])) ? $value['text'] : $value)));
    }

    public function setPublishDateAttribute($value)
    {
        $this->attributes['publish_date'] = Carbon::createFromFormat('d.m.Y H:i', $value);
    }

    public function setWebmasterIdAttribute($value)
    {
        $this->attributes['webmaster_id'] = strip_tags($value);
    }

    public function setStatusIdAttribute($value)
    {
        $this->attributes['status_id'] = (int) $value;
    }

    protected $revisionCreationsEnabled = true;

    use Status;

    /**
     * Настройка полей для поиска.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $arr = array_only($this->toArray(), ['id', 'title', 'description', 'content']);

        $arr['categories'] = $this->categories->map(function ($item) {
            return array_only($item->toSearchableArray(), ['id', 'title']);
        })->toArray();

        $arr['tags'] = $this->tags->map(function ($item) {
            return array_only($item->toSearchableArray(), ['id', 'name']);
        })->toArray();

        $arr['ingredients'] = $this->ingredients->map(function ($item) {
            return array_only($item->toSearchableArray(), ['id', 'title']);
        })->toArray();

        $arr['products'] = $this->products->map(function ($item) {
            return array_only($item->toSearchableArray(), ['id', 'title']);
        })->toArray();

        return $arr;
    }

    /**
     * Возвращаем конфиг для генерации slug модели.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title',
                'unique' => true,
            ],
        ];
    }

    /**
     * Правила для транслита.
     *
     * @param Slugify $engine
     *
     * @return Slugify
     */
    public function customizeSlugEngine(Slugify $engine)
    {
        $rules = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
            'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'je', 'ю' => 'ju', 'я' => 'ja',
        ];

        $engine->addRules($rules);

        return $engine;
    }

    /**
     * Ссылка на материал.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getHrefAttribute()
    {
        return url(self::HREF.(! empty($this->slug) ? $this->slug : $this->id));
    }

    /**
     * Тип материала.
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return self::MATERIAL_TYPE;
    }
}
