<?php
/**
 * User: Sergio
 * Date: 2/28/2020
 */

namespace Srg\LaranxSeo;

use Illuminate\Support\Facades\Log;

class Seo
{
    /**
     * Text for title element
     *
     * @var
     */
    protected $title;

    /**
     * Array for element tags like: title, description
     *
     * @var
     */
    protected $tags;

    /**
     * Array for json ld structure
     *
     * @var
     */
    protected $jsonld;

    /**
     * String can be used instead of the jsonld array. jsonldString has precedence
     *
     * @var
     */
    protected $jsonldString;

    /**
     * Sets the page title
     *
     * @param $title
     */
    public function title($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the page title
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Meta tag for description
     *
     * @param $content
     * @throws \Exception
     */
    public function description($content)
    {
        $this->addTag('description', 'meta', ['name' => 'description', 'content' => $content]);
    }

    /**
     * Meta tag for canonical
     *
     * @param $url
     * @return $this
     * @throws \Exception
     */
    public function canonical($url)
    {
        $this->addTag('link', 'link', ['rel' => 'canonical', 'href' => $url]);

        return $this;
    }

    /**
     * Sets opengraph tags
     *
     * @param $property
     * @param $content
     * @throws \Exception
     */
    public function opengraph($property, $content)
    {
        $prop = 'og:' . $property;
        $this->addTag($prop, 'meta', ['property' => $prop, 'content' => $content]);
    }

    /**
     * Sets opengraph image tags
     *
     * @param $url
     * @throws \Exception
     */
    public function opengraphImage($url)
    {
        $this->opengraph('image', $url);

        if ($dimensions = self::imageDimensions($url)) {
            $this->opengraph('image:width', $dimensions['width']);
            $this->opengraph('image:height', $dimensions['height']);
        }
    }

    /**
     * Sets twitter tags
     *
     * @param $property
     * @param $content
     * @throws \Exception
     */
    public function twitter($property, $content)
    {
        $prop = 'twitter:' . $property;
        $this->addTag($prop, 'meta', ['property' => $prop, 'content' => $content]);
    }

    /**
     * Sets jsonld json
     *
     * @param $property
     * @param $content
     * @throws \Exception
     */
    public function jsonld($property, $content)
    {
        $this->jsonld[$property] = $content;
    }

    /**
     * Sets jsonldString json
     *
     * @param $jsonString
     * @throws \Exception
     */
    public function jsonldString($jsonString)
    {
        $this->jsonldString = $jsonString;
    }

    /**
     * Adds tag elements
     *
     * @param $identifier  allows for overriding tag
     * @param $type
     * @param array $attributes
     * @throws \Exception
     */
    public function addTag($identifier, $type, array $attributes)
    {
        $validTypes = ['meta', 'link'];

        if (array_search($type, $validTypes) === false)
            throw new \Exception('Invalid Seo Tag: ' . $type);

        $this->tags[$type][$identifier] = $attributes;
    }

    /**
     * Renders all formatted HTML tags for the given type.  To render all tags user render()
     *
     * @param $type
     * @return string
     */
    public function renderTags($type)
    {
        $tagsString = '';
        $attributes = isset($this->tags[$type]) ? $this->tags[$type] : null;

        if (isset($attributes)) {
            foreach ($attributes as $attribute) {
                $attributeString = '';

                foreach ($attribute as $name => $value) {
                    if (!(is_null($value) || $value == ''))
                        $attributeString .= ' ' . $name . '=' . '"' . str_replace(["\n", "\r", "\t", '  '], '', $value) . '"';
                }

                $tag = '<' . $type . $attributeString . '>';

                $tagsString .= "\n\t" . $tag ;
            }
        }

        return $tagsString;
    }

    /**
     * @return string
     */
    public function renderJsonLd()
    {
        //if not set returns empty string
        $html = '';

        if (!is_null($this->jsonldString) || (!is_null($this->jsonld) && sizeof($this->jsonld))) {
            $schema = $this->jsonldString ?? json_encode($this->jsonld, JSON_UNESCAPED_SLASHES);
            $html = '<script type=application/ld+json>'
                . $schema
                . '</script>';
        }

        return $html;
    }

    /**
     * Renders all formatted HTML tags
     *
     * @return string
     */
    public function render()
    {
        $html = "<title>{$this->title}</title>\n\t";

        if ($this->tags) {
            foreach ($this->tags as $type => $attributes) {
                $html .= $this->renderTags($type);
            }
        }

        $html .= "\n\t" . $this->renderJsonLd();

        return $html;
    }

    /**
     * Returns an image dimensions
     *
     * @param $url
     * @return null|array
     */
    public static function imageDimensions($url)
    {
        $dimensions = null;
        try {
            $size = getimagesize($url);
            $dimensions['width'] = $size[0];
            $dimensions['height'] = $size[1];

        } catch (\Exception $e) {
            Log::error('Seo::imageDimensions. ' . $e->getMessage());
        }

        return $dimensions;
    }

    /**
     * Fills seo tags to be rendered with Tag data
     *
     * @param $pageIdentifier
     * @param null $siteName
     * @throws \Exception
     */
    public function fill($pageIdentifier, $siteName)
    {
        if ($tag = (new Tag)->findByPage($pageIdentifier)) {
            $this->title($tag->title);
            $this->description($tag->description);
            $this->canonical($tag->canonical);

            $this->opengraph('type', 'website');
            $this->opengraph('site_name', $siteName);
            $this->opengraph('title', $tag->og_title ?? $tag->title);
            $this->opengraph('description', $tag->og_description ?? $tag->description);
            $this->opengraph('url', $tag->canonical);

            //fallback on feature_image if it is set and og_image is not
            if (!empty($tag->og_image))
                $this->opengraphImage($tag->og_image);
            elseif (!empty($tag->feature_image))
                $this->opengraphImage($tag->feature_image);

            $this->twitter('card', 'summary_large_image');
            $this->twitter('title', $tag->twitter_title ?? $tag->title);
            $this->twitter('description', $tag->twitter_description ?? $tag->description);
            $this->twitter('url', $tag->canonical);

            //fallback on feature_image if it is set and twitter_image is not
            if (!empty($tag->twitter_image))
                $this->twitter('image', $tag->twitter_image);
            elseif (!empty($tag->feature_image))
                $this->twitter('image',$tag->feature_image);

            //jsonld
            $this->jsonldString($tag->jsonld);

        } else {
            Log::error('LaranxSeo tag not found page: "' . $pageIdentifier . '"');
        }
    }
}
