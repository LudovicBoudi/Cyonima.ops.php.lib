<?php

declare(strict_types=1);

/**
 * Example usage of the Cyonima OPS PHP Library
 * 
 * This file demonstrates various use cases for the library
 */

require_once 'vendor/autoload.php';

use Cyonima\Ops\Linux\UbuntuOps;
use Cyonima\Ops\Network\CiscoOps;
use Cyonima\Ops\Helper\UtilityHelper;
use Cyonima\Ops\Exception\{ConnectionException, AuthenticationException, ExecutionException};

// ============================================================================
// Example 1: Basic Ubuntu package installation
// ============================================================================

echo "=== Example 1: Ubuntu Package Installation ===\n";

$ubuntu = new UbuntuOps();
$ubuntu->setHost('192.168.1.10')
    ->setCredentials('ubuntu', 'password')
    ->setSshPort(22);

try {
    $ubuntu->openConnection();
    echo "Connected to Ubuntu server\n";
    
    // Install nginx package
    $output = $ubuntu->installPackage('nginx');
    echo "Installation output:\n" . $output;
    
    // Check service status
    $output = $ubuntu->systemctl('nginx', 'status');
    echo "Service status:\n" . $output;
    
    $ubuntu->closeConnection();
    echo "Disconnected\n";
} catch (ConnectionException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage() . "\n";
} catch (ExecutionException $e) {
    echo "Command execution failed: " . $e->getMessage() . "\n";
}

// ============================================================================
// Example 2: RSA Key Authentication
// ============================================================================

echo "\n=== Example 2: RSA Key Authentication ===\n";

$ubuntu2 = new UbuntuOps();
$ubuntu2->setHost('192.168.1.20')
    ->setRsaAuthentication(
        'ubuntu',
        '/home/user/.ssh/id_rsa',
        '/home/user/.ssh/id_rsa.pub'
    );

try {
    $ubuntu2->openConnection();
    echo "Connected via RSA key\n";
    
    // Execute a command
    $output = $ubuntu2->remoteExec('uname -a');
    echo "System info:\n" . $output;
    
    $ubuntu2->closeConnection();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ============================================================================
// Example 3: Using Proxy/Jump Host
// ============================================================================

echo "\n=== Example 3: Connection via Proxy Host ===\n";

$ubuntu3 = new UbuntuOps();
$ubuntu3->setProxy('jump.example.com')  // Jump host
    ->setHost('192.168.1.30')            // Final target
    ->setCredentials('ubuntu', 'password');

try {
    $ubuntu3->openConnection();
    echo "Connected via proxy\n";
    
    $output = $ubuntu3->remoteExec('hostname');
    echo "Hostname: " . trim($output) . "\n";
    
    $ubuntu3->closeConnection();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ============================================================================
// Example 4: File Transfer
// ============================================================================

echo "\n=== Example 4: File Transfer ===\n";

$ubuntu4 = new UbuntuOps();
$ubuntu4->setHost('192.168.1.40')
    ->setCredentials('ubuntu', 'password');

try {
    $ubuntu4->openConnection();
    
    // Send file to remote server
    $ubuntu4->scpPut('/local/config.txt', '/tmp/config.txt', '0644');
    echo "File uploaded successfully\n";
    
    // Receive file from remote server
    $ubuntu4->scpGet('/tmp/backup.tar.gz', '/local/backup.tar.gz');
    echo "File downloaded successfully\n";
    
    $ubuntu4->closeConnection();
} catch (ExecutionException $e) {
    echo "Transfer failed: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ============================================================================
// Example 5: Cisco Network Equipment Configuration
// ============================================================================

echo "\n=== Example 5: Cisco Configuration ===\n";

$cisco = new CiscoOps();
$cisco->setHost('10.0.0.10')
    ->setCredentials('admin', 'password');

try {
    $cisco->openConnection();
    echo "Connected to Cisco device\n";
    
    // Configure a VLAN
    $output = $cisco->configureVlan('10', 'Management', 'Gi0/1');
    echo "VLAN configured\n";
    
    // Set IP on VLAN interface
    $cisco->setVlanIpAddress('10', 'Management', '10.0.0.1', '255.255.255.0');
    
    // Add a route
    $cisco->addRoute('192.168.0.0', '255.255.0.0', '10.0.0.254', '1');
    
    $cisco->closeConnection();
    echo "Configuration complete\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ============================================================================
// Example 6: User Management
// ============================================================================

echo "\n=== Example 6: User Management ===\n";

$ubuntu5 = new UbuntuOps();
$ubuntu5->setHost('192.168.1.50')
    ->setCredentials('ubuntu', 'password');

try {
    $ubuntu5->openConnection();
    
    // Add user with privilege elevation
    $output = $ubuntu5->addUserWithPrivilege('newuser', 'SecurePass123!', 'sudopass');
    echo "User created\n";
    
    // Change password
    $ubuntu5->changePassword('newuser', 'NewPassword456!');
    echo "Password changed\n";
    
    // Delete user
    $ubuntu5->deleteUser('newuser');
    echo "User deleted\n";
    
    $ubuntu5->closeConnection();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ============================================================================
// Example 7: Utility Helper Functions
// ============================================================================

echo "\n=== Example 7: Utility Helper Functions ===\n";

// Check password strength
$password = 'MySecureP@ss123';
if (UtilityHelper::checkPasswordStrength($password)) {
    echo "Password is strong\n";
} else {
    echo "Password is weak\n";
}

// File operations
try {
    UtilityHelper::seekAndReplace('old_text', 'new_text', '/path/to/file.txt');
    echo "File updated\n";
} catch (\RuntimeException $e) {
    echo "File operation failed: " . $e->getMessage() . "\n";
}

echo "\n=== All examples completed ===\n";
