<?php
// set of ssh functions with ProxyJump
function ProxySshCmd($proxy,$host, $login, $mdp, $command)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($proxy, 22))) 
		{
			return "connection failed";
		}
	else
		{
			if (!ssh2_auth_password($con, $login, $mdp))
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

function ProxyScpPut($proxy, $host, $login, $mdp, $src, $dest)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($proxy, 22))) 
		{
			return "connection failed";
		}
	else
		{
			if (!ssh2_auth_password($con, $login, $mdp))
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

function ProxyScpGet($proxy, $host, $login, $mdp, $src, $dest)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($proxy, 22))) 
		{
			return "connection failed";
		}
	else
		{
			if (!ssh2_auth_password($con, $login, $mdp))
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