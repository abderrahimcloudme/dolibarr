<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014	   Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       dev/skeletons/crelancetype.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2015-07-06 00:47
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Crelancetype extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='crelancetype';			//!< Id that identify managed objects
	var $table_element='c_relance_type';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $code;
	var $label;
	var $nbre_jours;
	var $description;

    


    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }


    /**
     *  Create object into database
     *
     *  @param	User	$user        User that creates
     *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->code)) $this->code=trim($this->code);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->nbre_jours)) $this->nbre_jours=trim($this->nbre_jours);
		if (isset($this->description)) $this->description=trim($this->description);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_element."(";
		
		$sql.= "code,";
		$sql.= "label,";
		$sql.= "nbre_jours,";
		$sql.= "description";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->code)?'NULL':"'".$this->db->escape($this->code)."'").",";
		$sql.= " ".(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").",";
		$sql.= " ".(! isset($this->nbre_jours)?'NULL':"'".$this->nbre_jours."'").",";
		$sql.= " ".(! isset($this->description)?'NULL':"'".$this->db->escape($this->description)."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(__METHOD__, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX.$this->table_element);

			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //$result=$this->call_trigger('MYOBJECT_CREATE',$user);
	            //if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
	            //// End call triggers
			}
        }

        // Commit or rollback
        if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(__METHOD__." ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
            return $this->id;
		}
    }


    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    	Id object
     *  @param	string	$ref	Ref
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch($id,$ref='')
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.code,";
		$sql.= " t.label,";
		$sql.= " t.nbre_jours,";
		$sql.= " t.description";

		
        $sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
        if ($ref) $sql.= " WHERE t.ref = '".$ref."'";
        else $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch");
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->code = $obj->code;
				$this->label = $obj->label;
				$this->nbre_jours = $obj->nbre_jours;
				$this->description = $obj->description;

                
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            return -1;
        }
    }


    /**
     *  Update object into database
     *
     *  @param	User	$user        User that modifies
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return int     		   	 <0 if KO, >0 if OK
     */
    function update($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->code)) $this->code=trim($this->code);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->nbre_jours)) $this->nbre_jours=trim($this->nbre_jours);
		if (isset($this->description)) $this->description=trim($this->description);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element." SET";
        
		$sql.= " code=".(isset($this->code)?"'".$this->db->escape($this->code)."'":"null").",";
		$sql.= " label=".(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " nbre_jours=".(isset($this->nbre_jours)?$this->nbre_jours:"null").",";
		$sql.= " description=".(isset($this->description)?"'".$this->db->escape($this->description)."'":"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(__METHOD__);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //$result=$this->call_trigger('MYOBJECT_MODIFY',$user);
	            //if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
	            //// End call triggers
			 }
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(__METHOD__." ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    }


 	/**
	 *  Delete object in database
	 *
     *	@param  User	$user        User that deletes
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
		        // want this action calls a trigger.

	            //// Call triggers
	            //$result=$this->call_trigger('MYOBJECT_DELETE',$user);
	            //if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
	            //// End call triggers
			}
		}

		if (! $error)
		{
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX.$this->table_element;
    		$sql.= " WHERE rowid=".$this->id;

    		dol_syslog(__METHOD__);
    		$resql = $this->db->query($sql);
        	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(__METHOD__." ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}



	/**
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param	int		$fromid     Id of object to clone
	 * 	@return	int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Crelancetype($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->statut=0;

		// Clear fields
		// ...

		// Create clone
		$result=$object->create($user);

		// Other options
		if ($result < 0)
		{
			$this->error=$object->error;
			$error++;
		}

		if (! $error)
		{


		}

		// End
		if (! $error)
		{
			$this->db->commit();
			return $object->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Initialise object with example values
	 *	Id must be 0 if object instance is a specimen
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;
		
		$this->code='';
		$this->label='';
		$this->nbre_jours='';
		$this->description='';

		
	}
function getList()
	{
		$tab=array();

		$sql = "SELECT c.rowid ";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_relance_type as c";
		
		dol_syslog(get_class($this)."::getList()", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num=$this->db->num_rows($resql);
			$i=0;
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($resql);
				$relancetype=new Crelancetype($this->db);
				$re = $relancetype->fetch($obj->rowid);
				$tab[]=$relancetype;
				$i++;
			}
			return $tab;
		}
		else
		{
			$this->error=$this->db->error();
			return -1;
		}
	}
	
 function getMenuRelance($index ) {
		
		$relancetypeArr = $this->getList();
		foreach ($relancetypeArr as $typeRelance)
		{
			$this->menu[$index++]=array(
					'fk_menu'=>'fk_mainmenu=accountancy,fk_leftmenu=relance', //On utilise les ancres définis dans le menu parent déclaré au dessus
					'type'=> 'left', // Toujours un menu gauche
					'titre'=> $typeRelance->label,
					'mainmenu'=> 'accountancy',
					'leftmenu'=> '', // On n'indique rien ici car on ne souhaite pas intégrer de sous-menus à ce menu
					'url'=> '/relance/rappel.php?late='.$typeRelance->nbre_jours,
					'langs'=> 'relance@relance',
					'position'=> 2021,
					'enabled'=> '1',
					'perms'=> '',
					'target'=> '',
					'user'=> 0
			);
		}
		
		
	}
	
}
