<?php

namespace InetStudio\Articles\Models;

use Cocur\Slugify\Slugify;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use InetStudio\StatusesPackage\Statuses\Models\Traits\Status;
use InetStudio\MetaPackage\Meta\Models\Traits\HasMeta;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Rating\Contracts\Models\Traits\RateableContract;
use InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract;
use InetStudio\AdminPanel\Base\Models\Traits\Scopes\BuildQueryScopeTrait;

/**
 * Class ArticleModel.
 */
class ArticleModel extends Model implements ArticleModelContract, HasMedia, FavoritableContract, RateableContract, Auditable
{
    use HasMeta;
    use BuildQueryScopeTrait;
    use \Laravel\Scout\Searchable;
    use \OwenIt\Auditing\Auditable;
    use \Cviebrock\EloquentSluggable\Sluggable;
    use \InetStudio\TagsPackage\Tags\Models\Traits\HasTags;
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \InetStudio\Rating\Models\Traits\Rateable;
    use \InetStudio\Access\Models\Traits\Accessable;
    use \InetStudio\Uploads\Models\Traits\HasImages;
    use \InetStudio\Widgets\Models\Traits\HasWidgets;
    use \InetStudio\CommentsPackage\Comments\Models\Traits\HasComments;
    use \InetStudio\Favorites\Models\Traits\Favoritable;
    use \Cviebrock\EloquentSluggable\SluggableScopeHelpers;
    use \InetStudio\CategoriesPackage\Categories\Models\Traits\HasCategories;
    use \InetStudio\Classifiers\Models\Traits\HasClassifiers;
    use \InetStudio\SimpleCounters\Counters\Models\Traits\HasSimpleCountersTrait;

    const ENTITY_TYPE = 'article';

    const BASE_MATERIAL_TYPE = 'article';

    /**
     * Конфиг изображений.
     *
     * @var array
     */
    private $images = [
        'config' => 'articles',
        'model' => '',
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

    /**
     * Should the timestamps be audited?
     *
     * @var bool
     */
    protected $auditTimestamps = true;

    /**
     * Загрузка модели.
     */
    protected static function boot()
    {
        parent::boot();

        self::$buildQueryScopeDefaults['columns'] = [
            'id',
            'slug',
            'title',
        ];

        self::$buildQueryScopeDefaults['relations'] = [
            'classifiers' => function ($query) {
                $query->select(['classifiers_entries.id', 'classifiers_entries.value', 'classifiers_entries.alias']);
            },

            'meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            },

            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk', 'mime_type', 'custom_properties', 'responsive_images']);
            },

            'tags' => function ($query) {
                $query->select(['id', 'name', 'slug']);
            },

            'categories' => function ($query) {
                $query->select(['id', 'parent_id', 'name', 'slug', 'title', 'description']);
            },

            'counters' => function ($query) {
                $query->select(['countable_id', 'countable_type', 'type', 'counter']);
            },

            'status' => function ($query) {
                $query->select(['id', 'name', 'alias', 'color_class']);
            },
        ];
    }

    /**
     * Сеттер атрибута title.
     *
     * @param $value
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = strip_tags($value);
    }

    /**
     * Сеттер атрибута slug.
     *
     * @param $value
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strip_tags($value);
    }

    /**
     * Сеттер атрибута description.
     *
     * @param $value
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = trim(str_replace("&nbsp;", ' ', strip_tags((isset($value['text'])) ? $value['text'] : (! is_array($value) ? $value : ''))));
    }

    /**
     * Сеттер атрибута content.
     *
     * @param $value
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = trim(str_replace("&nbsp;", ' ', (isset($value['text'])) ? $value['text'] : (! is_array($value) ? $value : '')));
    }

    /**
     * Сеттер атрибута corrections.
     *
     * @param $value
     */
    public function setCorrectionsAttribute($value)
    {
        $this->attributes['corrections'] = trim(str_replace("&nbsp;", ' ', (isset($value['text'])) ? $value['text'] : (! is_array($value) ? $value : '')));
    }

    /**
     * Сеттер атрибута publish_date.
     *
     * @param $value
     */
    public function setPublishDateAttribute($value)
    {
        $this->attributes['publish_date'] = ($value) ? Carbon::createFromFormat('d.m.Y H:i', $value) : null;
    }

    /**
     * Сеттер атрибута webmaster_id.
     *
     * @param $value
     */
    public function setWebmasterIdAttribute($value)
    {
        $this->attributes['webmaster_id'] = strip_tags($value);
    }

    /**
     * Сеттер атрибута status_id.
     *
     * @param $value
     */
    public function setStatusIdAttribute($value)
    {
        $this->attributes['status_id'] = (! $value) ? 1 : (int) $value;
    }

    /**
     * Сеттер атрибута material_type.
     *
     * @param $type
     */
    public function setMaterialTypeAttribute($value)
    {
        $this->attributes['material_type'] = ($value) ? $value : self::BASE_MATERIAL_TYPE;
        $this->images['model'] = $this->attributes['material_type'];
    }

    /**
     * Геттер атрибута href.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getHrefAttribute()
    {
        return url($this->material_type.'/'.(! empty($this->slug) ? $this->slug : $this->id));
    }

    /**
     * Геттер атрибута type.
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return self::ENTITY_TYPE;
    }

    /**
     * Геттер атрибута material_type.
     *
     * @return string
     */
    public function getMaterialTypeAttribute()
    {
        $materialType = $this->classifiers()->whereHas('groups', function ($query) {
            $query->where('name', '=', 'Тип материала');
        })->pluck('alias')->toArray();

        $materialType = (empty($materialType)) ? ($this->attributes['material_type'] ?? self::BASE_MATERIAL_TYPE) : str_replace('material_type_', '', $materialType[0]);

        return $materialType;
    }

    use Status;

    /**
     * Настройка полей для поиска.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $arr = Arr::only($this->toArray(), ['id', 'title', 'description', 'content']);

        $arr['tags'] = $this->tags->map(function ($item) {
            return Arr::only($item->toSearchableArray(), ['id', 'name']);
        })->toArray();

        return $arr;
    }

    /**
     * Get the _type name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return '_doc';
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
                'includeTrashed' => true,
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
}
