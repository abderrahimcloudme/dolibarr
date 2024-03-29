<?php
/* Copyright (C) 2006-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2012      JF FERRY             <jfefe@aternatik.fr>
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
*
* Path to WSDL is: http://localhost/dolibarr/webservices/server_productorservice.php?wsdl
*/

/**
 *       \file       htdocs/webservices/server_productorservice.php
 *       \brief      File that is entry point to call Dolibarr WebServices
*/

// This is to make Dolibarr working with Plesk
set_include_path($_SERVER['DOCUMENT_ROOT'].'/htdocs');

require_once '../master.inc.php';
require_once NUSOAP_PATH.'/nusoap.php';        // Include SOAP
require_once DOL_DOCUMENT_ROOT.'/core/lib/ws.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once(DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/abonnement/class/abonnement.class.php';



dol_syslog("Call Dolibarr webservices interfaces");

$langs->load("main");

// Enable and test if module web services is enabled
if (empty($conf->global->MAIN_MODULE_WEBSERVICES))
{
	$langs->load("admin");
	dol_syslog("Call Dolibarr webservices interfaces with module webservices disabled");
	print $langs->trans("WarningModuleNotActive",'WebServices').'.<br><br>';
	print $langs->trans("ToActivateModule");
	exit;
}

// Create the soap Object
$server = new nusoap_server();
$server->soap_defencoding='UTF-8';
$server->xml_encoding='UTF-8';
$server->decode_utf8=true;
$ns='http://www.dolibarr.org/ns/';
$server->configureWSDL('WebServicesDolibarrAbonnement',$ns);
$server->wsdl->schemaTargetNamespace=$ns;


// Define WSDL Authentication object
$server->wsdl->addComplexType(
		'authentication',
		'complexType',
		'struct',
		'all',
		'',
		array(
				'dolibarrkey' => array('name'=>'dolibarrkey','type'=>'xsd:string'),
				'sourceapplication' => array('name'=>'sourceapplication','type'=>'xsd:string'),
				'login' => array('name'=>'login','type'=>'xsd:string'),
				'password' => array('name'=>'password','type'=>'xsd:string'),
				'entity' => array('name'=>'entity','type'=>'xsd:string'),
		)
);
// Define WSDL Return object
$server->wsdl->addComplexType(
		'result',
		'complexType',
		'struct',
		'all',
		'',
		array(
				'result_code' => array('name'=>'result_code','type'=>'xsd:string'),
				'result_label' => array('name'=>'result_label','type'=>'xsd:string'),
		)
);

$productorservice_fields = array(
		'id' => array('name'=>'id','type'=>'xsd:string'),
		'ref' => array('name'=>'ref','type'=>'xsd:string'),
		'ref_ext' => array('name'=>'ref_ext','type'=>'xsd:string'),
		'type' => array('name'=>'type','type'=>'xsd:string'),
		'label' => array('name'=>'label','type'=>'xsd:string'),
		'description' => array('name'=>'description','type'=>'xsd:string'),
		'date_creation' => array('name'=>'date_creation','type'=>'xsd:dateTime'),
		'date_modification' => array('name'=>'date_modification','type'=>'xsd:dateTime'),
		'note' => array('name'=>'note','type'=>'xsd:string'),
		'status_tobuy' => array('name'=>'status_tobuy','type'=>'xsd:string'),
		'status_tosell' => array('name'=>'status_tosell','type'=>'xsd:string'),
		'barcode' => array('name'=>'barcode','type'=>'xsd:string'),
		'barcode_type' => array('name'=>'barcode_type','type'=>'xsd:string'),
		'country_id' => array('name'=>'country_id','type'=>'xsd:string'),
		'country_code' => array('name'=>'country_code','type'=>'xsd:string'),
		'customcode' => array('name'=>'customcode','type'=>'xsd:string'),

		'price_net' => array('name'=>'price_net','type'=>'xsd:string'),
		'price' => array('name'=>'price','type'=>'xsd:string'),
		'price_min_net' => array('name'=>'price_min_net','type'=>'xsd:string'),
		'price_min' => array('name'=>'price_min','type'=>'xsd:string'),

		'price_base_type' => array('name'=>'price_base_type','type'=>'xsd:string'),

		'vat_rate' => array('name'=>'vat_rate','type'=>'xsd:string'),
		'vat_npr' => array('name'=>'vat_npr','type'=>'xsd:string'),
		'localtax1_tx' => array('name'=>'localtax1_tx','type'=>'xsd:string'),
		'localtax2_tx' => array('name'=>'localtax2_tx','type'=>'xsd:string'),

		'stock_alert' => array('name'=>'stock_alert','type'=>'xsd:string'),
		'stock_real' => array('name'=>'stock_real','type'=>'xsd:string'),
		'stock_pmp' => array('name'=>'stock_pmp','type'=>'xsd:string'),
		'canvas' => array('name'=>'canvas','type'=>'xsd:string'),
		'import_key' => array('name'=>'import_key','type'=>'xsd:string'),

		'dir' => array('name'=>'dir','type'=>'xsd:string'),
		'images' => array('name'=>'images','type'=>'tns:ImagesArray')
);

