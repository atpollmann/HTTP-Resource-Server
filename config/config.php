<?php
return array(
  "config" => array(
    "PRODUCT_IMG_STORE_LOCATION" => "/var/www/static/img/product/",
    "RESOURCE_CACHE_LOCATION" => "/var/www/static/cache/",
    "PRODUCT_WATERMARK_LOCATION" => "/var/www/static/img/watermark/",
    "PRODUCT_WATERMARK_BASENAME" => "watermark",
    "PRODUCT_WATERMARK_EXTENSION" => "png",
    "DEFAULT_CONTENT_TYPE" => "text/plain",
    "LOG_LEVEL" => 1
  ),
  "agents" => array(
    "jpg" => "Intervention",
    "png" => "Intervention",
    "pdf" => "WhooHooPdf",
    "doc" => "SuperWord",
    "docx" => "SuperWord"
  ),
  "mimes" => array(
    "png" => "image/png",
    "jpg" => "image/jpeg",
    "html" => "text/html"
  ),
  "resourceParams" => array(
    "resource" => array(
      "auth_key" => array(
        "cacheable" => false,
        "validationRegex" => '^[a-f0-9]{40}$'
      ),
      "no_caching" => array(
        "cacheable" => false,
        "validationRegex" => '[01]|^$'
      )
    ),
    "img" => array(
      "width" => array(
        "cacheable" => true,
        "validationRegex" => '[0-9]{2,4}'
      ),
      "height" => array(
        "cacheable" => true,
        "validationRegex" => '[0-9]{2,4}'
      )
    ),
    "doc" => array(
      "page" => array(
        "cacheable" => true,
        "validationRegex" => '[0-9]{1,3}'
      ),
      "zoom" => array(
        "cacheable" => true,
        "validationRegex" => '[0-9]{1,3}'
      )
    ),
    "productImg" => array(),
  ),
  "resourceCacheControl" => array(
    "ProductImg" => array(
      "isReusable" => true,
      "alwaysMustRevalidate" => false,
      "isPublic" => true,
      "maxCacheLifetime" => 432000
    )
  )
);
