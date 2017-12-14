<?php

namespace InetStudio\Articles\Models;

use Spatie\Tags\HasTags;
use Cocur\Slugify\Slugify;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\Media;
use Cog\Likeable\Traits\Likeable;
use Phoenix\EloquentMeta\MetaTrait;
use InetStudio\Tags\Models\TagModel;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use InetStudio\Products\Traits\HasProducts;
use InetStudio\Statuses\Models\StatusModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\Rating\Models\Traits\Rateable;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Venturecraft\Revisionable\RevisionableTrait;
use InetStudio\Comments\Models\Traits\HasComments;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use InetStudio\Categories\Models\Traits\HasCategories;
use Cog\Likeable\Contracts\Likeable as LikeableContract;
use InetStudio\Classifiers\Models\Traits\HasClassifiers;
use InetStudio\Ingredients\Models\Traits\HasIngredients;
use InetStudio\SimpleCounters\Traits\HasSimpleCountersTrait;
use InetStudio\Rating\Contracts\Models\Traits\RateableContract;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

/**
 * InetStudio\Articles\Models\ArticleModel.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string|null $content
 * @property string|null $publish_date
 * @property string $webmaster_id
 * @property int|null $status_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Kalnoy\Nestedset\Collection|\InetStudio\Categories\Models\CategoryModel[] $categories
 * @property-read \Illuminate\Contracts\Routing\UrlGenerator|string $href
 * @property \Illuminate\Database\Eloquent\Collection|\InetStudio\Ingredients\Models\IngredientModel[] $ingredients
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @property-read \Illuminate\Database\Eloquent\Collection|\Phoenix\EloquentMeta\Meta[] $meta
 * @property \Illuminate\Database\Eloquent\Collection|\InetStudio\Products\Models\ProductModel[] $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property \Illuminate\Database\Eloquent\Collection|\InetStudio\Tags\Models\TagModel[] $tags
 * @property-read \InetStudio\Statuses\Models\StatusModel $status
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel findSimilarSlugs(\Illuminate\Database\Eloquent\Model $model, $attribute, $config, $slug)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Articles\Models\ArticleModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel wherePublishDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel whereWebmasterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withAllCategories($categories, $column = 'slug')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withAllIngredients($ingredients, $column = 'slug')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withAllProducts($products, $column = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withAllTags($tags, $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withAnyCategories($categories, $column = 'slug')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withAnyIngredients($ingredients, $column = 'slug')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withAnyProducts($products, $column = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withAnyTags($tags, $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withCategories($categories, $column = 'slug')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withIngredients($ingredients, $column = 'slug')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withProducts($products, $column = 'id')
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Articles\Models\ArticleModel withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withoutAnyCategories()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withoutAnyIngredients()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withoutAnyProducts()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withoutCategories($categories, $column = 'slug')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withoutIngredients($ingredients, $column = 'slug')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Articles\Models\ArticleModel withoutProducts($products, $column = 'id')
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Articles\Models\ArticleModel withoutTrashed()
 * @mixin \Eloquent
 */
class ArticleModel extends Model implements HasMediaConversions, LikeableContract, RateableContract
{
    use HasTags;
    use Likeable;
    use Rateable;
    use MetaTrait;
    use Sluggable;
    use Searchable;
    use HasComments;
    use HasProducts;
    use SoftDeletes;
    use HasCategories;
    use HasMediaTrait;
    use HasClassifiers;
    use HasIngredients;
    use RevisionableTrait;
    use SluggableScopeHelpers;
    use HasSimpleCountersTrait;

    const HREF = '/article/';
    const MATERIAL_TYPE = 'article';

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
        'publish_date', 'webmaster_id', 'status_id',
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
    ];

    protected $revisionCreationsEnabled = true;

    /**
     * Отношение "один к одному" с моделью статуса.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->hasOne(StatusModel::class, 'id', 'status_id');
    }

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

    /**
     * Возвращаем класс модели тега.
     *
     * @return string
     */
    public static function getTagClassName()
    {
        return TagModel::class;
    }

    /**
     * Регистрируем преобразования изображений.
     *
     * @param Media|null $media
     */
    public function registerMediaConversions(Media $media = null)
    {
        $quality = (config('articles.images.quality')) ? config('articles.images.quality') : 75;

        if (config('articles.images.conversions')) {
            foreach (config('articles.images.conversions') as $collection => $image) {
                foreach ($image as $crop) {
                    foreach ($crop as $conversion) {
                        $imageConversion = $this->addMediaConversion($conversion['name']);

                        if (isset($conversion['size']['width'])) {
                            $imageConversion->width($conversion['size']['width']);
                        }

                        if (isset($conversion['size']['height'])) {
                            $imageConversion->height($conversion['size']['height']);
                        }

                        if (isset($conversion['fit']['width']) && isset($conversion['fit']['height'])) {
                            $imageConversion->fit('max', $conversion['fit']['width'], $conversion['fit']['height']);
                        }

                        if (isset($conversion['quality'])) {
                            $imageConversion->quality($conversion['quality']);
                            $imageConversion->optimize();
                        } else {
                            $imageConversion->quality($quality);
                        }

                        $imageConversion->performOnCollections($collection);
                    }
                }
            }
        }
    }
}