//Retreive all extrafield for product
// fetch optionals attributes and labels
$extrafields=new ExtraFields($db);
$extralabels=$extrafields->fetch_name_optionals_label('product',true);
if (count($extrafields)>0) {
	$extrafield_array = array();
}
foreach($extrafields->attribute_label as $key=>$label)
{
	$type =$extrafields->attribute_type[$key];
	if ($type=='date' || $type=='datetime') {
		$type='xsd:dateTime';
	}
	else {$type='xsd:string';
	}

	$extrafield_array['options_'.$key]=array('name'=>'options_'.$key,'type'=>$type);
}

$productorservice_fields=array_merge($productorservice_fields,$extrafield_array);

// Define other specific objects
$server->wsdl->addComplexType(
		'product',
		'complexType',
		'struct',
		'all',
		'',
		$productorservice_fields
);


/*
 * Image of product
*/
$server->wsdl->addComplexType(
		'ImagesArray',
		'complexType',
		'array',
		'sequence',
		'',
		array(
				'image' => array(
						'name' => 'image',
						'type' => 'tns:image',
						'minOccurs' => '0',
						'maxOccurs' => 'unbounded'
				)
		)
);

/*
 * An image
*/
$server->wsdl->addComplexType(
		'image',
		'complexType',
		'struct',
		'all',
		'',
		array(
				'photo' => array('name'=>'photo','type'=>'xsd:string'),
				'photo_vignette' => array('name'=>'photo_vignette','type'=>'xsd:string'),
				'imgWidth' => array('name'=>'imgWidth','type'=>'xsd:string'),
				'imgHeight' => array('name'=>'imgHeight','type'=>'xsd:string')
		)
);


// Define other specific objects
$server->wsdl->addComplexType(
		'filterproduct',
		'complexType',
		'struct',
		'all',
		'',
		array(
				//'limit' => array('name'=>'limit','type'=>'xsd:string'),
				'type' => array('name'=>'type','type'=>'xsd:string'),
				'status_tobuy' => array('name'=>'status_tobuy','type'=>'xsd:string'),
				'status_tosell' => array('name'=>'status_tosell','type'=>'xsd:string'),
		)
);

$server->wsdl->addComplexType(
		'ProductsArray2',
		'complexType',
		'array',
		'sequence',
		'',
		array(
				'product' => array(
						'name' => 'product',
						'type' => 'tns:product',
						'minOccurs' => '0',
						'maxOccurs' => 'unbounded'
				)
		)
);

// Define other specific objects
$server->wsdl->addComplexType(
		'user',
		'complexType',
		'struct',
		'all',
		'',
		array(
				'element' => array('name'=>'element','type'=>'xsd:string'),
				'id' => array('name'=>'id','type'=>'xsd:string'),
				'lastname' => array('name'=>'lastname','type'=>'xsd:string'),
				'firstname' => array('name'=>'firstname','type'=>'xsd:string'),
				'note' => array('name'=>'note','type'=>'xsd:string'),
				'email' => array('name'=>'email','type'=>'xsd:string'),
				'signature' => array('name'=>'signature','type'=>'xsd:string'),
				'office_phone' => array('name'=>'office_phone','type'=>'xsd:string'),
				'office_fax' => array('name'=>'office_fax','type'=>'xsd:string'),
				'user_mobile' => array('name'=>'user_mobile','type'=>'xsd:string'),
				'admin' => array('name'=>'admin','type'=>'xsd:string'),
				'login' => array('name'=>'login','type'=>'xsd:string'),
				'entity' => array('name'=>'entity','type'=>'xsd:string'),
				'pass_indatabase' => array('name'=>'pass_indatabase','type'=>'xsd:string'),
				'pass_indatabase_crypted' => array('name'=>'pass_indatabase_crypted','type'=>'xsd:string'),
				'datec' => array('name'=>'datec','type'=>'xsd:dateTime'),
				'datem' => array('name'=>'datem','type'=>'xsd:dateTime'),
				'fk_thirdparty' => array('name'=>'fk_thirdparty','type'=>'xsd:string'),
				'fk_contact' => array('name'=>'fk_contact','type'=>'xsd:string'),
				'fk_member' => array('name'=>'fk_member','type'=>'xsd:string'),
				'datelastlogin' => array('name'=>'datelastlogin','type'=>'xsd:dateTime'),
				'datepreviouslogin' => array('name'=>'datepreviouslogin','type'=>'xsd:dateTime'),
				'statut' => array('name'=>'statut','type'=>'xsd:string'),
				'photo' => array('name'=>'photo','type'=>'xsd:string'),
				'lang' => array('name'=>'lang','type'=>'xsd:string'),
				'entrepots' => array('name'=>'entrepots','type'=>'xsd:string'),
				//'rights' => array('name'=>'rights','type'=>'xsd:string'),
				'canvas' => array('name'=>'canvas','type'=>'xsd:string')
		)
);

// 5 styles: RPC/encoded, RPC/literal, Document/encoded (not WS-I compliant), Document/literal, Document/literal wrapped
// Style merely dictates how to translate a WSDL binding to a SOAP message. Nothing more. You can use either style with any programming model.
// http://www.ibm.com/developerworks/webservices/library/ws-whichwsdl/
$styledoc='rpc';       // rpc/document (document is an extend into SOAP 1.0 to support unstructured messages)
$styleuse='encoded';   // encoded/literal/literal wrapped
// Better choice is document/literal wrapped but literal wrapped not supported by nusoap.


