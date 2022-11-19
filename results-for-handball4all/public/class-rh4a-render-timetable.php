<?php

/**
 * Specifiy renderer for the shortcode type "Timetable"
 *
 * @package    results-h4a
 * @subpackage results-h4a/public
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class RH4A_Render_Timetable extends RH4A_Render_Abstract {

    protected function get_item() {
        return $this->db->get_timetable($this->ID);
    }

    public function render_item_specific() {

        $type = sanitize_key($this->item['type']);
        $objkey = sanitize_key($this->item['objkey']);
        $highlight = sanitize_text_field($this->item['highlight']);

        $dataList = $this->http->get_contents($type, $objkey);
        
        // ob_start();
        // var_dump($dataList);
        // // var_dump(time());
        // return ob_get_clean();

        $content = '';
        if(count($dataList) > 0) {
            $content .= '<table class="rh4a-timetable alignwide">';
            foreach($dataList as $row) {
                $content .= $this->render_row($row, $highlight);
            }
            $content .= '</table>';
        }
        return $content;
    }

    private function render_row($row, $highlight = "") {
        $content = "";
        $home_bold = $row->gHomeTeam === $highlight;
        $away_bold = $row->gGuestTeam === $highlight;

        $home_goals = esc_html($row->gHomeGoals);
        $away_goals = esc_html($row->gGuestGoals);

        if($home_bold or $away_bold) {
            $content .= "<tr class='rh4a-own-team-row'>";
        } else {
            $content .= "<tr>";
        }

        $content .= "<td><div class='rh4a-location-wrapper'><span class='dashicons dashicons-location rh4a-location-icon'></span><div class='rh4a-location-popover'>";
        $content .= "<span class='rh4a-location-name'>".esc_html($row->gGymnasiumName)."</span><br>".esc_html($row->gGymnasiumStreet)."<br>".esc_html($row->gGymnasiumPostal)." ".esc_html($row->gGymnasiumTown)."</div></div></td>";

        $content .= "<td>$row->gDate</td>";
        $content .= "<td>$row->gTime</td>";

        $content .= "<td>";

        if($home_bold) $content .= "<span class='rh4a-own-team-span'>";
        $content .= esc_html($row->gHomeTeam);
        if($home_bold) $content .= "</span>";

        $content .= "</td><td>";

        if($away_bold) $content .= "<span class='rh4a-own-team-span'>";
        $content .= esc_html($row->gGuestTeam);
        if($away_bold) $content .= "</span>";

        $content .= "</td><td>";
        $content .= $home_goals;
        if(" " !== $home_goals && " " !== $away_goals) {
            $content .= " : ";
        }
        $content .= $away_goals;
        $content .= "</td></tr>";
        return $content;
    }
}
