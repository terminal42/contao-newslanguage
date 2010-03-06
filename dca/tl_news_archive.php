<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2009
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Palettes
 */
if (version_compare(VERSION, '2.7', '<'))
{
	$GLOBALS['TL_DCA']['tl_news_archive']['palettes']['default'] = str_replace(';jumpTo', ',master;jumpTo', $GLOBALS['TL_DCA']['tl_news_archive']['palettes']['default'], $count);
}
else
{
	$GLOBALS['TL_DCA']['tl_news_archive']['palettes']['default'] = str_replace('title,', 'title,language,master,', $GLOBALS['TL_DCA']['tl_news_archive']['palettes']['default']);
	$GLOBALS['TL_DCA']['tl_news_archive']['subpalettes']['makeFeed'] = str_replace(',language,', ',', $GLOBALS['TL_DCA']['tl_news_archive']['subpalettes']['makeFeed']);
	$GLOBALS['TL_DCA']['tl_news_archive']['fields']['format']['eval']['tl_class'] = 'clr';
}


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_news_archive']['fields']['master'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_news_archive']['master'],
	'inputType'			=> 'select',
	'options_callback'	=> array('tl_news_archive_changelanguage', 'getArchives'),
	'eval'				=> array('includeBlankOption'=>true, 'blankOptionLabel'=>&$GLOBALS['TL_LANG']['tl_news_archive']['isMaster']),
);


class tl_news_archive_changelanguage extends Backend
{
	public function getArchives(DataContainer $dc)
	{
		$objArchive = $this->Database->prepare("SELECT * FROM tl_news_archive WHERE id=?")->execute($dc->id);
		
		$arrArchives = array();
		$objArchives = $this->Database->prepare("SELECT * FROM tl_news_archive WHERE language!=? ORDER BY title")->execute($objArchive->language);
		
		while( $objArchives->next() )
		{
			if ($objArchives->id == $dc->id || $objArchives->master > 0)
				continue;
				
			$arrArchives[$objArchives->id] = sprintf($GLOBALS['TL_LANG']['tl_news_archive']['isSlave'], $objArchives->title);
		}
		
		return $arrArchives;
	}
}

