<?php
/*
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
 * along with this program. If not, see <htt'p://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   abonnement     Module abonnement
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/abonnement/core/modules directory.
 *  \file       htdocs/abonnement/core/modules/modabonnement.class.php
 *  \ingroup    abonnement
 *  \brief      Description and activation file for module abonnement
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module abonnement
 */
class modAbonnement extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
        global $langs,$conf;

        $this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 600001;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'abonnement';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Description of module abonnement";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0';
		// Key used in llx_const table to save module status enabled/disabled (where abonnement is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='abonnement@abonnement';

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /abonnement/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /abonnement/core/modules/barcode)
		// for specific css file (eg: /abonnement/css/abonnement.css.php)
		//$this->module_parts = array(
		//                        	'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
		//							'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
		//							'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
		//							'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
		//							'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
		//                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
		//							'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
		//							'models' => 0,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
		//							'css' => array('/abonnement/css/abonnement.css.php'),	// Set this to relative path of css file if module has its own css file
	 	//							'js' => array('/abonnement/js/abonnement.js'),          // Set this to relative path of js file if module must load a js on all pages
		//							'hooks' => array('hookcontext1','hookcontext2')  	// Set here all hooks context managed by module
		//							'dir' => array('output' => 'othermodulename'),      // To force the default directories names
		//							'workflow' => array('WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2'=>array('enabled'=>'! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)', 'picto'=>'yourpicto@abonnement')) // Set here all workflow context managed by module
		//                        );
		$this->module_parts = array('triggers' => 1);
		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/abonnement/temp");
		$this->dirs = array("/abonnement/societe");

		// Config pages. Put here list of php page, stored into abonnement/admin directory, to use to setup module.
		$this->config_page_url = array("admin_abonnement.php@abonnement");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,7);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("abonnement@abonnement");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array();
		$this->const = array(
				0 => array(
						'NBRE_JOURS_AVANT_RENOUVELLEMENT',
						'chaine',
						'',
						'Nombre de jour avant renouvellement du contrat  ',
						0,
						'allentities',
						1
				),
				1 => array(
						'NBRE_MAX_ABONNE_WEB',
						'chaine',
						'',
						'Nombre maximum d\'abonné web  ',
						0,
						'allentities',
						1
				),
				2 => array(
						'PROFIL_ABONNE_WEB',
						'chaine',
						'',
						'Profil d\'abonné web  ',
						0,
						'allentities',
						1
				),
				3 => array(
						'PROFIL_CLIENT',
						'chaine',
						'',
						'Profil par défaut du client ',
						0,
						'allentities',
						1
				)
				);
		$this->tabs = array('contract:+tabname1:AbonneWeb:abonnement@abonnement:1:/abonnement/contact.php?id=__ID__',
				            'contract:+tabname2:AbonnePapier:abonnement@abonnement:1:/abonnement/contactpapier.php?id=__ID__',
				            'contract:-tabname:AbonnePapier',
				
				          );  					// To add a new tab identified by code tabname1
				                        //    'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@abonnement:$user->rights->othermodule->read:/abonnement/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
				                        //    'objecttype:-tabname:NU:conditiontoremove'
				//);                                                     										// To remove an existing tab identified by code tabname
				

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:mylangfile@abonnement:$user->rights->abonnement->read:/abonnement/mynewtab1.php?id=__ID__',  					// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@abonnement:$user->rights->othermodule->read:/abonnement/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
        //                              'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
		// where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in fundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in customer order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view
        //$this->tabs = array();

        // Dictionaries
	    if (! isset($conf->abonnement->enabled))
        {
        	$conf->abonnement=new stdClass();
        	$conf->abonnement->enabled=0;
        }
		$this->dictionaries=array();
        /* Example:
        if (! isset($conf->abonnement->enabled)) $conf->abonnement->enabled=0;	// This is to avoid warnings
        $this->dictionaries=array(
            'langs'=>'mylangfile@abonnement',
            'tabname'=>array(MAIN_DB_PREFIX."table1",MAIN_DB_PREFIX."table2",MAIN_DB_PREFIX."table3"),		// List of tables we want to see into dictonnary editor
            'tablib'=>array("Table1","Table2","Table3"),													// Label of tables
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),	// Request to select fields
            'tabsqlsort'=>array("label ASC","label ASC","label ASC"),																					// Sort order
            'tabfield'=>array("code,label","code,label","code,label"),																					// List of fields (result of select to show dictionary)
            'tabfieldvalue'=>array("code,label","code,label","code,label"),																				// List of fields (list of fields to edit a record)
            'tabfieldinsert'=>array("code,label","code,label","code,label"),																			// List of fields (list of fields for insert)
            'tabrowid'=>array("rowid","rowid","rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
            'tabcond'=>array($conf->abonnement->enabled,$conf->abonnement->enabled,$conf->abonnement->enabled)												// Condition to show each dictionary
        );
        */
		
		$this->dictionnaries=array(
				'langs'=>'abonnement@abonnement',
				'tabname'=>array(MAIN_DB_PREFIX."c_texte_document"),
				'tablib'=>array("TexteDocument"),
				'tabsql'=>array('SELECT f.rowid as rowid, f.code,  f.label, f.content FROM '.MAIN_DB_PREFIX.'c_texte_document as f'),
				'tabsqlsort'=>array("rowid ASC"),
				'tabfield'=>array("code,label,content"),
				'tabfieldvalue'=>array("code,label,content"),
				'tabfieldinsert'=>array("code,label,content"),
				'tabrowid'=>array("rowid"),
				'tabcond'=>array($conf->abonnement->enabled)
		);

        // Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array();			// List of boxes
		$r = 0;
			
		$this->boxes[$r][1] = "box_abon_trop_percu.php@abonnement";
		//$r ++;
		//$this->boxes[$r][1] = "box_abon_late@abonnement";
		// Example:
		//$this->boxes=array(array(0=>array('file'=>'myboxa.php','note'=>'','enabledbydefaulton'=>'Home'),1=>array('file'=>'myboxb.php','note'=>''),2=>array('file'=>'myboxc.php','note'=>'')););

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;
		$this->rights[$r][0] = 6000011;
		$this->rights[$r][1] = 'Ajouter abonné Web';//'create  Web subscriber';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'createsubscriber';
		$r ++;
		$this->rights[$r][0] = 6000012;
		$this->rights[$r][1] = 'Supprimer abonné web';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'deletesubscriberweb';
		$r ++;
		$this->rights[$r][0] = 6000013;
		$this->rights[$r][1] = 'Ajouter abonné papier';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'addsubscriberpaper';
		$r ++;
		
		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		// $this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		// $this->rights[$r][1] = 'Permision label';	// Permission label
		// $this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		// $this->rights[$r][4] = 'level1';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $r++;


		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;

		// Add here entries to declare new menus
		//
		// Example to declare a new Top Menu entry and its Left menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>0,			                // Put 0 if this is a top menu
		//							'type'=>'top',			                // This is a Top menu entry
		//							'titre'=>'abonnement top menu',
		//							'mainmenu'=>'abonnement',
		//							'leftmenu'=>'abonnement',
		//							'url'=>'/abonnement/pagetop.php',
		//							'langs'=>'mylangfile@abonnement',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'$conf->abonnement->enabled',	// Define condition to show or hide menu entry. Use '$conf->abonnement->enabled' if entry must be visible if module is enabled.
		//							'perms'=>'1',			                // Use 'perms'=>'$user->rights->abonnement->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		// $r++;
		//
		// Example to declare a Left Menu entry into an existing Top menu entry:
