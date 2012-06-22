<?php

/*
 Author: Cesar Caballero Gallego
Date: 07/05/2012 - 18:29:09
File: resize.php
*/

class Resize {

	// Vars
	private $image;
	private $width;
	private $height;
	private $imageResized;
	
	// Metodo constructor
	public function __construct($filename) {

		// Asignamos imagen
		$this->image = $this->openImage($filename);
		
		// Get width and height
		$this->width = imagesx($this->image);
		$this->height = imagesy($this->image);
		
	}

	
	// Metodo para reescalar
	public function resize($newWidth, $newHeight, $option = "auto") {
	
		// Recupera ancho y alto optimos según opcion
		$optionArray = $this->getDimensions($newWidth, $newHeight, strtolower($option));
	
		// Parametros
		$optimalWidth = $optionArray['optimalWidth'];
		$optimalHeight = $optionArray['optimalHeight'];
	
		// Creamos nueva imagen con parametros anteriores
		$this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
		imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);
	
		// Si tenemos que recortar la imagen, recortamos
		if ($option == 'crop') {
			$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
		}
	
	}
	
	// Guardar imagen fisicamente
	public function saveImage($savePath, $imageQuality="100")
	{
		// *** Get extension
		$extension = strrchr($savePath, '.');
		$extension = strtolower($extension);
	
		switch($extension)
		{
			case '.jpg':
			case '.jpeg':
				if (imagetypes() & IMG_JPG) {
					imagejpeg($this->imageResized, $savePath, $imageQuality);
				}
				break;
	
			case '.gif':
				if (imagetypes() & IMG_GIF) {
					imagegif($this->imageResized, $savePath);
				}
				break;
	
			case '.png':
				// *** Scale quality from 0-100 to 0-9
				$scaleQuality = round(($imageQuality/100) * 9);
	
				// *** Invert quality setting as 0 is best, not 9
				$invertScaleQuality = 9 - $scaleQuality;
	
				if (imagetypes() & IMG_PNG) {
					imagepng($this->imageResized, $savePath, $invertScaleQuality);
				}
				break;
	
				// ... etc
	
			default:
				// *** No extension - No save.
				break;
		}
	
		imagedestroy($this->imageResized);
		
	}	

	// Guardar imagen fisicamente
	public function onfly($extension = 'jpg', $imageQuality="100")
	{
	
		switch(strtolower($extension))
		{
			case '.jpg':
			case '.jpeg':
				if (imagetypes() & IMG_JPG) {
					header("Content-type: image/jpeg");
					imagejpeg($this->imageResized, '', $imageQuality);
					die();
				}
				break;
	
			case '.gif':
				if (imagetypes() & IMG_GIF) {
					header("Content-type: image/gif");
					imagegif($this->imageResized, '');
					die();
				}
				break;
	
			case '.png':
				// *** Scale quality from 0-100 to 0-9
				$scaleQuality = round(($imageQuality/100) * 9);
	
				// *** Invert quality setting as 0 is best, not 9
				$invertScaleQuality = 9 - $scaleQuality;
	
				if (imagetypes() & IMG_PNG) {
					header("Content-type: image/png");
					imagepng($this->imageResized, '', $invertScaleQuality);
					die();
				}
				break;
	
				// ... etc
	
			default:
				// *** No extension - No save.
				break;
		}
				
	
	}	
	
	/************ METODOS PRIVADOS **************/
	
	
	// Metodo para leer imagenes
	private function openImage($file) {
		
		set_time_limit(10000);
		ini_set("memory_limit","192M");
		
		// Usar getimagefile and pathinfo
		
		// Get extension
		$extension = strtolower(strrchr($file, '.'));
		
		// Creamos segun tipo
		switch ($extension) {
			
			case '.jpg':
			case '.jpeg':
				$img = imagecreatefromjpeg($file);
			break;
			case '.gif':
				$img = imagecreatefromgif($file);
			break;
			case '.png':
				$img = imagecreatefrompng($file);
			break;
			default:
				$img = false;
			break;
			
		}
		
		set_time_limit(100);
		
		return $img;	
		
	}
	
	// Metodo para obtener dimensiones optimas segun tipo de escalados
	private function getDimensions($newWidth, $newHeight, $option)
	{
	
		switch ($option)
		{
			case 'exact':
				$optimalWidth = $newWidth;
				$optimalHeight= $newHeight;
				break;
			case 'portrait':
				$optimalWidth = $this->getSizeByFixedHeight($newHeight);
				$optimalHeight= $newHeight;
				break;
			case 'landscape':
				$optimalWidth = $newWidth;
				$optimalHeight= $this->getSizeByFixedWidth($newWidth);
				break;
			case 'auto':
				$optionArray = $this->getSizeByAuto($newWidth, $newHeight);
				$optimalWidth = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];
				break;
			case 'crop':
				$optionArray = $this->getOptimalCrop($newWidth, $newHeight);
				$optimalWidth = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];
				break;
		}
		
		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	
	}
	
	// Metodo para obtener dimensiones si altura es fija
	private function getSizeByFixedHeight($newHeight)
	{
		$ratio = $this->width / $this->height;
		$newWidth = $newHeight * $ratio;
		return $newWidth;
	}

	// Metodo para obtener dimensiones si ancho es fijo	
	private function getSizeByFixedWidth($newWidth)
	{
		$ratio = $this->height / $this->width;
		$newHeight = $newWidth * $ratio;
		return $newHeight;
	}
	
	// Metodo para obtener dimensiones ajustadas automaticamente segun datos	
	private function getSizeByAuto($newWidth, $newHeight)
	{
		if ($this->height < $this->width)
		// *** Image to be resized is wider (landscape)
		{
			$optimalWidth = $newWidth;
			$optimalHeight= $this->getSizeByFixedWidth($newWidth);
		}
		elseif ($this->height > $this->width)
		// *** Image to be resized is taller (portrait)
		{
			$optimalWidth = $this->getSizeByFixedHeight($newHeight);
			$optimalHeight= $newHeight;
		}
		else
		// *** Image to be resizerd is a square
		{
			if ($newHeight < $newWidth) {
				$optimalWidth = $newWidth;
				$optimalHeight= $this->getSizeByFixedWidth($newWidth);
			} else if ($newHeight > $newWidth) {
				$optimalWidth = $this->getSizeByFixedHeight($newHeight);
				$optimalHeight= $newHeight;
			} else {
				// *** Sqaure being resized to a square
				$optimalWidth = $newWidth;
				$optimalHeight= $newHeight;
			}
		}
	
		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}
	
	// Metodo para obtener dimensiones de recorte	
	private function getOptimalCrop($newWidth, $newHeight)
	{
	
		$heightRatio = $this->height / $newHeight;
		$widthRatio  = $this->width /  $newWidth;
	
		if ($heightRatio < $widthRatio) {
			$optimalRatio = $heightRatio;
		} else {
			$optimalRatio = $widthRatio;
		}
	
		$optimalHeight = $this->height / $optimalRatio;
		$optimalWidth  = $this->width  / $optimalRatio;
	
		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
		
	}	
	
	// Metodo para recortar imagen
	private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)
	{
		// *** Find center - this will be used for the crop
		$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
		$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );
	
		$crop = $this->imageResized;
		//imagedestroy($this->imageResized);
	
		// *** Now crop from center to exact requested size
		$this->imageResized = imagecreatetruecolor($newWidth , $newHeight);
		imagecopyresampled($this->imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
	}	
	
	
}

?>