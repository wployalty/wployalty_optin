<?php
/**
 * @author      Wployalty (Roshan Britto)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Model;

use \Wlopt\App\Model\Model;
defined( 'ABSPATH' ) or die();

class Users extends Model
{
    /**
     * Table name and output type
     *
     * @var string
     */
    const TABLE_NAME = 'optin_users', OUTPUT_TYPE = ARRAY_A;

    /**
     * Create opt_in_users table
     */
    public function create()
    {
        $query = "CREATE TABLE {table} (
                 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                 `user_email` varchar(180) DEFAULT NULL,
                 `wp_user_id` bigint(20) unsigned NOT NULL,    
                 `wlr_user_id` bigint(20) unsigned NOT NULL,
                 `optin_status` BIGINT DEFAULT 1,
                PRIMARY KEY (id),
                UNIQUE (user_email)
            ) {charset_collate};";

        self::execDBQuery($query); // to create or update table
    }

    /**
     * Get opt-in data.
     *
     * @param $user_email
     * @return array|false
     */
    public static function getOptinData($user_email) {
        if ( empty( $user_email ) ) {
            return false;
        }

        if (is_email( $user_email )) {
            $user_data = self::getRow( ['user_email' => $user_email], ['%s'] );
            if (empty($user_data)) {
                return 'no_data';
            }
            return $user_data;
        }
        return false;
    }

    /**
     * Get user optin status.
     *
     * @param $user_id_or_email
     * @return bool|string
     */
    public static function getUserOptinStatus( $user_id_or_email )
    {
        if ( empty( $user_id_or_email ) ) {
            return false;
        }

        if (is_numeric( $user_id_or_email )) {
            $user_data = self::getRow( ['wp_user_id' => $user_id_or_email], ['%d'] );
            if (empty($user_data)) {
                return 'no_data';
            }
            return !empty($user_data['optin_status']);
        }

        if (is_email( $user_id_or_email )) {
            $user_data = self::getRow( ['user_email' => $user_id_or_email], ['%s'] );
            if (empty($user_data)) {
                return 'no_data';
            }
            return !empty($user_data['optin_status']);
        }
        return 'no_data';
    }

    /**
     * To save optin user status
     *
     * @param $user
     * @return bool
     */
    public static function save( $user )
    {
        if (empty($user) || !is_email($user['user_email'])) {
            return false;
        }

        $optin_users_table = self::getTableName();

        $exists_user_id = self::getScalar(
            self::prepareQuery("SELECT id FROM $optin_users_table WHERE user_email = %s", [$user['user_email']])
        );

        $data = [
            'user_email' => $user['user_email'],
            'wp_user_id' => !empty($user['wp_user_id']) ? (int)$user['wp_user_id'] : 0,
            'wlr_user_id' => !empty($user['wlr_user_id']) ? (int)$user['wlr_user_id'] : 0,
            'optin_status' => (int)$user['optin_status'] ?? 0,
        ];
        $format = ['%s', '%d', '%d', '%d'];

        if (empty($exists_user_id)) {
            if (!($user['id'] = self::insert($data, $format))) {
                return false;
            }
        } else {
            if (!self::updateById((int)$exists_user_id, $data, $format)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get users details.
     *
     * @param $user_status
     * @param $page_no
     * @param $per_limit
     * @param $like_args
     * @return array|null
     */
    public static function getUsersDetails( $user_status, $page_no, $per_limit, $like_args = '' ) {
        $wlr_users_table = 'wp_wlr_users';
        $optin_users_table = self::getTableName();
        $offset = ( $page_no - 1 ) * $per_limit;
        $where_clauses = ["ou.optin_status = {$user_status}"];
        if ( !empty( $like_args ) ) {
            $like = '%' . self::db()->esc_like( $like_args ) . '%';
            $where_clauses[] = "(ou.user_email LIKE '{$like}')";
        }

        $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

        $query = "
            SELECT
                ou.*, 
                COALESCE(lu.used_total_points, 'N/A') AS used_total_points, 
                COALESCE(lu.earn_total_point, 'N/A') AS earn_total_point,
                COALESCE(lu.points, 'N/A') AS points
            FROM {$optin_users_table} AS ou
            LEFT JOIN {$wlr_users_table} AS lu ON ou.user_email = lu.user_email
            {$where_sql}
            LIMIT {$per_limit} OFFSET {$offset}
        ";
        return self::getResults( $query );
    }

    /**
     * To get users count
     *
     * @param $user_status
     * @return string|null
     */
    public static function totalUsersCount( $user_status )
    {
        return self::getScalar("SELECT COUNT(`id`) FROM {table} WHERE `optin_status` = %d ", [$user_status]);
    }

}