// 		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=xxx',		    // Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
// 									'type'=>'left',			                // This is a Left menu entry
// 									'titre'=>'abonnement left menu',
// 									'mainmenu'=>'xxx',
// 									'leftmenu'=>'abonnement',
// 									'url'=>'/abonnement/pagelevel2.php',
// 									'langs'=>'mylangfile@abonnement',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
// 									'position'=>100,
// 									'enabled'=>'$conf->abonnement->enabled',  // Define condition to show or hide menu entry. Use '$conf->abonnement->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
// 									'perms'=>'1',			                // Use 'perms'=>'$user->rights->abonnement->level1->level2' if you want your menu with a permission rules
// 									'target'=>'',
// 									'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		// $r++;
		$this->menu[$r]=array(	'fk_menu'=>0,			                // Put 0 if this is a top menu
								'type'=>'top',			                // This is a Top menu entry
								'titre'=>'Abonnement',
								'mainmenu'=>'abonnement',
								'leftmenu'=>'abonnement',
								'url'=>'/abonnement/abonnementtab.php',
								'langs'=>'abonnement@abonnement',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1000+$r,
								'enabled'=>'$conf->abonnement->enabled',	// Define condition to show or hide menu entry. Use '$conf->abonnement->enabled' if entry must be visible if module is enabled.
								'perms'=>'$conf->abonnement->enabled',			                // Use 'perms'=>'$user->rights->abonnement->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
				$r++;
				$this->menu[$r] = array(
						'fk_menu' => 'fk_mainmenu=abonnement',
						'type' => 'left',
						'titre' => 'Contrat',
						'leftmenu' => 'abonnement',
						'url' => '/contrat/list.php?leftmenu=contracts',
						'langs' => 'abonnement@abonnement',
						'position' => 1000+$r,
						'enabled' => '1',
						'perms' => '1',
						'target' => '',
						'user' => 2
				);
				$r ++;
				
				$this->menu[$r] = array(
						'fk_menu' => 'fk_mainmenu=abonnement,fk_leftmenu=abonnement',
						'type' => 'left',
						'titre' => 'Services',
						'url' => '/contrat/services.php?leftmenu=contracts',
						'langs' => 'abonnement@abonnement',
						'position' => 1000+$r,
						'enabled' => '1',
						'perms' => '1',
						'target' => '',
						'user' => 2
				);
				$r ++;
				
				$this->menu[$r] = array(
						'fk_menu' => 'fk_mainmenu=abonnement,fk_leftmenu=abonnement',
						'type' => 'left',
						'titre' => 'ContactList',
						'url' => '/contact/list.php?leftmenu=contacts',
						'langs' => 'abonnement@abonnement',
						'position' => 1000+$r,
						'enabled' => '1',
						'perms' => '1',
						'target' => '',
						'user' => 0
				);
				$r ++;
		$this->menu[$r]=array(
				'fk_menu'=>'fk_mainmenu=commercial,fk_leftmenu=contracts',
				'type'=>'left',
				'titre'=> 'MenuReAbonnement',
				'mainmenu'=>'commercial',
				'leftmenu'=> 'abonnement',
				'url'=> '/abonnement/reabonnement.php?typeliste=all',
				'langs'=> 'abonnement@abonnement',
				'position'=> 1000+$r,
				'enabled'=> '1',
				'perms'=> '',
				'user'=> 0
		);
		$r++;
		global  $user ;
		
		//var_dump($user);
		//$id = new User($db);
		$socid = $user->socid;
		$this->menu[$r] = array(
				'fk_menu' => 'fk_mainmenu=abonnement',
				'type' => 'left',
				'titre' => 'PROFIL_USER',
				'leftmenu' => 'abonnement',
				'url' => "/societe/soc.php?socid=$socid",
				'langs' => 'abonnement@abonnement',
				'position' => 1000+$r,
				'enabled' => '1',
				'perms' => '1',
				'target' => '',
				'user' => 2
		);
		$r ++;
		//var_dump($user);exit;
		
		$this->menu[$r++]=array(
				'fk_menu'=>'fk_mainmenu=commercial,fk_leftmenu=abonnement', //On utilise les ancres définis dans le menu parent déclaré au dessus
				'type'=> 'left', // Toujours un menu gauche
				'titre'=> 'AbonnementList',
				'mainmenu'=> 'commercial',
				'leftmenu'=> '', // On n'indique rien ici car on ne souhaite pas intégrer de sous-menus à ce menu
				'url'=> '/abonnement/abonrenouveler.php',
				'langs'=> 'abonnement@abonnement',
				'position'=> 1000+$r,
				'enabled'=> '1',
				'perms'=> '',
				'target'=> '',
				'user'=> 0
		);
		$r++;
		
		$this->menu[$r++]=array(
				'fk_menu'=>'fk_mainmenu=commercial,fk_leftmenu=abonnement', //On utilise les ancres définis dans le menu parent déclaré au dessus
				'type'=> 'left', // Toujours un menu gauche
				'titre'=> 'REABONNEMENTATTENTE',
				'mainmenu'=> 'commercial',
				'leftmenu'=> '', // On n'indique rien ici car on ne souhaite pas intégrer de sous-menus à ce menu
				'url'=> '/abonnement/reabonnement.php?typeliste=attente',
				'langs'=> 'abonnement@abonnement',
				'position'=> 1000+$r,
				'enabled'=> '1',
				'perms'=> '',
				'target'=> '',
				'user'=> 0
		);
		$r++;
		
		
// 		$this->menu[$r++]=array(
// 				'fk_menu'=>'fk_mainmenu=commercial,fk_leftmenu=abonnement', //On utilise les ancres définis dans le menu parent déclaré au dessus
// 				'type'=> 'left', // Toujours un menu gauche
// 				'titre'=> 'AbonnementCreate',
// 				'mainmenu'=> 'commercial',
// 				'leftmenu'=> '', // On n'indique rien ici car on ne souhaite pas intégrer de sous-menus à ce menu
// 				'url'=> '/abonnement/abonnement.php',
// 				'langs'=> 'abonnement@abonnement',
// 				'position'=> 1012,
// 				'enabled'=> '1',
// 				'perms'=> '',
// 				'target'=> '',
// 				'user'=> 0
// 		);
		
// 		$r++;
        
		
		$this->menu[$r++]=array(
				'fk_menu'=>'fk_mainmenu=tools', //On utilise les ancres définis dans le menu parent déclaré au dessus
				'type'=> 'left', // Toujours un menu gauche
				'titre'=> 'AbonImportReleve',
				'mainmenu'=> 'tools',
				'leftmenu'=> 'importpaiement', // On n'indique rien ici car on ne souhaite pas intégrer de sous-menus à ce menu
				'url'=> '/abonnement/import.php',
				'langs'=> 'abonnement@abonnement',
				'position'=> 1000+$r,
				'enabled'=> '1',
				'perms'=> '',
				'target'=> '',
				'user'=> 0
		);
		$r++;
		
		
		$this->menu[$r++]=array(
				'fk_menu'=>'fk_mainmenu=tools,fk_leftmenu=importpaiement', //On utilise les ancres définis dans le menu parent déclaré au dessus
				'type'=> 'left', // Toujours un menu gauche
				'titre'=> 'AbonImportReleveNouveau',
				'mainmenu'=> 'tools',
				'leftmenu'=> '', // On n'indique rien ici car on ne souhaite pas intégrer de sous-menus à ce menu
				'url'=> '/abonnement/import.php',
				'langs'=> 'abonnement@abonnement',
				'position'=> 1000+$r,
				'enabled'=> '1',
				'perms'=> '',
				'target'=> '',
				'user'=> 0
		);
		
		$r++;
		
// 		$this->menu[$r++]=array(
// 				'fk_menu'=>'fk_mainmenu=commercial,fk_leftmenu=abonnement', //On utilise les ancres définis dans le menu parent déclaré au dessus
// 				'type'=> 'left', // Toujours un menu gauche
// 				'titre'=> 'AbonnementInfo',
// 				'mainmenu'=> 'commercial',
// 				'leftmenu'=> '', // On n'indique rien ici car on ne souhaite pas intégrer de sous-menus à ce menu
// 				'url'=> '/contrat/list.php',
// 				'langs'=> 'abonnement@abonnement',
// 				'position'=> 1021,
// 				'enabled'=> '1',
// 				'perms'=> '',
// 				'target'=> '',
// 				'user'=> 0
// 		);
		

		// Exports
		$r=1;

		// Example:
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='CustomersInvoicesAndInvoiceLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
        // $this->export_enabled[$r]='1';                               // Condition to show export in list (ie: '$user->id==3'). Set to 1 to always show when module is enabled.
		// $this->export_permission[$r]=array(array("facture","facture","export"));
		// $this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>'CompanyName','s.address'=>'Address','s.zip'=>'Zip','s.town'=>'Town','s.fk_pays'=>'Country','s.phone'=>'Phone','s.siren'=>'ProfId1','s.siret'=>'ProfId2','s.ape'=>'ProfId3','s.idprof4'=>'ProfId4','s.code_compta'=>'CustomerAccountancyCode','s.code_compta_fournisseur'=>'SupplierAccountancyCode','f.rowid'=>"InvoiceId",'f.facnumber'=>"InvoiceRef",'f.datec'=>"InvoiceDateCreation",'f.datef'=>"DateInvoice",'f.total'=>"TotalHT",'f.total_ttc'=>"TotalTTC",'f.tva'=>"TotalVAT",'f.paye'=>"InvoicePaid",'f.fk_statut'=>'InvoiceStatus','f.note'=>"InvoiceNote",'fd.rowid'=>'LineId','fd.description'=>"LineDescription",'fd.price'=>"LineUnitPrice",'fd.tva_tx'=>"LineVATRate",'fd.qty'=>"LineQty",'fd.total_ht'=>"LineTotalHT",'fd.total_tva'=>"LineTotalTVA",'fd.total_ttc'=>"LineTotalTTC",'fd.date_start'=>"DateStart",'fd.date_end'=>"DateEnd",'fd.fk_product'=>'ProductId','p.ref'=>'ProductRef');
		// $this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>'company','s.address'=>'company','s.zip'=>'company','s.town'=>'company','s.fk_pays'=>'company','s.phone'=>'company','s.siren'=>'company','s.siret'=>'company','s.ape'=>'company','s.idprof4'=>'company','s.code_compta'=>'company','s.code_compta_fournisseur'=>'company','f.rowid'=>"invoice",'f.facnumber'=>"invoice",'f.datec'=>"invoice",'f.datef'=>"invoice",'f.total'=>"invoice",'f.total_ttc'=>"invoice",'f.tva'=>"invoice",'f.paye'=>"invoice",'f.fk_statut'=>'invoice','f.note'=>"invoice",'fd.rowid'=>'invoice_line','fd.description'=>"invoice_line",'fd.price'=>"invoice_line",'fd.total_ht'=>"invoice_line",'fd.total_tva'=>"invoice_line",'fd.total_ttc'=>"invoice_line",'fd.tva_tx'=>"invoice_line",'fd.qty'=>"invoice_line",'fd.date_start'=>"invoice_line",'fd.date_end'=>"invoice_line",'fd.fk_product'=>'product','p.ref'=>'product');
		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM ('.MAIN_DB_PREFIX.'facture as f, '.MAIN_DB_PREFIX.'facturedet as fd, '.MAIN_DB_PREFIX.'societe as s)';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product as p on (fd.fk_product = p.rowid)';
		// $this->export_sql_end[$r] .=' WHERE f.fk_soc = s.rowid AND f.rowid = fd.fk_facture';
		// $this->export_sql_order[$r] .=' ORDER BY s.nom';
		// $r++;
		$this->export_code[$r]=$this->rights_class.'_'.$r;
		$this->export_label[$r]='CONTACTPAPERCONTRACT';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_enabled[$r]='1';                               // Condition to show export in list (ie: '$user->id==3'). Set to 1 to always show when module is enabled.
		//$this->export_permission[$r]=array(array("contrat","contrat","export"));
		$this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>"CompanyName",'s.status'=>"Status",'s.code_client'=>"CustomerCode",'d.nom'=>'State','co.label'=>"Country",'co.code'=>"CountryCode",'t.rowid'=>"IdContact",'t.lastname'=>'LastName','t.firstname'=>'FirstName','t.address'=>'Address','t.zip'=>'Zip','t.town'=>'Town','t.fk_pays'=>'Country','t.phone'=>'Phone','c.rowid'=>"ContractId",'c.datec'=>"ContractDateCreation",'c.statut'=>'ContractStatus','cd.rowid'=>'LineId','cd.description'=>"LineDescription",'cd.date_ouverture'=>"DateStart",'cd.date_fin_validite'=>"DateEnd",'cd.fk_product'=>'ProductId','p.ref'=>'ProductRef','fk_categorie'=>'SITEID','ca.label'=>'SITE');
		$this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>"company",'s.status'=>"company",'s.code_client'=>"company",'d.nom'=>'contact','co.label'=>"contact",'co.code'=>"contact",'t.rowid'=>"contact",'t.lastname'=>'contact','t.firstname'=>'contact','t.address'=>'contact','t.zip'=>'contact','t.town'=>'contact','t.fk_pays'=>'contact','t.phone'=>'contact','c.rowid'=>"contract",'c.datec'=>"contract",'c.statut'=>'contract','cd.rowid'=>'contract_line','cd.description'=>"contract_line",'cd.date_ouverture'=>"contract_line",'cd.date_fin_validite'=>"contract_line",'cd.fk_product'=>'service','p.ref'=>'service','fk_categorie'=>'service','ca.label'=>'service');
		
		$this->export_TypeFields_array[$r]=array('s.rowid'=>"List:societe:nom",'s.nom'=>'Text','s.address'=>'Text','s.zip'=>'Text','s.town'=>'Text','co.code'=>'Text',
				's.phone'=>'Text','s.siren'=>'Text','s.siret'=>'Text','s.ape'=>'Text','s.idprof4'=>'Text','s.code_compta'=>'Text',
				's.tva_intra'=>'Text','tc.code'=>'Text',
				'c.ref'=>"Text",'c.datec'=>"Date",'c.date_contrat'=>"Date",'c.mise_en_service'=>"Date",
				'c.fin_validite'=>"Date",'c.date_cloture'=>"Date",'c.note_private'=>"Text",'c.note_public'=>"Text",
				'cd.label'=>"Text",'cd.description'=>"Text",'cd.price_ht'=>"Numeric",'cd.tva_tx'=>"Numeric",
				'cd.qty'=>"Numeric",'cd.total_ht'=>"Numeric",'cd.total_tva'=>"Numeric",'cd.total_ttc'=>"Numeric",
				'cd.date_ouverture'=>"Date",'cd.date_ouverture_prevue'=>"Date",'cd.date_fin_validite'=>"Date",'cd.date_cloture'=>"Date",
				'fk_categorie'=>'List:categorie:label','p.rowid'=>'List:Product:label','p.ref'=>'Text','p.label'=>'Text');
		
		$this->export_sql_start[$r]='SELECT  ';
		$this->export_sql_end[$r] .=' FROM	'.MAIN_DB_PREFIX.'c_type_contact tc,'.MAIN_DB_PREFIX.'element_contact ec ,'.MAIN_DB_PREFIX.'contrat AS c ';
		
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'socpeople as t  ON c.fk_soc = t.fk_soc';
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON t.fk_soc = s.rowid';
		
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'contratdet AS cd ON c.rowid = cd.fk_contrat 						';
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_departements as d ON t.fk_departement = d.rowid';
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_country as co ON t.fk_pays = co.rowid';
		
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product AS p ON p.rowid = cd.fk_product';
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'categorie_product as cp ON p.rowid = cp.fk_product';
		
		$this->export_sql_end[$r] .=" WHERE  ec.element_id= c.rowid AND    ec.fk_socpeople = t.rowid
		AND  ec.fk_c_type_contact=tc.rowid AND tc.element='contrat'
		AND tc.source = 'external'
		AND tc.code like'ABONPAPIER%'
		AND tc.active=1 ";
		$this->export_sql_order[$r] .=' ORDER BY t.fk_pays,d.nom,t.town,t.address';
		$r++;
		
		$this->export_code[$r]=$this->rights_class.'_'.$r;
		$this->export_label[$r]='CONTACTOFCONTRACT';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_enabled[$r]='1';                               // Condition to show export in list (ie: '$user->id==3'). Set to 1 to always show when module is enabled.
		//$this->export_permission[$r]=array(array("contrat","contrat","export"));
		$this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>"CompanyName",'s.status'=>"Status",'s.code_client'=>"CustomerCode",'d.nom'=>'State','co.label'=>"Country",'co.code'=>"CountryCode",'t.rowid'=>"IdContact",'t.lastname'=>'LastName','t.firstname'=>'FirstName','t.address'=>'Address','t.zip'=>'Zip','t.town'=>'Town','t.fk_pays'=>'Country','t.phone'=>'Phone','tc.code'=>'typeContact','c.rowid'=>"contractId",'c.ref'=>"contactRef",'c.datec'=>"contractDateCreation",'c.date_contrat'=>"DateContract",'c.mise_en_service'=>"DateMiseService",'c.statut'=>'ContractStatus','c.fin_validite'=>"EndValidity",'c.date_cloture'=>"Cloture",'c.note_private'=>"NotePrivate",'c.note_public'=>"NotePublic",
				'cd.rowid'=>'LineId','cd.label'=>"LineLabel",'cd.description'=>"LineDescription",'cd.date_ouverture'=>"DateStart",'cd.date_fin_validite'=>"DateEnd",'cd.fk_product'=>'ProductId','p.ref'=>'ProductRef','fk_categorie'=>'SITEID','ca.label'=>'SITE');
		$this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>"company",'s.status'=>"company",'s.code_client'=>"company",'d.nom'=>'contact','co.label'=>"contact",'co.code'=>"contact",'t.rowid'=>"contact",'t.lastname'=>'contact','t.firstname'=>'contact','t.address'=>'contact','t.zip'=>'contact','t.town'=>'contact','t.fk_pays'=>'contact','t.phone'=>'contact','tc.code'=>'contact','c.rowid'=>"contract",'c.ref'=>"contract",'c.datec'=>"contract",'c.date_contrat'=>"contract",'c.mise_en_service'=>"contract",'c.statut'=>'contract','c.fin_validite'=>"contract",'c.date_cloture'=>"contract",'c.note_private'=>"contract",'c.note_public'=>"contract"
				,'cd.rowid'=>'contract_line','cd.label'=>"contract_line",'cd.description'=>"contract_line",'cd.date_ouverture'=>"contract_line",'cd.date_fin_validite'=>"contract_line",'cd.fk_product'=>'service','p.ref'=>'service','fk_categorie'=>'SITE','ca.label'=>'SITE');
		
		$this->export_TypeFields_array[$r]=array('s.rowid'=>"List:societe:nom",'s.nom'=>'Text','s.address'=>'Text','s.zip'=>'Text','s.town'=>'Text','co.code'=>'Text',
				's.phone'=>'Text','s.siren'=>'Text','s.siret'=>'Text','s.ape'=>'Text','s.idprof4'=>'Text','s.code_compta'=>'Text',
				's.tva_intra'=>'Text','tc.code'=>'Text',
				'c.ref'=>"Text",'c.datec'=>"Date",'c.date_contrat'=>"Date",'c.mise_en_service'=>"Date",
				'c.fin_validite'=>"Date",'c.date_cloture'=>"Date",'c.note_private'=>"Text",'c.note_public'=>"Text",
				'cd.label'=>"Text",'cd.description'=>"Text",'cd.price_ht'=>"Numeric",'cd.tva_tx'=>"Numeric",
				'cd.qty'=>"Numeric",'cd.total_ht'=>"Numeric",'cd.total_tva'=>"Numeric",'cd.total_ttc'=>"Numeric",
				'cd.date_ouverture'=>"Date",'cd.date_ouverture_prevue'=>"Date",'cd.date_fin_validite'=>"Date",'cd.date_cloture'=>"Date",
				'fk_categorie'=>'List:categorie:label','p.rowid'=>'List:Product:label','p.ref'=>'Text','p.label'=>'Text');
		
		$this->export_sql_start[$r]='SELECT  ';
		$this->export_sql_end[$r] .=' FROM	'.MAIN_DB_PREFIX.'c_type_contact tc,'.MAIN_DB_PREFIX.'element_contact ec ,'.MAIN_DB_PREFIX.'contrat AS c ';
		
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'socpeople as t  ON c.fk_soc = t.fk_soc';
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON t.fk_soc = s.rowid';
		
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'contratdet AS cd ON c.rowid = cd.fk_contrat 						';
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_departements as d ON t.fk_departement = d.rowid';
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_country as co ON t.fk_pays = co.rowid';
		
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product AS p ON p.rowid = cd.fk_product';
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'categorie_product as cp ON (p.rowid = cp.fk_product )';
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'categorie as ca ON (ca.rowid = cp.fk_categorie )';
		
		$this->export_sql_end[$r] .=" WHERE  ec.element_id= c.rowid AND    ec.fk_socpeople = t.rowid
		AND  ec.fk_c_type_contact=tc.rowid AND tc.element='contrat'
		AND tc.source = 'external'
		AND tc.code like'ABON%'
		AND tc.active=1 ";
		$this->export_sql_order[$r] .=' ORDER BY t.fk_pays,d.nom,t.town,t.address';
		$r++;
		
