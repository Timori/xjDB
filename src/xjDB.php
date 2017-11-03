<?php
/**
* Script, which makes it easy to use XML-Files as a Flat File Database System.
* @copyright 2017 Tobias Schotenröhr
*/

namespace Timori;

class xjDB
{
  /**
  * @var \SimpleXMLElement Represents the root element of the xml-File, which is the table tag
  */
  private $xmlRoot = null;
  
  /**
  * @var String The path to the file.
  */
  private $path = null;
  
  /**
  * Constructor, creates the xml-File, if it doesn't exist.
  * Also the directory will be created, if it doesn't exist.
  *
  * @param String $file Represents the xml-File itself
  * @param String $dir Represents the directory, where the file should be created
  */
  public function __construct($file, $dir = null)
  {
    $this->path = $dir.$file;
    if($dir)
    {
      $this->createIfNotExists($dir, true);
    }
    $this->createIfNotExists($this->path);
    $this->xmlRoot = simplexml_load_file($this->path);
  }
  
  /**
  * Clears all rows in the file.
  */
  public function clearAll()
  {
    unset($this->xmlRoot->row);
    
    $this->xmlRoot->asXml($this->path);
  }
  
  /**
  * Clears all rows with the given attribute and value
  *
  * @param String $attribute The row attribute
  * @param String $value The attribute's value
  */
  public function clear($attribute, $value)
  {
    list($result) = $this->xmlRoot->xpath("//row[@$attribute='$value']");
    unset($result[0]);
    
    $this->xmlRoot->asXml($this->path);
  }
  
  /**
  * Inserts a new row into the file.
  *
  * @param array $data Associative array of attributes
  */
  public function insert($data)
  {
    $row = $this->xmlRoot->addChild("row");
    foreach($data as $attribute => $value)
    {
      $row->addAttribute($attribute, $value);
    }
    
    $this->xmlRoot->asXml($this->path);
  }
  
  /**
  * Updates an entry with a given id.
  *
  * @param array where Associative array of attributes
  * @param String $attribute Attribute, where the old value is found
  * @param String $value Value to be update
  */
  public function update($where, $attribute, $value)
  {
    $result = $this->rows($where);
    $result[0]->attributes()[$attribute] = $value;
    
    $this->xmlRoot->asXml($this->path);
  }
 
  /**
  * Gets multiple rows based on at least one kriteria.
  *
  * @param array $where Associative array of attributes
  * 
  * @return array SimpleXMLElements representing the results.
  */
  public function rows($where = null)
  {
    $result = null;
    if($where)
    {
      foreach($where as $attribute => $value)
      {
        $result_set = array();
        
        if(!$result)
        {
          $result = $this->xmlRoot->xpath("//row[@$attribute='$value']");
        }

        foreach($result as $entry)
        {
          if($entry->attributes()[$attribute] == $value)
          {
            $result_set[] = $entry;
          }
        }
        $result = $result_set;
      }
    }
    else
    {
      $result = $this->xmlRoot->xpath("//row");
    }
    return $result;
  }
  
  /**
  * Gets one row by attribute holds the given value.
  *
  * @param String $attribute Attribute, which holds the value
  * @param String $value The wanted value
  *
  * @return \SimpleXMLElement representing the result
  */
  public function row($attribute, $value)
  {
    return $this->rows([$attribute => $value])[0]->attributes();
  }
  
  //Creates the Directory and/or File
  /**
  * Creates either a file or a directory.
  *
  * @param String $file Path to the file or directory
  * @param boolean $isDir Is the given path a directory?
  */
  private function createIfNotExists($file, $isDir = false)
  {
    if (!file_exists($file))
    {
      if($isDir == true)
      {
        mkdir($file);
      }
      else
      {
        $this->createFile($file);
      }
    }
  }
  
  /**
  * Creates the file with basic XML structure.
  *
  * @param String $file The file including path to be created.
  */
  private function createFile($file)
  {
    $fp = fopen($file, "wb");
    $xmlRoot = new \SimpleXMLElement("<table></table>");
    fwrite($fp, $xmlRoot->asXML());
    fclose($fp);
  }
}

?>
