<?php

namespace Axilweb\AiJobListing\Helpers;
 
trait General_Settings_Query_Helpers_Trait
{

    /**
     * Retrieves general setting values by their names.
     *
     * This function fetches the values for the specified setting names from
     * the `axil_job_listing_general_settings` table and returns them as an
     * associative array.
     *
     * @since 1.0.0
     *
     * @param array|string $names The names of the settings to retrieve. Can be a string or an array of strings.
     * @return array An associative array with setting names as keys and their values as values.
     */
    public static function getGeneral_SettingValueByNames($names)
    {
        $names = is_array($names) ? $names : [$names];
        $placeholders = implode(', ', array_fill(0, count($names), '%s'));

        global $wpdb;
        //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching 
        $results = $wpdb->get_results(
            $wpdb->prepare(
                //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare 
                "select * from `$wpdb->axilweb_ajl_general_settings` where `name` in ($placeholders)", $names
            )
        );
        $data = [];
        foreach ($results as $result) {
            $data[$result->name] = $result->value;
        }
        return $data;

    }
}
