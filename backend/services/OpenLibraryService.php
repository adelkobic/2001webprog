<?php

class OpenLibraryService {
    public function coverForIsbn($isbn) {
        $clean = preg_replace('/[^0-9Xx]/', '', (string) $isbn);
        $config = Flight::get('config');
        $timeout = (int) ($config['openlibrary_timeout'] ?? 5);
        if ($timeout < 1) $timeout = 5;

        $cachePath = __DIR__ . '/../cache/' . hash('sha256', $clean) . '.json';
        $cached = $this->readCache($cachePath);
        if ($cached !== null) return $cached;

        $url = 'https://openlibrary.org/api/books?bibkeys=ISBN:' . rawurlencode($clean) . '&format=json&jscmd=data';
        $result = $this->fetch($url, $timeout);
        if ($result['error'] !== '') {
            Logger::error('OpenLibrary isbn=' . $clean . ' ' . $result['error']);
            $response = ['cover_url' => $this->coverUrl($clean), 'message' => ''];
            $this->writeCache($cachePath, $response, $config);
            return $response;
        }
        $data = json_decode($result['body'], true);
        if (!is_array($data)) {
            Logger::error('OpenLibrary isbn=' . $clean . ' invalid response.');
            $response = ['cover_url' => $this->coverUrl($clean), 'message' => ''];
            $this->writeCache($cachePath, $response, $config);
            return $response;
        }
        $cover = null;
        $key = 'ISBN:' . $clean;
        if (isset($data[$key]['cover']['medium'])) $cover = $data[$key]['cover']['medium'];

        Logger::info('OpenLibrary isbn=' . $clean . ' ' . ($cover ? 'ok' : 'no cover'));
        if (!$cover) {
            $response = ['cover_url' => $this->coverUrl($clean), 'message' => ''];
            $this->writeCache($cachePath, $response, $config);
            return $response;
        }
        $response = ['cover_url' => $cover, 'message' => ''];
        $this->writeCache($cachePath, $response, $config);
        return $response;
    }

    private function coverUrl($isbn) {
        return 'https://covers.openlibrary.org/b/isbn/' . rawurlencode($isbn) . '-M.jpg?default=false';
    }

    private function readCache($path) {
        if (!is_file($path)) return null;
        $contents = file_get_contents($path);
        if ($contents === false) return null;
        $cached = json_decode($contents, true);
        if (!is_array($cached) || !isset($cached['expires_at'], $cached['response'])
            || (int) $cached['expires_at'] <= time()
            || !is_array($cached['response'])
            || !array_key_exists('cover_url', $cached['response'])
            || !is_string($cached['response']['cover_url'])
            || !array_key_exists('message', $cached['response'])) {
            return null;
        }
        return $cached['response'];
    }

    private function writeCache($path, $response, $config) {
        $ttl = (int) ($config['openlibrary_cache_ttl'] ?? 604800);
        if ($ttl < 1) return;
        $directory = dirname($path);
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) return;
        $contents = json_encode(['expires_at' => time() + $ttl, 'response' => $response]);
        if ($contents === false || file_put_contents($path, $contents, LOCK_EX) === false) {
            Logger::error('Could not write OpenLibrary cover cache.');
        }
    }

    private function fetch($url, $timeout) {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $body = curl_exec($ch);
            $error = curl_error($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($body === false || $code >= 400) {
                return ['body' => '', 'error' => $error !== '' ? $error : ('HTTP ' . $code)];
            }
            return ['body' => $body, 'error' => ''];
        }
        $context = stream_context_create(['http' => ['timeout' => $timeout, 'ignore_errors' => true]]);
        $body = @file_get_contents($url, false, $context);
        if ($body === false) return ['body' => '', 'error' => 'Could not reach Open Library.'];
        $statusLine = $http_response_header[0] ?? '';
        if (preg_match('/\s([45]\d\d)\s/', $statusLine, $matches)) {
            return ['body' => '', 'error' => 'HTTP ' . $matches[1]];
        }
        return ['body' => $body, 'error' => ''];
    }
}
