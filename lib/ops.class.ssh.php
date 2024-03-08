<?php

class Ops_class_ssh 
{
    public function SshCmd($host, $login, $mdp, $command)
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
    public function ScpPut($host, $login, $mdp, $src, $dest)
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
    public function ScpGet($host, $login, $mdp, $src, $dest)
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
    public function IdRsaSshCmd($host, $login, $id_rsa_pub, $id_rsa, $command)
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
    public function IdRsaScpPut($host, $login, $id_rsa_pub, $id_rsa, $src, $dest)
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
    public function IdRsaScpGet($host, $login, $id_rsa_pub, $id_rsa, $src, $dest)
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
    public function ProxySshCmd($proxy,$host, $login, $mdp, $command)
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
    public function ProxyScpPut($proxy, $host, $login, $mdp, $src, $dest)
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
    public function ProxyScpGet($proxy, $host, $login, $mdp, $src, $dest)
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
    public function IdRsaProxySshCmd($proxy,$host, $login, $id_rsa_pub, $id_rsa, $command)
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
    public function IdRsaProxyScpPut($proxy, $host, $login, $id_rsa_pub, $id_rsa, $src, $dest)
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
    public function IdRsaProxyScpGet($proxy, $host, $login, $id_rsa_pub, $id_rsa, $src, $dest)
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
    public function __call($method, $arguments)
    {
        if($method == 'ssh_command')
        {
            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'SshCmd'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'IdRsaSshCmd'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'ssh_command_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'ProxySshCmd'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'IdRsaProxySshCmd'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'scp_put')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'ScpPut'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'IdRsaScpPut'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'scp_put_with_proxyjump')
        {

            if(count($arguments) == 6)
            {
                return call_user_func_array(array($this,'ProxyScpPut'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'IdRsaProxyScpPut'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'scp_get')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'ScpGet'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'IdRsaScpGet'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'scp_get_with_proxyjump')
        {

            if(count($arguments) == 6)
            {
                return call_user_func_array(array($this,'ProxyScpGet'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'IdRsaProxyScpGet'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else 
        {
            return "Unknow method";
        }
    }


}

?>