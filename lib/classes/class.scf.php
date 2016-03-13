<?php

/**
 *  SCF
 *   oggetto per la gestione scaffali
 */

Class SCF {
	
	
	
	/**
	 * crea un array associativo contenente gli scaffali disponibili 
	 * del tipo 
	 * array[<id>] = <descrizione>
	 */
	static function getScfAssoc()
	{
		$retArray = array();
		
		$q = mysql_query("SELECT * FROM scf_desc ORDER BY descriz ASC");
		
		while($row = mysql_fetch_assoc($q)) {

			$retArray[$row['id']] = $row['descriz'];			
		}
		
		return $retArray; 
	}

	
	

	/**
	 * restituisce il posizionamento scaffale metro di un codice articolo
	 * restituisce un array composto da 
	 * 
	 * array['desc'] = <descrittivo dello scaffale>
	 *      ['mt'] = <identificativo metro>
	 *      ['mtDesc'] = <descrittivo metro>
	 * 
	 */
	static function getScfDescMt($codArt)
	{
		
		$q = mysql_query("SELECT scf_codarts.codart, scf_desc.descriz, scf_assoc.mt FROM scf_codarts " .
							" INNER JOIN scf_assoc ON scf_codarts.scf_assoc_id = scf_assoc.id " .
							" INNER JOIN scf_desc ON scf_assoc.scf_desc_id = scf_desc.id " .
							" WHERE scf_codarts.codart = '".$codArt."' LIMIT 1 ");
							 
		if(!$q) die(__CLASS__.'::'.__FUNCTION__." errore nella query. ".mysql_error());
		
		$retArray = array();
		
		if(mysql_num_rows($q)) {
			
			$row = mysql_fetch_assoc($q);
			
			$retArray['descriz'] = $row['descriz'];
			$retArray['mt'] = $row['mt'];
			$retArray['mtDesc'] = SCF::convMt2MtDesc($row['mt']);
			
		}
		
		return $retArray;
	}
	
	
	
	/**
	 * converte un id Mt in un descrittivo
	 */
	static function convMt2MtDesc($mtId)
	{
		$id = substr($mtId, 0, 1);
		
		if($id == 'T') {
			
			return 'Testata '.substr($mtId, 1);
			
		}
		
		if($id == 'G') { 
			
			return 'Griglia '.substr($mtId, 1);
			
		}
		
		return 'Metro '.$mtId;
	}
	
	
		
		
	static function checkInstall()
	{
		$q = mysql_query("SELECT id FROM scf_desc ");
		
		if($q) {

			return true;
			
		}
		
		$sql = array();
		$sql[] = "CREATE TABLE scf_assoc (
				  id bigint(20) unsigned NOT NULL auto_increment,
				  mt varchar(3) NOT NULL,
				  scf_desc_id bigint(20) unsigned NOT NULL,
				  `data` text NOT NULL,
				  PRIMARY KEY  (id),
				  KEY scf_desc_id (scf_desc_id)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE scf_codarts (
				  id bigint(20) unsigned NOT NULL auto_increment,
				  codart varchar(13) NOT NULL,
				  scf_assoc_id bigint(20) unsigned NOT NULL,
				  fl_mv varchar(1) NOT NULL,
				  docven_date bigint(20) NOT NULL,
				  PRIMARY KEY  (id),
				  KEY scf_assoc_id (scf_assoc_id)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE scf_desc (
				  id bigint(20) unsigned NOT NULL auto_increment,
				  `descriz` varchar(255) NOT NULL,
				  `data` text NOT NULL,
				  PRIMARY KEY  (id)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

		$sql[] = "ALTER TABLE `scf_assoc`
				  ADD CONSTRAINT scf_assoc_ibfk_1 FOREIGN KEY (scf_desc_id) REFERENCES scf_desc (id) ON DELETE CASCADE ON UPDATE NO ACTION;";
		$sql[] = "ALTER TABLE `scf_codarts`
				  ADD CONSTRAINT scf_codarts_ibfk_1 FOREIGN KEY (scf_assoc_id) REFERENCES scf_assoc (id) ON DELETE CASCADE ON UPDATE NO ACTION;";

		foreach($sql as $statement) {
			
			$q = mysql_query($statement);
			
		}
		
		return true;
	}
	
}
?>
