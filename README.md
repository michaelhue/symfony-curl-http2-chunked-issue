# CurlHttpClient HTTP/2 chunked issue

## Setup

Clone the repository and install dependencies:

```bash
$ git clone https://github.com/michaelhue/symfony-curl-http2-chunked-issue.git
$ cd symfony-curl-http2-chunked-issue
$ composer install
```

The repo contains a HTTP/2 capable test server for [Deno](https://deno.com). You need to [install Deno](https://docs.deno.com/runtime/manual#install-deno) before running the tests.

## Usage

First, start the HTTP server with Deno:

```bash
$ deno run --allow-net server.ts
```

Wait until the server is ready and then run the tests:

```bash
$ php test.php
```

The console output will look like this:

```
✅ HTTP/1 default behavior
❌ HTTP/2 default behavior
  expected: 'bar'
  actual: 2


3
bar
2


e
✅ HTTP/1 suppressed header
✅ HTTP/2 suppressed header
```
