<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\app\http\LeagueHttpRequest;
use cl\pcorp\ResourceServer\app\http\SymfonyHttpRequest;
use cl\pcorp\ResourceServer\app\RequestParser;
use cl\pcorp\ResourceServer\business\model\EntityType;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\business\model\ResourceType;
use cl\pcorp\ResourceServer\business\services\ResourceServiceFactory;
use cl\pcorp\ResourceServer\common\Config;
use cl\pcorp\ResourceServer\common\Logger;
use cl\pcorp\ResourceServer\exceptions\BadParamException;
use cl\pcorp\ResourceServer\exceptions\BadURIFormatException;
use cl\pcorp\ResourceServer\exceptions\InvalidParamValueException;

class BaseTester extends \PHPUnit_Framework_TestCase {

  private $URIdataSet = array(
    array(
      'URIString' => 'https://foo.bar.com/img',
      'expected' => array(
        'exception' => BadURIFormatException::class
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/img/product/D018F6701000.jpg',
      'expected' => array(
        'URI' => 'img/product/D018F6701000.jpg',
        'cacheableURI' => 'img/product/D018F6701000.jpg',
        'path' => 'img/product/D018F6701000.jpg',
        'pathArray' => array(
          'img',
          'product',
          'D018F6701000.jpg'
        ),
        'pathCount' => 3,
        'query' => '',
        'queryArray' => array(),
        'rawParams' => array(),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_IMAGE,
        'entityType' => EntityType::ENTITY_TYPE_PRODUCT,
        'basename' => 'D018F6701000',
        'filename' => 'D018F6701000.jpg',
        'extension' => 'jpg',
        'validParams' => true,
        'params' => array()
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/img/product/D018F6701000.jpg?width=90&height=10&auth_key=f7cec550c45a600a1cd12deefd296d9b160398ba',
      'expected' => array(
        'URI' => 'img/product/D018F6701000.jpg?width=90&height=10&auth_key=f7cec550c45a600a1cd12deefd296d9b160398ba',
        'cacheableURI' => 'img/product/D018F6701000.jpg?height=10&width=90',
        'path' => 'img/product/D018F6701000.jpg',
        'pathArray' => array(
          'img',
          'product',
          'D018F6701000.jpg'
        ),
        'pathCount' => 3,
        'query' => 'auth_key=f7cec550c45a600a1cd12deefd296d9b160398ba&height=10&width=90',
        'queryArray' => array(
          'width' => 90,
          'height' => 10,
          'auth_key' => 'f7cec550c45a600a1cd12deefd296d9b160398ba'
        ),
        'rawParams' => array(
          'width' => 90,
          'height' => 10,
          'auth_key' => 'f7cec550c45a600a1cd12deefd296d9b160398ba'
        ),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_IMAGE,
        'entityType' => EntityType::ENTITY_TYPE_PRODUCT,
        'basename' => 'D018F6701000',
        'filename' => 'D018F6701000.jpg',
        'extension' => 'jpg',
        'validParams' => true,
        'params' => array(
          array(
            'key' => 'auth_key',
            'value' => 'f7cec550c45a600a1cd12deefd296d9b160398ba',
            'isValid' => true
          ),
          array(
            'key' => 'height',
            'value' => '10',
            'isValid' => true
          ),
          array(
            'key' => 'width',
            'value' => '90',
            'isValid' => true
          )
        )
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com///img/product/valv-solenoide-10-999.jpg?width=90&height=80',
      'expected' => array(
        'URI' => 'img/product/valv-solenoide-10-999.jpg?width=90&height=80',
        'cacheableURI' => 'img/product/valv-solenoide-10-999.jpg?height=80&width=90',
        'path' => 'img/product/valv-solenoide-10-999.jpg',
        'pathArray' => array(
          'img',
          'product',
          'valv-solenoide-10-999.jpg'
        ),
        'pathCount' => 3,
        'query' => 'height=80&width=90',
        'queryArray' => array(
          'width' => '90',
          'height' => '80'
        ),
        'rawParams' => array(
          'width' => '90',
          'height' => '80'
        ),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_IMAGE,
        'entityType' => EntityType::ENTITY_TYPE_PRODUCT,
        'basename' => 'valv-solenoide-10-999',
        'filename' => 'valv-solenoide-10-999.jpg',
        'extension' => 'jpg',
        'params' => array(
          array(
            'key' => 'width',
            'value' => '90',
            'isValid' => true
          ),
          array(
            'key' => 'height',
            'value' => '80',
            'isValid' => true
          )
        )
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com///img/product/image355.jpg?width=317',
      'expected' => array(
        'URI' => 'img/product/image355.jpg?width=317',
        'cacheableURI' => 'img/product/image355.jpg?width=317',
        'path' => 'img/product/image355.jpg',
        'pathArray' => array(
          'img',
          'product',
          'image355.jpg'
        ),
        'pathCount' => 3,
        'query' => 'width=317',
        'queryArray' => array(
          'width' => '317'
        ),
        'rawParams' => array(
          'width' => '317'
        ),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_IMAGE,
        'entityType' => EntityType::ENTITY_TYPE_PRODUCT,
        'basename' => 'image355',
        'filename' => 'image355.jpg',
        'extension' => 'jpg',
        'params' => array(
          array(
            'key' => 'width',
            'value' => '317',
            'isValid' => true
          )
        )
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/./../img/tech/sabroe/smc104mk3/sec_a.jpg',
      'expected' => array(
        'URI' => 'img/tech/sabroe/smc104mk3/sec_a.jpg',
        'cacheableURI' => 'img/tech/sabroe/smc104mk3/sec_a.jpg',
        'path' => 'img/tech/sabroe/smc104mk3/sec_a.jpg',
        'pathArray' => array(
          'img',
          'tech',
          'sabroe',
          'smc104mk3',
          'sec_a.jpg'
        ),
        'pathCount' => 5,
        'query' => '',
        'queryArray' => array(),
        'rawParams' => array(),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_IMAGE,
        'entityType' => EntityType::ENTITY_TYPE_TECHNICAL,
        'basename' => 'sec_a',
        'filename' => 'sec_a.jpg',
        'extension' => 'jpg',
        'params' => array()
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/img/web/logo365x400.png?width=90&height=80&bum=bar',
      'expected' => array(
        'exception' =>BadParamException::class
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/img/web/logo365x400.png?width=SELECT%20*%20FROM%table&height=80',
      'expected' => array(
        'exception' =>InvalidParamValueException::class
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/img/favicon.ico',
      'expected' => array(
        'exception' => BadURIFormatException::class
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/img/web/background',
      'expected' => array(
        'exception' => BadURIFormatException::class
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/imgx/product/D018F6701000.jpg?width=90&height=80',
      'expected' => array(
        'exception' => BadURIFormatException::class
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/doc/tech/danfoss/ir/barra-transmisor-aks4100.pdf?page=45&zoom=90',
      'expected' => array(
        'URI' => 'doc/tech/danfoss/ir/barra-transmisor-aks4100.pdf?page=45&zoom=90',
        'cacheableURI' => 'doc/tech/danfoss/ir/barra-transmisor-aks4100.pdf?page=45&zoom=90',
        'path' => 'doc/tech/danfoss/ir/barra-transmisor-aks4100.pdf',
        'pathArray' => array(
          'doc',
          'tech',
          'danfoss',
          'ir',
          'barra-transmisor-aks4100.pdf'
        ),
        'pathCount' => 5,
        'query' => 'page=45&zoom=90',
        'queryArray' => array(
          'page' => '45',
          'zoom' => '90'
        ),
        'rawParams' => array(
          'page' => '45',
          'zoom' => '90'
        ),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_DOC,
        'entityType' => EntityType::ENTITY_TYPE_TECHNICAL,
        'basename' => 'barra-transmisor-aks4100',
        'filename' => 'barra-transmisor-aks4100.pdf',
        'extension' => 'pdf',
        'params' => array(
          array(
            'key' => 'page',
            'value' => '45',
            'isValid' => true
          ),
          array(
            'key' => 'zoom',
            'value' => '90',
            'isValid' => true
          )
        )
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/doc/tech/frick/RXFCompressorParts.pdf',
      'expected' => array(
        'URI' => 'doc/tech/frick/RXFCompressorParts.pdf',
        'cacheableURI' => 'doc/tech/frick/RXFCompressorParts.pdf',
        'path' => 'doc/tech/frick/RXFCompressorParts.pdf',
        'pathArray' => array(
          'doc',
          'tech',
          'frick',
          'RXFCompressorParts.pdf'
        ),
        'pathCount' => 4,
        'query' => '',
        'queryArray' => array(),
        'rawParams' => array(),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_DOC,
        'entityType' => EntityType::ENTITY_TYPE_TECHNICAL,
        'basename' => 'RXFCompressorParts',
        'filename' => 'RXFCompressorParts.pdf',
        'extension' => 'pdf',
        'params' => array()
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/doc/trans/inv/503012.pdf?auth_key=a5ccc35c70b532355ed530b77a73522278ebec15',
      'expected' => array(
        'URI' => 'doc/trans/inv/503012.pdf?auth_key=a5ccc35c70b532355ed530b77a73522278ebec15',
        'cacheableURI' => 'doc/trans/inv/503012.pdf',
        'path' => 'doc/trans/inv/503012.pdf',
        'pathArray' => array(
          'doc',
          'trans',
          'inv',
          '503012.pdf'
        ),
        'pathCount' => 4,
        'query' => 'auth_key=a5ccc35c70b532355ed530b77a73522278ebec15',
        'queryArray' => array(
          'auth_key' => 'a5ccc35c70b532355ed530b77a73522278ebec15'
        ),
        'rawParams' => array(
          'auth_key' => 'a5ccc35c70b532355ed530b77a73522278ebec15'
        ),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_DOC,
        'entityType' => EntityType::ENTITY_TYPE_TRANSACTIONAL,
        'basename' => '503012',
        'filename' => '503012.pdf',
        'extension' => 'pdf',
        'params' => array(
          array(
            'key' => 'auth_key',
            'value' => 'a5ccc35c70b532355ed530b77a73522278ebec15',
            'isValid' => true
          )
        )
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/doc/trans/inv/3022.xml?auth_key=f7cec550c45a394a1cd12deefd296d9b160398ba',
      'expected' => array(
        'URI' => 'doc/trans/inv/3022.xml?auth_key=f7cec550c45a394a1cd12deefd296d9b160398ba',
        'cacheableURI' => 'doc/trans/inv/3022.xml',
        'path' => 'doc/trans/inv/3022.xml',
        'pathArray' => array(
          'doc',
          'trans',
          'inv',
          '3022.xml'
        ),
        'pathCount' => 4,
        'query' => 'auth_key=f7cec550c45a394a1cd12deefd296d9b160398ba',
        'queryArray' => array(
          'auth_key' => 'f7cec550c45a394a1cd12deefd296d9b160398ba'
        ),
        'rawParams' => array(
          'auth_key' => 'f7cec550c45a394a1cd12deefd296d9b160398ba'
        ),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_DOC,
        'entityType' => EntityType::ENTITY_TYPE_TRANSACTIONAL,
        'basename' => '3022',
        'filename' => '3022.xml',
        'extension' => 'xml',
        'params' => array(
          array(
            'key' => 'auth_key',
            'value' => 'f7cec550c45a394a1cd12deefd296d9b160398ba',
            'isValid' => true
          )
        )
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/doc/trans/to/to789876321222-09.jpg?auth_key=',
      'expected' => array(
        'exception' => InvalidParamValueException::class
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/audio/fx/beep.ogg',
      'expected' => array(
        'URI' => 'audio/fx/beep.ogg',
        'cacheableURI' => 'audio/fx/beep.ogg',
        'path' => 'audio/fx/beep.ogg',
        'pathArray' => array(
          'audio',
          'fx',
          'beep.ogg'
        ),
        'pathCount' => 3,
        'query' => '',
        'queryArray' => array(),
        'rawParams' => array(),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_AUDIO,
        'entityType' => EntityType::ENTITY_TYPE_EFFECT,
        'basename' => 'beep',
        'filename' => 'beep.ogg',
        'extension' => 'ogg',
        'params' => array()
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/js/local/cot.js',
      'expected' => array(
        'URI' => 'js/local/cot.js',
        'cacheableURI' => 'js/local/cot.js',
        'path' => 'js/local/cot.js',
        'pathArray' => array(
          'js',
          'local',
          'cot.js'
        ),
        'pathCount' => 3,
        'query' => '',
        'queryArray' => array(),
        'rawParams' => array(),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_JAVASCRIPT,
        'entityType' => EntityType::ENTITY_TYPE_LOCAL,
        'basename' => 'cot',
        'filename' => 'cot.js',
        'extension' => 'js',
        'params' => array()
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/js/ext/jquery.1.10.0.min.js',
      'expected' => array(
        'URI' => 'js/ext/jquery.1.10.0.min.js',
        'cacheableURI' => 'js/ext/jquery.1.10.0.min.js',
        'path' => 'js/ext/jquery.1.10.0.min.js',
        'pathArray' => array(
          'js',
          'ext',
          'jquery.1.10.0.min.js'
        ),
        'pathCount' => 3,
        'query' => '',
        'queryArray' => array(),
        'rawParams' => array(),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_JAVASCRIPT,
        'entityType' => EntityType::ENTITY_TYPE_THIRD_PARTY,
        'basename' => 'jquery.1.10.0.min',
        'filename' => 'jquery.1.10.0.min.js',
        'extension' => 'js',
        'params' => array()
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/css/local/dmb/dsx.css',
      'expected' => array(
        'URI' => 'css/local/dmb/dsx.css',
        'cacheableURI' => 'css/local/dmb/dsx.css',
        'path' => 'css/local/dmb/dsx.css',
        'pathArray' => array(
          'css',
          'local',
          'dmb',
          'dsx.css'
        ),
        'pathCount' => 4,
        'query' => '',
        'queryArray' => array(),
        'rawParams' => array(),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_CSS,
        'entityType' => EntityType::ENTITY_TYPE_LOCAL,
        'basename' => 'dsx',
        'filename' => 'dsx.css',
        'extension' => 'css',
        'params' => array()
      )
    ),
    array(
      'URIString' => 'https://foo.bar.com/css/local/dmb/images/arrow-pencil.png',
      'expected' => array(
        'URI' => 'css/local/dmb/images/arrow-pencil.png',
        'cacheableURI' => 'css/local/dmb/images/arrow-pencil.png',
        'path' => 'css/local/dmb/images/arrow-pencil.png',
        'pathArray' => array(
          'css',
          'local',
          'dmb',
          'images',
          'arrow-pencil.png'
        ),
        'pathCount' => 5,
        'query' => '',
        'queryArray' => array(),
        'rawParams' => array(),
        'exception' => false,
        'resourceType' => ResourceType::RESOURCE_TYPE_IMAGE,
        'entityType' => EntityType::ENTITY_TYPE_WEB_ASSET,
        'basename' => 'arrow-pencil',
        'filename' => 'arrow-pencil.png',
        'extension' => 'png',
        'params' => array()
      )
    )
  );
  private $resourceTypesDataSet = array(
    array(
      'typeString' => 'img',
      'expected' => array(
        'valid' => true,
        'type' => ResourceType::RESOURCE_TYPE_IMAGE
      )
    ),
    array(
      'typeString' => 'doc',
      'expected' => array(
        'valid' => true,
        'type' => ResourceType::RESOURCE_TYPE_DOC
      )
    ),
    array(
      'typeString' => 'js',
      'expected' => array(
        'valid' => true,
        'type' => ResourceType::RESOURCE_TYPE_JAVASCRIPT
      )
    ),
    array(
      'typeString' => 'audio',
      'expected' => array(
        'valid' => true,
        'type' => ResourceType::RESOURCE_TYPE_AUDIO
      )
    ),
    array(
      'typeString' => 'css',
      'expected' => array(
        'valid' => true,
        'type' => ResourceType::RESOURCE_TYPE_CSS
      )
    ),
    array(
      'typeString' => 'imagex',
      'expected' => array(
        'valid' => false,
        'type' => null
      )
    ),
    array(
      'typeString' => '',
      'expected' => array(
        'valid' => false,
        'type' => null
      )
    ),
    array(
      'typeString' => '$%&',
      'expected' => array(
        'valid' => false,
        'type' => null
      )
    ),
    array(
      'typeString' => '/',
      'expected' => array(
        'valid' => false,
        'type' => null
      )
    ),
    array(
      'typeString' => 'video',
      'expected' => array(
        'valid' => false,
        'type' => null
      )
    )
  );
  private $entityTypesDataSet = array(
    array(
      'typeString' => 'product',
      'expected' => array(
        'valid' => true,
        'type' => EntityType::ENTITY_TYPE_PRODUCT
      )
    ),
    array(
      'typeString' => 'web',
      'expected' => array(
        'valid' => true,
        'type' => EntityType::ENTITY_TYPE_WEB_ASSET
      )
    ),
    array(
      'typeString' => 'corporate',
      'expected' => array(
        'valid' => true,
        'type' => EntityType::ENTITY_TYPE_CORPORATE
      )
    ),
    array(
      'typeString' => 'tech',
      'expected' => array(
        'valid' => true,
        'type' => EntityType::ENTITY_TYPE_TECHNICAL
      )
    ),
    array(
      'typeString' => 'trans',
      'expected' => array(
        'valid' => true,
        'type' => EntityType::ENTITY_TYPE_TRANSACTIONAL
      )
    ),
    array(
      'typeString' => 'fx',
      'expected' => array(
        'valid' => true,
        'type' => EntityType::ENTITY_TYPE_EFFECT
      )
    ),
    array(
      'typeString' => 'local',
      'expected' => array(
        'valid' => true,
        'type' => EntityType::ENTITY_TYPE_LOCAL
      )
    ),
    array(
      'typeString' => 'ext',
      'expected' => array(
        'valid' => true,
        'type' => EntityType::ENTITY_TYPE_THIRD_PARTY
      )
    ),
    array(
      'typeString' => 'web-asset',
      'expected' => array(
        'valid' => false,
        'type' => null
      )
    ),
    array(
      'typeString' => '/&$%&$',
      'expected' => array(
        'valid' => false,
        'type' => null
      )
    ),
    array(
      'typeString' => '',
      'expected' => array(
        'valid' => false,
        'type' => null
      )
    ),
    array(
      'typeString' => 56,
      'expected' => array(
        'valid' => false,
        'type' => null
      )
    )
  );
  private $badURISDataset = array(
    array("ftp://fjoijsodfijdshouhd"),
    array("ssm://"),
    array("ftpx://097079213"),
    array("://DROP DATABASE preference_schema")
  );

  public static function setUpBeforeClass() {
    parent::setUpBeforeClass(); // TODO: Change the autogenerated stub
    Config::init(__DIR__ . '/config/config.yml');
    Logger::init();
  }

  /**
   * @return ResourceRequest[][]
   */
  public function resourceRequestProvider() {
    $validURIDataSet = $this->ValidURIDataSetProvider();
    return $this->buildResourceRequests($validURIDataSet);
  }

  public function implementedServiceResourceRequestProvider() {
    $URIDataSet = array(
      $this->URIdataSet[1],
      $this->URIdataSet[2],
      $this->URIdataSet[3],
      $this->URIdataSet[4]
    );
    return $this->buildResourceRequests($URIDataSet);
  }

  private function buildResourceRequests($URIDataSet) {
    $resourceRequests = array();
    foreach($URIDataSet as $entry) {
      $URIString = $entry["URIString"];
      $requestParser = new RequestParser(new SymfonyHttpRequest($URIString));
      $request = $requestParser->getResourceRequest();
      $resourceRequests[] = array($request);
    }
    return $resourceRequests;
  }

  /**
   * @return array The URI dataset
   */
  public function URIDataSetProvider() {
    return $this->URIdataSet;
  }

  /**
   * @return array The valid URIs in the URI dataset
   */
  public function ValidURIDataSetProvider() {
    $validURIs = array();
    foreach($this->URIdataSet as $entry) {
      if($entry["expected"]["exception"] == false) {
        $validURIs[] = $entry;
      }
    }
    return $validURIs;
  }

  /**
   * @return array The types dataset
   */
  public function resourceTypeDataProvider() {
    return $this->resourceTypesDataSet;
  }

  public function entityTypeDataProvider() {
    return $this->entityTypesDataSet;
  }

  public function badURIDataProvider() {
    return $this->badURISDataset;
  }

}