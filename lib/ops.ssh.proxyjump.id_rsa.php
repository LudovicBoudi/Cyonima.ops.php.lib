<?php
// set of ssh functions with ProxyJump and id_rsa auth
function IdRsaProxySshCmd($proxy,$host, $login, $id_rsa_pub, $id_rsa, $command)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($proxy, 22))) 
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
				$tunnel = ssh2_tunnel($con, $host, 22);
				if(!($stream = ssh2_exec($tunnel, $command)))
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

function IdRsaProxyScpPut($proxy, $host, $login, $id_rsa_pub, $id_rsa, $src, $dest)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($proxy, 22))) 
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
				$tunnel = ssh2_tunnel($con, $host, 22);
				ssh2_scp_send($tunnel, $src, $dest, 0644);
				return 0;
			}
		}
}

function IdRsaProxyScpGet($proxy, $host, $login, $id_rsa_pub, $id_rsa, $src, $dest)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($proxy, 22))) 
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
				$tunnel = ssh2_tunnel($con, $host, 22);
				ssh2_scp_recv($tunnel, $src, $dest);
				return 0;
			}
		}
}
?>