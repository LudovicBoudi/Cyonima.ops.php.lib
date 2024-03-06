<?php
function ReadStdIn()
{
        $fr=fopen("php://stdin","r"); 
        $input = fgets($fr,128);  
        $input = rtrim($input); 
        fclose ($fr); 
        return $input;
}
?>