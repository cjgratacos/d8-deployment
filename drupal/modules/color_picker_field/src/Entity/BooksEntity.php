<?php

namespace Drupal\books\Entity;

use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Annotation\PluralTranslation;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * @ContentEntity(
 *  id = "book",
 *  label = @Translation("Book"),
 *  label_collection = @Translation("Book"),
 *  label_singular = @Translation("book"),
 *  label_plural = @Translation("books"),
 *  label_count = @PluralTranslation(
 *    singular = "@count book",
 *    plural = "@count books"
 *  ),
 *  handlers = {
 *     
 *  }
 * )
 */
class BooksEntity extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
  }

}
