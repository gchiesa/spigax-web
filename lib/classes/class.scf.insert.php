<?php
/**
 * SCFINSERT
 *   oggetto per la gestione inserimenti in scaffali
 */

require_once('class.scf.php');

Class SCFInsert extends SCF {
	
	
	/**
	 * inserisce una nuove voce nelle descrizioni degli scaffali
	 */
	function createScfDesc($descriz)
	{
		$sql = "INSERT INTO scf_desc(descriz) VALUES ('".$descriz."') ";
		$q = mysql_query($sql) 
		or die(__CLASS__.'-'.__FUNCTION__." - errore nell'inserimento sql ".mysql_error()." - STATEMENT : $sql");
		
		$lastInsert = mysql_insert_id();
		
		return $lastInsert;
	}
	
	
	
	
	/**
	 * aggiorno il db scaffali inserendo i nuovi codici articoli con le associazioni 
	 * scaffali - metri
	 */
	function update($aPost, &$errors)
	{
		$scfSector = $aPost['scfSector'];
		$scfMt = $aPost['scfMt'];
		$scfNewDesc = trim($aPost['scfNewDesc']);
		$scfEraseBefore = (isset($aPost['scfEraseBefore']))?true:false;
		$scfUnlinkExists = (isset($aPost['scfUnlinkExists']))?true:false;
		$codArts = $aPost['inScf'];
		
		/*
		 * se scfNewDesc non è vuoto allora considero un inserimento di nuovo scaffale
		 */
		if(!empty($scfNewDesc)) {
			
			$scfSector = $this->createScfDesc($scfNewDesc);
			if(SPIGAXDBG) error_log(__CLASS__.'::'.__FUNCTION__." : inserito nuova descrizione scaffale $scfNewDesc con id $scfSector");
			
		}
		
		// creo nuova associazione scaffale-metro
		$assocId = $this->createScfMt($scfSector, $scfMt);
		
		// se richiesta la pulizia del metro di scaffale lo effettuo ora
		if($scfEraseBefore) {
			
			$q = mysql_query("DELETE FROM scf_codarts WHERE scf_assoc_id='".$assocId."' ");
			
		}
		
		
		foreach($codArts as $codArt) {
			
			$codArt = trim($codArt);
			if(empty($codArt)) continue;
			
			// se richiesto l'unlink dell'articolo se presente su altri scaffali lo effettuo ora
			if($scfUnlinkExists) {

				$q = mysql_query("DELETE FROM scf_codarts WHERE codart='".$codArt."' AND scf_assoc_id <> '".$assocId."' ");			
				
			}
			
			// se l'articolo esiste già in scaffale non lo inserisco
			$q = mysql_query("SELECT id FROM scf_codarts WHERE codart='".$codArt."' AND scf_assoc_id='".$assocId."' ");
			if(mysql_num_rows($q)) continue;
			
			$q = mysql_query("INSERT INTO scf_codarts (codart, scf_assoc_id, fl_mv, docven_date) VALUES('".$codArt."', '".$assocId."', 'V', '')");
			
		}
	}
	
	
	
	
	/**
	 * creao una nuova associazione, se non esiste, tra scaffale e metro
	 * @return string $assocId id dell metro-scaffale creato
	 */
	function createScfMt($scfId, $scfMt)
	{
		$q = mysql_query("SELECT id, mt FROM scf_assoc WHERE scf_desc_id ='".$scfId."' AND mt='".$scfMt."' ");
		
		if(!mysql_num_rows($q)) {
			
			$q = mysql_query("INSERT INTO scf_assoc(mt, scf_desc_id, data) VALUES('".$scfMt."', '".$scfId."', '')");
			$assocId = mysql_insert_id();
			
		} else {
			
			$row = mysql_fetch_assoc($q);
			$assocId = $row['id'];
		}
		
		return $assocId;
	}
	
	
}
?>
