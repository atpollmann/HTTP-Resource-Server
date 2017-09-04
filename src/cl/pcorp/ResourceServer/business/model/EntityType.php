<?php
namespace cl\pcorp\ResourceServer\business\model;

abstract class EntityType extends BaseType {

  const ENTITY_TYPE_PRODUCT = 'product';
  const ENTITY_TYPE_CORPORATE = 'corporate';
  const ENTITY_TYPE_WEB_ASSET = 'web';
  const ENTITY_TYPE_TECHNICAL = 'tech';
  const ENTITY_TYPE_TRANSACTIONAL = 'trans';
  const ENTITY_TYPE_EFFECT = 'fx';
  const ENTITY_TYPE_LOCAL = 'local';
  const ENTITY_TYPE_THIRD_PARTY = 'ext';

}