// Register WSDL
$server->register(
		'getProductOrService',
		// Entry values
		array('authentication'=>'tns:authentication','id'=>'xsd:string','ref'=>'xsd:string','ref_ext'=>'xsd:string','lang'=>'xsd:string'),
		// Exit values
		array('result'=>'tns:result','product'=>'tns:product'),
		$ns,
		$ns.'#getProductOrService',
		$styledoc,
		$styleuse,
		'WS to get product or service'
);

// Register WSDL
$server->register(
		'createProductOrService',
		// Entry values
		array('authentication'=>'tns:authentication','product'=>'tns:product'),
		// Exit values
		array('result'=>'tns:result','id'=>'xsd:string'),
		$ns,
		$ns.'#createProductOrService',
		$styledoc,
		$styleuse,
		'WS to create a product or service'
);

// Register WSDL
$server->register(
		'updateProductOrService',
		// Entry values
		array('authentication'=>'tns:authentication','product'=>'tns:product'),
		// Exit values
		array('result'=>'tns:result','id'=>'xsd:string'),
		$ns,
		$ns.'#updateProductOrService',
		$styledoc,
		$styleuse,
		'WS to update a product or service'
);

// Register WSDL
$server->register(
		'deleteProductOrService',
		// Entry values
		array('authentication'=>'tns:authentication','listofid'=>'xsd:string'),
		// Exit values
		array('result'=>'tns:result','nbdeleted'=>'xsd:int'),
		$ns,
		$ns.'#deleteProductOrService',
		$styledoc,
		$styleuse,
		'WS to delete a product or service'
);

// Register WSDL
$server->register(
		'getListOfProductsOrServices',
		// Entry values
		array('authentication'=>'tns:authentication','filterproduct'=>'tns:filterproduct'),
		// Exit values
		array('result'=>'tns:result','products'=>'tns:ProductsArray2'),
		$ns,
		$ns.'#getListOfProductsOrServices',
		$styledoc,
		$styleuse,
		'WS to get list of all products or services id and ref'
);
$server->register(
		'getListOfProductsOrServicesForCategory',
		// Entry values
		array('authentication'=>'tns:authentication','id'=>'xsd:string','filterproduct'=>'tns:filterproduct'),
		// Exit values
		array('result'=>'tns:result','products'=>'tns:ProductsArray2'),
		$ns,
		$ns.'#getListOfProductsOrServicesForCategory',
		$styledoc,
		$styleuse,
		'WS to get list of all products or services id and ref'
);

$server->register(
		'getListOfCountry',
		// Entry values
		array('authentication'=>'tns:authentication'),
		// Exit values
		array('result'=>'tns:result','products'=>'tns:ProductsArray2'),
		$ns,
		$ns.'#getListOfCountry',
		$styledoc,
		$styleuse,
		'WS to get list of all country'
);

// Register WSDL
$server->register(
		'getProductsForCategory',
		// Entry values
		array('authentication'=>'tns:authentication','id'=>'xsd:string','lang'=>'xsd:string'),
		// Exit values
		array('result'=>'tns:result','products'=>'tns:ProductsArray2'),
		$ns,
		$ns.'#getProductsForCategory',
		$styledoc,
		$styleuse,
		'WS to get list of all products or services for a category'
);

// Register WSDL
$server->register(
		'getUser',
		// Entry values
		array('authentication'=>'tns:authentication'),
		// Exit values
		array('result'=>'tns:result','user'=>'tns:user'),
		$ns,
		$ns.'#getUser',
		$styledoc,
		$styleuse,
		'WS to authentication user'
);


/**
 * Get produt or service
 *
 * @param	array		$authentication		Array of authentication information
 * @param	int			$id					Id of object
 * @param	string		$ref				Ref of object
 * @param	string		$ref_ext			Ref external of object
 * @param   string      $lang               Lang to force
 * @return	mixed
 */
