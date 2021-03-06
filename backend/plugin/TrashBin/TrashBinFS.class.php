<?php

/**
 * TrashBinFS.class.php
 *
 * Copyright 2015- Samuli J�rvel�
 * Released under GPL License.
 *
 * License: http://www.kloudspeaker.com/license.php
 */

require_once "include/filesystem/KloudspeakerFilesystem.class.php";
require_once "include/filesystem/LocalFilesystem.class.php";

class TrashBinFS extends LocalFilesystem {
	private $env;
	private $folder;
	private $rootItems;
	private $rootItemsById;

	function __construct($env, $folder, $rootItems) {
		parent::__construct("trash", array("name" => "trash", "path" => $folder), $this);
		$this->env = $env;
		$this->folder = $folder;
		$this->rootItems = $rootItems;
		$this->rootItemsById = array();
		foreach ($this->rootItems as $item) {
			$this->rootItemsById[$item["id"]] = $item;
		}
	}

	/*public function getItem($id) {
		//TODO path?
		$item = array_key_exists($id, $this->rootItemsById) ? $this->rootItemsById[$id] : FALSE;
		if (!$item) return NULL;

		$isFile = (strcasecmp(substr($item["path"], -1), itemIdProvider::PATH_DELIMITER) != 0);
		return $this->createItem($id, $id.($isFile ? "" : DIRECTORY_SEPARATOR));
	}*/

	public function env() {
		return $this->env;
	}

	public function itemIdProvider() {
		return $this;
	}

	public function isItemIgnored($parentPath, $name, $nativePath) {
		$p = substr($parentPath, strlen($this->folder));
		if (strlen($p) == 0 and !array_key_exists($name, $this->rootItemsById)) {
			return TRUE;
		}

		return FALSE;
	}

	protected function itemName($parentPath, $name, $nativePath) {
		$p = substr($parentPath, strlen($this->folder));
		if (strlen($p) == 0) {
			$item = array_key_exists($name, $this->rootItemsById) ? $this->rootItemsById[$name] : FALSE;
			if (!$item) {
				return FALSE;
			}

			$realName = rtrim($item["path"], DIRECTORY_SEPARATOR);
			$n = strrchr($realName, DIRECTORY_SEPARATOR);
			if ($n !== FALSE) {
				return ltrim($n, DIRECTORY_SEPARATOR);
			}

			return $realName;
		}
		return $this->env->convertCharset($name);
	}

	public function getItemId($loc) {
		if (strlen($loc) == 7) return "trash"; //"trash:/" = 7
		return $this->env->filesystem()->itemIdProvider()->getItemId($loc);
	}
}
?>