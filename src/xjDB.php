<?php

namespace Timori;

class xjDB
{
  private $xmlRoot = null;
  private $path = null;
  
  //Initiates the Class
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
  
  //Clears all Entrys
  public function clearAll()
  {
    unset($this->xmlRoot->row);
    
    $this->xmlRoot->asXml($this->path);
  }
  
  //Clears only Entry by Attribute
  public function clear($attribute, $value)
  {
    list($result) = $this->xmlRoot->xpath("//row[@$attribute='$value']");
    unset($result[0]);
    
    $this->xmlRoot->asXml($this->path);
  }
  
  //Insert a new Row into the File
  public function insert($data)
  {
    $row = $this->xmlRoot->addChild("row");
    foreach($data as $name => $attribute)
    {
      $row->addAttribute($name, $attribute);
    }
    
    $this->xmlRoot->asXml($this->path);
  }
  
  //Update a specific attribute
  public function update($id, $attribute, $value)
  {
    $result = $this->xmlRoot->xpath("//row[@id='".$id."']");
    $result[0]->attributes()[$column] = $value;
    
    $this->xmlRoot->asXml($this->path);
  }
  
  //Gets all Rows matching the kriteria
  public function rows($attribute, $value)
  {
    return $this->xmlRoot->xpath("//row[@$attribute='$value']");
  }
  
  //Gets one specific row
  public function row($attribute, $value)
  {
    return $this->rows($attribute,$value)[0]->attributes();
  }
  
  //Creates the Directory and/or File
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
  
  private function createFile($file)
  {
    $fp = fopen($file, "wb");
    $xmlRoot = new \SimpleXMLElement("<table></table>");
    fwrite($fp, $xmlRoot->asXML());
    fclose($fp);
  }
}

?>
