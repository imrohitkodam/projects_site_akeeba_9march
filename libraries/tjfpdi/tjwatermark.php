<?php
/**
 * @package     Techjoomla.Libraries
 * @subpackage  TjWatermark
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

use setasign\Fpdi\Fpdi;
use Joomla\CMS\Filesystem\File;

/**
 * TjWatermark
 *
 * @package     Techjoomla.Libraries
 * @subpackage  TjWatermark
 * @since       1.0
 */

class TjWatermark
{
	private $fontStyle;

	private $fontSize;

	private $fontColor;

	private $waterMarkText;

	private $waterMarkImage;

	private $waterMarkPosition;

	/**
	 * The constructor
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		// Load the required files
		JLoader::import('libraries.tjfpdi.fpdi.vendor.fpdf.fpdf', JPATH_SITE);
		JLoader::import('libraries.tjfpdi.fpdi.vendor.fpdi2.src.autoload', JPATH_SITE);

		// Set the default values
		$this->fontStyle = 'Helvetica';
		$this->fontSize = 12;
		$this->fontColor = '#000000';
		$this->waterMarkPosition = 'center';
	}

	/**
	 * Function to add text watermark on PDF
	 *
	 * @param   STRING  $pdfFilePath     PDF file path
	 * @param   STRING  $wmText          order status
	 * @param   CHAR    $saveOrDownload  F/D F-to save D-to download
	 *
	 * @return  BOOLEAN|STRING
	 */
	public function textOnPdf($pdfFilePath, $wmText, $saveOrDownload = 'F')
	{
		if (!File::exists($pdfFilePath))
		{
			return false;
		}

		$pdfFileName = basename($pdfFilePath);

		if (strtolower(File::getExt($pdfFileName)) != 'pdf')
		{
			return false;
		}

		$pdf = new Fpdi;
		$pdf = new \setasign\Fpdi\Fpdi();

		// Set the source file
		$pageCount = $pdf->setSourceFile($pdfFilePath);
		// $pageCount = $pdf->setSourceFileWithParserParams(
		// 	$pdfFilePath,
		// 	['password' => 'Please paste password here']
		// );

		for ($i = 1; $i <= $pageCount; $i++)
		{
			// Import page by page count
			$pageObj = $pdf->importPage($i);

			// Add a blank page to the pdf
			$pdf->AddPage();

			// Place watermark on the blank page
			$rgbArray = $this->hex2RGB($this->fontColor);
			$pdf->SetFont($this->fontStyle);
			$pdf->SetTextColor($rgbArray['red'], $rgbArray['green'], $rgbArray['blue']);
			$pdf->SetFontSize($this->fontSize);
			$templateDimension = $pdf->getTemplateSize($pageObj);

			$wWidth = $pdf->GetStringWidth($wmText);
			$wHeight = imagefontheight($this->fontSize);
			$wmXY = $this->getWaterMarkTextCoordinates(
				$this->waterMarkPosition,
				$wWidth,
				$wHeight,
				$templateDimension['width'],
				$templateDimension['height']
			);
			$pdf->SetXY($wmXY['width'], $wmXY['height']);
			$pdf->Write(0, $wmText);

			// Place the existing page content on the watermark
			$pdf->useTemplate($pageObj);
		}
		// echo $saveOrDownload . '-------------------';
		// print_r($pdfFilePath);
		// die('-------------------------------------fgfg');

		if ($saveOrDownload == 'D')
		{
			$pdf->Output($saveOrDownload, $outputFilePath);

			return true;
		}
		else
		{
			$outputFilePath = str_replace($pdfFileName, time() . '_' . $pdfFileName, $pdfFilePath);
			$pdf->Output('F', $outputFilePath);

			return $outputFilePath;
		}
	}

