<?php
/**
 * GatewayOS2 Website - User Repository
 *
 * CRUD operations for user records stored in users.json.
 * Uses JsonStore for thread-safe file access.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/helpers/JsonStore.php';

class UserRepository
{
    /**
     * Get the path to the users data file.
     *
     * @return string
     */
    private static function file(): string
    {
        return defined('USERS_FILE') ? USERS_FILE : BASE_DIR . '/data/users.json';
    }

    /**
     * Return all users.
     *
     * @return array
     */
    public static function all(): array
    {
        return JsonStore::read(self::file());
    }

    /**
     * Find a user by their unique ID.
     *
     * @param string $id User ID.
     * @return array|null User record or null.
     */
    public static function find(string $id): ?array
    {
        $users = self::all();
        foreach ($users as $user) {
            if ($user['id'] === $id) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Find a user by username (case-insensitive).
     *
     * @param string $username Username to search for.
     * @return array|null User record or null.
     */
    public static function findByUsername(string $username): ?array
    {
        $users = self::all();
        $lower = strtolower($username);
        foreach ($users as $user) {
            if (strtolower($user['username']) === $lower) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Find a user by email address (case-insensitive).
     *
     * @param string $email Email to search for.
     * @return array|null User record or null.
     */
    public static function findByEmail(string $email): ?array
    {
        $users = self::all();
        $lower = strtolower($email);
        foreach ($users as $user) {
            if (strtolower($user['email']) === $lower) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Create a new user record.
     *
     * Generates a unique ID and appends the user to the store.
     *
     * @param array $data User data (username, email, password_hash, etc.)
     * @return array The created user record with generated ID.
     */
    public static function create(array $data): array
    {
        $users = self::all();

        $user = array_merge([
            'id'                 => bin2hex(random_bytes(16)),
            'created_at'         => date('c'),
            'email_verified'     => false,
            'role'               => 'user',
        ], $data);

        $users[] = $user;
        JsonStore::write(self::file(), $users);

        return $user;
    }

    /**
     * Update specific fields on an existing user.
     *
     * @param string $id   User ID.
     * @param array  $data Fields to update.
     * @return array|null Updated user record or null if not found.
     */
    public static function update(string $id, array $data): ?array
    {
        $users = self::all();
        $updated = null;

        foreach ($users as &$user) {
            if ($user['id'] === $id) {
                foreach ($data as $key => $value) {
                    $user[$key] = $value;
                }
                $user['updated_at'] = date('c');
                $updated = $user;
                break;
            }
        }
        unset($user);

        if ($updated !== null) {
            JsonStore::write(self::file(), $users);
        }

        return $updated;
    }

    /**
     * Delete a user by ID.
     *
     * @param string $id User ID.
     * @return bool True if the user was found and removed.
     */
    public static function delete(string $id): bool
    {
        $users = self::all();
        $original = count($users);

        $users = array_values(array_filter($users, function ($user) use ($id) {
            return $user['id'] !== $id;
        }));

        if (count($users) < $original) {
            JsonStore::write(self::file(), $users);
            return true;
        }

        return false;
    }
}
