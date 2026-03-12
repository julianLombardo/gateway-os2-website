<?php
/**
 * GatewayOS2 Website - JSON File Storage
 *
 * Generic JSON read/write utility with file locking for safe
 * concurrent access. Used by all repository classes.
 */

class JsonStore
{
    /**
     * Read a JSON file and return its contents as an array.
     *
     * Uses LOCK_SH (shared lock) so multiple readers can access
     * the file simultaneously without blocking each other.
     *
     * @param string $file Absolute path to the JSON file.
     * @return array Decoded data, or empty array on failure.
     */
    public static function read(string $file): array
    {
        if (!file_exists($file)) {
            return [];
        }

        $handle = fopen($file, 'r');
        if ($handle === false) {
            return [];
        }

        flock($handle, LOCK_SH);
        $contents = stream_get_contents($handle);
        flock($handle, LOCK_UN);
        fclose($handle);

        if ($contents === false || $contents === '') {
            return [];
        }

        $data = json_decode($contents, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Write data to a JSON file with exclusive locking.
     *
     * Uses LOCK_EX (exclusive lock) to prevent race conditions.
     * Creates the parent directory if it does not exist.
     *
     * @param string $file Absolute path to the JSON file.
     * @param array  $data Data to encode and write.
     * @return bool True on success, false on failure.
     */
    public static function write(string $file, array $data): bool
    {
        $dir = dirname($file);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return false;
            }
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return false;
        }

        $handle = fopen($file, 'c');
        if ($handle === false) {
            return false;
        }

        flock($handle, LOCK_EX);
        ftruncate($handle, 0);
        rewind($handle);
        $written = fwrite($handle, $json);
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);

        return $written !== false;
    }
}