	/**
	 * Function to add text watermark on PDF
	 *
	 * @param   STRING  $pdfFilePath      PDF file path
	 * @param   STRING  $wmImageFilePath  Image file path
	 * @param   CHAR    $saveOrDownload   F/D F-to save D-to download
	 *
	 * @return  BOOLEAN|STRING
	 */
	public function imageOnPdf($pdfFilePath, $wmImageFilePath, $saveOrDownload = 'F')
	{
		if (!File::exists($pdfFilePath))
		{
			return false;
		}
		
		if (!File::exists($wmImageFilePath))
		{
			$wmImageFilePath = $wmImageFilePath ? explode("#joomlaImage", $wmImageFilePath)[0] : '';

			if (!File::exists($wmImageFilePath))
			{
				return false;
			}
		}
		
		$pdfFileName = basename($pdfFilePath);

		if (strtolower(File::getExt($pdfFileName)) != 'pdf')
		{
			return false;
		}

		// Load the required files
		JLoader::import('libraries.tjfpdi.fpdi.vendor.pdfwatermarker.pdfwatermarker', JPATH_SITE);
		JLoader::import('libraries.tjfpdi.fpdi.vendor.pdfwatermarker.pdfwatermark', JPATH_SITE);

		// Specify path to watermark image. The image must have a 96 DPI resolution.
		$watermark = new PDFWatermark($wmImageFilePath);

		// Set the position
		$watermark->setPosition($this->waterMarkPosition);

		// Place watermark behind original PDF content
		$watermark->setAsBackground();

		$outputFilePath = str_replace($pdfFileName, time() . '_' . $pdfFileName, $pdfFilePath);

		// Add watermark on the pdf pages
		$watermarker = new PDFWatermarker($pdfFilePath, $outputFilePath, $watermark);

		// Set page range. Use 1-based index.
		$watermarker->setPageRange(1);

		if ($saveOrDownload == 'D')
		{
			$watermarker->downloadPdf();

			// Delete the file once download is completed
			if (File::exists($outputFilePath))
			{
				File::delete($outputFilePath);
			}

			return true;
		}
		else
		{
			$watermarker->savePdf();

			return $outputFilePath;
		}
	}

	/**
	 * Function to convert Hexadecimal code to RGB
	 *
	 * @param   STRING  $hexStr  Hexadecimal color code
	 *
	 * @return  ARRAY
	 */
	private function hex2RGB($hexStr)
	{
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr);
		$rgbArray = array();

		if (strlen($hexStr) == 6)
		{
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		}
		elseif (strlen($hexStr) == 3)
		{
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		}
		else
		{
			return false;
		}

		return $rgbArray;
	}

	/**
	 * Function to convert Hexadecimal code to RGB
	 *
	 * @param   STRING  $position  Position
	 * @param   INT     $wWidth    Watermark width
	 * @param   INT     $wHeight   Watermark height
	 * @param   INT     $tWidth    Template width
	 * @param   INT     $tHeight   Template height
	 *
	 * @return  ARRAY
	 */
	private function getWaterMarkTextCoordinates($position, $wWidth, $wHeight, $tWidth, $tHeight)
	{
		switch ($position)
		{
			case 'topleft':
				$x = 10;
				$y = 10;
				break;
			case 'topright':
				$x = $tWidth - ($wWidth + 15);
				$y = 10;
				break;
			case 'bottomright':
				$x = $tWidth - ($wWidth + 15);
				$y = $tHeight - ($wHeight + 7);
				break;
			case 'bottomleft':
				$x = 10;
				$y = $tHeight - ($wHeight + 7);
				break;
			default:
				$x = ( $tWidth - $wWidth ) / 2;
				$y = ( $tHeight - $wHeight ) / 2;
				break;
		}

		return array('width' => $x, 'height' => $y);
	}

	/**
	 * Function to set watermark text font size
	 *
	 * @param   INT  $fontSize  Font size
	 *
	 * @return  NULL
	 */
	public function setFontSize($fontSize)
	{
		$this->fontSize = $fontSize;
	}

	/**
	 * Function to set watermark text font size
	 *
	 * @param   STRING  $fontStyle  Font style
	 *
	 * @return  NULL
	 */
	public function setFontStyle($fontStyle)
	{
		$this->fontStyle = $fontStyle;
	}

	/**
	 * Function to set watermark text font size
	 *
	 * @param   STRING  $fontColor  Font color
	 *
	 * @return  NULL
	 */
	public function setFontColor($fontColor)
	{
		$this->fontColor = $fontColor;
	}

	/**
	 * Function to set watermark text font size
	 *
	 * @param   STRING  $position  Position
	 *
	 * @return  NULL
	 */
	public function setPosition($position)
	{
		$this->waterMarkPosition = $position;
	}
}