// 		$this->export_code[$r]=$this->rights_class.'_'.$r;
// 		$this->export_label[$r]='CONTACTOFROUTERSIMPLE';	// Translation key (used only if key ExportDataset_xxx_z not found)
// 		$this->export_enabled[$r]='1';                               // Condition to show export in list (ie: '$user->id==3'). Set to 1 to always show when module is enabled.
// 		//$this->export_permission[$r]=array(array("contrat","contrat","export"));
// 		$this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>"CompanyName",'s.status'=>"Status",'s.code_client'=>"CustomerCode",'d.nom'=>'State','co.label'=>"Country",'co.code'=>"CountryCode",'t.rowid'=>"IdContact",'t.lastname'=>'LastName','t.firstname'=>'FirstName','t.address'=>'Address','t.zip'=>'Zip','t.town'=>'Town','t.fk_pays'=>'Country','t.phone'=>'Phone','tc.code'=>'typeContact','c.rowid'=>"contractId",'c.ref'=>"contactRef",'c.datec'=>"contractDateCreation",'c.date_contrat'=>"DateContract",'c.mise_en_service'=>"DateMiseService",'c.statut'=>'ContractStatus','c.fin_validite'=>"EndValidity",'c.date_cloture'=>"Cloture",'c.note_private'=>"NotePrivate",'c.note_public'=>"NotePublic",
// 				'cd.rowid'=>'LineId','cd.label'=>"LineLabel",'cd.description'=>"LineDescription",'cd.date_ouverture'=>"DateStart",'cd.date_fin_validite'=>"DateEnd",'cd.fk_product'=>'ProductId','p.ref'=>'ProductRef');
// 		$this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>"company",'s.status'=>"company",'s.code_client'=>"company",'d.nom'=>'contact','co.label'=>"contact",'co.code'=>"contact",'t.rowid'=>"contact",'t.lastname'=>'contact','t.firstname'=>'contact','t.address'=>'contact','t.zip'=>'contact','t.town'=>'contact','t.fk_pays'=>'contact','t.phone'=>'contact','tc.code'=>'contact','c.rowid'=>"contract",'c.ref'=>"contract",'c.datec'=>"contract",'c.date_contrat'=>"contract",'c.mise_en_service'=>"contract",'c.statut'=>'contract','c.fin_validite'=>"contract",'c.date_cloture'=>"contract",'c.note_private'=>"contract",'c.note_public'=>"contract"
// 				,'cd.rowid'=>'contract_line','cd.label'=>"contract_line",'cd.description'=>"contract_line",'cd.date_ouverture'=>"contract_line",'cd.date_fin_validite'=>"contract_line",'cd.fk_product'=>'service','p.ref'=>'service');
		
