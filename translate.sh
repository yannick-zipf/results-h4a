#!/usr/bin/env bash
cd ../../tools/i18n/trunk
php makepot.php wp-plugin ../../../ergebnisse-h4a-free/plugin/results-h4a
mv results-h4a.pot ../../../ergebnisse-h4a-free/plugin/results-h4a/languages/results-h4a.pot
