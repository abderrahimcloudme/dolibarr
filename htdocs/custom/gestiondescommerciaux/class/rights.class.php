<?php
/* Copyright (C) 2019 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    gestiondescommerciaux/class/rights.class.php
 * \ingroup gestiondescommerciaux
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

/**
 * Class RightsGestionDesCommerciaux
 */
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/multicurrency/class/multicurrency.class.php';

class RightsGestionDesCommerciaux extends CommonObject
{
    /**
     * @var DoliDB Database handler.
     */
    public $db;

    /**
     * @var string Error code (or message)
     */
    public $error = '';

    /**
     * @var array Errors
     */
    public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
	    $this->db = $db;
	}


	
	function checkIfCommercial($id)
	{
		$sql = "SELECT fk_user";
		$sql .= " FROM llx_usergroup_user";
		$sql .= " WHERE fk_user=".$id;
		$sql .= " AND fk_usergroup=1";
		$result=$this->db->query($sql);
		$obj = $this->db->fetch_object($result);
		$this->user = $obj->fk_user;
		if ($this->user != $id){return 1;} 
		else {return 0;}
	}
}