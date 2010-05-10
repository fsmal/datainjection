<?php

/*
 ----------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2008 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org/
 ----------------------------------------------------------------------

 LICENSE

   This file is part of GLPI.

    GLPI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    GLPI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GLPI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 ------------------------------------------------------------------------
*/

// Original Author of file: Walid Nouh
// Purpose of file:
// ----------------------------------------------------------------------
class PluginDatainjectionBackendcsv extends PluginDatainjectionBackend {

   private $delemiter = '';
   private $isHeaderPresent = true;

   function __construct() {
      $this->errmsg= "";
   }

   //Getters & setters
   function getDelimiter() {
      return $this->delimiter;
   }

   function isHeaderPresent() {
      return $this->isHeaderPresent;
   }

   function setDelimiter($delimiter) {
      $this->delimiter = $delimiter;
   }

   function setHeaderPresent($present = true) {
      $this->isHeaderPresent = $present;
   }


   //CSV File parsing methods
   static function parseLine($fic, $data, $encoding= 1) {
      $csv= array();
      $num= count($data);
      for($c= 0; $c < $num; $c++) {
         //If field is not the last, or if field is the last of the line and is not empty

         if($c <($num -1)
               || ($c ==($num -1)
                  && $data[$num -1] != PluginDatainjectionCommonInjectionLib::EMPTY_VALUE)) {
            switch($encoding) {
               //If file is ISO8859-1 : encode the datas in utf8
               case PluginDatainjectionBackend :: ENCODING_ISO8859_1 :
                  $csv[0][]= utf8_encode(addslashes($data[$c]));
                  break;
               case PluginDatainjectionBackend :: ENCODING_UFT8 :
                  $csv[0][]= addslashes($data[$c]);
                  break;
               case PluginDatainjectionBackend :: ENCODING_AUTO :
                  $csv[0][]= PluginDatainjectionBackend :: toUTF8(addslashes($data[$c]));
                  break;
            }
         }
      }
      return $csv;
   }

   function init($newfile,$encoding) {
      $this->file= $newfile;
      $this->encoding= $encoding;
   }

   /**
    * Read a CSV file and store data in an array
    * @param only_firstline indicates if only the first line must be returned (header or not)
    */
   function read($only_firstline = false) {
      $fic= fopen($this->file, 'r');

      $injectionData = new PluginDatainjectionData;
      while(($data= fgetcsv($fic,
                            3000,
                            $this->getDelimiter())) !== FALSE) {
         //If line is not empty
         if(count($data) > 1 || $data[0] != PluginDatainjectionCommonInjectionLib::EMPTY_VALUE) {
            $line= self::parseLine($fic, $data, $this->encoding);
            if(count($line[0]) > 0) {
               $injectionData->addToDatas($line);
               if ($only_firstline) {
                  break;
               }
            }
         }
      }
      fclose($fic);
      return $injectionData;
   }

   function deleteFile() {
      unlink($this->file);
   }

   function export($file, $model, $tab_result) {
      $tmpfile= fopen($file, "w");

      $header= $this->getHeader($model->isHeaderPresent());

      fputcsv($tmpfile, $header, $this->getDelimiter());

      foreach($tab_result[0] as $value) {
         $list= $this->getDataAtLine($value->getLineID());

         fputcsv($tmpfile, $list, $this->getDelimiter());
      }

      fclose($tmpfile);
   }

   function readLinesFromTo($start_line, $end_line) {
      $row= 0;
      $fic= fopen($this->file, 'r');
      $injectionData = new PluginDatainjectionData;

      while((($data= fgetcsv($fic, 3000, $this->delimiter)) !== FALSE) && $row <= $end_line) {
         if($row >= $start_line && $row <= $end_line)
            $injectionData->addToDatas(self :: parseLine($fic,$data));
         $row++;
      }

      fclose($fic);
      return $injectionData;
   }
}
?>