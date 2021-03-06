<?php
/**
 * This script can create and manipulate xml-"databases".
 *
 * This script is planned to be an alternative to MySQL, PostgreSQL and SQLite. It is currently stable to use, but also in development.
 *
 * @category     Flat File Database
 * @author       Tobias Schotenröhr (tschotenroehr@yandex.com)
 * @copyright    Tobias Schotenröhr 2017
 */

namespace Timori;

/**
 * Class to create xml files which can be used to hold permanent data.
 */
class xjDB {
  /**
   * @var \SimpleXMLElement Represents the root element of the xml-File, which is the table tag
   */
  private $__xmlRoot = null;

  /**
   * @var string The path to the file.
   */
  private $__path = null;

  /**
   * Constructor, creates the xml-File, if it doesn't exist.
   * Also the directory will be created, if it doesn't exist.
   *
   * @param string $file Represents the xml-File itself
   * @param string $dir Represents the directory, where the file should be created
   */
  public function __construct($file, $dir = null) {
    $this->__path = $dir . $file;

    if ($dir) {
      $this->__createIfNotExists($dir, true);
    }

    $this->__createIfNotExists($this->__path);
    $this->__xmlRoot = simplexml_load_file($this->__path);
  }

  /**
   * Clears all rows in the file.
   */
  public function clearAll() {
    unset($this->__xmlRoot->row);

    $this->__xmlRoot->asXml($this->__path);
  }

  /**
   * Clears all rows with the given attribute and value
   *
   * @param string $attribute The row attribute
   * @param string $value The attribute's value
   */
  public function clear($attribute, $value) {
    list($result) = $this->__xmlRoot->xpath("//row[@$attribute='$value']");
    unset($result[0]);

    $this->__xmlRoot->asXml($this->__path);
  }

  /**
   * Inserts a new row into the file.
   *
   * @param array $data Associative array of attributes
   */
  public function insert($data) {
    $row = $this->__xmlRoot->addChild("row");

    foreach ($data as $attribute => $value) {
      $row->addAttribute($attribute, $value);
    }

    $this->__xmlRoot->asXml($this->__path);
  }

  /**
   * Updates an entry with a given id.
   *
   * @param array $where Associative array of attributes
   * @param string $attribute Attribute, where the old value is found
   * @param string $value Value to be update
   */
  public function update($where, $attribute, $value) {
    $result                              = $this->rows($where);
    $result[0]->attributes()[$attribute] = $value;

    $this->__xmlRoot->asXml($this->__path);
  }

  /**
   * Gets multiple rows based on at least one kriteria.
   *
   * @param array $where Associative array of attributes
   *
   * @return array SimpleXMLElements representing the results.
   */
  public function rows($where = null) {
    $result = null;

    if ($where) {

      foreach ($where as $attribute => $value) {
        $result_set = [];

        if (!$result) {
          $result = $this->__xmlRoot->xpath("//row[@$attribute='$value']");
        }

        foreach ($result as $entry) {

          if ($entry->attributes()[$attribute] == $value) {
            $result_set[] = $entry;
          }

        }

        $result = $result_set;
      }

    } else {
      $result = $this->__xmlRoot->xpath("//row");
    }

    return $result;
  }

  /**
   * Gets one row by attribute holds the given value.
   *
   * @param string $attribute Attribute, which holds the value
   * @param string $value The wanted value
   *
   * @return \SimpleXMLElement representing the result
   */
  public function row($attribute, $value) {
    return $this->rows([$attribute => $value])[0]->attributes();
  }

  //Creates the Directory and/or File
  /**
   * Creates either a file or a directory.
   *
   * @param string $file Path to the file or directory
   * @param boolean $isDir Is the given path a directory?
   */
  private function __createIfNotExists($file, $isDir = false) {

    if (!file_exists($file)) {

      if ($isDir == true) {
        mkdir($file);
      } else {
        $this->__createFile($file);
      }

    }

  }

  /**
   * Creates the file with basic XML structure.
   *
   * @param string $file The file including path to be created.
   */
  private function __createFile($file) {
    $fp      = fopen($file, "wb");
    $__xmlRoot = new \SimpleXMLElement("<table></table>");
    fwrite($fp, $__xmlRoot->asXML());
    fclose($fp);
  }

}

?>