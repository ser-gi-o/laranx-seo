<?php

namespace Srg\LaranxSeo;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'laranx_tags';

    protected $fillable = [
        'page',
        'title',
        'description',
        'canonical',
        'feature_image',
        'og_title',
        'og_description',
        'og_image',
        'twitter_image',
        'twitter_title',
        'twitter_description',
        'jsonld',
    ];

    /**
     * Saves or updates all setting data.
     *
     * @param $data
     * @return mixed
     */
    public function store($page, $data)
    {
        $fields = $this->getFillable();
        $prep = [];

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $prep[$field] = $data[$field];
            }
        }

        $model = $this->updateOrCreate(['page' => $page], $prep);

        return $model;
    }

    /**
     * Returns the setting based on key
     *
     * @param $page
     * @return mixed
     */
    public function findByPage($page)
    {
        $model = null;

        $collection = $this->where('page', $page)
            ->get();

        if (!$collection->isEmpty())
            $model = $collection->first();

        return $model;
    }

    /**
     * Returns if field has valid value
     *
     * @param $field
     * @return bool
     */
    private function has($field)
    {
        $value = $this->$field;
        return  !(is_null($value) || $value == '');
    }

    /**
     * Returns tag has title
     *
     * @return bool
     */
    public function hasTitle()
    {
        return  $this->has('title');
    }

    /**
     * Returns tag has description
     *
     * @return bool
     */
    public function hasDescription()
    {
        return  $this->has('description');
    }

    /**
     * Returns tag has feature image
     *
     * @return bool
     */
    public function hasFeatureImage()
    {
        return  $this->has('feature_image');
    }

    /**
     * Returns tag has opengraph field covered
     *
     * @return bool
     */
    public function hasOpenGraph()
    {
        //check for site_name, title, description, image
        //$siteName = $this->hasTitle() || $this->has('og_site_name');
        $title = $this->hasTitle() || $this->has('og_title');
        $description = $this->hasDescription() || $this->has('og_description');
        $image = $this->hasFeatureImage() || $this->has('og_image');

        return $title && $description && $image;
    }

    /**
     * Returns tag has opengraph field covered
     *
     * @return bool
     */
    public function hasTwitter()
    {
        //check for title, description, image
        $title = $this->hasTitle() || $this->has('twitter_title');
        $description = $this->hasDescription() || $this->has('twitter_description');
        $image = $this->hasFeatureImage() || $this->has('twitter_image');

        return $title && $description && $image;
    }

    /**
     * Wrapper to quickly set a value in a field and save record
     *
     * @param $field
     * @param $value
     */
    public function set($field, $value)
    {
        $this->$field = $value;
        $this->save();
    }
}
