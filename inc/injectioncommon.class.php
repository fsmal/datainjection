<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2008 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

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
 --------------------------------------------------------------------------
 */
class PluginDatainjectionInjectionCommon {

   // Check Status
   const CHECK_OK = 0;
   const CHECK_NOTOK = 1;

   // Check message
   const TYPE_CHECK_OK = 1;
   const ERROR_IMPORT_WRONG_TYPE = 2;

   //Line of data to inject
   var $data_to_inject = array();

   //Result after line check
   var $check_results = array();

   //Result after line injection
   var $injection_results = array();

   //Model to use for injection
   var $model = false;

   static function getInstance($itemtype) {
      $injectionClass = 'PluginDatainjection'.ucfirst($itemtype).'Injection';
      return new $injectionClass();
   }

   static function getItemtypeInstanceByInjection($injectionClassName) {
      $pattern = "/PluginDatainjection(.*)Injection/";
      if (preg_match($pattern,$injectionClassName,$results)) {
         return new $results[2];
      }
      return false;
   }
}
?>