<?php
/**
 +------------------------------------------------------------------------------
 * 文件名称： core/Thumb.class.php
 +------------------------------------------------------------------------------
 * 文件描述： 缩略图生成
 +------------------------------------------------------------------------------
 */
defined('WF_CORE_ROOT') or die( 'Access not allowed');

class WF_Thumb {

	private $resFile = '';
	private $tmpFile = '';

	public function __construct()
	{
	}

	public function create($path, $width=48, $heigh=48){
		$this->resFile   = $path;
		$this->tmpFile   = WF_DATA_PATH . 'cache/'. md5($path) . '.tmp';
		$this->tmbWidth  = $width;
		$this->tmbHeight = $heigh;

		$info = $this->getImageInfo();
		if(!$info) return false;

		$new_info = $this->tmbEffects($info['width'], $info['height'],  $this->tmbWidth, $this->tmbHeight);
		if($info['mime'] == 'image/jpeg'){
			$img = imagecreatefromjpeg($this->resFile);
		}elseif ($info['mime'] == 'image/png'){
			$img = imagecreatefrompng($this->resFile);
		}elseif ($info['mime'] == 'image/gif'){
			$img = imagecreatefromgif($this->resFile);
		}else{
			return false;
		}

		if ($img &&  false != ($tmp = imagecreatetruecolor($this->tmbWidth, $this->tmbHeight))){
			if (!imagecopyresampled($tmp, $img, 0, 0, $new_info[0], $new_info[1], $this->tmbWidth, $this->tmbHeight, $new_info[2], $new_info[3])) {
				return false;
			}

			$result = imagejpeg($tmp, $this->tmpFile, 80);
			imagedestroy($img);
			imagedestroy($tmp);
		}

		return $result ? true : false;
	}

	public function show(){
		$file = $this->tmpFile;
		header('Content-type: image/jpeg');
		header('Content-length: ' . filesize($file));
		readfile($file);
		// file_exists($file) && unlink($file);
	}

	private function getImageInfo() {
		$imageInfo = getimagesize($this->resFile);
		if (false !== $imageInfo) {
			$imageType = strtolower(substr(image_type_to_extension($imageInfo[2]),1));
			$imageSize = filesize($this->resFile);
			$info = array(
			'width' => $imageInfo[0], 'height' => $imageInfo[1],
			'type' => $imageType, 'size' => $imageSize,
			'mime' => $imageInfo['mime']
			);
			return $info;
		} else {
			return false;
		}
	}

	private function tmbEffects($resWidth, $resHeight, $tmbWidth, $tmbHeight, $crop = true) {
		$x = $y = 0;
		$size_w = $size_h = 0;

		$scale1  = $resWidth / $resHeight;
		$scale2  = $tmbWidth / $tmbHeight;
		if ($scale1 < $scale2){
			$size_w = $resWidth;
			$size_h = round($size_w * ($tmbHeight / $tmbWidth));
			$y = ceil(($resHeight - $size_h)/2);
		}else{
			$size_h = $resHeight;
			$size_w = round($size_h * ($tmbWidth / $tmbHeight));
			$x = ceil(($resWidth - $size_w)/2);
		}
		return array($x, $y, $size_w, $size_h);
	}



	/**Class END**/
}