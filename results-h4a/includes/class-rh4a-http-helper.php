<?php

/**
 * Central http helper class for retrieving the data from the h4a api.
 * Includes cahcing functionalities and constants for the shortcode types.
 *
 * @package    results-h4a
 * @subpackage results-h4a/includes
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class RH4A_HTTP_Helper {

    public const LEAGUE = 'l';
    public const TEAM = 't';
    public const CLUB = 'c';
    public const STANDING = 's';

    private const MAX_CACHE_TIMEOUT_DAYS = 3;
    private const MATCH_DURATION_MINUTES = 120;

    public function get_contents($type, $objkey, $raw = false) {

        $type = sanitize_key($type);
        $objkey = sanitize_key($objkey);

        $url = $this->get_url($type);
            
        if(!empty($url) && !empty($objkey)) {

            $options = get_option('rh4a_options');
            $cache = $options['rh4a_cache_active'] ?? null ;
            $refresh = false;
            if(isset($cache) && intval($cache)) {
                $cached_obj = get_transient($this->get_transient_name($type, $objkey));
                if(false === $cached_obj or [] === $cached_obj) {
                    $refresh = true;
                }
            } else {
                // delete transient, if any
                delete_transient($this->get_transient_name($type, $objkey));
                $refresh = true;
            }
            if($refresh) {
                $response = wp_remote_get($url . $objkey);
                $body     = wp_remote_retrieve_body( $response );
                // Letztes "]" finden und alles dahinter abschneiden. Erstes "[" ebenfalls abschneiden.
                $last = strrpos($body, ']');
                $json = substr($body, 1, $last - 1);
                $json = json_decode($json);
                // When not type standing, then get rid of entries with no time
                if($type !== self::STANDING) {
                    $data = array();
                    $tz1 = new DateTime("now", new DateTimeZone("Europe/Berlin"));
                    $tz2 = new DateTime("now", new DateTimeZone("UTC"));
                    $tz_offset = $tz1->getOffset() - $tz2->getOffset();
                    foreach($json->dataList as $row) {
                        if($row->gTime !== "" and !is_null($row->gTime)) {
                            // Add timestamp
                            $date = explode(".", $row->gDate);
                            $time = explode(":", $row->gTime);
                            $row->_timestamp = (gmmktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]) - $tz_offset);
                            $data[] = $row;
                        }
                    }
                    $json->dataList = $data;
                }
                if(isset($cache) && intval($cache)) {
                    set_transient(
                        $this->get_transient_name($type, $objkey), 
                        $json, 
                        $this->get_cache_timeout($json->dataList, $type)
                    );
                }
                return $raw ? $json : $json->dataList;
            } else {
                return $raw ? $cached_obj : $cached_obj->dataList;
            }
        } else {
            return "";
        } 
    }

    // Called by this class and the uninstall.php file
    public function get_transient_name($type, $objkey) {
        return 'rh4a_'.$type.'_'.$objkey;
    }

    // Called by the uninstall.php file
    public function delete_transients($db) {
        $timetables = $db->get_timetables();
        foreach($timetables as $timetable) {
            delete_transient($this->get_transient_name($timetable->type, $timetable->objkey));
        }

        $standings = $db->get_standings();
        foreach($standings as $standing) {
            delete_transient($this->get_transient_name(self::STANDING, $standing->objkey));
        }

        $next_matches = $db->get_next_matches();
        foreach($next_matches as $next_match) {
            if(strpos($next_match->objkey, ",") !== false) {
                $ids = explode(",", $next_match->objkey);
                foreach($ids as $id) {
                    delete_transient($this->get_transient_name(self::TEAM, $id));
                }
            } else {
                delete_transient($this->get_transient_name(self::TEAM, $next_match->objkey));
            }
        }
    }

    public function get_cache_timeout($data, $type) {
        switch($type) {
            case self::TEAM:
            case self::LEAGUE:
            case self::CLUB:
                // get next match, because no data is changing before the next match took place
                $next_match = $this->get_next_match($data);
                if(isset($next_match)) {
                    // check, if start time of next match is after today and before max cache timeout constant
                    $now = time();
                    $match_end = $next_match->_timestamp + (self::MATCH_DURATION_MINUTES * MINUTE_IN_SECONDS);
                    if($match_end > $now && $match_end < $now + self::MAX_CACHE_TIMEOUT_DAYS * DAY_IN_SECONDS) {
                        return $match_end - $now;
                    } else {
                        return self::MAX_CACHE_TIMEOUT_DAYS * DAY_IN_SECONDS; 
                    }
                } else {
                    return self::MAX_CACHE_TIMEOUT_DAYS * DAY_IN_SECONDS; 
                }
                break;
            case self::STANDING:
                return self::MAX_CACHE_TIMEOUT_DAYS * DAY_IN_SECONDS;
                break;
        }
    }

    public function get_next_match($data) {
        $next_match = null;
        if(count($data) > 0) {
            // get sorted matches (descending)
            $sorted_matches = $this->get_sorted_matches($data, true);

            // take the most far away match as next, if
            // - it is not played yet
            // - its date is in the future
            if ("" !== $sorted_matches[0]->gTime && 
                !is_null($sorted_matches[0]->gTime) && 
                " " === $sorted_matches[0]->gHomeGoals && 
                " " === $sorted_matches[0]->gGuestGoals &&
                $sorted_matches[0]->_timestamp + (self::MATCH_DURATION_MINUTES * MINUTE_IN_SECONDS) > time()
            ) {
                $next_match = $sorted_matches[0];
            }
            
            // loop backwards over matches and use item as next match when
            // - it is not played yet
            // - its date is lower than the current $next_match
            // - its date is in the future
            foreach($sorted_matches as $match) {
                $match_end = $match->_timestamp + (self::MATCH_DURATION_MINUTES * MINUTE_IN_SECONDS);
                if ("" !== $match->gTime && 
                    !is_null($match->gTime) && 
                    " " === $match->gHomeGoals && 
                    " " === $match->gGuestGoals &&
                    $match_end < $next_match->_timestamp + (self::MATCH_DURATION_MINUTES * MINUTE_IN_SECONDS) &&
                    $match_end > time()
                ) {
                    $next_match = $match;
                }
            }
        }
        return $next_match;
    }

    public function get_sorted_matches($data, $desc = false) {
        $new_data = [];
        if(is_array($data) && count($data) > 0) {
            // values will be copied
            $new_data = $data;
            // sort data 
            if($desc) {
                // descending
                usort($new_data, function($a, $b) {
                    if($a->_timestamp > $b->_timestamp) return -1;
                    if($a->_timestamp < $b->_timestamp) return 1;
                    return 0;
                });
            } else {
                // ascending (default)
                usort($new_data, function($a, $b) {
                    if($a->_timestamp > $b->_timestamp) return 1;
                    if($a->_timestamp < $b->_timestamp) return -1;
                    return 0;
                });
            }
        }
        return $new_data;
    }

    public function get_url($type) {
        switch($type) {
            case self::TEAM:
                return 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=team&lvIDNext=';
                break;
            case self::LEAGUE:
                return 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&lvIDNext=';
                break;
            case self::CLUB:
                return 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=club&lvIDNext=';
                break;
            case self::STANDING:
                return 'https://api.h4a.mobi/spo/spo-proxy_public.php?cmd=data&lvTypeNext=class&subType=table&lvIDNext=';
                break;
        }
    }

}
