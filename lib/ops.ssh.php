<?php
// set of ssh functions
function SshCmd($host, $login, $mdp, $command)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($host, 22))) 
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

function ScpPut($host, $login, $mdp, $src, $dest)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($host, 22))) 
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
				ssh2_scp_send($con, $src, $dest, 0644);
				return 0;
			}
		}
}

function ScpGet($host, $login, $mdp, $src, $dest)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($host, 22))) 
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
				ssh2_scp_recv($con, $src, $dest);
				return 0;
			}
		}
}
?>