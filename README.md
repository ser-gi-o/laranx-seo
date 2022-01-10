# LaraNx Seo 

LaraNx Seo enables your Laravel app to store Seo and social media meta tag data in database
instead of your code.  Move marketing data out of your code base and into your
database where it is easily modified.

LaraNx Seo creates tag nodes and associates them with files, then renders tags in your views' head section.

This package and the Pro LaraNx package are meant for Laravel websites homepage and primary pages, since these 
pages are usually highly customized.  This is the perfect solution for sites that require application logic
to be applied to public pages where SEO is important without needing to use embed logic in CMS software like 
WordpPress and Ghost. 

## How LaraNx Seo works
### Create Tag:
This is an example.  You can create your own CRUD resource or get [LaraNx Full Version](https://laranx.com)
``` php
$tag = new Tag;
$tagData = [
    'page'                => 'about',             //identifier
    'title'               => 'about title',
    'description'         => 'about description',
    'canonical'           => 'https://example.com',
    'feature_image'       => 'https://example.com/images/feature.png',
    'og_title'            => '',                 //if blank render will use title
    'og_description'      => '',                 //if blank render will use description
    'og_image'            => '',                 //if blank render will use feature_image
    'twitter_title'       => '',                 //if blank render will use title
    'twitter_description' => '',                 //if blank render will use description
    'twitter_image'       => '',                 //if blank render will use feature_image
    'jsonld'              => '',                 //add validated jsonld string
];
$tag->store($tagData['page'], $tagData);
```

### Retrieve tag node
In controller retrieve tag:
``` php
$this->seo = new Seo;
$this->seo->fill('about', 'Site Name');

//pass seo to view
```

### Output meta tags
layout.blade.php
``` html
<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="/css/app.css" rel="stylesheet">
    {{-- SEO tags --}}
    {!! $seo->render() !!}
</head>
<body>
    <!-- ... -->
</body>
</html>

```
Rendered Tags:
![Rendered tags!](https://laranx.com/images/laranxseo/About-LaraNx-SEO-Laravel-open-source-package-for-storing-meta-tags.png "LaraNx Seo")

Want a complete marketing solution with admin interface, site configuration, site tag fallback, and theme management features consider 
purchasing [LaraNx Seo and Theme Management](https://laranx.com)


## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.