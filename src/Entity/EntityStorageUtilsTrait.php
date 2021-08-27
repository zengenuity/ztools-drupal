<?php

namespace Zengenuity\Tools\Drupal\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Query\QueryInterface;

trait EntityStorageUtilsTrait {

  /**
   * @param string $entity_type
   * @param array $values
   * @param \Drupal\Core\Entity\EntityStorageInterface|string $entity_type_or_storage
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function loadOneByProperties(array $values, $entity_type_or_storage) : ?EntityInterface {
    if (is_string($entity_type_or_storage)) {
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type_or_storage);
    }
    else {
      $storage = $entity_type_or_storage;
    }
    $query = $storage->getQuery();
    $query->accessCheck(FALSE);
    foreach ($values as $name => $value) {
      $query->condition($name, (array) $value, 'IN');
    }
    $query->range(0, 1);
    $result = $query->execute();
    if (!empty($result)) {
      $id = reset($result);
      return $storage->load($id);
    }
    return NULL;
  }

  /**
   * @param string $entity_type
   * @param array $results
   * @param \Drupal\Core\Entity\EntityStorageInterface|string $entity_type_or_storage
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function loadFirstResult(array $results, $entity_type_or_storage) : ?EntityInterface {
    if (is_string($entity_type_or_storage)) {
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type_or_storage);
    }
    else {
      $storage = $entity_type_or_storage;
    }
    if (!empty($results)) {
      $id = reset($results);
      return $storage->load($id);
    }
    return NULL;
  }

}
