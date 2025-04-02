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
                PRIMARY KEY (id)
            ) {charset_collate};";

        self::execDBQuery($query); // to create or update table
    }

    /**
     * Get opt-in data.
     *
     * @param $user_email
     * @return array|false
     */
    public static function getOptionData($user_email) {
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
        if (empty($user)) {
            return false;
        }

        $id = $user['id'] ?? 0;
        $data = [
            'user_email' => $user['user_email'],
            'wp_user_id' => !empty($user['wp_user_id']) ? (int)$user['wp_user_id'] : 0,
            'wlr_user_id' => !empty($user['wlr_user_id']) ? (int)$user['wlr_user_id'] : 0,
            'optin_status' => (int)$user['optin_status'] ?? 0,
        ];
        $format = ['%s', '%d', '%d', '%d'];

        if (empty($id)) {
            if (!($user['id'] = self::insert($data, $format))) {
                return false;
            }
        } else {
            if (!self::updateById((int)$id, $data, $format)) {
                return false;
            }
        }
        return true;
    }

    public static function getUsersDetails( $user_status ) {
        $loyalty_users_table = 'wp_wlr_users';
        $where = "AS ou LEFT JOIN " . $loyalty_users_table . " AS lu ON wlr_user_id = lu.id ";
        $where .= self::db()->prepare("WHERE ou.optin_status = %d ", $user_status);
        return self::getRows($where, null);
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