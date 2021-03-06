<?php
/**
 * Copyright (c) 2013 Georg Ehrke georg@ownCloud.com
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
namespace OC\Preview;

class MP3 extends Provider {

	public function getMimeType() {
		return '/audio\/mpeg/';
	}

	public function getThumbnail($path, $maxX, $maxY, $scalingup, $fileview) {
		require_once('getid3/getid3.php');

		$getID3 = new \getID3();

		$tmpPath = $fileview->toTmpFile($path);

		$tags = $getID3->analyze($tmpPath);
		\getid3_lib::CopyTagsToComments($tags);
		if(isset($tags['id3v2']['APIC'][0]['data'])) {
			$picture = @$tags['id3v2']['APIC'][0]['data'];
			unlink($tmpPath);
			$image = new \OC_Image($picture);
			return $image->valid() ? $image : $this->getNoCoverThumbnail();
		}

		return $this->getNoCoverThumbnail();
	}

	private function getNoCoverThumbnail() {
		$icon = \OC::$SERVERROOT . '/core/img/filetypes/audio.png';

		if(!file_exists($icon)) {
			return false;
		}

		$image = new \OC_Image($icon);
		return $image->valid() ? $image : false;
	}

}

\OC\Preview::registerProvider('OC\Preview\MP3');