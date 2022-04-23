#!/usr/bin/env bash
cd ../../tools/i18n/trunk
php makepot.php wp-plugin ../../../ergebnisse-h4a-free/plugin/results-for-handball4all
mv results-h4a.pot ../../../ergebnisse-h4a-free/plugin/results-for-handball4all/languages/results-h4a.pot
