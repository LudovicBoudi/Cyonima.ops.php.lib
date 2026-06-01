<?php

declare(strict_types=1);

namespace Cyonima\Ops\Windows;

use Cyonima\Ops\Exception\ConnectionException;
use Cyonima\Ops\Exception\ExecutionException;
use Cyonima\Ops\InputValidator;
use Cyonima\Ops\Logger\SimpleLogger;
use Cyonima\Ops\RemoteCommandOutput;
use Psr\Log\LoggerInterface;

/**
 * Minimal WinRM client using SOAP over HTTP(S)
 */
class WinRmClient
{
    private string $endpoint;
    private string $host;
    private int $port;
    private bool $useHttps;
    private string $username;
    private string $password;
    private LoggerInterface $logger;
    private string $resourceUri = 'http://schemas.microsoft.com/wbem/wsman/1/windows/shell/cmd';

    public function __construct(
        string $host,
        string $username,
        string $password,
        int $port = 5985,
        bool $useHttps = false,
        ?LoggerInterface $logger = null
    ) {
        InputValidator::validateHost($host);
        InputValidator::validateUsername($username);
        InputValidator::validatePassword($password, 1);
        InputValidator::validatePort($port);

        if (!function_exists('curl_init')) {
            throw new ConnectionException('cURL extension is required for WinRM support.');
        }

        $this->host = $host;
        $this->port = $port;
        $this->useHttps = $useHttps;
        $this->username = $username;
        $this->password = $password;
        $this->logger = $logger ?? new SimpleLogger();
        $protocol = $useHttps ? 'https' : 'http';
        $this->endpoint = sprintf('%s://%s:%d/wsman', $protocol, $host, $port);
    }

    public function executePowerShell(string $script): RemoteCommandOutput
    {
        $command = $this->buildPowerShellCommand($script);
        return $this->executeCommand($command);
    }

    public function executeCommand(string $command): RemoteCommandOutput
    {
        $shellId = $this->createShell();
        try {
            $commandId = $this->sendCommand($shellId, $command);
            return $this->receiveOutput($shellId, $commandId);
        } finally {
            $this->deleteShell($shellId);
        }
    }

    protected function createShell(): string
    {
        $body = <<<XML
<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
            xmlns:w="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd"
            xmlns:rsp="http://schemas.microsoft.com/wbem/wsman/1/windows/shell">
  <s:Header>
    <w:ResourceURI s:mustUnderstand="true">{$this->resourceUri}</w:ResourceURI>
    <w:OperationTimeout>PT60S</w:OperationTimeout>
    <w:OptionSet>
      <w:Option Name="WINRS_NOPROFILE">TRUE</w:Option>
      <w:Option Name="WINRS_SKIP_CMD_SHELL">FALSE</w:Option>
    </w:OptionSet>
  </s:Header>
  <s:Body>
    <rsp:Shell>
      <rsp:InputStreams>stdin</rsp:InputStreams>
      <rsp:OutputStreams>stdout stderr</rsp:OutputStreams>
    </rsp:Shell>
  </s:Body>
</s:Envelope>
XML;

        $response = $this->request($body, 'http://schemas.xmlsoap.org/ws/2004/09/transfer/Create');
        $shellId = $this->findXmlValue($response, '//rsp:ShellId');

        if ($shellId === '') {
            throw new ExecutionException('Failed to create WinRM shell.');
        }

        return $shellId;
    }

    protected function sendCommand(string $shellId, string $command): string
    {
        $body = <<<XML
<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
            xmlns:w="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd"
            xmlns:rsp="http://schemas.microsoft.com/wbem/wsman/1/windows/shell">
  <s:Header>
    <w:ResourceURI s:mustUnderstand="true">{$this->resourceUri}</w:ResourceURI>
    <w:OperationTimeout>PT60S</w:OperationTimeout>
    <w:SelectorSet>
      <w:Selector Name="ShellId">{$shellId}</w:Selector>
    </w:SelectorSet>
  </s:Header>
  <s:Body>
    <rsp:CommandLine>
      <rsp:Command>powershell.exe</rsp:Command>
      <rsp:Arguments>-NoProfile -NonInteractive -ExecutionPolicy Bypass -EncodedCommand {$this->encodeToEncodedCommand($command)}</rsp:Arguments>
    </rsp:CommandLine>
  </s:Body>
</s:Envelope>
XML;

        $response = $this->request($body, 'http://schemas.microsoft.com/wbem/wsman/1/windows/shell/Command');
        $commandId = $this->findXmlValue($response, '//rsp:CommandId');

        if ($commandId === '') {
            throw new ExecutionException('Failed to send WinRM command.');
        }

        return $commandId;
    }

