<?php
// set of ssh functions with openssh key authentification
function IdRsaSshCmd($host, $login, $id_rsa_pub, $id_rsa, $command)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($host, 22))) 
		{
			return "connection failed";
		}
	else
		{
			if (!ssh2_auth_pubkey_file($con, $login, $id_rsa_pub, $id_rsa))
			{
				return "authentication failed";
			}
			else
			{
				if(!($stream = ssh2_exec($con, $command)))
				{
					return "shell command line failed";
				}
				else
				{
					stream_set_blocking($stream, true);
					$data ="";
					while ($buf = fread($stream, 4096))
					{
						$data .= $buf;						
					}
				fclose($stream);
				return $data;
				}
			}
		}
}

function IdRsaScpPut($host, $login, $id_rsa_pub, $id_rsa, $src, $dest)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($host, 22))) 
		{
			return "connection failed";
		}
	else
		{
			if (!ssh2_auth_pubkey_file($con, $login, $id_rsa_pub, $id_rsa))
			{
				return "authentication failed";
			}
			else
			{
				ssh2_scp_send($con, $src, $dest, 0644);
				return 0;
			}
		}
}

function IdRsaScpGet($host, $login, $id_rsa_pub, $id_rsa, $src, $dest)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($host, 22))) 
		{
			return "connection failed";
		}
	else
		{
			if (!ssh2_auth_pubkey_file($con, $login, $id_rsa_pub, $id_rsa))
			{
				return "authentication failed";
			}
			else
			{
				ssh2_scp_recv($con, $src, $dest);
				return 0;
			}
		}
}
?>