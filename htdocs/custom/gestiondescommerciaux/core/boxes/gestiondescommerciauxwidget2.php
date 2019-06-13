<?php
/* Copyright (C) 2004-2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2018       Frédéric France     <frederic.france@netlogic.fr>
 * Copyright (C) 2019 SuperAdmin
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
 * \file    modulebuilder/template/core/boxes/gestiondescommerciauxwidget2.php
 * \ingroup gestiondescommerciaux
 * \brief   Widget provided by GestionDesCommerciaux
 *
 * Put detailed description here.
 */

/** Includes */
include_once DOL_DOCUMENT_ROOT . "/core/boxes/modules_boxes.php";

/**
 * Class to manage the box
 *
 * Warning: for the box to be detected correctly by dolibarr,
 * the filename should be the lowercase classname
 */
class gestiondescommerciauxwidget2 extends ModeleBoxes
{
	/**
	 * @var string Alphanumeric ID. Populated by the constructor.
	 */
	public $boxcode = "gestiondescommerciauxbox";

	/**
	 * @var string Box icon (in configuration page)
	 * Automatically calls the icon named with the corresponding "object_" prefix
	 */
	public $boximg = "gestiondescommerciaux@gestiondescommerciaux";

	/**
	 * @var string Box label (in configuration page)
	 */
	public $boxlabel;

	/**
	 * @var string[] Module dependencies
	 */
	public $depends = array('gestiondescommerciaux');

	/**
	 * @var DoliDb Database handler
	 */
	public $db;

	/**
	 * @var mixed More parameters
	 */
	public $param;

	/**
	 * @var array Header informations. Usually created at runtime by loadBox().
	 */
	public $info_box_head = array();

	/**
	 * @var array Contents informations. Usually created at runtime by loadBox().
	 */
	public $info_box_contents = array();

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 * @param string $param More parameters
	 */
	public function __construct(DoliDB $db, $param = '')
	{
		global $user, $conf, $langs;
		$langs->load("boxes");
		$langs->load('gestiondescommerciaux@gestiondescommerciaux');

		parent::__construct($db, $param);

		$this->boxlabel = $langs->transnoentitiesnoconv("MyWidget");

		$this->param = $param;

		//$this->enabled = $conf->global->FEATURES_LEVEL > 0;         // Condition when module is enabled or not
		//$this->hidden = ! ($user->rights->gestiondescommerciaux->myobject->read);   // Condition when module is visible by user (test on permission)
	}

	/**
	 * Load data into info_box_contents array to show array later. Called by Dolibarr before displaying the box.
	 *
	 * @param int $max Maximum number of records to load
	 * @return void
	 */
	public function loadBox($max = 5)
	{
		global $langs, $db;

		// Use configuration value for max lines count
		$this->max = $max;

		include_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
		$thirdpartystatic=new Societe($db);

		//include_once DOL_DOCUMENT_ROOT . "/gestiondescommerciaux/class/gestiondescommerciaux.class.php";

		// Populate the head at runtime
		$text = $langs->trans("Client â appeler", $max);
		$this->info_box_head = array(
			// Title text
			'text' => $text,
			// Add a link
			'sublink' => '/gestiondescommerciaux/list.php',
			// Sublink icon placed after the text
			'subpicto' => 'object_gestiondescommerciaux@gestiondescommerciaux',
			// Sublink icon HTML alt text
			'subtext' => '',
			// Sublink HTML target
			'target' => '',
			// HTML class attached to the picto and link
			'subclass' => 'center',
			// Limit and truncate with "…" the displayed text lenght, 0 = disabled
			'limit' => 0,
			// Adds translated " (Graph)" to a hidden form value's input (?)
			'graph' => false
		);

		/*if ($user->rights->societe->lire)
		{*/
			
			/**/
			//
			$sql = "SELECT s.nom as name, s.rowid as socid";
            // $sql.= ", s.nom";
            $sql.= ", s.phone";
            $sql.= ", s.code_client";
            $sql.= ", s.datec, s.tms, s.status";
            // $sql.= ", s.client";

			$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
			$sql.= " WHERE (s.dateFirstCall = '".date('Y-m-d')."' and issueFirstCall is Null)  or ( s.dateSecondCall = '".date('Y-m-d')."'  and issueSecondCall is Null) or ( s.dateThirdCall = '".date('Y-m-d')."'  and issueThirdCall is Null)";
			/*if (!$user->rights->societe->client->voir && !$user->societe_id) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
			$sql.= " WHERE s.client IN (1, 3)";
			$sql.= " AND s.entity IN (".getEntity('societe').")";
			if (!$user->rights->societe->client->voir && !$user->societe_id) $sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
			if ($user->societe_id) $sql.= " AND s.rowid = $user->societe_id";
			$sql.= " ORDER BY s.tms DESC";
			$sql.= $db->plimit($max, 0);*/
			// dateFirstCall dateSecondCall  dateThirdCall  

			$sql.= $db->plimit($max, 0);

			dol_syslog(get_class($this)."::loadBox", LOG_DEBUG);
			$result = $db->query($sql);
			if ($result)
			{
				$num = $db->num_rows($result);

				$line = 0;
				while ($line < $num)
				{
					$objp = $db->fetch_object($result);
                    $thirdpartystatic->id = $objp->socid;
                    $thirdpartystatic->name = $objp->name;
                    $thirdpartystatic->code_client = $objp->code_client;
                    $thirdpartystatic->phone = $objp->phone;

                    $this->info_box_contents[$line][] = array(
                        'td' => '',
                        'text' => $thirdpartystatic->getNomUrl(1),
                        'asis' => 1,
                    );

                    $this->info_box_contents[$line][] = array(
                        'td' => 'class="right"',
                        'text' => $thirdpartystatic->code_client
                    );
                    $this->info_box_contents[$line][] = array(
                        'td' => 'class="right"',
                        'text' => $thirdpartystatic->phone
                    );

                    $this->info_box_contents[$line][] = array(
                        'td' => 'align="right" width="18"',
                        'text' => $thirdpartystatic->LibStatut($objp->status,3)
                    );

					$line++;
				}

				if ($num==0) $this->info_box_contents[$line][0] = array('td' => 'align="center"','text'=>$langs->trans("NoRecordedCustomers"));

				$db->free($result);
			}
			else {
				$this->info_box_contents[0][0] = array(	'td' => '',
    	        										'maxlength'=>500,
	            										'text' => (' sql='.$sql));
			}
		/*}
		else {
			$this->info_box_contents[0][0] = array(
			    'td' => 'align="left" class="nohover opacitymedium"',
                'text' => $langs->trans("ReadPermissionNotAllowed")
			);
		}*/
		
	}

	/**
	 * Method to show box. Called by Dolibarr eatch time it wants to display the box.
	 *
	 * @param array $head       Array with properties of box title
	 * @param array $contents   Array with properties of box lines
     * @param int   $nooutput   No print, only return string
	 * @return void
	 */
	public function showBox($head = null, $contents = null, $nooutput = 0)
	{
		// You may make your own code here…
		// … or use the parent's class function using the provided head and contents templates
		parent::showBox($this->info_box_head, $this->info_box_contents);
	}
}
