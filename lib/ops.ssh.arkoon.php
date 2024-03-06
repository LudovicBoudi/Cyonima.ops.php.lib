<?php
// specific ssh command for arkoon firewalls

function ArkScpPut($host, $login, $mdp, $src, $dest)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($host, 822))) 
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

function ArkScpGet($host, $login, $mdp, $src, $dest)
{
	if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
	if (!($con = ssh2_connect($host, 822))) 
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