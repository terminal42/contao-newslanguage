<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2009-2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_news_archive']['palettes']['default'] = str_replace('title,', 'title,language,master,', $GLOBALS['TL_DCA']['tl_news_archive']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_news_archive']['subpalettes']['makeFeed'] = str_replace(',language,', ',', $GLOBALS['TL_DCA']['tl_news_archive']['subpalettes']['makeFeed']);
$GLOBALS['TL_DCA']['tl_news_archive']['fields']['format']['eval']['tl_class'] = 'clr';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_news_archive']['fields']['master'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_news_archive']['master'],
	'exclude'			=> true,
	'inputType'			=> 'select',
	'options_callback'	=> array('tl_newslanguage', 'getArchives'),
	'eval'				=> array('includeBlankOption'=>true, 'blankOptionLabel'=>&$GLOBALS['TL_LANG']['tl_news_archive']['isMaster']),
);


class tl_newslanguage extends Backend
{

	/**
	 * Get an array of possible news archives
	 *
	 * @param	DataContainer
	 * @return	array
	 * @link	http://www.contao.org/callbacks.html#options_callback
	 */
	public function getArchives(DataContainer $dc)
	{
		$arrArchives = array();
		$objArchives = $this->Database->prepare("SELECT * FROM tl_news_archive WHERE language!=? AND id!=? AND master=0 ORDER BY title")->execute($dc->activeRecord->language, $dc->id);
		
		while( $objArchives->next() )
		{
			$arrArchives[$objArchives->id] = sprintf($GLOBALS['TL_LANG']['tl_news_archive']['isSlave'], $objArchives->title);
		}
		
		return $arrArchives;
	}
}

