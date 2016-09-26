<?php 
	class FileMaker {

		public $nameFile;
		public $extFile;


		public static function buildFile($file, $dir, $name){

		$doc_root = $_SERVER["DOCUMENT_ROOT"];
        $num = getrandmax();
        $date = date("Y-m-d H:i:s");
        $new_name = md5($num.$name.$date);
        $file->move($doc_root."/photos/".$dir, $new_name.".".$extFile);

		}

	}


?>