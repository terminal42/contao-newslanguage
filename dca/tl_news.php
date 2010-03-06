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
$GLOBALS['TL_DCA']['tl_news']['config']['onload_callback'][] = array('tl_news_changelanguage', 'showSelectbox');


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_news']['fields']['languageMain'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news']['languageMain'],
	'exclude'                 => false,
	'inputType'               => 'select',
	'options_callback'        => array('tl_news_changelanguage', 'getMasterArchive'),
	'eval'					  => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
);


class tl_news_changelanguage extends Backend 
{
	public function getMasterArchive(DataContainer $dc)
	{
		// Handle "edit all" option
		if ($this->Input->get('act') == 'editAll')
		{
			if (!is_array($GLOBALS['languageMain_IDS']))
			{
				$ids = $this->Session->get('CURRENT');
				$GLOBALS['languageMain_IDS'] = $ids['IDS'];
			}
			$this->Input->setGet('id', array_shift($GLOBALS['languageMain_IDS']));
		}
		
		
		$arrItems = array();
		$objArchive = $this->Database->prepare("SELECT tl_news_archive.* FROM tl_news_archive LEFT OUTER JOIN tl_news ON tl_news.pid=tl_news_archive.id WHERE tl_news.id=?")->execute($dc->id);
		
		if ($objArchive->numRows && $objArchive->master > 0)
		{
			$objItems = $this->Database->prepare("SELECT * FROM tl_news WHERE pid=? ORDER BY date DESC, time DESC")->execute($objArchive->master);
			
			if ($objItems->numRows)
			{
				while( $objItems->next() )
				{
					$arrItems[$objItems->id] = $objItems->headline . ' (' . $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $objItems->time) . ')';
				}
			}
		}
		
		return $arrItems;
	}
	
		
	public function showSelectbox(DataContainer $dc)
	{
		if($this->Input->get('act') == "edit")
		{
			$objArchive = $this->Database->prepare("SELECT tl_news_archive.* FROM tl_news_archive LEFT OUTER JOIN tl_news ON tl_news.pid=tl_news_archive.id WHERE tl_news.id=?")
										 ->limit(1)
										 ->execute($dc->id);

			if($objArchive->numRows && $objArchive->master > 0)
			{
				$GLOBALS['TL_DCA']['tl_news']['palettes']['default'] = preg_replace('@([,|;])(alias[,|;])@','$1languageMain,$2', $GLOBALS['TL_DCA']['tl_news']['palettes']['default']);
				$GLOBALS['TL_DCA']['tl_news']['palettes']['internal'] = preg_replace('@([,|;])(alias[,|;])@','$1languageMain,$2', $GLOBALS['TL_DCA']['tl_news']['palettes']['internal']);
				$GLOBALS['TL_DCA']['tl_news']['palettes']['external'] = preg_replace('@([,|;])(alias[,|;])@','$1languageMain,$2', $GLOBALS['TL_DCA']['tl_news']['palettes']['external']);
				$GLOBALS['TL_DCA']['tl_news']['fields']['headline']['eval']['tl_class'] = 'w50';
				$GLOBALS['TL_DCA']['tl_news']['fields']['alias']['eval']['tl_class'] = 'clr w50';
			}
		}
		else if($this->Input->get('act') == "editAll")
		{
			$GLOBALS['TL_DCA']['tl_page']['palettes']['regular'] = preg_replace('@([,|;]{1}language)([,|;]{1})@','$1,languageMain$2', $GLOBALS['TL_DCA']['tl_page']['palettes']['regular']);
		}
	}
}

