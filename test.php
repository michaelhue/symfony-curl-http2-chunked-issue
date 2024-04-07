<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

/**
 * Run the tests for HTTP/1 and HTTP/2 with the default (erroneous) behavior.
 *
 * The test for HTTP/2 will fail because CurlHttpClient will force chunked
 * transfer encoding, which is not supported for HTTP/2.
 */
test('HTTP/1 default behavior', fn () => request(1.1, false));
test('HTTP/2 default behavior', fn () => request(2.0, false));

/**
 * Repeat the tests, but this time we will explicitly set the 'transfer-encoding'
 * header to NULL. Doing this will pevent CurlHttpClient from setting the header
 * itself for chunked requests (line 254-256).
 *
 * This means that CURL will handle 'transfer-encoding' header itself if
 * necessary, based on the HTTP protocol it negotiated with the server.
 *
 * Both tests should pass.
 */
test('HTTP/1 suppressed header', fn () => request(1.1, true));
test('HTTP/2 suppressed header', fn () => request(2.0, true));


/**
 * Make a request to the test server using CurlHttpClient. Returns the content
 * of the response.
 */
function request(float $httpVersion, bool $suppressHeader = false): string
{
	$headers = [];

	// Use form data body to generate a chunked request.
	$body = new FormDataPart(['foo' => 'bar']);

	// Add form data headers to request.
	foreach ($body->getPreparedHeaders()->all() as $header) {
		$headers[] = $header->toString();
	}

	// Setting the transfer-encoding header to NULL will prevent CurlHttpClient
	// from adding a 'transfer-encoding: chunked' header to the request.
	if ($suppressHeader) {
		$headers['transfer-encoding'] = null;
	}

	$client = new CurlHttpClient();

	// Send the request.
	$response = $client->request('POST', 'https://localhost:8000', [
		'http_version' => $httpVersion,

		'headers' => $headers,
		'body' => $body->toIterable(),

		// Allow self-signed certificates
		'verify_peer' => false,
	]);

	// Return the server response.
	return $response->getContent();
}

/**
 * Test helper that will run the given callable and check if it returns the
 * correct value, while generating somewhat readable console output.
 */
function test(string $name, callable $fn, $expect = 'bar')
{
	try {
		$result = $fn();
	} catch (Throwable $e) {
		echo "❌ {$name}\n";
		echo "  {$e->getMessage()}\n";
		return;
	}

	if ($result === $expect) {
		echo "✅ {$name}\n";
	} else {
		echo "❌ {$name}\n";
		echo "  expected: {$expect}\n";
		echo "  actual: {$result}\n";
	}
}