// 		$this->export_TypeFields_array[$r]=array('s.rowid'=>"List:societe:nom",'s.nom'=>'Text','s.address'=>'Text','s.zip'=>'Text','s.town'=>'Text','co.code'=>'Text',
// 				's.phone'=>'Text','s.siren'=>'Text','s.siret'=>'Text','s.ape'=>'Text','s.idprof4'=>'Text','s.code_compta'=>'Text',
// 				's.tva_intra'=>'Text','tc.code'=>'Text',
// 				'c.ref'=>"Text",'c.datec'=>"Date",'c.date_contrat'=>"Date",'c.mise_en_service'=>"Date",
// 				'c.fin_validite'=>"Date",'c.date_cloture'=>"Date",'c.note_private'=>"Text",'c.note_public'=>"Text",
// 				'cd.label'=>"Text",'cd.description'=>"Text",'cd.price_ht'=>"Numeric",'cd.tva_tx'=>"Numeric",
// 				'cd.qty'=>"Numeric",'cd.total_ht'=>"Numeric",'cd.total_tva'=>"Numeric",'cd.total_ttc'=>"Numeric",
// 				'cd.date_ouverture'=>"Date",'cd.date_ouverture_prevue'=>"Date",'cd.date_fin_validite'=>"Date",'cd.date_cloture'=>"Date",
// 				'p.rowid'=>'List:Product:label','p.ref'=>'Text','p.label'=>'Text');
		
