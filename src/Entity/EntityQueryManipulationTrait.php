<?php

namespace Zengenuity\Tools\Drupal\Entity;

use Drupal\Core\Entity\Query\ConditionInterface;
use Drupal\Core\Entity\Query\QueryInterface;

trait EntityQueryManipulationTrait {

  /**
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   * @param string $range_start
   * @param string $range_end
   * @param string $field_name
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   */
  public function dateRangeIncludes(QueryInterface $query, string $date, string $field_name = 'date') : QueryInterface {
    $and = $query->andConditionGroup();
    $and->condition($field_name . '.value', $date, '<=')
      ->condition($field_name . '.end_value', $date, '>=');
    $query->condition($and);
    return $query;
  }

  /**
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   * @param string $range_start
   * @param string $range_end
   * @param string $field_name
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   */
  public function dateInRange(QueryInterface $query, string $range_start, string $range_end, string $field_name = 'date') : QueryInterface {
    $and = $query->andConditionGroup();
    $and->condition($field_name . '.value', $range_start, '>=')
      ->condition($field_name . '.value', $range_end, '<=');
    $query->condition($and);
    return $query;
  }

  /**
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   * @param string $range_start
   * @param string $range_end
   * @param string $field_name
   *
   * @return \Drupal\Core\Entity\Query\ConditionInterface
   */
  public function dateNotInRange(QueryInterface $query, string $range_start, string $range_end, string $field_name = 'date') : ConditionInterface {
    $or = $query->orConditionGroup();
    $or->condition($field_name . '.value', $range_start, '<=')
      ->condition($field_name . '.value', $range_end, '>=');
    return $or;
  }

  /**
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   * @param string $start_date
   * @param string $end_date
   * @param string $field_name
   * @param string|null $end_date_field_name
   *
   * @return \Drupal\Core\Entity\Query\ConditionInterface
   */
  public function dateRangeMustNotOverlap(QueryInterface $query, string $start_date, string $end_date, string $field_name = 'date', string $end_date_field_name = NULL, bool $include_null_start_date = FALSE, bool $include_null_end_date = FALSE) : ConditionInterface {
    if ($end_date_field_name === NULL) {
      $end_date_field_name = $field_name;
      $start_column = 'value';
      $end_column = 'end_value';
    }
    else {
      $start_column = 'value';
      $end_column = 'value';
    }

    // If start is before this range and end is after this range, it overlaps.
    // Treat NULL values and infinitely in past or future, if specified by arguments
    $before_and_after = $query->andConditionGroup();
    if ($include_null_start_date) {
      $before_and_after->condition(
        $query->orConditionGroup()
          ->notExists($field_name . '.' . $start_column)
          ->condition($field_name . '.' . $start_column, $start_date, '<=')
      );
    }
    else {
      $before_and_after->condition($field_name . '.' . $start_column, $start_date, '<=');
    }
    if ($include_null_end_date) {
      $before_and_after->condition(
        $query->orConditionGroup()
          ->notExists($end_date_field_name . '.' . $end_column)
          ->condition($end_date_field_name . '.' . $end_column, $end_date, '>=')
      );
    }
    else {
      $before_and_after
        ->condition($end_date_field_name . '.' . $end_column, $end_date, '>=');
    }

    // If either start or end date are inside this date range, it overlaps.
    $before_or_after = $query->orConditionGroup();

    $start_date_inside = $query->andConditionGroup();
    $start_date_inside->condition($field_name . '.' . $start_column, $start_date, '>=')
      ->condition($field_name . '.' . $start_column, $end_date, '<=');

    $end_date_inside = $query->andConditionGroup();
    $end_date_inside->condition($end_date_field_name . '.' . $end_column, $start_date, '>=')
      ->condition($end_date_field_name . '.' . $end_column, $end_date, '<=');

    $before_or_after->condition($start_date_inside)
      ->condition($end_date_inside);

    $or = $query->orConditionGroup();
    $or->condition($before_and_after)
      ->condition($before_or_after);
    return $or;
  }

}