    protected function receiveOutput(string $shellId, string $commandId): RemoteCommandOutput
    {
        $body = <<<XML
<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
            xmlns:w="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd"
            xmlns:rsp="http://schemas.microsoft.com/wbem/wsman/1/windows/shell">
  <s:Header>
    <w:ResourceURI s:mustUnderstand="true">{$this->resourceUri}</w:ResourceURI>
    <w:OperationTimeout>PT60S</w:OperationTimeout>
    <w:SelectorSet>
      <w:Selector Name="ShellId">{$shellId}</w:Selector>
    </w:SelectorSet>
  </s:Header>
  <s:Body>
    <rsp:Receive>
      <rsp:DesiredStream>stdout stderr</rsp:DesiredStream>
    </rsp:Receive>
  </s:Body>
</s:Envelope>
XML;

        $response = $this->request($body, 'http://schemas.microsoft.com/wbem/wsman/1/windows/shell/Receive');
        $stdout = $this->collectStream($response, 'stdout');
        $stderr = $this->collectStream($response, 'stderr');
        $exitCode = $this->findExitCode($response);

        return new RemoteCommandOutput($stdout, $stderr, $exitCode);
    }

    protected function deleteShell(string $shellId): void
    {
        $body = <<<XML
<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope"
            xmlns:w="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd">
  <s:Header>
    <w:ResourceURI s:mustUnderstand="true">{$this->resourceUri}</w:ResourceURI>
    <w:SelectorSet>
      <w:Selector Name="ShellId">{$shellId}</w:Selector>
    </w:SelectorSet>
  </s:Header>
  <s:Body>
    <w:Delete/>
  </s:Body>
</s:Envelope>
XML;

        try {
            $this->request($body, 'http://schemas.xmlsoap.org/ws/2004/09/transfer/Delete');
        } catch (ExecutionException $e) {
            $this->logger->warning('WinRM shell cleanup failed: {error}', ['error' => $e->getMessage()]);
        }
    }

    protected function request(string $body, string $action): \SimpleXMLElement
    {
        $this->logger->debug('WinRM request to {endpoint} action {action}', [
            'endpoint' => $this->endpoint,
            'action' => $action,
        ]);

        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/soap+xml;charset=UTF-8',
            'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password),
            'User-Agent: Cyonima-WinRM',
            'Expect:',
        ]);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $errno !== 0 || $status >= 400) {
            throw new ConnectionException(sprintf('WinRM request failed (HTTP %d): %s', $status, $error));
        }

        $xml = @simplexml_load_string($response);
        if ($xml === false) {
            throw new ExecutionException('Unable to parse WinRM XML response.');
        }

        $xml->registerXPathNamespace('rsp', 'http://schemas.microsoft.com/wbem/wsman/1/windows/shell');

        return $xml;
    }

    protected function findXmlValue(\SimpleXMLElement $xml, string $path): string
    {
        $results = $xml->xpath($path);
        return $results && isset($results[0]) ? trim((string) $results[0]) : '';
    }

    protected function collectStream(\SimpleXMLElement $xml, string $streamName): string
    {
        $streams = $xml->xpath(sprintf('//rsp:Stream[@Name="%s"]', $streamName));
        if (!$streams) {
            return '';
        }

        $output = '';
        foreach ($streams as $stream) {
            $output .= (string) $stream;
        }

        $decoded = base64_decode(trim($output), true);
        if ($decoded !== false) {
            return $decoded;
        }

        return trim($output);
    }

    protected function findExitCode(\SimpleXMLElement $xml): int
    {
        $states = $xml->xpath('//rsp:CommandState');
        if ($states && isset($states[0]['ExitCode'])) {
            return (int) $states[0]['ExitCode'];
        }

        return 0;
    }

    protected function buildPowerShellCommand(string $script): string
    {
        $encoded = $this->encodeToEncodedCommand($script);
        return 'powershell.exe -NoProfile -NonInteractive -ExecutionPolicy Bypass -EncodedCommand ' . $encoded;
    }

    protected function encodeToEncodedCommand(string $script): string
    {
        if (function_exists('mb_convert_encoding')) {
            $utf16 = mb_convert_encoding($script, 'UTF-16LE', 'UTF-8');
        } elseif (function_exists('iconv')) {
            $utf16 = iconv('UTF-8', 'UTF-16LE', $script);
        } else {
            throw new ExecutionException('No available encoding extension for WinRM PowerShell encoding.');
        }

        return base64_encode($utf16);
    }
}