function getProductOrService($authentication,$id='',$ref='',$ref_ext='',$lang='')
{
	global $db,$conf,$langs;

	dol_syslog("Function: getProductOrService login=".$authentication['login']." id=".$id." ref=".$ref." ref_ext=".$ref_ext);

	$langcode=($lang?$lang:(empty($conf->global->MAIN_LANG_DEFAULT)?'auto':$conf->global->MAIN_LANG_DEFAULT));
	$langs->setDefaultLang($langcode);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters
	if (! $error && (($id && $ref) || ($id && $ref_ext) || ($ref && $ref_ext)))
	{
		$error++;
		$errorcode='BAD_PARAMETERS'; $errorlabel="Parameter id, ref and ref_ext can't be both provided. You must choose one or other but not both.";
	}

	if (! $error)
	{

		$langcode=($lang?$lang:(empty($conf->global->MAIN_LANG_DEFAULT)?'auto':$conf->global->MAIN_LANG_DEFAULT));
		$langs->setDefaultLang($langcode);

		$fuser->getrights();

		if ($fuser->rights->produit->lire || $fuser->rights->service->lire)
		{
			$product=new Product($db);
			$result=$product->fetch($id,$ref,$ref_ext);

			if ($result > 0)
			{
				$product->load_stock();

				$dir = (!empty($conf->product->dir_output)?$conf->product->dir_output:$conf->service->dir_output);
				$pdir = get_exdir($product->id,2) . $product->id ."/photos/";
				$dir = $dir . '/'. $pdir;

				if (! empty($product->multilangs[$langs->defaultlang]["label"]))     		$product->label =  $product->multilangs[$langs->defaultlang]["label"];
				if (! empty($product->multilangs[$langs->defaultlang]["description"]))     	$product->description =  $product->multilangs[$langs->defaultlang]["description"];
				if (! empty($product->multilangs[$langs->defaultlang]["note"]))     		$product->note =  $product->multilangs[$langs->defaultlang]["note"];

				$productorservice_result_fields = array(
		    'id' => $product->id,
						'ref' => $product->ref,
						'ref_ext' => $product->ref_ext,
						'label' => $product->label,
						'description' => $product->description,
						'date_creation' => dol_print_date($product->date_creation,'dayhourrfc'),
						'date_modification' => dol_print_date($product->date_modification,'dayhourrfc'),
						'note' => $product->note,
						'status_tosell' => $product->status,
						'status_tobuy' => $product->status_buy,
		    'type' => $product->type,
		    'barcode' => $product->barcode,
		    'barcode_type' => $product->barcode_type,
		    'country_id' => $product->country_id>0?$product->country_id:'',
		    'country_code' => $product->country_code,
		    'custom_code' => $product->customcode,

						'price_net' => $product->price,
						'price' => $product->price_ttc,
						'price_min_net' => $product->price_min,
						'price_min' => $product->price_min_ttc,
						'price_base_type' => $product->price_base_type,
		    'vat_rate' => $product->tva_tx,
		    //! French VAT NPR
		    'vat_npr' => $product->tva_npr,
		    //! Spanish local taxes
		    'localtax1_tx' => $product->localtax1_tx,
		    'localtax2_tx' => $product->localtax2_tx,

		    'stock_real' => $product->stock_reel,
		    'stock_alert' => $product->seuil_stock_alerte,
		    'pmp' => $product->pmp,
		    'import_key' => $product->import_key,
		    'dir' => $pdir,
		    'images' => $product->liste_photos($dir,$nbmax=10)
				);

				//Retreive all extrafield for thirdsparty
				// fetch optionals attributes and labels
				$extrafields=new ExtraFields($db);
				$extralabels=$extrafields->fetch_name_optionals_label('product',true);
				//Get extrafield values
				$product->fetch_optionals($product->id,$extralabels);

				foreach($extrafields->attribute_label as $key=>$label)
				{
					$productorservice_result_fields=array_merge($productorservice_result_fields,array('options_'.$key => $product->array_options['options_'.$key]));
				}

				// Create
				$objectresp = array(
						'result'=>array('result_code'=>'OK', 'result_label'=>''),
						'product'=>$productorservice_result_fields
				);
			}
			else
			{
				$error++;
				$errorcode='NOT_FOUND'; $errorlabel='Object not found for id='.$id.' nor ref='.$ref.' nor ref_ext='.$ref_ext;
			}
		}
		else
		{
			$error++;
			$errorcode='PERMISSION_DENIED'; $errorlabel='User does not have permission for this request';
		}
	}

	if ($error)
	{
		$objectresp = array('result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel));
	}
	//var_dump($objectresp);exit;
	return $objectresp;
}


/**
 * Create an invoice
 *
 * @param	array		$authentication		Array of authentication information
 * @param	Product		$product			Product
 * @return	array							Array result
 */
function createProductOrService($authentication,$product)
{
	global $db,$conf,$langs;

	$now=dol_now();

	dol_syslog("Function: createProductOrService login=".$authentication['login']);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters
	if ($product['price_net'] > 0) $product['price_base_type']='HT';
	if ($product['price'] > 0)     $product['price_base_type']='TTC';

	if ($product['price_net'] > 0 && $product['price'] > 0)
	{
		$error++; $errorcode='KO'; $errorlabel="You must choose between price or price_net to provide price.";
	}

	if ($product['barcode'] && !$product['barcode_type'])
	{
		$errror++; $errorcode='KO' ; $errorlabel="You must set a barcode type when setting a barcode.";
	}



	if (! $error)
	{
		include_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

		$newobject=new Product($db);
		$newobject->ref=$product['ref'];
		$newobject->ref_ext=$product['ref_ext'];
		$newobject->type=$product['type'];
		$newobject->libelle=$product['label'];    // TODO deprecated
		$newobject->label=$product['label'];
		$newobject->description=$product['description'];
		$newobject->note=$product['note'];
		$newobject->status=$product['status_tosell'];
		$newobject->status_buy=$product['status_tobuy'];
		$newobject->price=$product['price_net'];
		$newobject->price_ttc=$product['price'];
		$newobject->tva_tx=$product['vat_rate'];
		$newobject->price_base_type=$product['price_base_type'];
		$newobject->date_creation=$now;

		if ($product['barcode'])
		{
			$newobject->barcode = $product['barcode'];
			$newobject->barcode_type = $product['barcode_type'];
		}

		$newobject->stock_reel=$product['stock_real'];
		$newobject->pmp=$product['pmp'];
		$newobject->seuil_stock_alert=$product['stock_alert'];

		$newobject->country_id=$product['country_id'];
		if ($product['country_code']) $newobject->country_id=getCountry($product['country_code'],3);
		$newobject->customcode=$product['customcode'];

		$newobject->canvas=$product['canvas'];
		/*foreach($product['lines'] as $line)
		 {
		$newline=new FactureLigne($db);
		$newline->type=$line['type'];
		$newline->desc=$line['desc'];
		$newline->fk_product=$line['fk_product'];
		$newline->total_ht=$line['total_net'];
		$newline->total_vat=$line['total_vat'];
		$newline->total_ttc=$line['total'];
		$newline->vat=$line['vat_rate'];
		$newline->qty=$line['qty'];
		$newline->fk_product=$line['product_id'];
		}*/
		//var_dump($product['ref_ext']);
		//var_dump($product['lines'][0]['type']);

		$extrafields=new ExtraFields($db);
		$extralabels=$extrafields->fetch_name_optionals_label('product',true);
		foreach($extrafields->attribute_label as $key=>$label)
		{
			$key='options_'.$key;
			$newobject->array_options[$key]=$product[$key];
		}

		$db->begin();

		$result=$newobject->create($fuser,0);
		if ($result <= 0)
		{
			$error++;
		}

		if (! $error)
		{
			$db->commit();
			$objectresp=array('result'=>array('result_code'=>'OK', 'result_label'=>''),'id'=>$newobject->id,'ref'=>$newobject->ref);
		}
		else
		{
			$db->rollback();
			$error++;
			$errorcode='KO';
			$errorlabel=$newobject->error;
		}

	}

	if ($error)
	{
		$objectresp = array('result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel));
	}

	return $objectresp;
}


/**
 * Update a product or service
 *
 * @param	array		$authentication		Array of authentication information
 * @param	Product		$product			Product
 * @return	array							Array result
 */
function updateProductOrService($authentication,$product)
{
	global $db,$conf,$langs;

	$now=dol_now();

	dol_syslog("Function: updateProductOrService login=".$authentication['login']);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters
	if ($product['price_net'] > 0) $product['price_base_type']='HT';
	if ($product['price'] > 0)     $product['price_base_type']='TTC';

	if ($product['price_net'] > 0 && $product['price'] > 0)
	{
		$error++; $errorcode='KO'; $errorlabel="You must choose between price or price_net to provide price.";
	}


	if ($product['barcode'] && !$product['barcode_type'])
	{
		$errror++; $errorcode='KO' ; $errorlabel="You must set a barcode type when setting a barcode.";
	}

	if (! $error)
	{
		include_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

		$newobject=new Product($db);
		$newobject->fetch($product['id']);

		if (isset($product['ref']))     $newobject->ref=$product['ref'];
		if (isset($product['ref_ext'])) $newobject->ref_ext=$product['ref_ext'];
		$newobject->type=$product['type'];
		$newobject->libelle=$product['label'];    // TODO deprecated
		$newobject->label=$product['label'];
		$newobject->description=$product['description'];
		$newobject->note=$product['note'];
		$newobject->status=$product['status_tosell'];
		$newobject->status_buy=$product['status_tobuy'];
		$newobject->price=$product['price_net'];
		$newobject->price_ttc=$product['price'];
		$newobject->tva_tx=$product['vat_rate'];
		$newobject->price_base_type=$product['price_base_type'];
		$newobject->date_creation=$now;

		if ($product['barcode'])
		{
			$newobject->barcode = $product['barcode'];
			$newobject->barcode_type = $product['barcode_type'];
		}

		$newobject->stock_reel=$product['stock_real'];
		$newobject->pmp=$product['pmp'];
		$newobject->seuil_stock_alert=$product['stock_alert'];

		$newobject->country_id=$product['country_id'];
		if ($product['country_code']) $newobject->country_id=getCountry($product['country_code'],3);
		$newobject->customcode=$product['customcode'];

		$newobject->canvas=$product['canvas'];
		/*foreach($product['lines'] as $line)
		 {
		$newline=new FactureLigne($db);
		$newline->type=$line['type'];
		$newline->desc=$line['desc'];
		$newline->fk_product=$line['fk_product'];
		$newline->total_ht=$line['total_net'];
		$newline->total_vat=$line['total_vat'];
		$newline->total_ttc=$line['total'];
		$newline->vat=$line['vat_rate'];
		$newline->qty=$line['qty'];
		$newline->fk_product=$line['product_id'];
		}*/
		//var_dump($product['ref_ext']);
		//var_dump($product['lines'][0]['type']);

		$extrafields=new ExtraFields($db);
		$extralabels=$extrafields->fetch_name_optionals_label('product',true);
		foreach($extrafields->attribute_label as $key=>$label)
		{
			$key='options_'.$key;
			$newobject->array_options[$key]=$product[$key];
		}

		$db->begin();

		$result=$newobject->update($newobject->id,$fuser);
		if ($result <= 0)
		{
			$error++;
		}

		if (! $error)
		{
			$db->commit();
			$objectresp=array('result'=>array('result_code'=>'OK', 'result_label'=>''),'id'=>$newobject->id,'ref'=>$newobject->ref);
		}
		else
		{
			$db->rollback();
			$error++;
			$errorcode='KO';
			$errorlabel=$newobject->error;
		}

	}

	if ($error)
	{
		$objectresp = array('result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel));
	}

	return $objectresp;
}


/**
 * Delete a product or service
 *
 * @param	array		$authentication		Array of authentication information
 * @param	string		$listofidstring		List of id with comma
 * @return	array							Array result
 */
function deleteProductOrService($authentication,$listofidstring)
{
	global $db,$conf,$langs;

	$now=dol_now();

	dol_syslog("Function: deleteProductOrService login=".$authentication['login']);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);

	// User must be defined to user authenticated
	global $user;
	$user=$fuser;

	$listofid=explode(',',trim($listofidstring));
	$listofiddeleted=array();

	// Check parameters
	if (count($listofid) == 0 || empty($listofid[0]))
	{
		$error++; $errorcode='KO'; $errorlabel="List of Id of products or services to delete are required.";
	}

	if (! $error)
	{
		$firsterror='';

		$db->begin();

		foreach($listofid as $key => $id)
		{
			$newobject=new Product($db);
			$result=$newobject->fetch($id);

			if ($result == 0)
			{
				$error++;
				$firsterror='Product or service with id '.$id.' not found';
				break;
			}
			else
			{
				$result=$newobject->delete();
				if ($result <= 0)
				{
					$error++;
					$firsterror=$newobject->error;
					break;
				}

				$listofiddeleted[]=$id;
			}
		}

		if (! $error)
		{
			$db->commit();
			//$objectresp=array('result'=>array('result_code'=>'OK', 'result_label'=>''), 'listofid'=>$listofiddeleted);
			$objectresp=array('result'=>array('result_code'=>'OK', 'result_label'=>''), 'nbdeleted'=>count($listofiddeleted));
		}
		else
		{
			$db->rollback();
			$error++;
			$errorcode='KO';
			$errorlabel=$firsterror;
		}
	}

	if ($error)
	{
		//$objectresp = array('result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel), 'listofid'=>$listofiddeleted);
		$objectresp = array('result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel), 'nbdeleted'=>0);
	}
	else if (count($listofiddeleted) == 0)
	{
		//$objectresp=array('result'=>array('result_code'=>'NOT_FOUND', 'result_label'=>'No product or service with id '.join(',',$listofid).' found'), 'listofid'=>$listofiddeleted);
		$objectresp=array('result'=>array('result_code'=>'NOT_FOUND', 'result_label'=>'No product or service with id '.join(',',$listofid).' found'), 'nbdeleted'=>0);
	}

	return $objectresp;
}


/**
 * getListOfProductsOrServices
 *
 * @param	array		$authentication		Array of authentication information
 * @param	array		$filterproduct		Filter fields
 * @return	array							Array result
 */
function getListOfProductsOrServices($authentication,$filterproduct)
{
	global $db,$conf,$langs;

	$now=dol_now();

	dol_syslog("Function: getListOfProductsOrServices login=".$authentication['login']);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$arrayproducts=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters

	if (! $error)
	{
		$sql ="SELECT rowid, ref, ref_ext,label";
		$sql.=" FROM ".MAIN_DB_PREFIX."product";
		$sql.=" WHERE entity=".$conf->entity;
		foreach($filterproduct as $key => $val)
		{
			if ($key == 'type' && $val >= 0)   	$sql.=" AND fk_product_type = ".$db->escape($val);
			if ($key == 'tosell') 				$sql.=" AND to_sell = ".$db->escape($val);
			if ($key == 'tobuy')  				$sql.=" AND to_buy = ".$db->escape($val);
		}
		$resql=$db->query($sql);
		if ($resql)
		{
			$num=$db->num_rows($resql);

			$i=0;
			while ($i < $num)
			{
				$obj=$db->fetch_object($resql);
				$arrayproducts[]=array('id'=>$obj->rowid,'ref'=>$obj->ref,'ref_ext'=>$obj->ref_ext,'label'=>$obj->price.' - '.$obj->label);
				$i++;
			}
		}
		else
		{
			$error++;
			$errorcode=$db->lasterrno();
			$errorlabel=$db->lasterror();
		}
	}

	if ($error)
	{
		$objectresp = array(
				'result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel),
				'products'=>$arrayproducts
		);
	}
	else
	{
		$objectresp = array(
				'result'=>array('result_code' => 'OK', 'result_label' => ''),
				'products'=>$arrayproducts
		);
	}

	return $objectresp;
}

/**
 * getListOfProductsOrServicesForCategory
 *
 * @param	array		$authentication		Array of authentication information
 * @param	array		$filterproduct		Filter fields
 * @return	array							Array result
 */
function getListOfProductsOrServicesForCategory($authentication,$id,$filterproduct)
{
	global $db,$conf,$langs;

	$now=dol_now();

	dol_syslog("Function: getListOfProductsOrServicesForCategory login=".$authentication['login']);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$arrayproducts=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters
	//$prodcustprice = new Productcustomerprice($db);
	if (! $error)
	{
		$sql ="SELECT p.rowid, p.ref, p.ref_ext,p.label,p.price,tva_tx";
		$sql.=" FROM ".MAIN_DB_PREFIX."product as p,";
		$sql.="  ".MAIN_DB_PREFIX."categorie_product as cp,";
		$sql.="  ".MAIN_DB_PREFIX."categorie as c";
		$sql.=" WHERE cp.`fk_categorie`= c.rowid AND cp.`fk_product` = p.rowid ";
		$sql.=" AND p.entity=".$conf->entity;
		$sql.=" AND c.label='".$db->escape($id)."' ";
		
		foreach($filterproduct as $key => $val)
		{
			if ($key == 'type' && $val >= 0)   	$sql.=" AND p.fk_product_type = ".$db->escape($val);
			if ($key == 'tosell') 				$sql.=" AND p.tosell = ".$db->escape($val);
			if ($key == 'tobuy')  				$sql.=" AND p.tobuy = ".$db->escape($val);
		}
		$resql=$db->query($sql);
		if ($resql)
		{
			$num=$db->num_rows($resql);
			$i=0;
			global $conf;
			while ($i < $num)
			{
				
				$obj=$db->fetch_object($resql);
				$pu_ht = price2num($obj->price, 'MU');
				$pu_ttc = price2num($pu_ht * (1 + ($obj->tva_tx / 100)), 'MU').' '.$conf->currency;
				$arrayproducts[]=array('id'=>$obj->rowid,'ref'=>$obj->ref,'ref_ext'=>$obj->ref_ext,'label'=>$pu_ttc.' - '.$obj->label);
				$i++;
			}
		}
		else
		{
			$error++;
			$errorcode=$db->lasterrno();
			$errorlabel=$db->lasterror();
		}
	}

	if ($error)
	{
		$objectresp = array(
				'result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel),
				'products'=>$arrayproducts
		);
	}
	else
	{
		$objectresp = array(
				'result'=>array('result_code' => 'OK', 'result_label' => ''),
				'products'=>$arrayproducts
		);
	}

	return $objectresp;
}

/**
 * getListOfCountry
 *
 * @param	array		$authentication		Array of authentication information
 * @return	array							Array result
 */
function getListOfCountry($authentication)
{
	global $db,$conf,$langs;

	$now=dol_now();

	dol_syslog("Function: getListOfCountry login=".$authentication['login']);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$arrayproducts=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	// Check parameters

	if (! $error)
	{
		$sql ="SELECT rowid, code, label";
		$sql.=" FROM ".MAIN_DB_PREFIX."c_country";
		
		$resql=$db->query($sql);
		if ($resql)
		{
			$num=$db->num_rows($resql);

			$i=0;
			while ($i < $num)
			{
				$obj=$db->fetch_object($resql);
				$arrayproducts[]=array('id'=>$obj->rowid,'ref'=>$obj->code,'ref_ext'=>'','label'=>$obj->label);
				$i++;
			}
		}
		else
		{
			$error++;
			$errorcode=$db->lasterrno();
			$errorlabel=$db->lasterror();
		}
	}

	if ($error)
	{
		$objectresp = array(
				'result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel),
				'products'=>$arrayproducts
		);
	}
	else
	{
		$objectresp = array(
				'result'=>array('result_code' => 'OK', 'result_label' => ''),
				'products'=>$arrayproducts
		);
	}

	return $objectresp;
}


/**
 * Get list of products for a category
 *
 * @param	array		$authentication		Array of authentication information
 * @param	array		$id					Category id
 * @param	$lang		$lang				Force lang
 * @return	array							Array result
 */
function getProductsForCategory($authentication,$id,$lang='')
{
	global $db,$conf,$langs;

	$langcode=($lang?$lang:(empty($conf->global->MAIN_LANG_DEFAULT)?'auto':$conf->global->MAIN_LANG_DEFAULT));
	$langs->setDefaultLang($langcode);

	dol_syslog("Function: getProductsForCategory login=".$authentication['login']." id=".$id);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;

	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);


	if (! $error && !$id)
	{
		$error++;
		$errorcode='BAD_PARAMETERS'; $errorlabel="Parameter id must be provided.";
	}


	if (! $error)
	{
		$langcode=($lang?$lang:(empty($conf->global->MAIN_LANG_DEFAULT)?'auto':$conf->global->MAIN_LANG_DEFAULT));
		$langs->setDefaultLang($langcode);

		$fuser->getrights();

		if ($fuser->rights->produit->lire)
		{
			$categorie=new Categorie($db);
			$result=$categorie->fetch($id);
			if ($result > 0)
			{
				$table = "product";
				$field = "product";
				$sql  = "SELECT fk_".$field." FROM ".MAIN_DB_PREFIX."categorie_".$table;
				$sql .= " WHERE fk_categorie = ".$id;
				$sql .= " ORDER BY fk_".$field." ASC" ;


				dol_syslog("getProductsForCategory get id of product into category", LOG_DEBUG);
				$res  = $db->query($sql);
				if ($res)
				{
					while ($rec = $db->fetch_array($res))
					{
						$obj = new Product($db);
						$obj->fetch($rec['fk_'.$field]);
						$iProduct = 0;
						if($obj->status > 0 )
						{
							$dir = (!empty($conf->product->dir_output)?$conf->product->dir_output:$conf->service->dir_output);
							$pdir = get_exdir($obj->id,2) . $obj->id ."/photos/";
							$dir = $dir . '/'. $pdir;

							$products[] = array(
									'id' => $obj->id,
									'ref' => $obj->ref,
									'ref_ext' => $obj->ref_ext,
									'label' => ! empty($obj->multilangs[$langs->defaultlang]["label"]) ? $obj->multilangs[$langs->defaultlang]["label"] : $obj->label,
									'description' => ! empty($obj->multilangs[$langs->defaultlang]["description"]) ? $obj->multilangs[$langs->defaultlang]["description"] : $obj->description,
									'date_creation' => dol_print_date($obj->date_creation,'dayhourrfc'),
									'date_modification' => dol_print_date($obj->date_modification,'dayhourrfc'),
									'note' => ! empty($obj->multilangs[$langs->defaultlang]["note"]) ? $obj->multilangs[$langs->defaultlang]["note"] : $obj->note,
									'status_tosell' => $obj->status,
									'status_tobuy' => $obj->status_buy,
									'type' => $obj->type,
									'barcode' => $obj->barcode,
									'barcode_type' => $obj->barcode_type,
									'country_id' => $obj->country_id>0?$obj->country_id:'',
									'country_code' => $obj->country_code,
									'custom_code' => $obj->customcode,

									'price_net' => $obj->price,
									'price' => $obj->price_ttc,
									'vat_rate' => $obj->tva_tx,

									'price_base_type' => $obj->price_base_type,

									'stock_real' => $obj->stock_reel,
									'stock_alert' => $obj->seuil_stock_alerte,
									'pmp' => $obj->pmp,
									'import_key' => $obj->import_key,
									'dir' => $pdir,
									'images' => $obj->liste_photos($dir,$nbmax=10)
							);

							//Retreive all extrafield for thirdsparty
							// fetch optionals attributes and labels
							$extrafields=new ExtraFields($db);
							$extralabels=$extrafields->fetch_name_optionals_label('product',true);
							//Get extrafield values
							$product->fetch_optionals($obj->id,$extralabels);

							foreach($extrafields->attribute_label as $key=>$label)
							{
								$products[$iProduct]=array_merge($products[$iProduct],array('options_'.$key => $product->array_options['options_'.$key]));
							}

							$iProduct++;
						}

					}

					// Retour
					$objectresp = array(
							'result'=>array('result_code'=>'OK', 'result_label'=>''),
							'products'=> $products
					);

				}
				else
				{
					$errorcode='NORECORDS_FOR_ASSOCIATION'; $errorlabel='No products associated'.$sql;
					$objectresp = array('result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel));
					dol_syslog("getProductsForCategory:: ".$c->error, LOG_DEBUG);

				}
			}
			else
			{
				$error++;
				$errorcode='NOT_FOUND'; $errorlabel='Object not found for id='.$id;
			}
		}
		else
		{
			$error++;
			$errorcode='PERMISSION_DENIED'; $errorlabel='User does not have permission for this request';
		}
	}

	if ($error)
	{
		$objectresp = array('result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel));
	}

	return $objectresp;
}

function getUser($authentication)
{
	global $db,$conf,$langs;

	dol_syslog("Function: getUser login=".$authentication['login']." id=".$id." ref=".$ref." ref_ext=".$ref_ext);

	if ($authentication['entity']) $conf->entity=$authentication['entity'];

	// Init and check authentication
	$objectresp=array();
	$errorcode='';$errorlabel='';
	$error=0;
	$fuser=check_authentication($authentication,$error,$errorcode,$errorlabel);
	dol_syslog("auth ok", LOG_DEBUG);

        $abon = new Abonnement($db);
        $login = $authentication['login'];
        $arrContrat = $abon->getAllContratByLogin($login);
        dol_syslog("get all contrat: $arrContrat", LOG_DEBUG);
        // cloture les contrats expiré, pq ?
        foreach ($arrContrat as $id_contrat ) {
            dol_syslog("on ferme les contrat expirés ?", LOG_DEBUG);
            $abon->closeContratexpire($id_contrat);
        }
        dol_syslog("Fini de fermer les contrat expire", LOG_DEBUG);
        $arrcontrat_active = $abon->getContratActive($authentication['login']);

	if (! $error && count($arrcontrat_active)>0)
	{
		// Create
		$user = $fuser;
                dol_syslog(" prepare response with user data", LOG_DEBUG);
		$objectresp = array(
				'result'=>array('result_code'=>'OK', 'result_label'=>''),
				'user'=>array(
						'id' => $user->id,
						'lastname' => $user->lastname,
						'firstname' => $user->firstname,
						'note' => $user->note,
						'email' => $user->email,
						'signature' => $user->signature,
						'office_phone' => $user->office_phone,
						'office_fax' => $user->office_fax,
						'user_mobile' => $user->user_mobile,
						'admin' => $user->admin,
						'login' => $user->login,
						'entity' => $user->entity,
						'pass_indatabase' => $user->pass_indatabase,
						'pass_indatabase_crypted' => $user->pass_indatabase_crypted,
						'datec' => dol_print_date($user->datec,'dayhourrfc'),
						'datem' => dol_print_date($user->datem,'dayhourrfc'),
						'fk_thirdparty' => $user->societe_id,
						'fk_contact' => $user->contact_id,
						'fk_member' => $user->fk_member,
						'datelastlogin' => dol_print_date($user->datelastlogin,'dayhourrfc'),
						'datepreviouslogin' => dol_print_date($user->datepreviouslogin,'dayhourrfc'),
						'statut' => $user->statut,
						'photo' => $user->photo,
						'lang' => $user->lang,
						//'rights' => $user->rights,
						'canvas' => $user->canvas
				)
		);



	} 

	if(count($arrcontrat_active) <= 0) {
		$error ++;
                $errorcode='ERROR_CONTRAT_USER'; $errorlabel="Votre abonnement n'est pas encore activé, veuillez contacter alter";
	}

	if ($error)
	{
		$objectresp = array('result'=>array('result_code' => $errorcode, 'result_label' => $errorlabel));
	}

	return $objectresp;
}


// Return the results.
$server->service(file_get_contents("php://input"));
