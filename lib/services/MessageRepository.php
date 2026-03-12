<?php
/**
 * GatewayOS2 Website - Message Repository
 *
 * Manages contact form messages stored as individual JSON files
 * in the data/messages/ directory.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/helpers/JsonStore.php';

class MessageRepository
{
    /**
     * Get the messages directory path.
     *
     * @return string
     */
    private static function dir(): string
    {
        $dir = defined('MESSAGES_DIR') ? MESSAGES_DIR : BASE_DIR . '/data/messages';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * Return all messages sorted by date descending.
     *
     * @return array List of message records.
     */
    public static function all(): array
    {
        $dir = self::dir();
        $files = glob($dir . '/*.json');

        if (empty($files)) {
            return [];
        }

        $messages = [];
        foreach ($files as $file) {
            $data = JsonStore::read($file);
            if (!empty($data)) {
                $messages[] = $data;
            }
        }

        // Sort by created_at descending
        usort($messages, function ($a, $b) {
            return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
        });

        return $messages;
    }

    /**
     * Find a message by its ID.
     *
     * @param string $id Message ID.
     * @return array|null Message record or null.
     */
    public static function find(string $id): ?array
    {
        $dir = self::dir();
        $files = glob($dir . '/*.json');

        foreach ($files as $file) {
            $data = JsonStore::read($file);
            if (!empty($data) && ($data['id'] ?? '') === $id) {
                return $data;
            }
        }

        return null;
    }

    /**
     * Create a new contact message.
     *
     * Saves as an individual JSON file named with the date and ID.
     *
     * @param array $data Message fields (name, email, subject, message, etc.)
     * @return array The created message record.
     */
    public static function create(array $data): array
    {
        $msg = array_merge([
            'id'         => bin2hex(random_bytes(8)),
            'ip'         => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'created_at' => date('c'),
            'read'       => false,
        ], $data);

        $filename = self::dir() . '/' . date('Y-m-d_His') . '_' . $msg['id'] . '.json';
        JsonStore::write($filename, $msg);

        return $msg;
    }

    /**
     * Mark a message as read.
     *
     * @param string $id Message ID.
     * @return bool True if the message was found and updated.
     */
    public static function markRead(string $id): bool
    {
        $dir = self::dir();
        $files = glob($dir . '/*.json');

        foreach ($files as $file) {
            $data = JsonStore::read($file);
            if (!empty($data) && ($data['id'] ?? '') === $id) {
                $data['read'] = true;
                JsonStore::write($file, $data);
                return true;
            }
        }

        return false;
    }

    /**
     * Delete a message by ID.
     *
     * @param string $id Message ID.
     * @return bool True if the message file was found and removed.
     */
    public static function delete(string $id): bool
    {
        $dir = self::dir();
        $files = glob($dir . '/*.json');

        foreach ($files as $file) {
            $data = JsonStore::read($file);
            if (!empty($data) && ($data['id'] ?? '') === $id) {
                return unlink($file);
            }
        }

        return false;
    }

    /**
     * Count unread messages.
     *
     * @return int Number of unread messages.
     */
    public static function unreadCount(): int
    {
        $messages = self::all();
        $count = 0;

        foreach ($messages as $msg) {
            if (empty($msg['read'])) {
                $count++;
            }
        }

        return $count;
    }
}
