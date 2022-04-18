<?php

/**
 * Specifiy renderer for the shortcode type "Next Match"
 *
 * @package    results-h4a
 * @subpackage results-h4a/public
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class RH4A_Render_NextMatch extends RH4A_Render_Abstract {

    protected function get_item() {
        return $this->db->get_next_match($this->ID);
    }

    public function render_item_specific() {

        // Here sanitize_text_field instead of sanitize_key, because of multiple comma-separated IDs.
        $objkey = sanitize_text_field($this->item['objkey']);
        $dataLists = [];

        if(strpos($objkey, ",") !== false) {
            $ids = explode(",", $objkey);
            foreach($ids as $id) {
                $dataLists[] = $this->http->get_contents($this->http::TEAM, $id);
            }
        } else {
            $dataLists[] = $this->http->get_contents($this->http::TEAM, $objkey);
        }

        // ob_start();
        // var_dump($dataLists);
        // return ob_get_clean();

        if(count($dataLists) > 0) {
            $matches = []; 
            foreach($dataLists as $dataList) {
                $next_match = $this->http->get_next_match($dataList);
                if(isset($next_match)) {
                    $matches[] = $next_match;
                }
            }
        }

        $content = '';
        if(count($matches) > 0) {
            $content .= '<div class="rh4a-next-match-wrapper">';
            foreach($matches as $match) {
                $class = $this->http->get_contents($this->http::LEAGUE, $match->gClassID, true);
                $content .= $this->render_row($match, $class);
            }
            $content .= '</div>';
        }
        return $content;
    }

    private function render_row($match, $class) {
        $content = "";

        $content .= "<div class='rh4a-next-match'>";

        $classname = esc_html($class->lvTypeLabelStr);
        if(strpos($classname, "/ ") === 0) {
            $classname = substr($classname, 2);
        }
        $content .= "<span class='rh4a-next-match-class'>".$classname."</span>";

        $content .= "<span class='rh4a-next-match-datetime'>$match->gDate, $match->gTime Uhr</span>";

        $content .= "<span class='rh4a-next-match-clubs'>";
        $content .= esc_html($match->gHomeTeam);
        $content .= " - ";
        $content .= esc_html($match->gGuestTeam);
        $content .= "</span>";

        $content .= "<span class='rh4a-next-match-location'>";
        $content .= esc_html($match->gGymnasiumName)." (".esc_html($match->gGymnasiumStreet).", ".esc_html($match->gGymnasiumPostal)." ".esc_html($match->gGymnasiumTown).")";
        $content .= "</span>";

        $content .= "</div>";
        return $content;
    }
}
