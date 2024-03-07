<?php
function SeekAndReplace($Search,$Replace,$fichier)
{
	$contenu = str_replace($Search, $Replace, file_get_contents($fichier)); 
	file_put_contents($fichier, $contenu); 
}
?>