// 		$this->export_sql_start[$r]='SELECT  ';
// 		$this->export_sql_end[$r] .=' FROM	'.MAIN_DB_PREFIX.'c_type_contact tc,'.MAIN_DB_PREFIX.'element_contact ec ,'.MAIN_DB_PREFIX.'contrat AS c ';
		
// 		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'socpeople as t ON c.fk_soc = t.fk_soc ';
// 		$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON t.fk_soc = s.rowid LEFT JOIN '.MAIN_DB_PREFIX.'contratdet AS cd ON c.rowid = cd.fk_contrat ';
		
// 		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_departements as d ON t.fk_departement = d.rowid ';
// 		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_country as co ON t.fk_pays = co.rowid ';
		
// 		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product AS p ON p.rowid = cd.fk_product ';
// 		$this->export_sql_end[$r] .=" WHERE  ec.element_id= c.rowid AND    ec.fk_socpeople = t.rowid
// 		AND  ec.fk_c_type_contact=tc.rowid AND tc.element='contrat'
// 		AND tc.source = 'external'
// 		AND tc.code like'ABON%'
// 		AND tc.active=1 AND co.code ='BE' ";
// 		$this->export_sql_end[$r] .=" AND s.rowid IN ( SELECT  s.rowid FROM	".MAIN_DB_PREFIX."c_type_contact tc,
// 		                            ".MAIN_DB_PREFIX."element_contact ec ,".MAIN_DB_PREFIX."contrat AS c LEFT JOIN 
// 		                            ".MAIN_DB_PREFIX."socpeople as t ON c.fk_soc = t.fk_soc LEFT JOIN 
// 		                            ".MAIN_DB_PREFIX."societe as s ON t.fk_soc = s.rowid LEFT JOIN
// 		                            ".MAIN_DB_PREFIX."contratdet AS cd ON c.rowid = cd.fk_contrat LEFT JOIN
// 		                            ".MAIN_DB_PREFIX."c_departements as d ON t.fk_departement = d.rowid LEFT JOIN
// 		                            ".MAIN_DB_PREFIX."c_country as co ON t.fk_pays = co.rowid LEFT JOIN 
// 		                            ".MAIN_DB_PREFIX."product AS p ON p.rowid = cd.fk_product WHERE ec.element_id= c.rowid
// 		                             AND ec.fk_socpeople = t.rowid AND ec.fk_c_type_contact=tc.rowid AND tc.element='contrat'
// 		                             AND tc.source = 'external' AND tc.code like'ABONPAPIER%' AND tc.active=1
// 		                             group by s.nom ,s.rowid,c.rowid HAVING count( t.rowid) > 1)  ";
// 		$this->export_sql_order[$r] .=' ORDER BY t.fk_pays,d.nom,t.town,t.address';

		$this->export_code[$r]=$this->rights_class.'_'.$r;
				$this->export_label[$r]='CONTACTOFROUTERMULTI';	// Translation key (used only if key ExportDataset_xxx_z not found)
				$this->export_enabled[$r]='1';                               // Condition to show export in list (ie: '$user->id==3'). Set to 1 to always show when module is enabled.
				//$this->export_permission[$r]=array(array("contrat","contrat","export"));
				$this->export_fields_array[$r]=array('s.nom'=>"CompanyName",'CONCAT(t.firstname,t.lastname) as firstname'=>'FirstName',
						's.address'=>'Address','s.rowid'=>"IdCompany",'t.zip'=>'Zip','t.town'=>'Town','count( cd.rowid) as nombre'=>'Nombre',
						's.code_client'=>"CustomerCode",'d.nom'=>'State',
						'co.label'=>"Country",'co.code'=>"CountryCode",
						's.address'=>'Address','t.zip'=>'Zip',
						't.town'=>'Town','t.phone'=>'Phone'	);
				$this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>"company",'s.status'=>"company",'s.code_client'=>"company",'d.nom'=>'contact','co.label'=>"contact",'co.code'=>"contact",'t.rowid'=>"contact",'t.lastname'=>'contact','CONCAT(t.firstname,t.lastname) as firstname'=>'contact','s.address'=>'contact','t.zip'=>'contact','t.town'=>'contact','t.fk_pays'=>'contact','t.phone'=>'contact','tc.code'=>'contact','c.rowid'=>"contract",'c.ref'=>"contract",'c.datec'=>"contract",'c.date_contrat'=>"contract",'c.mise_en_service'=>"contract",'c.statut'=>'contract','c.fin_validite'=>"contract",'c.date_cloture'=>"contract",'c.note_private'=>"contract",'c.note_public'=>"contract"
								,'cd.rowid'=>'contract_line','cd.label'=>"contract_line",'count( cd.rowid) as nombre'=>'contract_line','cd.description'=>"contract_line",'cd.date_ouverture'=>"contract_line",'cd.date_fin_validite'=>"contract_line",'cd.fk_product'=>'service','p.ref'=>'service');
		
				$this->export_TypeFields_array[$r]=array('s.rowid'=>"List:societe:nom",'s.nom'=>'Text','s.address'=>'Text','s.zip'=>'Text','s.town'=>'Text','co.code'=>'Text',
								's.phone'=>'Text','s.siren'=>'Text','s.siret'=>'Text','s.ape'=>'Text','s.idprof4'=>'Text','s.code_compta'=>'Text',
								's.tva_intra'=>'Text','tc.code'=>'Text',
								'c.ref'=>"Text",'c.datec'=>"Date",'c.date_contrat'=>"Date",'c.mise_en_service'=>"Date",
								'c.fin_validite'=>"Date",'c.date_cloture'=>"Date",'c.note_private'=>"Text",'c.note_public'=>"Text",
								'cd.label'=>"Text",'cd.description'=>"Text",'cd.price_ht'=>"Numeric",'cd.tva_tx'=>"Numeric",
								'cd.qty'=>"Numeric",'cd.total_ht'=>"Numeric",'cd.total_tva'=>"Numeric",'cd.total_ttc'=>"Numeric",
								'cd.date_ouverture'=>"Date",'cd.date_ouverture_prevue'=>"Date",'cd.date_fin_validite'=>"Date",'cd.date_cloture'=>"Date",
								'p.rowid'=>'List:Product:label','p.ref'=>'Text','p.label'=>'Text');
				 
				$this->export_sql_start[$r]='SELECT  ';
				$this->export_sql_end[$r] .=' FROM	'.MAIN_DB_PREFIX.'c_type_contact tc,'.MAIN_DB_PREFIX.'element_contact ec ,'.MAIN_DB_PREFIX.'contrat AS c ';
		
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'socpeople as t ON c.fk_soc = t.fk_soc ';
				$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON t.fk_soc = s.rowid ';
				$this->export_sql_end[$r] .='  LEFT JOIN '.MAIN_DB_PREFIX.'contratdet AS cd ON c.rowid = cd.fk_contrat ';
		
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_departements as d ON t.fk_departement = d.rowid ';
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_country as co ON t.fk_pays = co.rowid ';
		
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product AS p ON p.rowid = cd.fk_product ';
				$this->export_sql_end[$r] .="  WHERE ec.element_id= c.rowid AND ec.fk_socpeople = t.rowid
											   AND ec.fk_c_type_contact=tc.rowid AND tc.element='contrat' 
											   AND tc.source = 'external' AND tc.code like'ABONPAPIER%' 
											   AND tc.active=1 AND co.code ='BE'   ";
				$this->export_sql_end[$r] .=" GROUP BY t.fk_pays,s.nom,c.rowid,t.town,s.address HAVING count( cd.rowid) > 1 ";
				$this->export_sql_order[$r] .=' ORDER BY t.fk_pays,s.nom,t.town,s.address';
				
				$r++;
				
				$this->export_code[$r]=$this->rights_class.'_'.$r;
				$this->export_label[$r]='CONTACTOFROUTERSIMPLE';	// Translation key (used only if key ExportDataset_xxx_z not found)
				$this->export_enabled[$r]='1';                               // Condition to show export in list (ie: '$user->id==3'). Set to 1 to always show when module is enabled.
				//$this->export_permission[$r]=array(array("contrat","contrat","export"));
				$this->export_fields_array[$r]=array('s.nom'=>"CompanyName",'t.firstname'=>'FirstName','t.lastname'=>'LastName',
						's.address'=>'Address','s.rowid'=>"IdCompany",'t.zip'=>'Zip','t.town'=>'Town',' nb_link as nombre'=>'Nombre',
						's.code_client'=>"CustomerCode",'d.nom'=>'State',
						'co.label'=>"Country",'co.code'=>"CountryCode",
						's.address'=>'Address','t.zip'=>'Zip',
						't.town'=>'Town','t.phone'=>'Phone','fk_categorie'=>'SITEID','ca.label'=>'SITE'	);
				$this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>"company",'s.status'=>"company",'s.code_client'=>"company",'d.nom'=>'contact','co.label'=>"contact",'co.code'=>"contact",'t.rowid'=>"contact",'t.lastname'=>'contact','t.firstname'=>'contact','s.address'=>'contact','t.zip'=>'contact','t.town'=>'contact','t.fk_pays'=>'contact','t.phone'=>'contact','tc.code'=>'contact','c.rowid'=>"contract",'c.ref'=>"contract",'c.datec'=>"contract",'c.date_contrat'=>"contract",'c.mise_en_service'=>"contract",'c.statut'=>'contract','c.fin_validite'=>"contract",'c.date_cloture'=>"contract",'c.note_private'=>"contract",'c.note_public'=>"contract"
						,'cd.rowid'=>'contract_line','cd.label'=>"contract_line",'nb_link as nombre'=>'contract_line','cd.description'=>"contract_line",'cd.date_ouverture'=>"contract_line",'cd.date_fin_validite'=>"contract_line",'cd.fk_product'=>'service','p.ref'=>'service');
				
				$this->export_TypeFields_array[$r]=array('s.rowid'=>"List:societe:nom",'s.nom'=>'Text','s.address'=>'Text','s.zip'=>'Text','s.town'=>'Text','co.code'=>'Text',
						's.phone'=>'Text','s.siren'=>'Text','s.siret'=>'Text','s.ape'=>'Text','s.idprof4'=>'Text','s.code_compta'=>'Text',
						's.tva_intra'=>'Text','tc.code'=>'Text',
						'c.ref'=>"Text",'c.datec'=>"Date",'c.date_contrat'=>"Date",'c.mise_en_service'=>"Date",
						'c.fin_validite'=>"Date",'c.date_cloture'=>"Date",'c.note_private'=>"Text",'c.note_public'=>"Text",
						'cd.label'=>"Text",'cd.description'=>"Text",'cd.price_ht'=>"Numeric",'cd.tva_tx'=>"Numeric",
						'cd.qty'=>"Numeric",'cd.total_ht'=>"Numeric",'cd.total_tva'=>"Numeric",'cd.total_ttc'=>"Numeric",
						'cd.date_ouverture'=>"Date",'cd.date_ouverture_prevue'=>"Date",'cd.date_fin_validite'=>"Date",'cd.date_cloture'=>"Date",
						'fk_categorie'=>'List:categorie:label','p.rowid'=>'List:Product:label','p.ref'=>'Text','p.label'=>'Text');
					
				$this->export_sql_start[$r]='SELECT  ';
				$this->export_sql_end[$r] .=' FROM	'.MAIN_DB_PREFIX.'c_type_contact tc,'.MAIN_DB_PREFIX.'element_contact ec ,'.MAIN_DB_PREFIX.'contrat AS c ';
				
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'socpeople as t ON c.fk_soc = t.fk_soc ';
				$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON t.fk_soc = s.rowid ';
				$this->export_sql_end[$r] .='  LEFT JOIN '.MAIN_DB_PREFIX.'contratdet AS cd ON c.rowid = cd.fk_contrat ';
				
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_departements as d ON t.fk_departement = d.rowid ';
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_country as co ON t.fk_pays = co.rowid ';
				
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product AS p ON p.rowid = cd.fk_product ';
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'categorie_product as cp ON p.rowid = cp.fk_product';
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'categorie as ca ON (ca.rowid = cp.fk_categorie )';
				
				$this->export_sql_end[$r] .="  WHERE ec.element_id= c.rowid AND ec.fk_socpeople = t.rowid
											   AND ec.fk_c_type_contact=tc.rowid AND tc.element='contrat' 
											   AND tc.source = 'external' AND tc.code like'ABONPAPIER%' 
											   AND tc.active=1 AND co.code ='BE'   ";
				$this->export_sql_end[$r] .=" GROUP BY t.fk_pays,s.nom,c.rowid,t.town,s.address HAVING count( cd.rowid) = 1 ";
				$this->export_sql_order[$r] .=' ORDER BY t.fk_pays,s.nom,t.town,s.address';
				
				$r++;
				
				$this->export_code[$r]=$this->rights_class.'_'.$r;
				$this->export_label[$r]='CONTACTOFROUTERETRANGER';	// Translation key (used only if key ExportDataset_xxx_z not found)
				$this->export_enabled[$r]='1';                               // Condition to show export in list (ie: '$user->id==3'). Set to 1 to always show when module is enabled.
				//$this->export_permission[$r]=array(array("contrat","contrat","export"));
				$this->export_fields_array[$r]=array('s.nom'=>"CompanyName",'t.firstname'=>'FirstName','t.lastname'=>'LastName',
						's.address'=>'Address','s.rowid'=>"IdCompany",'t.zip'=>'Zip','t.town'=>'Town',
						's.code_client'=>"CustomerCode",'d.nom'=>'State',
						'co.label'=>"Country",'co.code'=>"CountryCode",
						's.address'=>'Address','t.zip'=>'Zip',
						't.town'=>'Town','t.phone'=>'Phone','fk_categorie'=>'SITEID','ca.label'=>'SITE'	);
				$this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>"company",'s.status'=>"company",'s.code_client'=>"company",'d.nom'=>'contact','co.label'=>"contact",'co.code'=>"contact",'t.rowid'=>"contact",'t.lastname'=>'contact','t.firstname'=>'contact','s.address'=>'contact','t.zip'=>'contact','t.town'=>'contact','t.fk_pays'=>'contact','t.phone'=>'contact','tc.code'=>'contact','c.rowid'=>"contract",'c.ref'=>"contract",'c.datec'=>"contract",'c.date_contrat'=>"contract",'c.mise_en_service'=>"contract",'c.statut'=>'contract','c.fin_validite'=>"contract",'c.date_cloture'=>"contract",'c.note_private'=>"contract",'c.note_public'=>"contract"
						,'cd.rowid'=>'contract_line','cd.label'=>"contract_line",' nb_link as nombre'=>'contract_line','cd.description'=>"contract_line",'cd.date_ouverture'=>"contract_line",'cd.date_fin_validite'=>"contract_line",'cd.fk_product'=>'service','p.ref'=>'service');
				
				$this->export_TypeFields_array[$r]=array('s.rowid'=>"List:societe:nom",'s.nom'=>'Text','s.address'=>'Text','s.zip'=>'Text','s.town'=>'Text','co.code'=>'Text',
						's.phone'=>'Text','s.siren'=>'Text','s.siret'=>'Text','s.ape'=>'Text','s.idprof4'=>'Text','s.code_compta'=>'Text',
						's.tva_intra'=>'Text','tc.code'=>'Text',
						'c.ref'=>"Text",'c.datec'=>"Date",'c.date_contrat'=>"Date",'c.mise_en_service'=>"Date",
						'c.fin_validite'=>"Date",'c.date_cloture'=>"Date",'c.note_private'=>"Text",'c.note_public'=>"Text",
						'cd.label'=>"Text",'cd.description'=>"Text",'cd.price_ht'=>"Numeric",'cd.tva_tx'=>"Numeric",
						'cd.qty'=>"Numeric",'cd.total_ht'=>"Numeric",'cd.total_tva'=>"Numeric",'cd.total_ttc'=>"Numeric",
						'cd.date_ouverture'=>"Date",'cd.date_ouverture_prevue'=>"Date",'cd.date_fin_validite'=>"Date",'cd.date_cloture'=>"Date",
						'fk_categorie'=>'List:categorie:label','p.rowid'=>'List:Product:label','p.ref'=>'Text','p.label'=>'Text');
					
				$this->export_sql_start[$r]='SELECT  ';
				$this->export_sql_end[$r] .=' FROM	'.MAIN_DB_PREFIX.'c_type_contact tc,'.MAIN_DB_PREFIX.'element_contact ec ,'.MAIN_DB_PREFIX.'contrat AS c ';
				
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'socpeople as t ON c.fk_soc = t.fk_soc ';
				$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON t.fk_soc = s.rowid ';
				$this->export_sql_end[$r] .='  LEFT JOIN '.MAIN_DB_PREFIX.'contratdet AS cd ON c.rowid = cd.fk_contrat ';
				
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_departements as d ON t.fk_departement = d.rowid ';
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_country as co ON t.fk_pays = co.rowid ';
				
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product AS p ON p.rowid = cd.fk_product ';
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'categorie_product as cp ON p.rowid = cp.fk_product';
				$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'categorie as ca ON (ca.rowid = cp.fk_categorie )';
				
				$this->export_sql_end[$r] .="  WHERE ec.element_id= c.rowid AND ec.fk_socpeople = t.rowid
											   AND ec.fk_c_type_contact=tc.rowid AND tc.element='contrat' 
											   AND tc.source = 'external' AND tc.code like'ABONPAPIER%' 
											   AND tc.active=1 AND co.code <>'BE'   ";
				$this->export_sql_end[$r] .=" GROUP BY t.fk_pays,s.nom,c.rowid,t.town,s.address  ";
				$this->export_sql_order[$r] .=' ORDER BY t.fk_pays,s.nom,t.town,s.address';
				
		//var_dump($conf->global->MAIN_MODULES_FOR_EXTERNAL);exit;
		// Imports
		//--------
		$r=0;
		
		$now=dol_now();
		require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
		
		$r++; 
		$this->import_code[$r]=$this->rights_class.'_'.$r;
		$this->import_label[$r]="Virement Abonnement"; // Translation key
		$this->import_icon[$r]=$this->picto;
		$this->import_entities_array[$r]=array();		// We define here only fields that use another icon that the one defined into import_icon
		$this->import_tables_array[$r]=array('a'=>MAIN_DB_PREFIX.'paiement','cmd'=>MAIN_DB_PREFIX.'commande','soc'=>MAIN_DB_PREFIX.'societe');
		$this->import_tables_creator_array[$r]=array('a'=>'fk_user_author');    // Fields to store import user id
		//		$this->import_fields_array[$r]=array('a.civility'=>"UserTitle",'a.lastname'=>"Lastname*",'a.firstname'=>"Firstname",'a.login'=>"Login*","a.pass"=>"Password","a.fk_adherent_type"=>"MemberType*",'a.morphy'=>'Nature*','a.societe'=>'Company','a.address'=>"Address",'a.zip'=>"Zip",'a.town'=>"Town",'a.state_id'=>'StateId','a.country'=>"CountryId",'a.phone'=>"PhonePro",'a.phone_perso'=>"PhonePerso",'a.phone_mobile'=>"PhoneMobile",'a.email'=>"Email",'a.birth'=>"Birthday",'a.statut'=>"Status*",'a.photo'=>"Photo",'a.note'=>"Note",'a.datec'=>'DateCreation','a.datefin'=>'DateEndSubscription');
		
		$this->import_fields_array[$r]=array('cmd.ref'=>"Ref*",'cmd.socid'=>"Ref_client*",'soc.lastname'=>"Lastname",'soc.firstname'=>"Firstname",'a.amont'=>"AmountTTC*",'a.banque'=>'Bank*');
		
		
		//$this->import_fieldshidden_array[$r]=array('extra.fk_object'=>'lastrowid-'.MAIN_DB_PREFIX.'adherent');    // aliastable.field => ('user->id' or 'lastrowid-'.tableparent)
		//$this->import_regex_array[$r]=array('cmd.ref'=>'ref@'.MAIN_DB_PREFIX.'commande','a.datepaye'=>'^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$');
		
		$this->import_regex_array[$r]=array('cmd.ref'=>'ref@'.MAIN_DB_PREFIX.'commande');
		$this->import_examplevalues_array[$r]=array('cmd.ref'=>"C000-001",'cmd.socid'=>"0001",'soc.lastname'=>"Gland",'soc.firstname'=>"julia",'a.amont'=>"1000",'a.banque'=>'SGB');
		
		
	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		$sql = array();

		$result=$this->_load_tables('/abonnement/sql/');

		return $this->_init($sql, $options);
	}

	/**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Dolibarr database.
	 *		Data directories are not deleted
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function remove($options='')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}

}

