<?php

namespace Radar\Connect;

class Filter {
  /**
   * @var array
   *   Query stack.
   */
  private $query;

  /**
   * Add arbitary filter, knowing key.
   *
   * If you add something that doesn't work it usually just returns no results.
   *
   * @param string key
   * @param string value
   */
  public function add($key, $value) {
    $this->query[$key][] = $value;
  }

  /**
   * Filter by group.
   *
   * @param int $id
   *   Presently requires the internal node ID, not the uuid.
   */
  public function addGroup($id) {
    $this->query['group'][] = $id;
  }

  /**
   * Filter by country.
   *
   * @param string $country
   *   Country code. Generally ISO, but there are additions.
   *   @todo Make a query or a list for this.
   */
  public function addCountry($country) {
    $this->query['country'][] = $country;
  }

  /**
   * Filter by city.
   *
   * @param string $city
   *   Name of city.
   */
  public function addCity($city) {
    $this->query['city'][] = $city;
  }

  /**
   * Filter by year.
   *
   * @param string $year
   *   Optional: year in YYYY format. Default current year.
   */
  public function addYear($year = 'now') {
    if ($year == 'now') {
      $year = date('Y');
    }
    $this->query['date'][] = $year;
  }

  /**
   * Filter by month.
   *
   * @param string $month
   *   Optional: month in MM numeric format. Default current month.
   * @param string $year
   *   Optional: year in YYYY format. Default current year.
   */
  public function addMonth($month = 'now', $year = 'now') {
    if ($month = 'now') {
      $month = date('m');
    }
    if ($year = 'now') {
      $year = date('Y');
    }
    $this->query['date'][] = $year . '-' . $month;
  }
  /**
   * Filter by day.
   *
   * @param string day
   *   Optional: day in DD numeric format. Default curent day.
   * @param string $month
   *   Optional: month in MM numeric format. Default current month.
   * @param string $year
   *   Optional: year in YYYY format. Default current year.
   */
  public function addDay($day = 'now', $month = 'now', $year = 'now') {
    if ($day = 'now') {
      $day = date('d');
    }
    if ($month = 'now') {
      $month = date('m');
    }
    if ($year = 'now') {
      $year = date('Y');
    }
    $this->query['date'][] = $year . '-'  . $month . '-' . $day;
  }

  /**
   * Filter by date.
   *
   * @param \DateTime date
   */
  public function addDate(\DateTime $date) {
    $this->query['date'][] = $date->format('Y-m-d');
  }

  /**
   * Filter by category.
   *
   * @param string category.
   *   @todo make the list of fixed categories available.
   */
  public function addCategory($category) {
    $this->query['category'][] = $category;
  }

  /**
   * Filter by price.
   *
   * @param string $price
   *   'free entrance', 'by donation', other strings entered as free text.
   */
  public function addPrice($price) {
    $this->query['price'][] = $price;
  }

  /**
   * Return the query array.
   */
  public function getQuery() {
    return $this->query;
  }

}
