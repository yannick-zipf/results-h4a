<?php

/**
 * Specifiy renderer for the shortcode type "Standing"
 *
 * @package    results-h4a
 * @subpackage results-h4a/public
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class RH4A_Render_Standing extends RH4A_Render_Abstract {

    protected function get_item() {
        return $this->db->get_standing($this->ID);
    }

    public function render_item_specific() {

        $objkey = sanitize_key($this->item['objkey']);
        $highlight = sanitize_text_field($this->item['highlight']);
        $inv_cols = unserialize($this->item['invisible_columns']);

        $dataList = $this->http->get_contents($this->http::STANDING, $objkey);
        
        // ob_start();
        // var_dump($dataList);
        // // var_dump(time());
        // return ob_get_clean();

        $content = '';
        $anz_teams = count($dataList);
        $anz_matches = ($anz_teams-1)*2;
        if($anz_teams > 0) {
            $content .= '<table class="rh4a-standing alignwide">';
            $content .= "<tr>";
            
            $content .= "<th>".__('#', 'results-h4a')."</th>";
            $content .= "<th>".__('Club', 'results-h4a')."</th>";
            if($inv_cols['matches'] !== 1)      $content .= "<th>".__('Games', 'results-h4a')."</th>";
            if($inv_cols['wins'] !== 1)         $content .= "<th>".__('W', 'results-h4a')."</th>";
            if($inv_cols['ties'] !== 1)         $content .= "<th>".__('T', 'results-h4a')."</th>";
            if($inv_cols['losses'] !== 1)       $content .= "<th>".__('L', 'results-h4a')."</th>";
            if($inv_cols['goals'] !== 1)        $content .= "<th>".__('Goals', 'results-h4a')."</th>";
            if($inv_cols['difference'] !== 1)   $content .= "<th>".__('Diff.', 'results-h4a')."</th>";
            $content .= "<th>".__('Points', 'results-h4a')."</th>";
            
            $content .= "</tr>";

            foreach($dataList as $row) {
                $goal_diff = $row->numGoalsShot - $row->numGoalsGot;
                if($goal_diff > 0) $goal_diff = "+".$goal_diff;
                if($row->tabTeamname === $highlight) {
                    $content .= "<tr class='rh4a-own-team-row'>";
                } else {
                    $content .= "<tr>";
                }

                $content .= "<td>".esc_html($row->tabScore)."</td>";
                $content .= "<td>".esc_html($row->tabTeamname)."</td>";
                if($inv_cols['matches'] !== 1)      $content .= "<td>".esc_html($row->numPlayedGames)."/".esc_html($anz_matches)."</td>";
                if($inv_cols['wins'] !== 1)         $content .= "<td>".esc_html($row->numWonGames)."</td>";
                if($inv_cols['ties'] !== 1)         $content .= "<td>".esc_html($row->numEqualGames)."</td>";
                if($inv_cols['losses'] !== 1)       $content .= "<td>".esc_html($row->numLostGames)."</td>";
                if($inv_cols['goals'] !== 1)        $content .= "<td>".esc_html($row->numGoalsShot).":".esc_html($row->numGoalsGot)."</td>";
                if($inv_cols['difference'] !== 1)   $content .= "<td>".esc_html($goal_diff)."</td>";
                $content .= "<td>".esc_html($row->pointsPlus).":".esc_html($row->pointsMinus)."</td>";
                
                $content .= "</tr>";
            }

            $content .= '</table>';
        }
        return $content;
    }
}
